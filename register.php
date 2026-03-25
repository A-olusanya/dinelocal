<?php
session_start();
if (!empty($_SESSION['user_id'])) { header('Location: dashboard.php'); exit; }

require_once 'config/db.php';
require_once 'models/User.php';
$model  = new User();
$error  = '';
$success = false;
$data   = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name'     => htmlspecialchars(trim($_POST['name'] ?? '')),
        'email'    => filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL),
        'password' => $_POST['password'] ?? '',
        'confirm'  => $_POST['confirm'] ?? '',
        'phone'    => htmlspecialchars(trim($_POST['phone'] ?? '')),
        'dietary'  => htmlspecialchars(trim($_POST['dietary'] ?? '')),
    ];

    if (strlen($data['name']) < 2)                   $error = 'Full name must be at least 2 characters.';
    elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $error = 'Please enter a valid email address.';
    elseif (strlen($data['password']) < 6)           $error = 'Password must be at least 6 characters.';
    elseif ($data['password'] !== $data['confirm'])  $error = 'Passwords do not match.';
    else {
        $id = $model->register($data);
        if ($id === false) {
            $error = 'An account with this email already exists. Please log in.';
        } else {
            $_SESSION['user_id']   = $id;
            $_SESSION['user_name'] = $data['name'];
            header('Location: dashboard.php?welcome=1');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Create Account — DineLocal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,700;1,700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <style>
    :root{--orange:#C4551A;--orange-d:#9E3A0E;--cream:#FBF0DC;--brown:#3B1A08;--dark:#0d0702;--gold:#E8A83E;--serif:'Cormorant Garamond',serif;}
    *{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:'Inter',sans-serif;background:#0a0502;min-height:100vh;display:flex;flex-direction:column;}
    /* Split layout */
    .auth-wrap{flex:1;display:grid;grid-template-columns:1fr 1fr;min-height:100vh;}
    @media(max-width:768px){.auth-wrap{grid-template-columns:1fr;}}
    /* Photo side */
    .auth-photo{position:relative;overflow:hidden;}
    .auth-photo img{width:100%;height:100%;object-fit:cover;object-position:center;}
    .auth-photo-ov{position:absolute;inset:0;background:linear-gradient(135deg,rgba(13,7,2,.8),rgba(196,85,26,.4));display:flex;flex-direction:column;align-items:center;justify-content:center;padding:3rem;}
    .auth-logo{font-family:var(--serif);font-size:2rem;font-weight:700;color:var(--cream);margin-bottom:.5rem;}
    .auth-tagline{font-size:.9rem;color:rgba(251,240,220,.72);text-align:center;line-height:1.6;max-width:280px;}
    @media(max-width:768px){.auth-photo{min-height:220px;}.auth-photo img{height:220px;}.auth-photo-ov{padding:2rem;}.auth-logo{font-size:1.5rem;}}
    /* Form side */
    .auth-form-side{background:#faf5ee;display:flex;align-items:center;justify-content:center;padding:2.5rem 2rem;}
    .auth-box{width:100%;max-width:460px;}
    .auth-box h2{font-family:var(--serif);font-size:clamp(1.8rem,3vw,2.4rem);font-weight:700;color:var(--brown);margin-bottom:.35rem;letter-spacing:-.02em;}
    .auth-box p.sub{font-size:.84rem;color:rgba(59,26,8,.55);margin-bottom:1.75rem;}
    /* Fields */
    .rf{display:flex;flex-direction:column;gap:.28rem;margin-bottom:.85rem;}
    .rf label{font-size:.72rem;font-weight:600;color:var(--brown);display:flex;align-items:center;gap:.3rem;}
    .rf input,.rf select,.rf textarea{background:#F3E4C6;border:none;border-radius:.45rem;padding:.65rem .9rem;font-family:'Inter',sans-serif;font-size:.875rem;color:var(--brown);outline:none;width:100%;transition:background .2s,box-shadow .2s;}
    .rf input:focus,.rf select:focus,.rf textarea:focus{background:#fff9f3;box-shadow:0 0 0 2px rgba(196,85,26,.28);}
    .rf input::placeholder,.rf textarea::placeholder{color:rgba(59,26,8,.3);}
    .rf textarea{resize:vertical;min-height:64px;}
    .rerr{font-size:.66rem;color:#c0392b;font-weight:500;min-height:.8rem;display:block;}
    .btn-auth{width:100%;background:linear-gradient(135deg,var(--orange),var(--orange-d));color:#fff;padding:.88rem;border-radius:9999px;font-size:.9rem;font-weight:600;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:.5rem;box-shadow:0 4px 16px rgba(196,85,26,.4);transition:transform .2s,box-shadow .2s;}
    .btn-auth:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(196,85,26,.55);}
    .auth-switch{text-align:center;margin-top:1.25rem;font-size:.82rem;color:rgba(59,26,8,.6);}
    .auth-switch a{color:var(--orange);font-weight:600;text-decoration:none;}
    .auth-switch a:hover{text-decoration:underline;}
    .banner-err{background:rgba(192,57,43,.1);border:1px solid rgba(192,57,43,.25);color:#c0392b;border-radius:.5rem;padding:.75rem 1rem;font-size:.82rem;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;}
    .banner-ok{background:rgba(28,120,14,.1);border:1px solid rgba(28,120,14,.2);color:#27ae60;border-radius:.5rem;padding:.75rem 1rem;font-size:.82rem;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;}
    .divider{display:flex;align-items:center;gap:.75rem;margin:1.25rem 0;color:rgba(59,26,8,.38);font-size:.72rem;}
    .divider::before,.divider::after{content:'';flex:1;height:1px;background:rgba(59,26,8,.12);}
    .pass-wrap{position:relative;}
    .pass-wrap input{padding-right:2.5rem;}
    .pass-eye{position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;color:rgba(59,26,8,.4);cursor:pointer;font-size:.95rem;padding:0;display:flex;align-items:center;}
    .pass-eye:hover{color:var(--orange);}
  </style>
</head>
<body>
<div class="auth-wrap">
  <!-- Photo side -->
  <div class="auth-photo d-none d-md-block">
    <img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=900&h=1200&fit=crop" alt="DineLocal"/>
    <div class="auth-photo-ov">
      <div class="auth-logo">DineLocal</div>
      <p class="auth-tagline">Join our community and enjoy a seamless dining experience in Toronto's heart.</p>
      <div style="margin-top:2rem;display:flex;flex-direction:column;gap:.75rem;width:100%;max-width:260px">
        <div style="display:flex;align-items:center;gap:.75rem;color:rgba(251,240,220,.7);font-size:.8rem"><i class="bi bi-check-circle-fill" style="color:var(--gold)"></i> Track all your reservations</div>
        <div style="display:flex;align-items:center;gap:.75rem;color:rgba(251,240,220,.7);font-size:.8rem"><i class="bi bi-check-circle-fill" style="color:var(--gold)"></i> Manage dietary preferences</div>
        <div style="display:flex;align-items:center;gap:.75rem;color:rgba(251,240,220,.7);font-size:.8rem"><i class="bi bi-check-circle-fill" style="color:var(--gold)"></i> Cancel or modify bookings</div>
        <div style="display:flex;align-items:center;gap:.75rem;color:rgba(251,240,220,.7);font-size:.8rem"><i class="bi bi-check-circle-fill" style="color:var(--gold)"></i> Get exclusive member offers</div>
      </div>
    </div>
  </div>

  <!-- Form side -->
  <div class="auth-form-side">
    <div class="auth-box">
      <!-- Mobile logo -->
      <div class="text-center mb-4 d-md-none">
        <span style="font-family:var(--serif);font-size:1.5rem;font-weight:700;color:var(--brown)">DineLocal</span>
      </div>

      <h2>Create Account</h2>
      <p class="sub">Join DineLocal — it's free and takes 30 seconds.</p>

      <?php if ($error): ?>
      <div class="banner-err"><i class="bi bi-exclamation-circle-fill"></i><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" id="regForm" novalidate>
        <div class="row g-0">
          <div class="col-12">
            <div class="rf">
              <label><i class="bi bi-person"></i> Full Name *</label>
              <input type="text" name="name" placeholder="Julian Thorne" required value="<?= htmlspecialchars($data['name'] ?? '') ?>"/>
            </div>
          </div>
          <div class="col-12">
            <div class="rf">
              <label><i class="bi bi-envelope"></i> Email Address *</label>
              <input type="email" name="email" placeholder="you@example.ca" required value="<?= htmlspecialchars($data['email'] ?? '') ?>"/>
            </div>
          </div>
          <div class="col-12">
            <div class="rf">
              <label><i class="bi bi-lock"></i> Password * <small style="font-weight:400;color:rgba(59,26,8,.4)">(min 6 chars)</small></label>
              <div class="pass-wrap">
                <input type="password" name="password" id="pw1" placeholder="••••••••" required/>
                <button type="button" class="pass-eye" onclick="togglePass('pw1',this)"><i class="bi bi-eye"></i></button>
              </div>
            </div>
          </div>
          <div class="col-12">
            <div class="rf">
              <label><i class="bi bi-lock-fill"></i> Confirm Password *</label>
              <div class="pass-wrap">
                <input type="password" name="confirm" id="pw2" placeholder="••••••••" required/>
                <button type="button" class="pass-eye" onclick="togglePass('pw2',this)"><i class="bi bi-eye"></i></button>
              </div>
            </div>
          </div>

          <div class="divider">OPTIONAL PREFERENCES</div>

          <div class="col-12">
            <div class="rf">
              <label><i class="bi bi-telephone"></i> Phone Number</label>
              <input type="tel" name="phone" placeholder="(416) 555-0192" value="<?= htmlspecialchars($data['phone'] ?? '') ?>"/>
            </div>
          </div>
          <div class="col-12">
            <div class="rf">
              <label><i class="bi bi-heart"></i> Dietary Preferences / Allergies</label>
              <textarea name="dietary" placeholder="e.g. Vegetarian, nut allergy, gluten-free..."><?= htmlspecialchars($data['dietary'] ?? '') ?></textarea>
            </div>
          </div>
        </div>

        <button type="submit" class="btn-auth mt-1">
          <i class="bi bi-person-plus-fill"></i> Create My Account
        </button>
      </form>

      <div class="auth-switch">
        Already have an account? <a href="login.php">Sign in here</a>
      </div>
      <div class="auth-switch mt-2">
        <a href="index.php" style="color:rgba(59,26,8,.5)"><i class="bi bi-arrow-left"></i> Back to homepage</a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePass(id, btn) {
  const inp = document.getElementById(id);
  const isText = inp.type === 'text';
  inp.type = isText ? 'password' : 'text';
  btn.querySelector('i').className = isText ? 'bi bi-eye' : 'bi bi-eye-slash';
}
// JS validation (rubric)
document.getElementById('regForm').addEventListener('submit', function(e) {
  const pw1 = document.getElementById('pw1').value;
  const pw2 = document.getElementById('pw2').value;
  if (pw1.length < 6) {
    alert('Password must be at least 6 characters.');
    e.preventDefault(); return;
  }
  if (pw1 !== pw2) {
    alert('Passwords do not match. Please check and try again.');
    e.preventDefault(); return;
  }
});
</script>
</body>
</html>
