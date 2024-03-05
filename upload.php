<?php include 'session_check.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSV Upload</title>
</head>

<body>
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <label for="csv_file">Choose CSV file:</label>
        <input type="file" name="csv_file" accept=".csv" required>
        <button type="submit">Upload</button>
    </form>

    <?php
    include 'db_params.php';

    // Check if a file is uploaded
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == UPLOAD_ERR_OK) {
        // Get the temporary file name
        $tmpFileName = $_FILES['csv_file']['tmp_name'];

        // Prepare the SQL statement for inserting data
        $insertStatement = $transactionsDb->prepare('INSERT INTO transactions (transaction_date, description, debit, credit, balance) VALUES (:transaction_date, :description, :debit, :credit, :balance)');

        // Bind parameters
        $insertStatement->bindParam(':transaction_date', $transactionDate);
        $insertStatement->bindParam(':description', $description);
        $insertStatement->bindParam(':debit', $debit);
        $insertStatement->bindParam(':credit', $credit);
        $insertStatement->bindParam(':balance', $balance);

        // Open the uploaded CSV file
        $csvFile = fopen($tmpFileName, 'r');

        // Read and insert data from each row in the CSV file
        while (($row = fgetcsv($csvFile)) !== false) {
            list($transactionDate, $description, $debit, $credit, $balance) = $row;

            // Execute the prepared statement
            $insertStatement->execute();
        }

        // Close the CSV file
        fclose($csvFile);

        // Define categories and corresponding keywords
        $categoriesAndKeywords = [
            'Groceries' => ['SAFEWAY', 'REAL CDN SUPERS', 'WALMART', 'COSTCO WHOLESAL',],
            'Utilities' => ['FORTISBC GAS', 'SHAW CABLE', 'ROGERS MOBILE'],
            'Donations' => ['RED CROSS', 'World Vision'],
            'Eating Out' => ['ST JAMES RESTAURAT', 'Subway', 'PUR & SIMPLE RESTAUR', 'MCDONALDS', 'WHITE SPOT RESTAURAN', 'TIM HORTONS'],
            'Health' => ['GATEWAY          MSP',],
            'Other' => ['ICBC             INS', 'CANADIAN TIRE', 'ICBC', '7-ELEVEN', 'O.D.P. FEE', 'MONTHLY ACCOUNT FEE']
        ];

        // Prepare statement for inserting into the filters table
        $insertFilterStmt = $transactionsDb->prepare("INSERT INTO filters (category, keyword) VALUES (?, ?)");

        // Insert data into the filters table
        foreach ($categoriesAndKeywords as $category => $keywords) {
            foreach ($keywords as $keyword) {
                $insertFilterStmt->bindValue(1, $category, SQLITE3_TEXT);
                $insertFilterStmt->bindValue(2, $keyword, SQLITE3_TEXT);
                $insertFilterStmt->execute();
            }
        }

        $insertBucketStmt = $transactionsDb->prepare("INSERT INTO buckets (category, description)
            SELECT f.category, t.description
            FROM transactions t
            JOIN filters f ON LOWER(t.description) LIKE '%' || f.keyword || '%'");

        // Execute the prepared statement to insert data into the buckets table
        $insertBucketStmt->execute();

        // Prepare statement for inserting into the reports table
        $insertReportStmt = $transactionsDb->prepare("INSERT INTO reports (category, total)
            SELECT category, SUM(total) as total
            FROM (
                SELECT b.category, COUNT(*) as total
                FROM buckets b
                GROUP BY b.category, b.description
            )
            GROUP BY category");

        // Execute the prepared statement to insert data into the reports table
        $insertReportStmt->execute();

        // Prepare statement for inserting into the aggregated_data table
        $insertAggregatedDataStmt = $transactionsDb->prepare("INSERT INTO aggregated_data (category, total, transaction_date)
            SELECT b.category, COUNT(*) as total, MAX(t.transaction_date) as transaction_date
            FROM buckets b
            JOIN transactions t ON LOWER(b.description) LIKE '%' || LOWER(t.description) || '%'
            WHERE t.transaction_date IS NOT NULL
            GROUP BY b.category, t.transaction_date");

        // Execute the prepared statement to insert data into the aggregated_data table
        $insertAggregatedDataStmt->execute();



        // Display success message or redirect to another page
        echo "File uploaded successfully!";
    } else {
        // Display an error message if no file is uploaded or an error occurred
    
        //no file
        if (!isset($_FILES['csv_file'])) {
            echo "Please upload a file!";
        } else {
            echo "Error uploading file!";
        }

    }

    // Close the database connection
    $transactionsDb->close();
    ?>

    <!-- Button to generate report -->
    <br><br><a href="generate_report.php"><button>Generate Report</button></a>

    <?php include 'footer.php'; ?>
</body>

</html>