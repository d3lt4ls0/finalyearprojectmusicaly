<?php
include 'db.php';
session_start();

$success = "";

if(isset($_POST['create'])){
    $name = $_POST['name'];
    $conn->query("INSERT INTO playlists (name) VALUES ('$name')");
    $success = "\"" . htmlspecialchars($name) . "\" created successfully!";
}

$result = $conn->query("SELECT * FROM playlists ORDER BY playlist_id DESC");
$count  = $result->num_rows;
$result = $conn->query("SELECT * FROM playlists ORDER BY playlist_id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Musicly — Playlists</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --accent:  #a78bfa;
            --accent2: #f472b6;
            --bg:      #07050f;
            --bg3:     #150f28;
            --glass:   rgba(255,255,255,0.04);
            --gb:      rgba(255,255,255,0.08);
            --text:    #f1eeff;
            --muted:   rgba(241,238,255,0.45);
            --sidebar-w: 230px;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }
        body::before, body::after {
            content: ''; position: fixed; border-radius: 50%;
            filter: blur(110px); pointer-events: none; z-index: 0;
        }
        body::before {
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(167,139,250,0.16) 0%, transparent 70%);
            top: -130px; left: -100px;
        }
        body::after {
            width: 360px; height: 360px;
            background: radial-gradient(circle, rgba(244,114,182,0.11) 0%, transparent 70%);
            bottom: 60px; right: 60px;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-w); height: 100vh;
            position: fixed; left: 0; top: 0;
            background: rgba(14,10,26,0.85);
            backdrop-filter: blur(20px);
            border-right: 1px solid var(--gb);
            padding: 32px 20px;
            display: flex; flex-direction: column; gap: 40px;
            z-index: 10;
        }
        .sidebar-logo { display: flex; align-items: center; gap: 10px; padding-left: 8px; }
        .logo-icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; flex-shrink: 0;
        }
        .sidebar-logo span {
            font-family: 'Syne', sans-serif; font-size: 20px; font-weight: 800;
            background: linear-gradient(135deg, #fff 40%, var(--accent));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        .nav-label {
            font-size: 10px; font-weight: 600; letter-spacing: 1.5px;
            text-transform: uppercase; color: rgba(255,255,255,0.2);
            padding: 0 14px; margin-bottom: 4px;
        }
        nav ul { list-style: none; display: flex; flex-direction: column; gap: 3px; }
        nav li {
            display: flex; align-items: center; gap: 11px;
            padding: 10px 14px; border-radius: 11px;
            color: var(--muted); font-size: 14px; font-weight: 500;
            cursor: pointer; transition: all 0.2s;
        }
        nav li a { color: inherit; text-decoration: none; display: contents; }
        nav li:hover { background: var(--glass); color: var(--text); }
        nav li.active { background: rgba(167,139,250,0.15); color: var(--accent); }
        .ni { font-size: 15px; width: 20px; text-align: center; }

        /* Main */
        .main {
            margin-left: var(--sidebar-w);
            padding: 40px 48px;
            position: relative; z-index: 1;
            max-width: 1000px;
        }

        .page-header { margin-bottom: 36px; }
        .page-header h1 {
            font-family: 'Syne', sans-serif;
            font-size: 30px; font-weight: 800; letter-spacing: -0.5px; margin-bottom: 6px;
        }
        .page-header p { font-size: 14px; color: var(--muted); }

        /* Alert */
        .alert {
            display: flex; align-items: center; gap: 12px;
            background: rgba(52,211,153,0.1);
            border: 1px solid rgba(52,211,153,0.3);
            border-radius: 14px; padding: 14px 18px;
            margin-bottom: 28px; font-size: 14px; color: #6ee7b7;
            animation: slideIn 0.35s cubic-bezier(0.34,1.56,0.64,1) both;
        }
        @keyframes slideIn {
            from { transform: translateY(-10px); opacity: 0; }
            to   { transform: translateY(0); opacity: 1; }
        }

        /* Create card */
        .create-card {
            background: rgba(14,10,26,0.72);
            backdrop-filter: blur(20px);
            border: 1px solid var(--gb);
            border-radius: 22px;
            padding: 32px 36px;
            margin-bottom: 40px;
            position: relative; overflow: hidden;
        }
        .create-card::before {
            content: '';
            position: absolute; top: 0; left: 8%; right: 8%;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--accent), var(--accent2), transparent);
        }
        .create-card h2 {
            font-family: 'Syne', sans-serif;
            font-size: 16px; font-weight: 700; margin-bottom: 18px;
        }
        .create-row { display: flex; gap: 12px; }
        .input-wrap { position: relative; flex: 1; }
        .input-icon {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            color: var(--muted); font-size: 15px; pointer-events: none;
            transition: color 0.2s;
        }
        .input-wrap:focus-within .input-icon { color: var(--accent); }
        input[type=text] {
            width: 100%;
            padding: 13px 16px 13px 44px;
            background: var(--glass);
            border: 1px solid var(--gb);
            border-radius: 12px;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 15px; outline: none;
            transition: border-color 0.2s, background 0.2s;
        }
        input[type=text]:focus {
            border-color: rgba(167,139,250,0.55);
            background: rgba(167,139,250,0.07);
        }
        input[type=text]::placeholder { color: var(--muted); }
        .btn-create {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 13px 24px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            border: none; border-radius: 12px;
            color: #fff;
            font-family: 'Syne', sans-serif;
            font-size: 14px; font-weight: 700;
            cursor: pointer; white-space: nowrap;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 18px rgba(167,139,250,0.35);
        }
        .btn-create:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(167,139,250,0.5); }
        .btn-create:active { transform: scale(0.97); }

        /* Section header */
        .section-header {
            display: flex; align-items: center; gap: 10px; margin-bottom: 20px;
        }
        .section-header h2 {
            font-family: 'Syne', sans-serif; font-size: 20px; font-weight: 700; letter-spacing: -0.2px;
        }
        .section-count {
            background: var(--glass); border: 1px solid var(--gb);
            border-radius: 50px; padding: 3px 12px;
            font-size: 12px; color: var(--muted);
        }

        /* Grid */
        .playlist-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }

        /* Playlist card */
        .playlist-card {
            background: var(--bg3);
            border: 1px solid var(--gb);
            border-radius: 18px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.34,1.56,0.64,1);
            position: relative; overflow: hidden;
            display: flex; flex-direction: column; gap: 14px;
            text-decoration: none; color: inherit;
        }
        .playlist-card::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(167,139,250,0.07) 0%, transparent 60%);
            opacity: 0; transition: opacity 0.3s;
        }
        .playlist-card:hover {
            transform: translateY(-4px);
            border-color: rgba(167,139,250,0.35);
            background: #1a1232;
        }
        .playlist-card:hover::before { opacity: 1; }

        /* Art */
        .pl-art {
            width: 100%; aspect-ratio: 1;
            border-radius: 12px; overflow: hidden; position: relative;
        }
        .pl-art-inner {
            width: 100%; height: 100%;
            display: flex; align-items: center; justify-content: center;
            font-size: 42px;
        }
        .playlist-card:nth-child(6n+1) .pl-art-inner { background: linear-gradient(135deg,#2d1b69,#7c3aed); }
        .playlist-card:nth-child(6n+2) .pl-art-inner { background: linear-gradient(135deg,#7c2d12,#f97316); }
        .playlist-card:nth-child(6n+3) .pl-art-inner { background: linear-gradient(135deg,#0f4c75,#1b98e0); }
        .playlist-card:nth-child(6n+4) .pl-art-inner { background: linear-gradient(135deg,#134e4a,#0d9488); }
        .playlist-card:nth-child(6n+5) .pl-art-inner { background: linear-gradient(135deg,#4a1d96,#db2777); }
        .playlist-card:nth-child(6n+0) .pl-art-inner { background: linear-gradient(135deg,#1e3a5f,#f59e0b); }

        .pl-play-ov {
            position: absolute; inset: 0;
            background: rgba(0,0,0,0.35);
            display: flex; align-items: center; justify-content: center;
            opacity: 0; transition: opacity 0.2s;
        }
        .playlist-card:hover .pl-play-ov { opacity: 1; }
        .pl-play-btn {
            width: 46px; height: 46px; border-radius: 50%;
            background: var(--accent);
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; color: #fff;
            box-shadow: 0 4px 18px rgba(167,139,250,0.5);
        }

        .pl-name {
            font-family: 'Syne', sans-serif;
            font-size: 14px; font-weight: 700; letter-spacing: -0.1px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .pl-meta { font-size: 12px; color: var(--muted); margin-top: 2px; }

        .pl-open {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 14px; border-radius: 9px;
            background: var(--glass); border: 1px solid var(--gb);
            color: var(--muted); font-size: 12.5px; font-weight: 500;
            text-decoration: none; transition: all 0.2s; align-self: flex-start;
        }
        .pl-open:hover {
            background: rgba(167,139,250,0.1);
            border-color: rgba(167,139,250,0.3);
            color: var(--accent);
        }

        /* Empty */
        .empty {
            grid-column: 1 / -1; padding: 60px 20px;
            text-align: center; color: var(--muted);
        }
        .empty-icon { font-size: 48px; display: block; margin-bottom: 14px; opacity: 0.35; }
        .empty h3 {
            font-family: 'Syne', sans-serif; font-size: 18px; font-weight: 700;
            color: var(--text); margin-bottom: 6px;
        }
        .empty p { font-size: 13.5px; }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon">♫</div>
        <span>Musicly</span>
    </div>
    <div>
        <div class="nav-label">Menu</div>
        <nav><ul>
            <li><span class="ni">⌂</span><a href="dashboard.php">Home</a></li>
            <li><span class="ni">⬆</span><a href="upload.php">Upload</a></li>
            <li class="active"><span class="ni">☷</span> Playlists</li>
            <li><span class="ni">⚙</span><a href="admin.php">Admin</a></li>
        </ul></nav>
    </div>
</div>

<!-- Main -->
<div class="main">

    <div class="page-header">
        <h1>Your Playlists</h1>
        <p>Create and manage your personal music collections</p>
    </div>

    <?php if($success): ?>
    <div class="alert">✓ <?php echo $success; ?></div>
    <?php endif; ?>

    <!-- Create -->
    <div class="create-card">
        <h2>✚ New Playlist</h2>
        <form method="POST">
            <div class="create-row">
                <div class="input-wrap">
                    <span class="input-icon">☷</span>
                    <input type="text" name="name" placeholder="e.g. Late Night Vibes…" required>
                </div>
                <button type="submit" name="create" class="btn-create">✚ Create</button>
            </div>
        </form>
    </div>

    <!-- List -->
    <div class="section-header">
        <h2>All Playlists</h2>
        <span class="section-count"><?php echo $count; ?></span>
    </div>

    <div class="playlist-grid">
        <?php
        $playlists = [];
        while($row = $result->fetch_assoc()) $playlists[] = $row;
        $emojis = ['🎵','🎶','🎸','🥁','🎹','🎺'];
        ?>

        <?php if(empty($playlists)): ?>
        <div class="empty">
            <span class="empty-icon">🎵</span>
            <h3>No playlists yet</h3>
            <p>Create your first playlist above to get started.</p>
        </div>
        <?php else: ?>
        <?php foreach($playlists as $i => $row): ?>
        <div class="playlist-card">
            <div class="pl-art">
                <div class="pl-art-inner"><?php echo $emojis[$i % count($emojis)]; ?></div>
                <div class="pl-play-ov"><div class="pl-play-btn">▶</div></div>
            </div>
            <div>
                <div class="pl-name"><?php echo htmlspecialchars($row['name']); ?></div>
                <div class="pl-meta">Playlist · #<?php echo $row['playlist_id']; ?></div>
            </div>
            <a class="pl-open" href="view_playlist.php?id=<?php echo $row['playlist_id']; ?>">Open →</a>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>
</body>
</html>