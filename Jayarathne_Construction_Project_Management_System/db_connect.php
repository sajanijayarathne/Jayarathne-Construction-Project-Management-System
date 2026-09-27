<?php
// File Path: db_connect.php

$host = "localhost";
$user = "root";
$password = "";
$database = "jayarathne_construction_db";

// Create database connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>