<?php
$host = "localhost";
$user = "root"; // your DB username
$pass = "Sivani@123";     // your DB password  ttimmgumtntdwokt
$dbname = "fileshare"; // your DB name

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
