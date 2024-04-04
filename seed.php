<?php
$db = new SQLite3('kfs_chart_of_accounts.db');

// Create table users (id, netid, name, email, price_group)

$db -> exec('CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY, netid TEXT, name TEXT, email TEXT, price_group TEXT)');


// Create table accounts (id, name, number, status, effective_date, expiration_date)

$db -> exec('CREATE TABLE IF NOT EXISTS accounts (id INTEGER PRIMARY KEY, name TEXT, number TEXT, status TEXT, effective_date TEXT, expiration_date TEXT)');

// Create table authorized_users (id, account_id, netid, role), with foreign key account_id references accounts(id)

$db -> exec('CREATE TABLE IF NOT EXISTS authorized_users (id INTEGER PRIMARY KEY, account_id INTEGER, netid TEXT, role TEXT, FOREIGN KEY(account_id) REFERENCES accounts(id))');

