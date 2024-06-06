<?php
$servername = "localhost";
$username = "root"; // Change the username or password depending on the phpmyadmin
$password = ""; 
$dbname = "hmisvoting";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
