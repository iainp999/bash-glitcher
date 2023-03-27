<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>URL Status Report</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap4.min.css">
</head>
<body>
    <div class="container">
        <h1 class="my-5">URL Status Report</h1>
        <?php
            // Open the database
            $db = new SQLite3('../url_data.db');

            // Retrieve the data from the database
            $results = $db->query('SELECT * FROM url_info');

            // Output the data in a table
            echo '<table id="url-table" class="table table-striped table-bordered">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>ID</th>';
            echo '<th>URL</th>';
            echo '<th>Status Code</th>';
            echo '<th>Response Size</th>';
            echo '<th>Timestamp</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            while ($row = $results->fetchArray()) {
                echo '<tr>';
                echo '<td>' . $row['id'] . '</td>';
                echo '<td>' . $row['url'] . '</td>';
                echo '<td>' . $row['status_code'] . '</td>';
                echo '<td>' . $row['response_size'] . '</td>';
                echo '<td>' . date('Y-m-d H:i:s', $row['timestamp']) . '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';

            // Close the database
            $db->close();
        ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#url-table').DataTable();
        });
    </script>
</body>
</html>
