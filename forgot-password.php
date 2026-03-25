<?php
session_start();
if (!empty($_SESSION['user_id'])) { header('Location: dashboard.php'); exit; }

require_once 'config/db.php';
require_once 'models/User.php';
$model = new User();
$msg   = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $ok = $model->createResetRequest($email);
        if ($ok) {
            $msg = 'Request submitted! An admin will set a temporary password for you shortly. Check back and use it to log in.';
        } else {
            $error = 'No account found with that email address.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Forgot Password — DineLocal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,700;1,700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <style>
    :root{--orange:#C4551A;--orange-d:#9E3A0E;--cream:#FBF0DC;--brown:#3B1A08;--dark:#0d0702;--gold:#E8A83E;--serif:'Cormorant Garamond',serif;}
    *{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:'Inter',sans-serif;background:#0a0502;min-height:100vh;display:flex;flex-direction:column;}
    .auth-wrap{flex:1;display:grid;grid-template-columns:1fr 1fr;min-height:100vh;}
    @media(max-width:768px){.auth-wrap{grid-template-columns:1fr;}}
    .auth-photo{position:relative;overflow:hidden;}
    .auth-photo img{width:100%;height:100%;object-fit:cover;}
    .auth-photo-ov{position:absolute;inset:0;background:linear-gradient(135deg,rgba(13,7,2,.8),rgba(196,85,26,.4));display:flex;flex-direction:column;align-items:center;justify-content:center;padding:3rem;}
    .auth-logo{font-family:var(--serif);font-size:2rem;font-weight:700;color:var(--cream);margin-bottom:.5rem;}
    .auth-tagline{font-size:.9rem;color:rgba(251,240,220,.72);text-align:center;line-height:1.6;max-width:280px;}
    @media(max-width:768px){.auth-photo{min-height:200px;}.auth-photo img{height:200px;}}
    .auth-form-side{background:#faf5ee;display:flex;align-items:center;justify-content:center;padding:2.5rem 2rem;}
    .auth-box{width:100%;max-width:420px;}
    .auth-box h2{font-family:var(--serif);font-size:clamp(1.8rem,3vw,2.4rem);font-weight:700;color:var(--brown);margin-bottom:.35rem;letter-spacing:-.02em;}
    .auth-box p.sub{font-size:.84rem;color:rgba(59,26,8,.55);margin-bottom:1.75rem;}
    .rf{display:flex;flex-direction:column;gap:.28rem;margin-bottom:.85rem;}
    .rf label{font-size:.72rem;font-weight:600;color:var(--brown);display:flex;align-items:center;gap:.3rem;}
    .rf input{background:#F3E4C6;border:none;border-radius:.45rem;padding:.65rem .9rem;font-family:'Inter',sans-serif;font-size:.875rem;color:var(--brown);outline:none;width:100%;transition:background .2s,box-shadow .2s;}
    .rf input:focus{background:#fff9f3;box-shadow:0 0 0 2px rgba(196,85,26,.28);}
    .rf input::placeholder{color:rgba(59,26,8,.3);}
    .btn-auth{width:100%;background:linear-gradient(135deg,var(--orange),var(--orange-d));color:#fff;padding:.88rem;border-radius:9999px;font-size:.9rem;font-weight:600;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:.5rem;box-shadow:0 4px 16px rgba(196,85,26,.4);transition:transform .2s,box-shadow .2s;}
    .btn-auth:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(196,85,26,.55);}
    .auth-switch{text-align:center;margin-top:1.25rem;font-size:.82rem;color:rgba(59,26,8,.6);}
    .auth-switch a{color:var(--orange);font-weight:600;text-decoration:none;}
    .banner-err{background:rgba(192,57,43,.1);border:1px solid rgba(192,57,43,.25);color:#c0392b;border-radius:.5rem;padding:.75rem 1rem;font-size:.82rem;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;}
    .banner-ok{background:rgba(28,120,14,.1);border:1px solid rgba(28,120,14,.2);color:#27ae60;border-radius:.5rem;padding:.75rem 1rem;font-size:.82rem;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;}
  </style>
</head>
<body>
<div class="auth-wrap">
  <div class="auth-photo d-none d-md-block">
    <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=900&h=1200&fit=crop" alt="DineLocal"/>
    <div class="auth-photo-ov">
      <div class="auth-logo">DineLocal</div>
      <p class="auth-tagline">We'll get you back to your table in no time.</p>
    </div>
  </div>

  <div class="auth-form-side">
    <div class="auth-box">
      <div class="text-center mb-4 d-md-none">
        <span style="font-family:var(--serif);font-size:1.5rem;font-weight:700;color:var(--brown)">DineLocal</span>
      </div>

      <h2>Forgot Password</h2>
      <p class="sub">Enter your email and an admin will set a temporary password for you.</p>

      <?php if ($error): ?><div class="banner-err"><i class="bi bi-exclamation-circle-fill"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>
      <?php if ($msg):   ?><div class="banner-ok"><i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($msg) ?></div><?php endif; ?>

      <?php if (!$msg): ?>
      <form method="POST" id="fpForm" novalidate>
        <div class="rf">
          <label><i class="bi bi-envelope"></i> Email Address</label>
          <input type="email" name="email" placeholder="you@example.ca" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"/>
        </div>
        <button type="submit" class="btn-auth">
          <i class="bi bi-send"></i> Submit Reset Request
        </button>
      </form>
      <?php endif; ?>

      <div class="auth-switch mt-3"><a href="login.php"><i class="bi bi-arrow-left"></i> Back to Sign In</a></div>
    </div>
  </div>
</div>
</body>
</html>
