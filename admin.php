<?php
include 'db.php';
session_start();
if($_SESSION['role'] != 'admin'){
    die("Access denied");
}

// Fetch users
$users = $conn->query("SELECT * FROM users");
$userCount = $users->num_rows;

// Fetch songs
$songs = $conn->query("SELECT * FROM songs");
$songCount = $songs->num_rows;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Musicly — Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --accent:#a78bfa; --accent2:#f472b6;
            --bg:#07050f; --bg3:#150f28;
            --glass:rgba(255,255,255,0.04); --gb:rgba(255,255,255,0.08);
            --text:#f1eeff; --muted:rgba(241,238,255,0.45);
            --danger:#f87171; --danger-bg:rgba(248,113,113,0.1); --danger-bd:rgba(248,113,113,0.25);
            --sidebar-w:230px;
        }
        body { font-family:'DM Sans',sans-serif; background:var(--bg); color:var(--text); min-height:100vh; }
        body::before, body::after { content:''; position:fixed; border-radius:50%; filter:blur(110px); pointer-events:none; z-index:0; }
        body::before { width:500px; height:500px; background:radial-gradient(circle,rgba(167,139,250,0.15) 0%,transparent 70%); top:-130px; left:-100px; }
        body::after  { width:350px; height:350px; background:radial-gradient(circle,rgba(244,114,182,0.11) 0%,transparent 70%); bottom:80px; right:60px; }

        /* Sidebar */
        .sidebar { width:var(--sidebar-w); height:100vh; position:fixed; left:0; top:0; background:rgba(14,10,26,0.85); backdrop-filter:blur(20px); border-right:1px solid var(--gb); padding:32px 20px; display:flex; flex-direction:column; gap:40px; z-index:10; }
        .sidebar-logo { display:flex; align-items:center; gap:10px; padding-left:8px; }
        .logo-icon { width:36px; height:36px; background:linear-gradient(135deg,var(--accent),var(--accent2)); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0; }
        .sidebar-logo span { font-family:'Syne',sans-serif; font-size:20px; font-weight:800; letter-spacing:-0.5px; background:linear-gradient(135deg,#fff 40%,var(--accent)); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
        .nav-label { font-size:10px; font-weight:600; letter-spacing:1.5px; text-transform:uppercase; color:rgba(255,255,255,0.2); padding:0 14px; margin-bottom:4px; }
        nav ul { list-style:none; display:flex; flex-direction:column; gap:3px; }
        nav li { display:flex; align-items:center; gap:11px; padding:10px 14px; border-radius:11px; color:var(--muted); font-size:14px; font-weight:500; cursor:pointer; transition:all 0.2s; }
        nav li a { color:inherit; text-decoration:none; display:contents; }
        nav li:hover { background:var(--glass); color:var(--text); }
        nav li.active { background:rgba(167,139,250,0.15); color:var(--accent); }
        .ni { font-size:15px; width:20px; text-align:center; }

        /* Main */
        .main { margin-left:var(--sidebar-w); padding:40px 44px; position:relative; z-index:1; }

        /* Header */
        .page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:32px; }
        .page-header h1 { font-family:'Syne',sans-serif; font-size:28px; font-weight:800; letter-spacing:-0.5px; }
        .admin-badge { background:rgba(167,139,250,0.15); border:1px solid rgba(167,139,250,0.3); color:var(--accent); font-size:12px; font-weight:600; padding:6px 14px; border-radius:50px; letter-spacing:0.5px; }

        /* Stats */
        .stats-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:18px; margin-bottom:36px; }
        .stat-card { background:var(--bg3); border:1px solid var(--gb); border-radius:18px; padding:22px 24px; display:flex; align-items:center; gap:18px; transition:transform 0.2s,border-color 0.2s; }
        .stat-card:hover { transform:translateY(-2px); border-color:rgba(167,139,250,0.3); }
        .stat-icon { width:48px; height:48px; border-radius:13px; display:flex; align-items:center; justify-content:center; font-size:22px; flex-shrink:0; }
        .si-purple { background:rgba(167,139,250,0.15); }
        .si-pink   { background:rgba(244,114,182,0.15); }
        .si-green  { background:rgba(52,211,153,0.12); }
        .stat-num { font-family:'Syne',sans-serif; font-size:30px; font-weight:800; letter-spacing:-1px; line-height:1; }
        .stat-label { font-size:13px; color:var(--muted); margin-top:3px; }

        /* Section head */
        .section-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; }
        .section-head h2 { font-family:'Syne',sans-serif; font-size:18px; font-weight:700; letter-spacing:-0.2px; display:flex; align-items:center; gap:10px; }
        .count-badge { font-family:'DM Sans',sans-serif; font-size:11px; font-weight:600; background:var(--glass); border:1px solid var(--gb); border-radius:50px; padding:3px 10px; color:var(--muted); }

        /* Table */
        .table-wrap { background:rgba(14,10,26,0.7); backdrop-filter:blur(18px); border:1px solid var(--gb); border-radius:18px; overflow:hidden; margin-bottom:32px; }
        .table-wrap::before { content:''; display:block; height:2px; background:linear-gradient(90deg,transparent,var(--accent),var(--accent2),transparent); }
        table { width:100%; border-collapse:collapse; }
        thead th { padding:14px 20px; font-size:11px; font-weight:600; letter-spacing:1px; text-transform:uppercase; color:var(--muted); text-align:left; border-bottom:1px solid var(--gb); }
        tbody tr { border-bottom:1px solid rgba(255,255,255,0.04); transition:background 0.15s; }
        tbody tr:last-child { border-bottom:none; }
        tbody tr:hover { background:rgba(167,139,250,0.04); }
        tbody td { padding:14px 20px; font-size:14px; vertical-align:middle; }

        /* User cells */
        .user-avatar { width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-family:'Syne',sans-serif; font-size:13px; font-weight:700; flex-shrink:0; }
        .user-cell { display:flex; align-items:center; gap:12px; }
        .user-name { font-weight:500; font-size:14px; }
        .user-email { font-size:12px; color:var(--muted); margin-top:1px; }
        .role-badge { display:inline-flex; align-items:center; gap:5px; padding:4px 12px; border-radius:50px; font-size:12px; font-weight:600; letter-spacing:0.3px; }
        .role-admin  { background:rgba(167,139,250,0.15); color:var(--accent); border:1px solid rgba(167,139,250,0.25); }
        .role-artist { background:rgba(244,114,182,0.12); color:#f9a8d4; border:1px solid rgba(244,114,182,0.25); }
        .role-user   { background:rgba(52,211,153,0.1);  color:#6ee7b7; border:1px solid rgba(52,211,153,0.2); }

        /* Song cells */
        .song-cover { width:40px; height:40px; border-radius:9px; object-fit:cover; flex-shrink:0; }
        .song-placeholder { width:40px; height:40px; border-radius:9px; background:linear-gradient(135deg,var(--bg3),#1a1232); display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0; }
        .song-cell { display:flex; align-items:center; gap:13px; }
        .song-title { font-weight:500; font-size:14px; }
        .song-path  { font-size:12px; color:var(--muted); margin-top:1px; font-family:monospace; }
        audio { height:32px; border-radius:50px; outline:none; accent-color:var(--accent); }

        /* Delete */
        .btn-delete { display:inline-flex; align-items:center; gap:6px; padding:7px 14px; border-radius:8px; background:var(--danger-bg); border:1px solid var(--danger-bd); color:var(--danger); font-size:12.5px; font-weight:600; text-decoration:none; transition:all 0.2s; }
        .btn-delete:hover { background:rgba(248,113,113,0.2); border-color:rgba(248,113,113,0.45); transform:scale(1.03); }

        /* Empty */
        .empty { padding:48px; text-align:center; color:var(--muted); font-size:14px; }
        .empty-icon { font-size:36px; display:block; margin-bottom:12px; opacity:0.4; }
        .mono { color:var(--muted); font-size:13px; font-family:monospace; }
    </style>
</head>
<body>

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
            <li><span class="ni">☷</span><a href="playlist.php">Playlists</a></li>
            <li class="active"><span class="ni">⚙</span>Admin</li>
        </ul></nav>
    </div>
</div>

<div class="main">

    <div class="page-header">
        <h1>Admin Panel</h1>
        <span class="admin-badge">⚙ Administrator</span>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon si-purple">👥</div>
            <div>
                <div class="stat-num"><?php echo $userCount; ?></div>
                <div class="stat-label">Total Users</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon si-pink">🎵</div>
            <div>
                <div class="stat-num"><?php echo $songCount; ?></div>
                <div class="stat-label">Total Songs</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon si-green">📊</div>
            <div>
                <div class="stat-num"><?php echo $userCount + $songCount; ?></div>
                <div class="stat-label">Total Records</div>
            </div>
        </div>
    </div>

    <!-- Users -->
    <div class="section-head">
        <h2>👥 Users <span class="count-badge"><?php echo $userCount; ?></span></h2>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Role</th>
                    <th>ID</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $avatarColors = [
                ['rgba(167,139,250,0.2)','#a78bfa'],
                ['rgba(244,114,182,0.2)','#f472b6'],
                ['rgba(52,211,153,0.15)','#34d399'],
                ['rgba(251,191,36,0.15)','#fbbf24'],
                ['rgba(96,165,250,0.2)', '#60a5fa'],
            ];
            $i = 1; $hasUsers = false;
            $users->data_seek(0);
            while($u = $users->fetch_assoc()):
                $hasUsers = true;
                $col = $avatarColors[($i-1) % count($avatarColors)];
                $initial = strtoupper(substr($u['name'], 0, 1));
                $role = htmlspecialchars($u['role'] ?? 'user');
            ?>
            <tr>
                <td class="mono"><?php echo $i++; ?></td>
                <td>
                    <div class="user-cell">
                        <div class="user-avatar" style="background:<?php echo $col[0]; ?>;color:<?php echo $col[1]; ?>;"><?php echo $initial; ?></div>
                        <div>
                            <div class="user-name"><?php echo htmlspecialchars($u['name']); ?></div>
                            <div class="user-email"><?php echo htmlspecialchars($u['email']); ?></div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="role-badge role-<?php echo $role; ?>">
                        <?php if($role==='admin') echo '⚙ Admin'; elseif($role==='artist') echo '🎤 Artist'; else echo '🎧 Listener'; ?>
                    </span>
                </td>
                <td class="mono">#<?php echo htmlspecialchars($u['user_id']); ?></td>
            </tr>
            <?php endwhile; ?>
            <?php if(!$hasUsers): ?>
            <tr><td colspan="4"><div class="empty"><span class="empty-icon">👤</span>No users found.</div></td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Songs -->
    <div class="section-head">
        <h2>🎵 Songs <span class="count-badge"><?php echo $songCount; ?></span></h2>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Track</th>
                    <th>Preview</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $j = 1; $hasSongs = false;
            $songs->data_seek(0);
            while($s = $songs->fetch_assoc()):
                $hasSongs = true;
            ?>
            <tr>
                <td class="mono"><?php echo $j++; ?></td>
                <td>
                    <div class="song-cell">
                        <?php if(!empty($s['cover'])): ?>
                        <img class="song-cover" src="covers/<?php echo htmlspecialchars($s['cover']); ?>" alt="">
                        <?php else: ?>
                        <div class="song-placeholder">🎵</div>
                        <?php endif; ?>
                        <div>
                            <div class="song-title"><?php echo htmlspecialchars($s['title']); ?></div>
                            <div class="song-path"><?php echo htmlspecialchars($s['file_path']); ?></div>
                        </div>
                    </div>
                </td>
                <td>
                    <audio controls>
                        <source src="songs/<?php echo htmlspecialchars($s['file_path']); ?>">
                    </audio>
                </td>
                <td>
                    <a class="btn-delete"
                       href="delete_song.php?id=<?php echo htmlspecialchars($s['song_id']); ?>"
                       onclick="return confirm('Delete \'<?php echo addslashes(htmlspecialchars($s['title'])); ?>\'? This cannot be undone.')">
                        🗑 Delete
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
            <?php if(!$hasSongs): ?>
            <tr><td colspan="4"><div class="empty"><span class="empty-icon">🎵</span>No songs uploaded yet.</div></td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>