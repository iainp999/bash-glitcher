#!/bin/bash

input_file="urls.txt"
db_file="url_data.db"
log_file="curl_log.txt"

# Check if sqlite3 command is available
if command -v sqlite3 &> /dev/null; then
    sqlite3_installed=true
else
    sqlite3_installed=false
fi

# Function to create the table if it doesn't exist
create_table() {
    sqlite3 "$db_file" "CREATE TABLE IF NOT EXISTS url_info (id INTEGER PRIMARY KEY, url TEXT NOT NULL, status_code INTEGER NOT NULL, response_size INTEGER NOT NULL, timestamp INTEGER NOT NULL);"
}

# Create the database file and table if sqlite3 is installed
if $sqlite3_installed; then
    # Create the parent directory of the database file if it doesn't exist
    if ! mkdir -p "$(dirname "$db_file")"; then
        echo "Error: Could not create parent directory for database file" >&2
        exit 1
    fi

    touch "$db_file"
    create_table
fi

# Read URLs from input file
while IFS= read -r url; do
    if [[ -z "$url" ]]; then
        continue
    fi

    # Strip the URL scheme (method) if present
    stripped_url="${url#*://}"

    # Get the status code and response size
    response=$(curl --max-time 60 -sI -L -w "HTTPSTATUS:%{http_code};SIZE:%{size_download};FINALURL:%{url_effective}\n" -o /dev/null -v -L "$url" 2>"$log_file")
    status_code=$(echo "$response" | grep -oP 'HTTPSTATUS:\K\d+')

    # Follow redirects and retrieve response size after redirect
    final_url="$url"
    while [[ "$final_url" != "$(echo "$response" | grep -oP 'FINALURL:\K.*')" ]]; do
        final_url="$(echo "$response" | grep -oP 'FINALURL:\K.*')"
        response=$(curl --max-time 60 -sI -L -w "HTTPSTATUS:%{http_code};SIZE:%{size_download};FINALURL:%{url_effective}\n" -o /dev/null -v -L "$final_url" 2>"$log_file")
    done

    response_size=$(echo "$response" | grep -oP 'SIZE:\K\d+')
    timestamp=$(date +%s)

    # Output the data to terminal if sqlite3 is not available
    if ! $sqlite3_installed; then
        echo "URL: $stripped_url"
        echo "Status code: $status_code"
        echo "Response size: $response_size"
        echo "Timestamp: $timestamp"
        echo
        continue
    fi

    # Insert data into the SQLite database
    sqlite3 "$db_file" "INSERT INTO url_info (url, status_code, response_size, timestamp) VALUES ('$stripped_url', '$status_code', '$response_size', '$timestamp');"

done < "$input_file"
