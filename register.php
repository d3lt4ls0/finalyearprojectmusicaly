<?php
include 'db.php';

$success = "";
$error   = "";

if(isset($_POST['register'])){
    $name  = $_POST['name'];
    $email = $_POST['email'];
    $pass  = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role  = $_POST['role'];

    // Check if email already exists
    $check = $conn->query("SELECT * FROM users WHERE email='$email'");
    if($check->num_rows > 0){
        $error = "An account with this email already exists.";
    } else {
        $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name','$email','$pass','$role')";
        $conn->query($sql);
        $success = "Account created! <a href='login.php' style='color:#a78bfa;text-decoration:none;font-weight:600;'>Sign in →</a>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Musicly — Register</title>
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
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
            overflow-x: hidden;
            position: relative;
        }

        /* Blobs */
        .blob {
            position: fixed; border-radius: 50%;
            filter: blur(100px); pointer-events: none; z-index: 0;
        }
        .b1 { width:520px;height:520px;background:radial-gradient(circle,rgba(167,139,250,0.2) 0%,transparent 70%);top:-160px;left:-120px;animation:d1 12s ease-in-out infinite alternate; }
        .b2 { width:380px;height:380px;background:radial-gradient(circle,rgba(244,114,182,0.14) 0%,transparent 70%);bottom:-80px;right:-80px;animation:d2 10s ease-in-out infinite alternate; }
        .b3 { width:260px;height:260px;background:radial-gradient(circle,rgba(99,102,241,0.1) 0%,transparent 70%);top:40%;right:20%;animation:d3 14s ease-in-out infinite alternate; }
        @keyframes d1{from{transform:translate(0,0);}to{transform:translate(40px,30px);}}
        @keyframes d2{from{transform:translate(0,0);}to{transform:translate(-30px,-20px);}}
        @keyframes d3{from{transform:translate(0,0);}to{transform:translate(20px,-40px);}}

        /* Floating notes */
        .notes { position:fixed;inset:0;pointer-events:none;z-index:0;overflow:hidden; }
        .note  { position:absolute;opacity:0.06;color:var(--accent);animation:fl linear infinite;font-family:serif; }
        @keyframes fl{0%{transform:translateY(110vh) rotate(0deg);opacity:0;}10%{opacity:0.06;}90%{opacity:0.06;}100%{transform:translateY(-10vh) rotate(360deg);opacity:0;}}

        /* Card */
        .card {
            position: relative; z-index: 2;
            width: 100%; max-width: 460px;
            background: rgba(14,10,26,0.76);
            backdrop-filter: blur(24px);
            border: 1px solid var(--gb);
            border-radius: 26px;
            padding: 44px 42px 40px;
            animation: up 0.5s cubic-bezier(0.34,1.56,0.64,1) both;
        }
        @keyframes up{from{transform:translateY(28px);opacity:0;}to{transform:translateY(0);opacity:1;}}
        .card::before {
            content:'';position:absolute;top:0;left:10%;right:10%;height:2px;
            background:linear-gradient(90deg,transparent,var(--accent),var(--accent2),transparent);
            border-radius:2px;
        }

        /* Logo */
        .logo { display:flex;align-items:center;gap:10px;margin-bottom:30px; }
        .logo-icon {
            width:38px;height:38px;
            background:linear-gradient(135deg,var(--accent),var(--accent2));
            border-radius:11px;display:flex;align-items:center;justify-content:center;
            font-size:19px;flex-shrink:0;
        }
        .logo span {
            font-family:'Syne',sans-serif;font-size:22px;font-weight:800;
            background:linear-gradient(135deg,#fff 40%,var(--accent));
            -webkit-background-clip:text;-webkit-text-fill-color:transparent;
        }

        h2 { font-family:'Syne',sans-serif;font-size:26px;font-weight:700;letter-spacing:-0.4px;margin-bottom:5px; }
        .subtitle { font-size:14px;color:var(--muted);margin-bottom:28px; }

        /* Alerts */
        .alert {
            display:flex;align-items:center;gap:10px;
            border-radius:13px;padding:13px 16px;
            margin-bottom:22px;font-size:13.5px;
            animation:shake 0.35s ease;
        }
        @keyframes shake{0%,100%{transform:translateX(0);}20%{transform:translateX(-6px);}40%{transform:translateX(6px);}60%{transform:translateX(-4px);}80%{transform:translateX(4px);}}
        .alert-error   { background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.28);color:#fca5a5; }
        .alert-success { background:rgba(52,211,153,0.1);border:1px solid rgba(52,211,153,0.28);color:#6ee7b7;animation:none; }
        .alert-icon { font-size:16px;flex-shrink:0; }

        /* Two-column layout for name + email */
        .form-grid { display:grid;grid-template-columns:1fr 1fr;gap:16px; }
        .form-group { display:flex;flex-direction:column;gap:7px;margin-bottom:16px; }
        .form-group.full { grid-column:1/-1; }
        .form-group:last-child { margin-bottom:0; }

        label {
            font-size:11.5px;font-weight:600;letter-spacing:0.7px;
            text-transform:uppercase;color:var(--muted);
        }

        /* Input */
        .input-wrap { position:relative; }
        .iico {
            position:absolute;left:14px;top:50%;transform:translateY(-50%);
            color:var(--muted);font-size:15px;pointer-events:none;
            transition:color 0.2s;
        }
        .input-wrap:focus-within .iico { color:var(--accent); }
        input[type=text], input[type=email], input[type=password] {
            width:100%;padding:12px 16px 12px 43px;
            background:var(--glass);border:1px solid var(--gb);
            border-radius:12px;color:var(--text);
            font-family:'DM Sans',sans-serif;font-size:14.5px;
            outline:none;transition:border-color 0.2s,background 0.2s;
        }
        input:focus {
            border-color:rgba(167,139,250,0.55);
            background:rgba(167,139,250,0.07);
        }
        input::placeholder { color:var(--muted); }

        /* Eye toggle */
        .eye {
            position:absolute;right:13px;top:50%;transform:translateY(-50%);
            background:none;border:none;color:var(--muted);
            cursor:pointer;font-size:15px;padding:0;transition:color 0.2s;
        }
        .eye:hover { color:var(--text); }

        /* Role pills */
        .role-grid { display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:7px; }
        .rpill {
            display:flex;flex-direction:column;align-items:center;gap:5px;
            padding:12px 8px;border:1px solid var(--gb);border-radius:12px;
            background:var(--glass);cursor:pointer;transition:all 0.2s ease;
        }
        .rpill:hover { border-color:rgba(167,139,250,0.35);background:rgba(167,139,250,0.06); }
        .rpill.on    { border-color:var(--accent);background:rgba(167,139,250,0.14); }
        .rpill .pi   { font-size:20px; }
        .rpill .pl   { font-size:12px;font-weight:500;color:var(--muted);transition:color 0.2s; }
        .rpill.on .pl{ color:var(--accent); }
        #role        { display:none; }

        /* Password strength */
        .strength-bar {
            height:3px;border-radius:3px;margin-top:8px;
            background:rgba(255,255,255,0.08);overflow:hidden;
        }
        .strength-fill {
            height:100%;border-radius:3px;width:0%;
            transition:width 0.3s,background 0.3s;
        }
        .strength-label {
            font-size:11px;color:var(--muted);margin-top:4px;
            min-height:14px;transition:color 0.3s;
        }

        /* Divider */
        .divider {
            display:flex;align-items:center;gap:12px;
            margin:22px 0;
        }
        .divider::before,.divider::after {
            content:'';flex:1;height:1px;background:var(--gb);
        }
        .divider span { font-size:12px;color:var(--muted);white-space:nowrap; }

        /* Submit */
        .btn-register {
            width:100%;padding:15px;margin-top:24px;
            background:linear-gradient(135deg,var(--accent),var(--accent2));
            border:none;border-radius:13px;color:#fff;
            font-family:'Syne',sans-serif;font-size:16px;font-weight:700;
            cursor:pointer;position:relative;overflow:hidden;
            transition:transform 0.2s,box-shadow 0.2s;
            box-shadow:0 4px 22px rgba(167,139,250,0.35);
        }
        .btn-register:hover { transform:translateY(-2px);box-shadow:0 8px 28px rgba(167,139,250,0.5); }
        .btn-register:active { transform:scale(0.98); }

        /* Footer */
        .card-footer { text-align:center;margin-top:20px;font-size:13.5px;color:var(--muted); }
        .card-footer a { color:var(--accent);text-decoration:none;font-weight:500; }
        .card-footer a:hover { opacity:0.8; }

        /* Terms note */
        .terms { font-size:11.5px;color:var(--muted);text-align:center;margin-top:14px;line-height:1.5; }
        .terms a { color:var(--accent);text-decoration:none; }
    </style>
</head>
<body>

<div class="blob b1"></div>
<div class="blob b2"></div>
<div class="blob b3"></div>
<div class="notes" id="notes"></div>

<div class="card">

    <div class="logo">
        <div class="logo-icon">♫</div>
        <span>Musicly</span>
    </div>

    <h2>Create account</h2>
    <p class="subtitle">Join Musicly and start listening</p>

    <?php if($error): ?>
    <div class="alert alert-error">
        <span class="alert-icon">⚠</span>
        <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>

    <?php if($success): ?>
    <div class="alert alert-success">
        <span class="alert-icon">✓</span>
        <?php echo $success; ?>
    </div>
    <?php endif; ?>

    <form method="POST" id="regForm">

        <!-- Name + Email row -->
        <div class="form-grid">
            <div class="form-group">
                <label for="name">Full Name</label>
                <div class="input-wrap">
                    <span class="iico">👤</span>
                    <input type="text" id="name" name="name"
                           placeholder="Your name" required
                           value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-wrap">
                    <span class="iico">✉</span>
                    <input type="email" id="email" name="email"
                           placeholder="you@email.com" required
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
            </div>
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password">Password</label>
            <div class="input-wrap">
                <span class="iico">🔒</span>
                <input type="password" id="password" name="password"
                       placeholder="Create a strong password" required
                       oninput="checkStrength(this.value)">
                <button type="button" class="eye" id="eyeBtn" onclick="toggleEye()">👁</button>
            </div>
            <div class="strength-bar"><div class="strength-fill" id="sFill"></div></div>
            <div class="strength-label" id="sLabel"></div>
        </div>

        <!-- Role -->
        <div class="form-group">
            <label>I am a…</label>
            <div class="role-grid">
                <div class="rpill on" onclick="pick('user', this)">
                    <span class="pi">🎧</span>
                    <span class="pl">Listener</span>
                </div>
                <div class="rpill" onclick="pick('artist', this)">
                    <span class="pi">🎤</span>
                    <span class="pl">Artist</span>
                </div>
                <div class="rpill" onclick="pick('admin', this)">
                    <span class="pi">⚙</span>
                    <span class="pl">Admin</span>
                </div>
            </div>
            <select name="role" id="role" required>
                <option value="user">Listener</option>
                <option value="artist">Artist</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <button type="submit" name="register" class="btn-register">
            Create Account →
        </button>

    </form>

    <p class="terms">By registering you agree to our <a href="#">Terms</a> and <a href="#">Privacy Policy</a></p>

    <div class="card-footer">
        Already have an account? <a href="login.php">Sign in</a>
    </div>

</div>

<script>
/* Floating notes */
const nc=['♩','♪','♫','♬','𝄞','𝄢'];
const nb=document.getElementById('notes');
for(let i=0;i<18;i++){
    const s=document.createElement('span');
    s.className='note';
    s.textContent=nc[i%nc.length];
    s.style.cssText=`left:${Math.random()*100}vw;animation-duration:${14+Math.random()*18}s;animation-delay:${Math.random()*20}s;font-size:${13+Math.random()*14}px;`;
    nb.appendChild(s);
}

/* Role picker */
function pick(val, el){
    document.querySelectorAll('.rpill').forEach(p=>p.classList.remove('on'));
    el.classList.add('on');
    document.getElementById('role').value=val;
}

/* Eye toggle */
function toggleEye(){
    const inp=document.getElementById('password');
    const btn=document.getElementById('eyeBtn');
    inp.type=inp.type==='password'?'text':'password';
    btn.textContent=inp.type==='password'?'👁':'🙈';
}

/* Password strength */
function checkStrength(val){
    const fill=document.getElementById('sFill');
    const label=document.getElementById('sLabel');
    let score=0;
    if(val.length>=8)  score++;
    if(/[A-Z]/.test(val)) score++;
    if(/[0-9]/.test(val)) score++;
    if(/[^A-Za-z0-9]/.test(val)) score++;

    const levels=[
        {w:'0%',   c:'transparent', t:''},
        {w:'30%',  c:'#f87171',     t:'Weak'},
        {w:'55%',  c:'#fb923c',     t:'Fair'},
        {w:'75%',  c:'#facc15',     t:'Good'},
        {w:'100%', c:'#34d399',     t:'Strong 💪'},
    ];
    const l=val.length===0 ? levels[0] : levels[Math.min(score,4)];
    fill.style.width=l.w;
    fill.style.background=l.c;
    label.textContent=l.t;
    label.style.color=l.c;
}
</script>

</body>
</html>