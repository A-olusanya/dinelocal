<?php
session_start();
if (empty($_SESSION['user_id'])) { header('Location: login.php?required=1'); exit; }

require_once 'config/db.php';
require_once 'models/User.php';
require_once 'models/Reservation.php';
$userModel = new User();
$resModel  = new Reservation();

$userId = (int)$_SESSION['user_id'];
$user   = $userModel->getById($userId);
$myRes  = $userModel->getReservations($userId);

// Handle profile update
$profileMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'profile') {
        $userModel->updateProfile($userId, [
            'name'    => htmlspecialchars(trim($_POST['name'] ?? '')),
            'phone'   => htmlspecialchars(trim($_POST['phone'] ?? '')),
            'dietary' => htmlspecialchars(trim($_POST['dietary'] ?? '')),
        ]);
        $_SESSION['user_name'] = htmlspecialchars(trim($_POST['name'] ?? ''));
        $user = $userModel->getById($userId); // refresh
        $profileMsg = 'Profile updated successfully!';
    }
    if ($_POST['action'] === 'password') {
        $cur  = $_POST['current_pw'] ?? '';
        $new  = $_POST['new_pw'] ?? '';
        $conf = $_POST['confirm_pw'] ?? '';
        // Verify current password
        $check = $userModel->login($user['email'], $cur);
        if (!$check)            $profileMsg = 'ERROR: Current password is incorrect.';
        elseif (strlen($new)<6) $profileMsg = 'ERROR: New password must be at least 6 characters.';
        elseif ($new !== $conf) $profileMsg = 'ERROR: New passwords do not match.';
        else { $userModel->changePassword($userId, $new); $profileMsg = 'Password changed successfully!'; }
    }
    if ($_POST['action'] === 'cancel_res') {
        $resId = (int)($_POST['res_id'] ?? 0);
        // Only cancel if this reservation belongs to user
        $r = $resModel->getById($resId);
        if ($r && $r['user_id'] == $userId) {
            $resModel->updateStatus($resId, 'cancelled');
        }
        header('Location: dashboard.php?tab=reservations#res');
        exit;
    }
}

// Stats
$total     = count($myRes);
$confirmed = count(array_filter($myRes, fn($r) => $r['status']==='confirmed'));
$upcoming  = count(array_filter($myRes, fn($r) => strtotime($r['date']) >= strtotime('today') && $r['status']!=='cancelled'));

