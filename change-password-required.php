<?php
session_start();
if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; }

require_once 'config/db.php';
require_once 'models/User.php';
$model = new User();
$userId = (int)$_SESSION['user_id'];
$user   = $model->getById($userId);

// If no forced change required, send to dashboard
if (empty($user['force_password_change'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$msg   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new  = $_POST['new_pw']     ?? '';
    $conf = $_POST['confirm_pw'] ?? '';

    if (strlen($new) < 6)    $error = 'Password must be at least 6 characters.';
    elseif ($new !== $conf)  $error = 'Passwords do not match.';
    else {
        $model->changePassword($userId, $new);
        $model->clearForceChange($userId);
        header('Location: dashboard.php?pw_changed=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Set New Password — DineLocal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,700;1,700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <style>
    :root{--orange:#C4551A;--orange-d:#9E3A0E;--cream:#FBF0DC;--brown:#3B1A08;--dark:#0d0702;--gold:#E8A83E;--serif:'Cormorant Garamond',serif;}
    *{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:'Inter',sans-serif;background:#0a0502;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1.5rem;}
    .card{background:#faf5ee;border-radius:1.5rem;padding:2.5rem 2rem;width:100%;max-width:440px;box-shadow:0 32px 80px rgba(0,0,0,.5);}
    .card-logo{text-align:center;margin-bottom:1.5rem;}
    .card-logo span{font-family:var(--serif);font-size:1.8rem;font-weight:700;color:var(--brown);}
    .alert-banner{background:rgba(196,85,26,.1);border:1px solid rgba(196,85,26,.3);color:var(--orange);border-radius:.65rem;padding:1rem 1.25rem;font-size:.84rem;margin-bottom:1.5rem;display:flex;gap:.65rem;align-items:flex-start;}
    .alert-banner i{font-size:1.1rem;margin-top:.05rem;flex-shrink:0;}
    h2{font-family:var(--serif);font-size:1.6rem;font-weight:700;color:var(--brown);margin-bottom:.3rem;}
    p.sub{font-size:.84rem;color:rgba(59,26,8,.55);margin-bottom:1.5rem;}
    .rf{display:flex;flex-direction:column;gap:.28rem;margin-bottom:.85rem;}
    .rf label{font-size:.72rem;font-weight:600;color:var(--brown);display:flex;align-items:center;gap:.3rem;}
    .pass-wrap{position:relative;}
    .pass-wrap input{background:#F3E4C6;border:none;border-radius:.45rem;padding:.65rem 2.5rem .65rem .9rem;font-family:'Inter',sans-serif;font-size:.875rem;color:var(--brown);outline:none;width:100%;transition:background .2s,box-shadow .2s;}
    .pass-wrap input:focus{background:#fff9f3;box-shadow:0 0 0 2px rgba(196,85,26,.28);}
    .pass-eye{position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;color:rgba(59,26,8,.4);cursor:pointer;font-size:.95rem;padding:0;}
    .pass-eye:hover{color:var(--orange);}
    .btn-save{width:100%;background:linear-gradient(135deg,var(--orange),var(--orange-d));color:#fff;padding:.88rem;border-radius:9999px;font-size:.9rem;font-weight:600;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:.5rem;box-shadow:0 4px 16px rgba(196,85,26,.4);transition:transform .2s;}
    .btn-save:hover{transform:translateY(-2px);}
    .banner-err{background:rgba(192,57,43,.1);border:1px solid rgba(192,57,43,.25);color:#c0392b;border-radius:.5rem;padding:.75rem 1rem;font-size:.82rem;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;}
  </style>
</head>
<body>
<div class="card">
  <div class="card-logo"><span>DineLocal</span></div>

  <div class="alert-banner">
    <i class="bi bi-shield-exclamation"></i>
    <div>
      <strong>Action Required</strong><br>
      You are signed in with a temporary password. Please set a new password before continuing.
    </div>
  </div>

  <h2>Set New Password</h2>
  <p class="sub">Hi <?= htmlspecialchars(explode(' ', $user['name'])[0]) ?>, choose a password you'll remember.</p>

  <?php if ($error): ?>
  <div class="banner-err"><i class="bi bi-exclamation-circle-fill"></i> <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" id="cpForm" novalidate>
    <div class="rf">
      <label><i class="bi bi-lock"></i> New Password</label>
      <div class="pass-wrap">
        <input type="password" name="new_pw" id="pw1" placeholder="Min 6 characters" required/>
        <button type="button" class="pass-eye" onclick="tp('pw1',this)"><i class="bi bi-eye"></i></button>
      </div>
    </div>
    <div class="rf">
      <label><i class="bi bi-lock-fill"></i> Confirm New Password</label>
      <div class="pass-wrap">
        <input type="password" name="confirm_pw" id="pw2" placeholder="Repeat password" required/>
        <button type="button" class="pass-eye" onclick="tp('pw2',this)"><i class="bi bi-eye"></i></button>
      </div>
    </div>
    <button type="submit" class="btn-save mt-2">
      <i class="bi bi-check-circle"></i> Save New Password
    </button>
  </form>
</div>
<script>
function tp(id, btn) {
  const inp = document.getElementById(id);
  inp.type = inp.type === 'password' ? 'text' : 'password';
  btn.querySelector('i').className = inp.type === 'text' ? 'bi bi-eye-slash' : 'bi bi-eye';
}
document.getElementById('cpForm').addEventListener('submit', function(e) {
  const p1 = document.getElementById('pw1').value;
  const p2 = document.getElementById('pw2').value;
  if (p1.length < 6) { alert('Password must be at least 6 characters.'); e.preventDefault(); return; }
  if (p1 !== p2)     { alert('Passwords do not match.'); e.preventDefault(); }
});
</script>
</body>
</html>
