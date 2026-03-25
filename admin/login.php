<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Login — DineLocal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
  <style>
    :root{--orange:#C4551A;--orange-d:#9E3A0E;--brown:#3B1A08;--gold:#E8A83E;--serif:'Cormorant Garamond',serif;}
    *{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:'Inter',sans-serif;background:linear-gradient(135deg,#0d0702 0%,#1a0800 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1.5rem;}
    .login-card{background:#fff;border-radius:1.5rem;padding:2.5rem 2rem;width:100%;max-width:420px;box-shadow:0 32px 80px rgba(0,0,0,.5);}
    .card-logo{text-align:center;margin-bottom:2rem;}
    .card-logo span{font-family:var(--serif);font-size:1.8rem;font-weight:700;color:var(--brown);}
    .card-logo p{font-size:.62rem;letter-spacing:.18em;color:rgba(59,26,8,.42);margin-top:.2rem;}
    .admin-badge{display:inline-flex;align-items:center;gap:.4rem;background:rgba(196,85,26,.1);color:var(--orange);font-size:.7rem;font-weight:700;letter-spacing:.1em;padding:.3rem .85rem;border-radius:9999px;border:1px solid rgba(196,85,26,.25);margin-bottom:1.5rem;}
    h2{font-family:var(--serif);font-size:1.5rem;font-weight:700;color:var(--brown);margin-bottom:.3rem;}
    p.sub{font-size:.82rem;color:rgba(59,26,8,.52);margin-bottom:1.5rem;}
    .rf{display:flex;flex-direction:column;gap:.28rem;margin-bottom:.85rem;}
    .rf label{font-size:.72rem;font-weight:600;color:var(--brown);display:flex;align-items:center;gap:.3rem;}
    .rf input{background:#F3E4C6;border:none;border-radius:.45rem;padding:.7rem 1rem;font-family:'Inter',sans-serif;font-size:.875rem;color:var(--brown);outline:none;width:100%;transition:background .2s,box-shadow .2s;}
    .rf input:focus{background:#fff9f3;box-shadow:0 0 0 2px rgba(196,85,26,.28);}
    .rf input::placeholder{color:rgba(59,26,8,.3);}
    .btn-login{width:100%;background:linear-gradient(135deg,var(--orange),var(--orange-d));color:#fff;padding:.9rem;border-radius:9999px;font-size:.9rem;font-weight:600;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:.5rem;box-shadow:0 4px 16px rgba(196,85,26,.4);transition:transform .2s,box-shadow .2s;margin-top:1rem;}
    .btn-login:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(196,85,26,.55);}
    .banner-err{background:rgba(192,57,43,.08);border:1px solid rgba(192,57,43,.25);color:#c0392b;border-radius:.5rem;padding:.7rem 1rem;font-size:.8rem;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;}
    .back-link{text-align:center;margin-top:1.25rem;font-size:.8rem;}
    .back-link a{color:rgba(59,26,8,.5);text-decoration:none;}
    .back-link a:hover{color:var(--orange);}
    .pass-wrap{position:relative;}
    .pass-wrap input{padding-right:2.5rem;}
    .pass-eye{position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;color:rgba(59,26,8,.4);cursor:pointer;font-size:.95rem;padding:0;}
    .pass-eye:hover{color:var(--orange);}
    .hint{font-size:.7rem;color:rgba(59,26,8,.38);text-align:center;margin-top:.75rem;}
  </style>
</head>
<body>
<?php
session_start();
// Already logged in as admin
if (!empty($_SESSION['admin_id'])) { header('Location: index.php'); exit; }

require_once '../config/db.php';
require_once '../controllers/AdminController.php';

$ctrl  = new AdminController();
$error = '';
if (isset($_GET['logout'])) $error = ''; // cleared

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars(trim($_POST['username'] ?? ''));
    $password = $_POST['password'] ?? '';
    if (!$username || !$password) {
        $error = 'Please enter your username and password.';
    } elseif (!$ctrl->login($username, $password)) {
        $error = 'Invalid username or password.';
    } else {
        header('Location: index.php');
        exit;
    }
}
?>

<div class="login-card">
  <div class="card-logo">
    <span>DineLocal</span>
    <p>MANAGEMENT SYSTEM</p>
  </div>
  <div class="text-center"><span class="admin-badge"><i class="bi bi-shield-lock-fill"></i> ADMIN ACCESS</span></div>
  <h2>Staff Login</h2>
  <p class="sub">Authorised personnel only.</p>

  <?php if ($error): ?>
  <div class="banner-err"><i class="bi bi-exclamation-circle-fill"></i><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" id="adminForm">
    <div class="rf">
      <label><i class="bi bi-person-badge"></i> Username</label>
      <input type="text" name="username" placeholder="admin" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" autocomplete="username"/>
    </div>
    <div class="rf">
      <label><i class="bi bi-lock"></i> Password</label>
      <div class="pass-wrap">
        <input type="password" name="password" id="apw" placeholder="••••••••" required autocomplete="current-password"/>
        <button type="button" class="pass-eye" onclick="t=document.getElementById('apw');t.type=t.type==='password'?'text':'password'"><i class="bi bi-eye"></i></button>
      </div>
    </div>
    <button type="submit" class="btn-login"><i class="bi bi-shield-check"></i> Sign In to Admin Panel</button>
  </form>
  <div class="back-link"><a href="../index.php"><i class="bi bi-arrow-left me-1"></i>Back to main website</a></div>
</div>

<script>
document.getElementById('adminForm').addEventListener('submit', function(e) {
  const u = this.username.value.trim();
  const p = this.password.value;
  if (!u || !p) { alert('Please enter both username and password.'); e.preventDefault(); }
});
</script>
</body>
</html>