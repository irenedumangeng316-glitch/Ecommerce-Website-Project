<?php

$dsn      = "mysql:host=localhost;dbname=shop_db;charset=utf8mb4";
$username = "root";        
$password = "";


$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       
    PDO::ATTR_EMULATE_PREPARES   => false,                  
    PDO::ATTR_PERSISTENT         => true           
];

try {
    $conn = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    // Log error securely instead of exposing details to users
    error_log("Database connection failed: " . $e->getMessage());
    die("Database connection error. Please try again later.");
}
?>