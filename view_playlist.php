<?php
include 'db.php';

$id = $_GET['id'];

$result = $conn->query("
    SELECT songs.* FROM songs
    JOIN playlist_song ON songs.song_id = playlist_song.song_id
    WHERE playlist_song.playlist_id = $id
");
?>

<h2>Playlist Songs</h2>

<?php while($row = $result->fetch_assoc()){ ?>
    <div>
        <p><?php echo $row['title']; ?></p>
        <audio controls>
            <source src="songs/<?php echo $row['file_path']; ?>">
        </audio>
    </div>
<?php } ?>