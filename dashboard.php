<?php
include 'db.php';
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
}

$search = "";
if(isset($_GET['search'])){
    $search = $_GET['search'];
    $result = $conn->query("SELECT * FROM songs WHERE title LIKE '%$search%'");
} else {
    $result = $conn->query("SELECT * FROM songs");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Musicly</title>
    <link rel="stylesheet" href="style.css">
    <!-- Phosphor Icons (free, lightweight) -->
    <script src="https://unpkg.com/@phosphor-icons/web@2.1.1/src/index.js"></script>
</head>

<body>

<!-- ══ Sidebar ══ -->
<div class="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon">♫</div>
        <span>Musicly</span>
    </div>

    <nav>
        <div class="sidebar-section-label">Menu</div>
        <ul>
            <li class="active">
                <span class="nav-icon"><i class="ph ph-house-simple"></i></span> Home
            </li>
            <li>
                <span class="nav-icon"><i class="ph ph-magnifying-glass"></i></span> Search
            </li>
            <li>
                <span class="nav-icon"><i class="ph ph-books"></i></span> Library
            </li>
            <li>
                <span class="nav-icon"><a href="playlist.php"><i class="ph ph-playlist"></i></a></span>
                <a href="playlist.php">Playlists</a>
            </li>
        </ul>

        <div class="sidebar-section-label" style="margin-top:24px;">Settings</div>
        <ul>
            <li>
                <span class="nav-icon"><a href="admin.php"><i class="ph ph-gear"></i></a></span>
                <a href="admin.php">Admin</a>
            </li>
        </ul>
    </nav>
</div>

<!-- ══ Main ══ -->
<div class="main">

    <!-- Topbar -->
    <div class="topbar">
        <form method="GET" style="flex:1;">
            <div class="search-wrap">
                <span class="search-icon"><i class="ph ph-magnifying-glass"></i></span>
                <input type="text" name="search" placeholder="Search songs, artists…" value="<?php echo htmlspecialchars($search); ?>">
            </div>
        </form>
        <div class="topbar-right">
            <div class="avatar">U</div>
        </div>
    </div>

    <!-- Section header -->
    <div class="section-header">
        <h2><?php echo $search ? 'Results for "'.htmlspecialchars($search).'"' : 'Trending Songs'; ?></h2>
        <a href="#">See all →</a>
    </div>

    <!-- Song Grid -->
    <div class="song-grid">
        <?php while($row = $result->fetch_assoc()) { ?>
            <div class="song-card"
                 onclick="playSong('songs/<?php echo htmlspecialchars($row['file_path']); ?>',
                                   '<?php echo addslashes(htmlspecialchars($row['title'])); ?>',
                                   'covers/<?php echo htmlspecialchars($row['cover']); ?>',
                                   this)">

                <div class="cover">
                    <img src="covers/<?php echo htmlspecialchars($row['cover']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" loading="lazy">
                    <div class="play-overlay">
                        <div class="play-btn-circle">▶</div>
                    </div>
                </div>

                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                <div class="song-meta">Trending</div>
            </div>
        <?php } ?>
    </div>

</div>

<!-- ══ Right Panel ══ -->
<div class="right-panel">
    <h3>Now Playing</h3>

    <div class="now-art" id="nowArt">
        <div class="idle-art" id="idleArt">♫</div>
        <img id="nowCover" src="" alt="" style="display:none; position:absolute;inset:0;width:100%;height:100%;object-fit:cover;">
    </div>

    <div>
        <p id="nowTitle" style="font-family:'Syne',sans-serif;font-size:17px;font-weight:700;letter-spacing:-0.2px;line-height:1.3;">
            Select a song
        </p>
        <p class="now-artist">—</p>
    </div>

    <!-- Animated waveform -->
    <div class="waveform" id="waveform">
        <span></span><span></span><span></span><span></span><span></span>
        <span></span><span></span><span></span><span></span><span></span>
    </div>
