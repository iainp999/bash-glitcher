<?php
$db_file = "../url_data.db";
$items_per_page = 10;

// Connect to the SQLite database
$db = new PDO("sqlite:$db_file");

// Get the current page number
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;

// Calculate the LIMIT and OFFSET values for the SQL query
$offset = ($page - 1) * $items_per_page;

// Get the total number of records in the table
$total_records = $db->query("SELECT COUNT(*) FROM url_info")->fetchColumn();
$total_pages = ceil($total_records / $items_per_page);

// Fetch the records for the current page
$stmt = $db->prepare("SELECT * FROM url_info LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>URL Data</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }
        .pagination a {
            padding: 8px 16px;
            text-decoration: none;
            border: 1px solid #ddd;
            color: #000;
        }
        .pagination a:hover {
            background-color: #ddd;
        }
    </style>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>URL</th>
                <th>Status Code</th>
                <th>Response Size</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['url']) ?></td>
                    <td><?= htmlspecialchars($row['status_code']) ?></td>
                    <td><?= htmlspecialchars($row['response_size']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?= $i ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
</body>
</html>

