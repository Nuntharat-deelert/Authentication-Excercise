<?php
$host = 'localhost';
$user = 's68160386';
$pass = 'n8HSZNWZ';
$dbname = 's68160386';

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');
?>