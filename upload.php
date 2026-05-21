<?php
include 'db.php';
session_start();
if($_SESSION['role'] != 'artist'){
    die("Only artists allowed");
}

$success = "";
$error   = "";

if(isset($_POST['upload'])){
    $title = $_POST['title'];

    // SONG FILE
    $song     = $_FILES['song']['name'];
    $song_tmp = $_FILES['song']['tmp_name'];

    // COVER IMAGE
    $cover     = $_FILES['cover']['name'];
    $cover_tmp = $_FILES['cover']['tmp_name'];

    move_uploaded_file($song_tmp, "songs/".$song);
    move_uploaded_file($cover_tmp, "covers/".$cover);

    $conn->query("INSERT INTO songs (title, file_path, cover) VALUES ('$title','$song','$cover')");

    $success = "\"" . htmlspecialchars($title) . "\" uploaded successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Musicly — Upload</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --accent:  #a78bfa;
            --accent2: #f472b6;
            --bg:      #07050f;
            --bg2:     #0e0a1a;
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

        /* Ambient blobs */
        body::before, body::after {
            content: '';
            position: fixed;
            border-radius: 50%;
            filter: blur(110px);
            pointer-events: none;
            z-index: 0;
        }
        body::before {
            width: 480px; height: 480px;
            background: radial-gradient(circle, rgba(167,139,250,0.16) 0%, transparent 70%);
            top: -120px; left: -100px;
        }
        body::after {
            width: 360px; height: 360px;
            background: radial-gradient(circle, rgba(244,114,182,0.12) 0%, transparent 70%);
            bottom: 60px; right: 60px;
        }

        /* ── Sidebar ── */
        .sidebar {
            width: var(--sidebar-w);
            height: 100vh;
            position: fixed;
            left: 0; top: 0;
            background: rgba(14,10,26,0.85);
            backdrop-filter: blur(20px);
            border-right: 1px solid var(--gb);
            padding: 32px 20px;
            display: flex;
            flex-direction: column;
            gap: 40px;
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
            font-family: 'Syne', sans-serif;
            font-size: 20px; font-weight: 800; letter-spacing: -0.5px;
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
        nav li:hover { background: var(--glass); color: var(--text); }
        nav li.active { background: rgba(167,139,250,0.15); color: var(--accent); }
        nav li a { color: inherit; text-decoration: none; display: contents; }
        .ni { font-size: 15px; width: 20px; text-align: center; }

        /* ── Main ── */
        .main {
            margin-left: var(--sidebar-w);
            padding: 40px 48px;
            position: relative;
            z-index: 1;
            max-width: 860px;
        }

        /* Page header */
        .page-header { margin-bottom: 36px; }
        .page-header h1 {
            font-family: 'Syne', sans-serif;
            font-size: 30px; font-weight: 800; letter-spacing: -0.5px;
            margin-bottom: 6px;
        }
        .page-header p { font-size: 14px; color: var(--muted); }

        /* ── Alerts ── */
        .alert {
            display: flex; align-items: center; gap: 12px;
            border-radius: 14px; padding: 14px 18px;
            margin-bottom: 28px; font-size: 14px;
            animation: slideIn 0.35s cubic-bezier(0.34,1.56,0.64,1) both;
        }
        @keyframes slideIn {
            from { transform: translateY(-10px); opacity: 0; }
            to   { transform: translateY(0);     opacity: 1; }
        }
        .alert-success {
            background: rgba(52,211,153,0.1);
            border: 1px solid rgba(52,211,153,0.3);
            color: #6ee7b7;
        }
        .alert-error {
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.3);
            color: #fca5a5;
        }
        .alert-icon { font-size: 18px; flex-shrink: 0; }

        /* ── Upload card ── */
        .upload-card {
            background: rgba(14,10,26,0.72);
            backdrop-filter: blur(20px);
            border: 1px solid var(--gb);
            border-radius: 22px;
            padding: 36px 38px;
            position: relative;
            overflow: hidden;
        }
        .upload-card::before {
            content: '';
            position: absolute;
            top: 0; left: 8%; right: 8%;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--accent), var(--accent2), transparent);
            border-radius: 2px;
        }

        /* ── Form layout ── */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }
        .form-group { display: flex; flex-direction: column; gap: 8px; }
        .form-group.full { grid-column: 1 / -1; }

        label {
            font-size: 11.5px; font-weight: 600;
            letter-spacing: 0.8px; text-transform: uppercase;
            color: var(--muted);
        }

        /* Text input */
        .input-wrap { position: relative; }
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
            font-size: 15px;
            outline: none;
            transition: border-color 0.2s, background 0.2s;
        }
        input[type=text]:focus {
            border-color: rgba(167,139,250,0.55);
            background: rgba(167,139,250,0.07);
        }
        input[type=text]::placeholder { color: var(--muted); }

        /* ── Drop zones ── */
        .drop-zone {
            border: 1.5px dashed var(--gb);
            border-radius: 16px;
            padding: 32px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.25s ease;
            background: var(--glass);
            position: relative;
            overflow: hidden;
        }
        .drop-zone:hover, .drop-zone.dragover {
            border-color: var(--accent);
            background: rgba(167,139,250,0.07);
        }
        .drop-zone input[type=file] {
            position: absolute; inset: 0;
            opacity: 0; cursor: pointer; width: 100%; height: 100%;
        }
        .dz-icon { font-size: 36px; margin-bottom: 10px; display: block; }
        .dz-title {
            font-family: 'Syne', sans-serif;
            font-size: 14px; font-weight: 700;
            margin-bottom: 4px;
        }
        .dz-sub { font-size: 12.5px; color: var(--muted); }

        /* Preview strip */
        .preview-strip {
            display: flex; align-items: center; gap: 12px;
            margin-top: 14px;
            background: rgba(167,139,250,0.08);
            border: 1px solid rgba(167,139,250,0.2);
            border-radius: 12px;
            padding: 10px 14px;
            animation: fadeIn 0.3s ease both;
        }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }
        .preview-strip.hidden { display: none; }
        .preview-strip .ps-icon { font-size: 22px; flex-shrink: 0; }
        .preview-strip .ps-name {
            font-size: 13px; font-weight: 500;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            color: var(--accent);
        }
        .preview-strip .ps-size { font-size: 12px; color: var(--muted); margin-top: 2px; }
        .cover-thumb {
            width: 44px; height: 44px; border-radius: 8px;
            object-fit: cover; flex-shrink: 0;
        }

        /* ── Divider ── */
        .divider {
            border: none;
            border-top: 1px solid var(--gb);
            margin: 28px 0;
        }

        /* ── Submit ── */
        .btn-upload {
            display: flex; align-items: center; justify-content: center; gap: 10px;
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            border: none; border-radius: 14px;
            color: #fff;
            font-family: 'Syne', sans-serif;
            font-size: 16px; font-weight: 700; letter-spacing: 0.2px;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 22px rgba(167,139,250,0.35);
        }
        .btn-upload:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(167,139,250,0.5);
        }
        .btn-upload:active { transform: scale(0.98); }
        .btn-icon { font-size: 18px; }

        /* ── Tips card ── */
        .tips-card {
            margin-top: 24px;
            background: rgba(167,139,250,0.06);
            border: 1px solid rgba(167,139,250,0.15);
            border-radius: 16px;
            padding: 20px 24px;
        }
        .tips-title {
            font-family: 'Syne', sans-serif;
            font-size: 13px; font-weight: 700;
            color: var(--accent); margin-bottom: 12px;
            display: flex; align-items: center; gap: 8px;
        }
        .tips-list { list-style: none; display: flex; flex-direction: column; gap: 8px; }
        .tips-list li {
            font-size: 13px; color: var(--muted);
            display: flex; align-items: flex-start; gap: 8px;
        }
        .tip-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: var(--accent); flex-shrink: 0; margin-top: 5px;
        }
    </style>
