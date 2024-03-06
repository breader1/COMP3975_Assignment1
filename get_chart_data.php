<?php
ob_start();
include 'db_params.php';

// Assuming $_GET['year'] is the parameter passed from the URL
$selectedYear = isset($_GET['year']) ? $_GET['year'] : '';

// Use a prepared statement to avoid SQL injection
$query = $transactionsDb->prepare("SELECT category, SUM(total) as total FROM aggregated_data WHERE SUBSTR(transaction_date, 7, 4) = :year GROUP BY category");
$query->bindParam(':year', $selectedYear, SQLITE3_TEXT);
$result = $query->execute();

$data = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $data[] = $row;
}

echo json_encode($data);
?>
<?php ob_end_flush(); ?>
