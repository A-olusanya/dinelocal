<?php
session_start();
if (!empty($_SESSION['user_id']))  { header('Location: dashboard.php'); exit; }
if (!empty($_SESSION['admin_id'])) { header('Location: admin/index.php'); exit; }

$action = $_GET['action'] ?? 'signin'; // 'signin' or 'signup'
$title  = $action === 'signup' ? 'Create Account' : 'Sign In';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= $title ?> — DineLocal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,700;1,700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <style>
    :root{--orange:#C4551A;--orange-d:#9E3A0E;--cream:#FBF0DC;--brown:#3B1A08;--dark:#0d0702;--gold:#E8A83E;--serif:'Cormorant Garamond',serif;}
    *{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:'Inter',sans-serif;background:#0a0502;min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:2rem;}
    .bg-overlay{position:fixed;inset:0;background:url('https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=1600&fit=crop') center/cover no-repeat;z-index:0;}
    .bg-overlay::after{content:'';position:absolute;inset:0;background:linear-gradient(135deg,rgba(13,7,2,.92),rgba(59,26,8,.85));}
    .page-content{position:relative;z-index:1;width:100%;max-width:560px;text-align:center;}
    .role-logo{font-family:var(--serif);font-size:2.2rem;font-weight:700;color:var(--cream);letter-spacing:-.01em;margin-bottom:.3rem;}
    .role-subtitle{font-size:.8rem;color:rgba(251,240,220,.45);letter-spacing:.18em;margin-bottom:2.5rem;}
    .role-heading{font-family:var(--serif);font-size:clamp(1.6rem,3vw,2rem);font-weight:700;color:var(--cream);margin-bottom:.5rem;}
    .role-desc{font-size:.85rem;color:rgba(251,240,220,.55);margin-bottom:2rem;}
    .role-cards{display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:2rem;}
    @media(max-width:480px){.role-cards{grid-template-columns:1fr;}}
    .role-card{background:rgba(251,240,220,.06);border:1px solid rgba(251,240,220,.1);border-radius:1.25rem;padding:2rem 1.5rem;text-decoration:none;color:var(--cream);transition:all .25s;cursor:pointer;display:flex;flex-direction:column;align-items:center;gap:1rem;}
    .role-card:hover{background:rgba(196,85,26,.2);border-color:rgba(196,85,26,.6);color:var(--cream);transform:translateY(-4px);box-shadow:0 16px 40px rgba(196,85,26,.3);}
    .role-card.admin-card:hover{background:rgba(232,168,62,.12);border-color:rgba(232,168,62,.5);box-shadow:0 16px 40px rgba(232,168,62,.2);}
    .role-icon{width:64px;height:64px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.6rem;}
    .user-icon{background:rgba(196,85,26,.2);color:var(--orange);}
    .admin-icon{background:rgba(232,168,62,.15);color:var(--gold);}
    .role-card:hover .user-icon{background:rgba(196,85,26,.35);}
    .role-card:hover .admin-icon{background:rgba(232,168,62,.25);}
    .role-name{font-family:var(--serif);font-size:1.3rem;font-weight:700;}
    .role-text{font-size:.76rem;color:rgba(251,240,220,.5);line-height:1.5;}
    .role-card:hover .role-text{color:rgba(251,240,220,.7);}
    .role-arrow{font-size:.8rem;color:rgba(251,240,220,.3);margin-top:.25rem;transition:all .2s;}
    .role-card:hover .role-arrow{color:var(--orange);transform:translateX(4px);}
    .admin-card:hover .role-arrow{color:var(--gold);}
    .back-link{font-size:.8rem;color:rgba(251,240,220,.38);text-decoration:none;display:inline-flex;align-items:center;gap:.4rem;transition:color .2s;}
    .back-link:hover{color:rgba(251,240,220,.7);}
    .toggle-action{font-size:.82rem;color:rgba(251,240,220,.45);margin-bottom:1.5rem;}
    .toggle-action a{color:var(--orange);font-weight:600;text-decoration:none;}
    .toggle-action a:hover{text-decoration:underline;}
  </style>
</head>
<body>
  <div class="bg-overlay"></div>
  <div class="page-content">

    <div class="role-logo">DineLocal</div>
    <div class="role-subtitle">TORONTO · EST. 2024</div>

    <h1 class="role-heading">
      <?= $action === 'signup' ? 'Create Your Account' : 'Welcome Back' ?>
    </h1>
    <p class="role-desc">
      <?= $action === 'signup' ? 'Who are you signing up as?' : 'How would you like to sign in?' ?>
    </p>

    <div class="role-cards">
      <a href="<?= $action === 'signup' ? 'register.php' : 'login.php' ?>" class="role-card">
        <div class="role-icon user-icon"><i class="bi bi-person-fill"></i></div>
        <div>
          <div class="role-name">Guest / Diner</div>
          <div class="role-text">
            <?= $action === 'signup'
              ? 'Create a personal account to book tables and manage reservations.'
              : 'Access your reservations, profile, and dining history.' ?>
          </div>
        </div>
        <div class="role-arrow"><i class="bi bi-arrow-right-circle-fill"></i> Continue</div>
      </a>

      <a href="admin/login.php" class="role-card admin-card">
        <div class="role-icon admin-icon"><i class="bi bi-shield-lock-fill"></i></div>
        <div>
          <div class="role-name">Staff / Admin</div>
          <div class="role-text">
            <?= $action === 'signup'
              ? 'Admin accounts are managed by the restaurant. Contact your manager.'
              : 'Manage reservations, menu items, and restaurant operations.' ?>
          </div>
        </div>
        <div class="role-arrow"><i class="bi bi-arrow-right-circle-fill"></i> Continue</div>
      </a>
    </div>

    <?php if ($action === 'signin'): ?>
    <div class="toggle-action">Don't have an account? <a href="choose-role.php?action=signup">Sign up free</a></div>
    <?php else: ?>
    <div class="toggle-action">Already have an account? <a href="choose-role.php?action=signin">Sign in</a></div>
    <?php endif; ?>

    <a href="index.php" class="back-link"><i class="bi bi-arrow-left"></i> Back to homepage</a>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>