</head>
<body>

<!-- ── Sidebar ── -->
<div class="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon">♫</div>
        <span>Musicly</span>
    </div>
    <div>
        <div class="nav-label">Menu</div>
        <nav><ul>
            <li><span class="ni">⌂</span> <a href="dashboard.php">Home</a></li>
            <li class="active"><span class="ni">⬆</span> Upload</li>
            <li><span class="ni">☷</span> <a href="playlist.php">Playlists</a></li>
            <li><span class="ni">⚙</span> <a href="admin.php">Admin</a></li>
        </ul></nav>
    </div>
</div>

<!-- ── Main ── -->
<div class="main">

    <div class="page-header">
        <h1>Upload a Track</h1>
        <p>Share your music with the world — fill in the details below</p>
    </div>

    <?php if($success): ?>
    <div class="alert alert-success">
        <span class="alert-icon">✓</span>
        <?php echo $success; ?>
    </div>
    <?php endif; ?>

    <?php if($error): ?>
    <div class="alert alert-error">
        <span class="alert-icon">⚠</span>
        <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>

    <div class="upload-card">
        <form method="POST" enctype="multipart/form-data" id="uploadForm">

            <div class="form-grid">

                <!-- Song title -->
                <div class="form-group full">
                    <label for="title">Song Title</label>
                    <div class="input-wrap">
                        <span class="input-icon">♪</span>
                        <input type="text" id="title" name="title"
                               placeholder="e.g. Midnight Pulse" required
                               value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
                    </div>
                </div>

                <!-- Song file -->
                <div class="form-group">
                    <label>Audio File</label>
                    <div class="drop-zone" id="songDrop">
                        <input type="file" name="song" id="songFile"
                               accept="audio/*" required
                               onchange="previewFile(this,'songPreview','song')">
                        <span class="dz-icon">🎵</span>
                        <div class="dz-title">Drop your audio here</div>
                        <div class="dz-sub">MP3, WAV, FLAC, OGG</div>
                    </div>
                    <div class="preview-strip hidden" id="songPreview">
                        <span class="ps-icon">🎵</span>
                        <div>
                            <div class="ps-name" id="songName">—</div>
                            <div class="ps-size" id="songSize">—</div>
                        </div>
                    </div>
                </div>

                <!-- Cover image -->
                <div class="form-group">
                    <label>Cover Image</label>
                    <div class="drop-zone" id="coverDrop">
                        <input type="file" name="cover" id="coverFile"
                               accept="image/*" required
                               onchange="previewFile(this,'coverPreview','cover')">
                        <span class="dz-icon">🖼</span>
                        <div class="dz-title">Drop your cover art</div>
                        <div class="dz-sub">JPG, PNG, WEBP — min 500×500</div>
                    </div>
                    <div class="preview-strip hidden" id="coverPreview">
                        <img class="cover-thumb" id="coverThumb" src="" alt="cover">
                        <div>
                            <div class="ps-name" id="coverName">—</div>
                            <div class="ps-size" id="coverSize">—</div>
                        </div>
                    </div>
                </div>

            </div>

            <hr class="divider">

            <button type="submit" name="upload" class="btn-upload">
                <span class="btn-icon">⬆</span> Publish Track
            </button>

        </form>
    </div>

    <!-- Tips -->
    <div class="tips-card">
        <div class="tips-title">⚡ Upload tips</div>
        <ul class="tips-list">
            <li><span class="tip-dot"></span>Use high-quality audio (320kbps MP3 or lossless FLAC) for the best listener experience.</li>
            <li><span class="tip-dot"></span>Cover art should be square and at least 500×500 px — it appears on song cards and the player.</li>
            <li><span class="tip-dot"></span>Give your track a clear, descriptive title so it's easy to find in search.</li>
        </ul>
    </div>

</div>

<script>
function formatBytes(b){
    if(b < 1024) return b + ' B';
    if(b < 1024*1024) return (b/1024).toFixed(1) + ' KB';
    return (b/(1024*1024)).toFixed(1) + ' MB';
}

function previewFile(input, previewId, type){
    const file = input.files[0];
    if(!file) return;
    const strip = document.getElementById(previewId);
    strip.classList.remove('hidden');

    if(type === 'song'){
        document.getElementById('songName').textContent = file.name;
        document.getElementById('songSize').textContent = formatBytes(file.size);
    } else {
        document.getElementById('coverName').textContent = file.name;
        document.getElementById('coverSize').textContent = formatBytes(file.size);
        const reader = new FileReader();
        reader.onload = e => { document.getElementById('coverThumb').src = e.target.result; };
        reader.readAsDataURL(file);
    }
}

// Drag-over highlight
['songDrop','coverDrop'].forEach(id => {
    const el = document.getElementById(id);
    el.addEventListener('dragover',  e => { e.preventDefault(); el.classList.add('dragover'); });
    el.addEventListener('dragleave', () => el.classList.remove('dragover'));
    el.addEventListener('drop',      () => el.classList.remove('dragover'));
});
</script>

</body>
</html>