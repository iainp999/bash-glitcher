#!/bin/bash

input_file="input_urls.txt"
db_file="url_data.db"

# Check if sqlite3 is available
command -v sqlite3 >/dev/null 2>&1
sqlite3_available=$?

# Create the SQLite database and table if they don't exist and sqlite3 is available
if [ $sqlite3_available -eq 0 ]; then
    sqlite3 "$db_file" <<EOF
    CREATE TABLE IF NOT EXISTS url_info (
        url TEXT,
        status_code INTEGER,
        response_size INTEGER
    );
EOF
fi

# Read the input file line by line and process each URL
while IFS= read -r url; do
    if [[ -n "$url" ]]; then
        # Send a curl request and store the output
        curl_output=$(curl -s -L -o /dev/null -w "status_code:%{http_code},response_size:%{size_download}" "$url")

        # Extract the status code and response size
        status_code=$(echo "$curl_output" | grep -oP '(?<=status_code:)\d+')
        response_size=$(echo "$curl_output" | grep -oP '(?<=response_size:)\d+(\.\d+)?')

        # Insert the data into the SQLite database if sqlite3 is available, otherwise output to the terminal
        if [ $sqlite3_available -eq 0 ]; then
            sqlite3 "$db_file" "INSERT INTO url_info (url, status_code, response_size) VALUES ('$url', $status_code, $response_size);"
        else
            echo "URL: $url, Status code: $status_code, Response size: $response_size"
        fi
    fi
done < "$input_file"

