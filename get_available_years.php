<?php
ob_start();
include 'db_params.php';

// Query to get distinct transaction dates from the aggregated_data table
$query = "SELECT DISTINCT SUBSTR(transaction_date, 7, 4) as year FROM aggregated_data";
$result = $transactionsDb->query($query);

$availableYears = [];

while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $availableYears[] = $row['year'];
}
// Return the available years as JSON
header('Content-Type: application/json');
echo json_encode($availableYears);
?>
<?php ob_end_flush(); ?>