$welcome = isset($_GET['welcome']);
$booked  = isset($_GET['booked']) ? (int)$_GET['booked'] : 0;
$tab     = $_GET['tab'] ?? 'reservations';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>My Account — DineLocal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,700;1,700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="assets/css/style.css"/>
  <style>
    :root{--orange:#C4551A;--orange-d:#9E3A0E;--cream:#FBF0DC;--brown:#3B1A08;--dark:#0d0702;--gold:#E8A83E;--serif:'Cormorant Garamond',serif;}
    body{background:#f4f1ec;}
    .dash-wrap{display:grid;grid-template-columns:260px 1fr;min-height:100vh;}
    @media(max-width:768px){.dash-wrap{grid-template-columns:1fr;}}
    /* Sidebar */
    .dash-side{background:var(--dark);padding:2rem 1.5rem;display:flex;flex-direction:column;}
    .dash-logo{font-family:var(--serif);font-size:1.3rem;font-weight:700;color:var(--cream);margin-bottom:.2rem;}
    .dash-logo-sub{font-size:.6rem;letter-spacing:.15em;color:rgba(251,240,220,.38);margin-bottom:2rem;}
    .dash-avatar{width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,var(--orange),var(--orange-d));display:flex;align-items:center;justify-content:center;font-family:var(--serif);font-size:1.6rem;font-weight:700;color:#fff;margin-bottom:.75rem;}
    .dash-name{font-family:var(--serif);font-size:1.1rem;font-weight:700;color:var(--cream);}
    .dash-email{font-size:.72rem;color:rgba(251,240,220,.42);margin-bottom:1.5rem;}
    .side-link{display:flex;align-items:center;gap:.65rem;padding:.62rem .85rem;border-radius:.5rem;font-size:.82rem;font-weight:500;color:rgba(251,240,220,.55);text-decoration:none;transition:all .2s;margin-bottom:.25rem;}
    .side-link:hover{background:rgba(251,240,220,.06);color:var(--cream);}
    .side-link.active{background:rgba(196,85,26,.18);color:var(--gold);}
    .side-link i{width:18px;text-align:center;}
    .dash-side-footer{margin-top:auto;padding-top:1.5rem;border-top:1px solid rgba(251,240,220,.08);}
    /* Main content */
    .dash-main{padding:2rem;}
    .dash-header{margin-bottom:2rem;}
    .dash-header h1{font-family:var(--serif);font-size:clamp(1.8rem,3vw,2.4rem);font-weight:700;color:var(--brown);letter-spacing:-.02em;}
    .dash-header p{font-size:.85rem;color:rgba(59,26,8,.55);}
    /* Stat cards */
    .stat-c{background:#fff;border-radius:.85rem;padding:1.25rem;border:1px solid rgba(59,26,8,.07);display:flex;align-items:center;gap:1rem;}
    .stat-ic{width:44px;height:44px;border-radius:.65rem;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;}
    .sic-o{background:rgba(196,85,26,.1);color:var(--orange);}
    .sic-g{background:rgba(28,120,14,.08);color:#27ae60;}
    .sic-b{background:rgba(232,168,62,.1);color:var(--gold);}
    .stat-v{font-family:var(--serif);font-size:1.9rem;font-weight:700;color:var(--brown);line-height:1;}
    .stat-l{font-size:.68rem;font-weight:600;color:rgba(59,26,8,.48);letter-spacing:.06em;}
    /* Reservation cards */
    .res-card{background:#fff;border-radius:1rem;border:1px solid rgba(59,26,8,.07);padding:1.25rem 1.5rem;margin-bottom:1rem;display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:1rem;transition:box-shadow .2s;}
    .res-card:hover{box-shadow:0 6px 24px rgba(59,26,8,.1);}
    .res-date{font-family:var(--serif);font-size:1.1rem;font-weight:700;color:var(--brown);}
    .res-meta{font-size:.78rem;color:rgba(59,26,8,.55);margin-top:.2rem;display:flex;flex-wrap:wrap;gap:.5rem 1rem;}
    .res-meta span{display:flex;align-items:center;gap:.3rem;}
    .res-special{font-size:.76rem;color:rgba(59,26,8,.45);margin-top:.4rem;font-style:italic;}
    .badge-p{background:rgba(232,168,62,.15);color:#b8860b;font-size:.65rem;font-weight:700;padding:.22rem .65rem;border-radius:9999px;}
    .badge-c{background:rgba(28,120,14,.1);color:#27ae60;font-size:.65rem;font-weight:700;padding:.22rem .65rem;border-radius:9999px;}
    .badge-x{background:rgba(192,57,43,.1);color:#c0392b;font-size:.65rem;font-weight:700;padding:.22rem .65rem;border-radius:9999px;}
    /* Profile form */
    .form-card{background:#fff;border-radius:1rem;border:1px solid rgba(59,26,8,.07);padding:1.5rem;}
    .form-card h3{font-family:var(--serif);font-size:1.15rem;font-weight:700;color:var(--brown);margin-bottom:1.25rem;padding-bottom:.75rem;border-bottom:1px solid rgba(59,26,8,.07);}
    .rf{display:flex;flex-direction:column;gap:.28rem;margin-bottom:.85rem;}
    .rf label{font-size:.72rem;font-weight:600;color:var(--brown);display:flex;align-items:center;gap:.3rem;}
    .rf input,.rf textarea{background:#F3E4C6;border:none;border-radius:.45rem;padding:.65rem .9rem;font-family:'Inter',sans-serif;font-size:.875rem;color:var(--brown);outline:none;width:100%;transition:background .2s,box-shadow .2s;}
    .rf input:focus,.rf textarea:focus{background:#fff9f3;box-shadow:0 0 0 2px rgba(196,85,26,.28);}
    .rf textarea{resize:vertical;min-height:72px;}
    .btn-save{background:linear-gradient(135deg,var(--orange),var(--orange-d));color:#fff;padding:.7rem 1.75rem;border-radius:9999px;font-size:.84rem;font-weight:600;border:none;cursor:pointer;box-shadow:0 3px 12px rgba(196,85,26,.35);transition:transform .2s;}
    .btn-save:hover{transform:translateY(-1px);}
    .banner-ok{background:rgba(28,120,14,.1);border:1px solid rgba(28,120,14,.2);color:#27ae60;border-radius:.5rem;padding:.65rem 1rem;font-size:.8rem;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;}
    /* Tabs */
    .dash-tabs{display:flex;gap:.5rem;margin-bottom:1.5rem;flex-wrap:wrap;}
    .dash-tab{background:#fff;border:1px solid rgba(59,26,8,.1);border-radius:.5rem;padding:.45rem 1.1rem;font-size:.8rem;font-weight:600;color:rgba(59,26,8,.55);cursor:pointer;transition:all .2s;}
    .dash-tab.active,.dash-tab:hover{background:var(--orange);border-color:var(--orange);color:#fff;}
    .tab-panel{display:none;}.tab-panel.active{display:block;}
    .empty-state{text-align:center;padding:3rem 1rem;}
    .empty-state i{font-size:2.5rem;color:rgba(59,26,8,.2);display:block;margin-bottom:.75rem;}
    .empty-state p{font-size:.88rem;color:rgba(59,26,8,.45);}
    /* Mobile sidebar toggle */
    .mob-dash-tog{display:none;background:var(--orange);color:#fff;border:none;padding:.55rem 1.1rem;border-radius:9999px;font-size:.82rem;font-weight:600;cursor:pointer;margin-bottom:1.25rem;box-shadow:0 3px 12px rgba(196,85,26,.35);}
    .dash-side-close{display:none;position:absolute;top:.85rem;right:.85rem;background:none;border:none;color:rgba(251,240,220,.5);font-size:1.2rem;cursor:pointer;}
    .dash-side-close:hover{color:var(--cream);}
    .dash-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:98;}
    @media(max-width:768px){
      .dash-side{display:none;position:fixed;top:0;left:0;bottom:0;width:280px;z-index:99;overflow-y:auto;}
      .dash-side.open{display:flex;}
      .dash-overlay.show{display:block;}
      .mob-dash-tog{display:inline-flex;align-items:center;gap:.4rem;}
      .dash-side-close{display:block;}
    }
  </style>
</head>
<body>

<!-- Overlay -->
<div class="dash-overlay" id="dashOverlay" onclick="closeDashSide()"></div>

<div class="dash-wrap">
  <!-- Sidebar -->
  <aside class="dash-side" id="dashSide" style="position:relative;">
    <button class="dash-side-close" onclick="closeDashSide()"><i class="bi bi-x-lg"></i></button>
    <div class="dash-logo">DineLocal</div>
    <div class="dash-logo-sub">MY ACCOUNT</div>
    <div class="dash-avatar"><?= strtoupper(substr($user['name'], 0, 1)) ?></div>
    <div class="dash-name"><?= htmlspecialchars($user['name']) ?></div>
    <div class="dash-email"><?= htmlspecialchars($user['email']) ?></div>

    <a href="?tab=reservations" class="side-link <?= $tab==='reservations'?'active':'' ?>"><i class="bi bi-calendar2-check"></i> My Reservations</a>
    <a href="?tab=profile"      class="side-link <?= $tab==='profile'?'active':'' ?>"><i class="bi bi-person-gear"></i> Edit Profile</a>
    <a href="?tab=password"     class="side-link <?= $tab==='password'?'active':'' ?>"><i class="bi bi-shield-lock"></i> Change Password</a>
    <a href="reservations.php"  class="side-link"><i class="bi bi-plus-circle"></i> New Reservation</a>
    <a href="menu.php"          class="side-link"><i class="bi bi-card-list"></i> View Menu</a>
    <a href="index.php"         class="side-link"><i class="bi bi-house"></i> Back to Site</a>

    <div class="dash-side-footer">
      <a href="logout.php" class="side-link" style="color:rgba(192,57,43,.7)"><i class="bi bi-box-arrow-right"></i> Sign Out</a>
    </div>
  </aside>

  <!-- Main -->
  <main class="dash-main">
    <button class="mob-dash-tog" onclick="openDashSide()">
      <i class="bi bi-list"></i> Menu
    </button>

    <?php if ($welcome): ?>
    <div class="banner-ok mb-3"><i class="bi bi-stars"></i> Welcome to DineLocal, <?= htmlspecialchars($user['name']) ?>! Your account is ready.</div>
    <?php endif; ?>

    <?php if ($booked): ?>
    <div class="banner-ok mb-3" style="background:rgba(232,168,62,.12);border-color:rgba(232,168,62,.35);color:#7a5500">
      <i class="bi bi-hourglass-split"></i>
      <span>Reservation #<?= $booked ?> submitted! It is <strong>pending</strong> — an admin will confirm it shortly. You can see it below.</span>
    </div>
    <?php endif; ?>

    <?php if ($profileMsg): ?>
    <div class="banner-ok <?= str_starts_with($profileMsg,'ERROR') ? 'border-danger' : '' ?> mb-3">
      <i class="bi <?= str_starts_with($profileMsg,'ERROR') ? 'bi-exclamation-circle-fill' : 'bi-check-circle-fill' ?>"></i>
      <?= htmlspecialchars(str_replace('ERROR: ','',$profileMsg)) ?>
    </div>
    <?php endif; ?>

    <!-- Stats row -->
    <div class="row g-3 mb-4">
      <div class="col-12 col-md-4">
        <div class="stat-c"><div class="stat-ic sic-o"><i class="bi bi-calendar2"></i></div><div><div class="stat-v"><?= $total ?></div><div class="stat-l">TOTAL BOOKINGS</div></div></div>
      </div>
      <div class="col-12 col-md-4">
        <div class="stat-c"><div class="stat-ic sic-g"><i class="bi bi-check-circle"></i></div><div><div class="stat-v"><?= $confirmed ?></div><div class="stat-l">CONFIRMED</div></div></div>
      </div>
      <div class="col-12 col-md-4">
        <div class="stat-c"><div class="stat-ic sic-b"><i class="bi bi-calendar-event"></i></div><div><div class="stat-v"><?= $upcoming ?></div><div class="stat-l">UPCOMING</div></div></div>
      </div>
    </div>

    <!-- RESERVATIONS TAB -->
    <?php if ($tab === 'reservations'): ?>
    <div class="dash-header"><h1>My Reservations</h1><p>View and manage all your bookings.</p></div>

    <div class="d-flex gap-2 mb-3 align-items-center flex-wrap">
      <a href="reservations.php" class="btn-save text-decoration-none d-inline-flex align-items-center gap-2" style="font-size:.8rem;padding:.5rem 1.2rem">
        <i class="bi bi-plus-circle"></i> New Reservation
      </a>
    </div>

    <?php if (empty($myRes)): ?>
    <div class="empty-state">
      <i class="bi bi-calendar-x"></i>
      <p>You haven't made any reservations yet.</p>
      <a href="reservations.php" style="color:var(--orange);font-weight:600;font-size:.85rem;text-decoration:none">Make your first booking →</a>
    </div>
    <?php else: ?>
      <?php foreach ($myRes as $r):
        $isPast = strtotime($r['date']) < strtotime('today');
        $badge  = ['pending'=>'badge-p','confirmed'=>'badge-c','cancelled'=>'badge-x'][$r['status']] ?? 'badge-p';
      ?>
      <div class="res-card" id="res">
        <div>
          <div class="res-date"><?= date('l, F j, Y', strtotime($r['date'])) ?></div>
          <div class="res-meta">
            <span><i class="bi bi-clock"></i><?= htmlspecialchars($r['time']) ?></span>
            <span><i class="bi bi-people"></i><?= htmlspecialchars($r['guests']) ?></span>
            <span><i class="bi bi-hash"></i>Booking #<?= $r['id'] ?></span>
          </div>
          <?php if ($r['special']): ?>
          <div class="res-special"><i class="bi bi-chat-text me-1"></i><?= htmlspecialchars($r['special']) ?></div>
          <?php endif; ?>
        </div>
        <div class="d-flex flex-column align-items-end gap-2">
          <span class="<?= $badge ?>"><?= ucfirst($r['status']) ?></span>
          <?php if (!$isPast && $r['status'] !== 'cancelled'): ?>
          <form method="POST" onsubmit="return confirm('Cancel this reservation for <?= date('M j', strtotime($r['date'])) ?>?')">
            <input type="hidden" name="action" value="cancel_res"/>
            <input type="hidden" name="res_id" value="<?= $r['id'] ?>"/>
            <button type="submit" style="background:none;border:none;color:rgba(192,57,43,.65);font-size:.75rem;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:.3rem;padding:0">
              <i class="bi bi-x-circle"></i> Cancel
            </button>
          </form>
          <a href="reservations.php" style="color:var(--orange);font-size:.75rem;font-weight:600;text-decoration:none;display:flex;align-items:center;gap:.3rem">
            <i class="bi bi-pencil"></i> Rebook
          </a>
          <?php endif; ?>
          <?php if ($isPast): ?>
          <span style="font-size:.68rem;color:rgba(59,26,8,.38)"><i class="bi bi-clock-history"></i> Past</span>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <!-- PROFILE TAB -->
    <?php elseif ($tab === 'profile'): ?>
    <div class="dash-header"><h1>Edit Profile</h1><p>Update your personal information and preferences.</p></div>
    <div class="form-card">
      <h3><i class="bi bi-person me-2" style="color:var(--orange)"></i>Personal Information</h3>
      <form method="POST">
        <input type="hidden" name="action" value="profile"/>
        <div class="row g-3">
          <div class="col-12 col-sm-6">
            <div class="rf"><label><i class="bi bi-person"></i> Full Name</label><input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required/></div>
          </div>
          <div class="col-12 col-sm-6">
            <div class="rf"><label><i class="bi bi-envelope"></i> Email (cannot change)</label><input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled style="opacity:.6"/></div>
          </div>
          <div class="col-12 col-sm-6">
            <div class="rf"><label><i class="bi bi-telephone"></i> Phone Number</label><input type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="(416) 555-0192"/></div>
          </div>
          <div class="col-12">
            <div class="rf"><label><i class="bi bi-heart"></i> Dietary Preferences / Allergies</label><textarea name="dietary" placeholder="e.g. Vegetarian, nut allergy..."><?= htmlspecialchars($user['dietary'] ?? '') ?></textarea></div>
          </div>
          <div class="col-12">
            <button type="submit" class="btn-save"><i class="bi bi-save me-1"></i> Save Changes</button>
          </div>
        </div>
      </form>
    </div>

    <!-- PASSWORD TAB -->
    <?php elseif ($tab === 'password'): ?>
    <div class="dash-header"><h1>Change Password</h1><p>Keep your account secure with a strong password.</p></div>
    <div class="form-card">
      <h3><i class="bi bi-shield-lock me-2" style="color:var(--orange)"></i>Update Password</h3>
      <form method="POST" id="pwForm">
        <input type="hidden" name="action" value="password"/>
        <div class="rf"><label><i class="bi bi-lock"></i> Current Password</label><input type="password" name="current_pw" required placeholder="Enter current password"/></div>
        <div class="rf"><label><i class="bi bi-lock-fill"></i> New Password <small style="font-weight:400;color:rgba(59,26,8,.38)">(min 6 chars)</small></label><input type="password" name="new_pw" id="npw" required placeholder="Enter new password"/></div>
        <div class="rf"><label><i class="bi bi-lock-fill"></i> Confirm New Password</label><input type="password" name="confirm_pw" id="cpw" required placeholder="Repeat new password"/></div>
        <button type="submit" class="btn-save"><i class="bi bi-shield-check me-1"></i> Change Password</button>
      </form>
    </div>
    <?php endif; ?>

  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openDashSide(){
  document.getElementById('dashSide').classList.add('open');
  document.getElementById('dashOverlay').classList.add('show');
}
function closeDashSide(){
  document.getElementById('dashSide').classList.remove('open');
  document.getElementById('dashOverlay').classList.remove('show');
}
document.getElementById('pwForm')?.addEventListener('submit', function(e) {
  const nw = document.getElementById('npw').value;
  const cf = document.getElementById('cpw').value;
  if (nw.length < 6) { alert('New password must be at least 6 characters.'); e.preventDefault(); return; }
  if (nw !== cf)     { alert('New passwords do not match.');                  e.preventDefault(); return; }
});
</script>
</body>
</html>