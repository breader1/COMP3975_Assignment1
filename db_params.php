<?php

$transactionsDatabaseFile = 'Transactions.db';
$usersDatabaseFile = 'Users.db';

// Create or open the transactions database
$transactionsDb = new SQLite3($transactionsDatabaseFile);

// Create or open the users database
$usersDb = new SQLite3($usersDatabaseFile);

// Assuming $adminUsername and $adminPassword are your admin credentials
$adminUsername = 'aa@aa.aa';
$adminPassword = 'P@$$w0rd';

// Create the admin table with a prepared statement
$createAdminTableStmt = $usersDb->prepare('CREATE TABLE IF NOT EXISTS admin (
    username TEXT PRIMARY KEY,
    password TEXT
)');
$createAdminTableStmt->execute();

// Create the table if it doesn't exist
$transactionsDb->exec('CREATE TABLE IF NOT EXISTS transactions (
    transaction_id INTEGER PRIMARY KEY AUTOINCREMENT,
    transaction_date TEXT,
    description TEXT,
    debit REAL,
    credit REAL,
    balance REAL
)');


// Create the filters table with an auto-incrementing id column
$transactionsDb->exec('CREATE TABLE IF NOT EXISTS filters (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    category TEXT,
    keyword TEXT
)');


// Create the buckets table
$transactionsDb->exec('CREATE TABLE IF NOT EXISTS buckets (
    category TEXT,
    description TEXT
)');

// Create the reports table
$transactionsDb->exec('CREATE TABLE IF NOT EXISTS reports (
    category TEXT,
    total REAL
)');

$transactionsDb->exec('CREATE TABLE IF NOT EXISTS aggregated_data (
    category TEXT,
    total REAL,
    transaction_date TEXT
)');

// Check if there is already an entry in the admin table
$checkAdminStmt = $usersDb->prepare('SELECT COUNT(*) FROM admin');
$result = $checkAdminStmt->execute();
$count = $result->fetchArray(SQLITE3_NUM)[0];

if ($count == 0) {
    // Insert the admin record using a prepared statement
    $insertAdminStmt = $usersDb->prepare('INSERT INTO admin (username, password) VALUES (:username, :password)');

    // Bind parameters
    $insertAdminStmt->bindParam(':username', $adminUsername, SQLITE3_TEXT);
    $insertAdminStmt->bindParam(':password', $adminPassword, SQLITE3_TEXT);

    $insertAdminStmt->execute();

    //echo "Admin record inserted successfully.";
} else {
    //echo "Admin record already exists.";
}
?>