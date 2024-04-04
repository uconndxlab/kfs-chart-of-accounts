<?php
$db = new SQLite3('kfs_chart_of_accounts.db');

// spit out all the accounts in the accounts table

$results = $db -> query('SELECT * FROM accounts');

while ($row = $results -> fetchArray()) {
    echo $row['id'] . ' ' . $row['name'] . ' ' . $row['number'] . ' ' . $row['status'] . ' ' . $row['effective_date'] . ' ' . $row['expiration_date'] . '<br>';
}

