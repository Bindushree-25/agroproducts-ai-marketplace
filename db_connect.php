<?php
// db_connection.php

// Database connection parameters
$host = 'localhost';          // e.g., 'localhost'
$username = 'root';  // e.g., 'root'
$password = '';  // your database password
$database = 'shop_db';  // e.g., 'myshopdb'

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>