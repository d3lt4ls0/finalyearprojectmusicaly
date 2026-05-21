<?php
session_start();
include 'db.php';

$error = "";

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $pass  = $_POST['password'];
    $role  = $_POST['role'];

    $result = $conn->query("SELECT * FROM users WHERE email='$email'");
    $user   = $result->fetch_assoc();

    if($user && password_verify($pass, $user['password'])){

        if($user['role'] != $role){
            $error = "Selected role does not match your account!";
        } else {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role']    = $user['role'];

            if($role == 'admin'){
                header("Location: admin.php");
            } elseif($role == 'artist'){
                header("Location: upload.php");
            } else {
                header("Location: dashboard.php");
            }
            exit;
        }

    } else {
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Musicly — Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
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
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Ambient blobs */
        .blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(100px);
            pointer-events: none;
            z-index: 0;
        }
        .blob-1 {
            width: 520px; height: 520px;
            background: radial-gradient(circle, rgba(167,139,250,0.2) 0%, transparent 70%);
            top: -160px; left: -120px;
            animation: drift1 12s ease-in-out infinite alternate;
        }
        .blob-2 {
            width: 380px; height: 380px;
            background: radial-gradient(circle, rgba(244,114,182,0.15) 0%, transparent 70%);
            bottom: -80px; right: -80px;
            animation: drift2 10s ease-in-out infinite alternate;
        }
        .blob-3 {
            width: 260px; height: 260px;
            background: radial-gradient(circle, rgba(99,102,241,0.12) 0%, transparent 70%);
            top: 50%; right: 25%;
            animation: drift3 14s ease-in-out infinite alternate;
        }
        @keyframes drift1 { from { transform: translate(0,0); } to { transform: translate(40px, 30px); } }
        @keyframes drift2 { from { transform: translate(0,0); } to { transform: translate(-30px,-20px); } }
        @keyframes drift3 { from { transform: translate(0,0); } to { transform: translate(20px,-40px); } }

        /* Decorative music notes */
        .notes {
            position: fixed; inset: 0; pointer-events: none; z-index: 0; overflow: hidden;
        }
        .note {
            position: absolute;
            font-size: 20px;
            opacity: 0.06;
            animation: float linear infinite;
            color: var(--accent);
        }
        @keyframes float {
            0%   { transform: translateY(110vh) rotate(0deg); opacity: 0; }
            10%  { opacity: 0.06; }
            90%  { opacity: 0.06; }
            100% { transform: translateY(-10vh) rotate(360deg); opacity: 0; }
        }

        /* Card */
        .card {
            position: relative;
            z-index: 2;
            width: 420px;
            background: rgba(14,10,26,0.75);
            backdrop-filter: blur(24px);
            border: 1px solid var(--gb);
            border-radius: 24px;
            padding: 44px 40px 40px;
            animation: slideUp 0.5s cubic-bezier(0.34,1.56,0.64,1) both;
        }
        @keyframes slideUp {
            from { transform: translateY(28px); opacity: 0; }
            to   { transform: translateY(0);    opacity: 1; }
        }

        /* Top accent line */
        .card::before {
            content: '';
            position: absolute;
            top: 0; left: 10%; right: 10%;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--accent), var(--accent2), transparent);
            border-radius: 2px;
        }

        /* Logo */
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 32px;
        }
        .logo-icon {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            border-radius: 11px;
            display: flex; align-items: center; justify-content: center;
            font-size: 19px;
            flex-shrink: 0;
        }
        .logo span {
            font-family: 'Syne', sans-serif;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #fff 40%, var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Headings */
        h2 {
            font-family: 'Syne', sans-serif;
            font-size: 26px;
            font-weight: 700;
            letter-spacing: -0.4px;
            margin-bottom: 6px;
        }
        .subtitle {
            font-size: 14px;
            color: var(--muted);
            margin-bottom: 32px;
        }

        /* Error */
        .error-box {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(239,68,68,0.12);
            border: 1px solid rgba(239,68,68,0.3);
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 13.5px;
            color: #fca5a5;
            animation: shake 0.35s ease;
        }
        @keyframes shake {
            0%,100% { transform: translateX(0); }
            20%      { transform: translateX(-6px); }
            40%      { transform: translateX(6px); }
            60%      { transform: translateX(-4px); }
            80%      { transform: translateX(4px); }
        }
        .error-icon { font-size: 16px; flex-shrink: 0; }

        /* Form */
        .form-group {
            margin-bottom: 18px;
        }
        label {
            display: block;
            font-size: 12.5px;
            font-weight: 500;
            color: var(--muted);
            letter-spacing: 0.6px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        /* Input wrapper for icon */
        .input-wrap {
            position: relative;
        }
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%; transform: translateY(-50%);
            color: var(--muted);
            font-size: 16px;
            pointer-events: none;
            transition: color 0.2s;
        }
        .input-wrap:focus-within .input-icon { color: var(--accent); }

        input[type=email],
        input[type=password],
        select {
            width: 100%;
            padding: 13px 16px 13px 44px;
            background: var(--glass);
            border: 1px solid var(--gb);
            border-radius: 12px;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 14.5px;
            outline: none;
            transition: border-color 0.2s, background 0.2s;
            -webkit-appearance: none;
        }
        input:focus, select:focus {
            border-color: rgba(167,139,250,0.55);
            background: rgba(167,139,250,0.07);
        }
        input::placeholder { color: var(--muted); }
        select { cursor: pointer; }
        select option { background: #150f28; color: var(--text); }

        /* Eye toggle */
        .eye-toggle {
            position: absolute;
            right: 14px; top: 50%; transform: translateY(-50%);
            background: none; border: none;
            color: var(--muted);
            cursor: pointer;
            font-size: 16px;
            padding: 0;
            transition: color 0.2s;
        }
        .eye-toggle:hover { color: var(--text); }

        /* Role pills */
        .role-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 8px;
        }
        .role-pill {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            padding: 12px 8px;
            border: 1px solid var(--gb);
            border-radius: 12px;
            background: var(--glass);
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .role-pill:hover {
            border-color: rgba(167,139,250,0.35);
            background: rgba(167,139,250,0.06);
        }
        .role-pill.selected {
            border-color: var(--accent);
            background: rgba(167,139,250,0.14);
        }
        .role-pill .pill-icon { font-size: 20px; }
        .role-pill .pill-label {
            font-size: 12px;
            font-weight: 500;
            color: var(--muted);
            transition: color 0.2s;
        }
        .role-pill.selected .pill-label { color: var(--accent); }
        /* Hidden real select */
        #role { display: none; }

        /* Submit button */
        .btn-login {
            width: 100%;
            padding: 15px;
            margin-top: 28px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            border: none;
            border-radius: 12px;
            color: #fff;
            font-family: 'Syne', sans-serif;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.2px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 22px rgba(167,139,250,0.35);
        }
        .btn-login::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255,255,255,0);
            transition: background 0.2s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(167,139,250,0.5);
        }
        .btn-login:hover::after { background: rgba(255,255,255,0.07); }
        .btn-login:active { transform: scale(0.98); }

        /* Footer */
        .card-footer {
            text-align: center;
            margin-top: 22px;
            font-size: 13.5px;
            color: var(--muted);
        }
        .card-footer a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.2s;
        }
        .card-footer a:hover { opacity: 0.8; }
    </style>