</div>

<!-- ══ Bottom Player ══ -->
<div class="player">

    <!-- Left: song info -->
    <div class="player-info">
        <div class="player-thumb" id="playerThumb">♫</div>
        <div style="min-width:0;">
            <p id="playerTitle">No song playing</p>
            <p class="player-sub">Musicly</p>
        </div>
    </div>

    <!-- Center: controls + progress -->
    <div class="player-center">
        <div class="player-controls">
            <button class="ctrl-btn" onclick="document.getElementById('audioPlayer').currentTime -= 10" title="Rewind 10s">⏮</button>
            <button class="ctrl-btn play-main" id="playPauseBtn" onclick="togglePlay()">▶</button>
            <button class="ctrl-btn" onclick="document.getElementById('audioPlayer').currentTime += 10" title="Forward 10s">⏭</button>
        </div>
        <div class="progress-wrap" onclick="seekAudio(event)">
            <div class="progress-fill" id="progressFill"></div>
        </div>
    </div>

    <!-- Right: volume -->
    <div class="player-right">
        <span class="volume-icon">🔊</span>
        <input type="range" min="0" max="1" step="0.01" value="1" id="volumeSlider"
               style="width:90px;cursor:pointer;" oninput="setVolume(this.value)">
    </div>

    <!-- Hidden audio element (all logic stays identical) -->
    <audio id="audioPlayer"></audio>
</div>

<script>
let currentCard = null;
let currentPlaying = false;

function playSong(file, title, cover, element) {
    const player   = document.getElementById('audioPlayer');
    const nowTitle = document.getElementById('nowTitle');
    const botTitle = document.getElementById('playerTitle');
    const waveform = document.getElementById('waveform');
    const nowCover = document.getElementById('nowCover');
    const idleArt  = document.getElementById('idleArt');
    const thumb    = document.getElementById('playerThumb');
    const btn      = document.getElementById('playPauseBtn');

    // Update audio source
    player.src = file;
    player.play();
    currentPlaying = true;

    // Update titles
    nowTitle.textContent = title;
    botTitle.textContent = title;

    // Update cover art
    if (cover) {
        nowCover.src = cover;
        nowCover.style.display = 'block';
        idleArt.style.display = 'none';
        // Player thumb
        thumb.innerHTML = `<img src="${cover}" alt="${title}" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">`;
    }

    // Waveform animation
    waveform.classList.add('playing');

    // Play/Pause button
    btn.textContent = '⏸';

    // Highlight active card
    document.querySelectorAll('.song-card').forEach(c => c.classList.remove('active'));
    element.classList.add('active');
    currentCard = element;

    // Progress bar
    player.ontimeupdate = () => {
        if (player.duration) {
            document.getElementById('progressFill').style.width =
                ((player.currentTime / player.duration) * 100) + '%';
        }
    };

    player.onended = () => {
        btn.textContent = '▶';
        waveform.classList.remove('playing');
        currentPlaying = false;
    };
}

function togglePlay() {
    const player = document.getElementById('audioPlayer');
    const btn    = document.getElementById('playPauseBtn');
    const wave   = document.getElementById('waveform');

    if (!player.src) return;

    if (player.paused) {
        player.play();
        btn.textContent = '⏸';
        wave.classList.add('playing');
        currentPlaying = true;
    } else {
        player.pause();
        btn.textContent = '▶';
        wave.classList.remove('playing');
        currentPlaying = false;
    }
}

function seekAudio(e) {
    const player = document.getElementById('audioPlayer');
    if (!player.src || !player.duration) return;
    const rect = e.currentTarget.getBoundingClientRect();
    const ratio = (e.clientX - rect.left) / rect.width;
    player.currentTime = ratio * player.duration;
}

function setVolume(val) {
    document.getElementById('audioPlayer').volume = val;
}
</script>

</body>
</html>