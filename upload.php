<?php ob_start(); ?>
<?php include 'session_check.php'; ?>
<!DOCTYPE html>

<body>
    <div class="container">
        <h1 class="mt-5">CSV Upload</h1>
        <form action="upload.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="csv_file">Choose CSV file:</label>
                <input type="file" class="form-control-file" name="csv_file" id="csv_file" accept=".csv" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
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

            // Get the original filename
            $originalFilename = $_FILES['csv_file']['name'];

            // Append ".imported" to the original filename
            $newFilename = pathinfo($originalFilename, PATHINFO_FILENAME) . '.imported' . '.' . pathinfo($originalFilename, PATHINFO_EXTENSION);

            // Rename the file
            rename($tmpFileName, $newFilename);
                
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
           echo "<div class='alert alert-success mt-3' role='alert'>File uploaded successfully!</div>";
        } else {
            // Display an error message if no file is uploaded or an error occurred
        
            //no file
            if (!isset($_FILES['csv_file'])) {
                echo "<div class='alert alert-danger mt-3' role='alert'>Please upload a file!</div>";
            } else {
                echo "<div class='alert alert-danger mt-3' role='alert'>Error uploading file!</div>";
            }
        }

        // Close the database connection
        $transactionsDb->close();
        ?>

    <!-- Button to generate report -->
    <a href="generate_report.php" class="btn btn-primary mt-3">Generate Report</a>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>
<?php ob_end_flush(); ?>