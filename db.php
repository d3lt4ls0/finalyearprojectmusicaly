<?php
$conn = new mysqli("localhost", "root", "", "musicly");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>