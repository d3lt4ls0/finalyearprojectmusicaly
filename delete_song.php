<?php
include 'db.php';

$id = $_GET['id'];

$conn->query("DELETE FROM songs WHERE song_id=$id");

header("Location: admin.php");
?>