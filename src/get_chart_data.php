<?php
include 'db_params.php';

$query = "SELECT category, SUM(total) as total FROM aggregated_data GROUP BY category";
$result = $transactionsDb->query($query);

$data = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $data[] = $row;
}

echo json_encode($data);
?>