</head>
<body>

<!-- Ambient -->
<div class="blob blob-1"></div>
<div class="blob blob-2"></div>
<div class="blob blob-3"></div>

<!-- Floating notes -->
<div class="notes" id="notes"></div>

<!-- Login Card -->
<div class="card">

    <div class="logo">
        <div class="logo-icon">♫</div>
        <span>Musicly</span>
    </div>

    <h2>Welcome back</h2>
    <p class="subtitle">Sign in to continue listening</p>

    <?php if($error): ?>
    <div class="error-box">
        <span class="error-icon">⚠</span>
        <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>

    <form method="POST" id="loginForm">

        <!-- Email -->
        <div class="form-group">
            <label for="email">Email</label>
            <div class="input-wrap">
                <span class="input-icon">✉</span>
                <input type="email" id="email" name="email"
                       placeholder="you@example.com" required
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password">Password</label>
            <div class="input-wrap">
                <span class="input-icon">🔒</span>
                <input type="password" id="password" name="password"
                       placeholder="Enter your password" required>
                <button type="button" class="eye-toggle" onclick="toggleEye()" id="eyeBtn">👁</button>
            </div>
        </div>

        <!-- Role -->
        <div class="form-group">
            <label>Sign in as</label>
            <div class="role-grid">
                <div class="role-pill selected" onclick="selectRole('user', this)">
                    <span class="pill-icon">🎧</span>
                    <span class="pill-label">Listener</span>
                </div>
                <div class="role-pill" onclick="selectRole('artist', this)">
                    <span class="pill-icon">🎤</span>
                    <span class="pill-label">Artist</span>
                </div>
                <div class="role-pill" onclick="selectRole('admin', this)">
                    <span class="pill-icon">⚙</span>
                    <span class="pill-label">Admin</span>
                </div>
            </div>
            <!-- Hidden select keeps the form POST working exactly as before -->
            <select name="role" id="role" required>
                <option value="user">Listener</option>
                <option value="artist">Artist</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <button type="submit" name="login" class="btn-login">Sign in →</button>

    </form>

    <div class="card-footer">
        Don't have an account? <a href="register.php">Create one</a>
    </div>

</div>

<script>
// Floating music notes
const noteChars = ['♩','♪','♫','♬','𝄞','𝄢'];
const container = document.getElementById('notes');
for(let i = 0; i < 18; i++){
    const el = document.createElement('span');
    el.className = 'note';
    el.textContent = noteChars[i % noteChars.length];
    el.style.left = (Math.random() * 100) + 'vw';
    el.style.animationDuration = (14 + Math.random() * 18) + 's';
    el.style.animationDelay    = (Math.random() * 20) + 's';
    el.style.fontSize = (14 + Math.random() * 16) + 'px';
    container.appendChild(el);
}

// Role pill selector
function selectRole(val, el){
    document.querySelectorAll('.role-pill').forEach(p => p.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('role').value = val;
}

// Password eye toggle
function toggleEye(){
    const inp = document.getElementById('password');
    const btn = document.getElementById('eyeBtn');
    if(inp.type === 'password'){
        inp.type = 'text';
        btn.textContent = '🙈';
    } else {
        inp.type = 'password';
        btn.textContent = '👁';
    }
}
</script>

</body>
</html>