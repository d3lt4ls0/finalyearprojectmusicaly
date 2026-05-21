<?php
include 'db.php';

$song_id = $_POST['song_id'];
$playlist_id = $_POST['playlist_id'];

$conn->query("INSERT INTO playlist_song (playlist_id, song_id) VALUES ('$playlist_id','$song_id')");

header("Location: dashboard.php");
?>