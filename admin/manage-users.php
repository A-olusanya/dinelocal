<?php
session_start();
require_once '../config/db.php';
require_once '../controllers/AdminController.php';
AdminController::requireAuth();
require_once '../models/User.php';
$model   = new User();
$message = '';

// Handle setting temp password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'set_temp_pw') {
        $userId    = (int)($_POST['user_id']    ?? 0);
        $requestId = (int)($_POST['request_id'] ?? 0);
        $tempPw    = trim($_POST['temp_password'] ?? '');
        if ($userId && strlen($tempPw) >= 6) {
            $model->setTempPassword($userId, $tempPw, $requestId);
            $message = 'Temporary password set. The user will be required to change it on next login.';
        } else {
            $message = 'ERROR: Password must be at least 6 characters.';
        }
    }
}

$pendingResets = $model->getPendingResets();
$allUsers      = $model->getAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage Users — DineLocal Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
  <style>
    :root{--orange:#C4551A;--gold:#E8A83E;--dark:#0d0702;--cream:#FBF0DC;--brown:#3B1A08;--serif:'Cormorant Garamond',serif;}
    *{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:'Inter',sans-serif;background:#f4f1ec;color:var(--brown);display:flex;min-height:100vh;}
    .sidebar{width:240px;background:var(--dark);flex-shrink:0;display:flex;flex-direction:column;position:fixed;top:0;bottom:0;left:0;z-index:100;}
    .sidebar-logo{padding:1.5rem 1.5rem 1rem;border-bottom:1px solid rgba(232,168,62,.12);}
    .sidebar-logo h2{font-family:var(--serif);font-size:1.3rem;font-weight:700;color:var(--cream);}
    .sidebar-logo p{font-size:.62rem;color:rgba(251,240,220,.4);letter-spacing:.12em;margin-top:.2rem;}
    .sidebar-nav{padding:1rem 0;flex:1;}
    .nav-item{display:flex;align-items:center;gap:.75rem;padding:.72rem 1.5rem;font-size:.82rem;font-weight:500;color:rgba(251,240,220,.55);text-decoration:none;transition:all .2s;border-left:2px solid transparent;}
    .nav-item:hover{color:var(--cream);background:rgba(251,240,220,.05);}
    .nav-item.active{color:var(--gold);border-left-color:var(--orange);background:rgba(196,85,26,.1);}
    .nav-item i{font-size:1rem;width:18px;text-align:center;}
    .sidebar-footer{padding:1rem 1.5rem;border-top:1px solid rgba(232,168,62,.1);}
    .sidebar-footer a{display:flex;align-items:center;gap:.6rem;font-size:.78rem;color:rgba(251,240,220,.42);text-decoration:none;transition:color .2s;}
    .sidebar-footer a:hover{color:var(--cream);}
    .main{margin-left:240px;flex:1;}
    .topbar{background:#fff;border-bottom:1px solid rgba(59,26,8,.08);padding:1rem 1.5rem;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50;}
    .topbar h1{font-family:var(--serif);font-size:1.5rem;font-weight:700;color:var(--brown);}
    .topbar-date{font-size:.78rem;color:rgba(59,26,8,.5);}
    @media(max-width:767px){.topbar{padding:.75rem 1rem;}.topbar h1{font-size:1.2rem;}.topbar-date{display:none;}.admin-badge{font-size:.65rem;padding:.25rem .6rem;}}
    .content{padding:2rem;}
    .card-box{background:#fff;border-radius:1rem;border:1px solid rgba(59,26,8,.06);overflow:hidden;margin-bottom:2rem;}
    .card-box-head{padding:1.25rem 1.5rem;border-bottom:1px solid rgba(59,26,8,.06);display:flex;align-items:center;justify-content:space-between;}
    .card-box-head h3{font-family:var(--serif);font-size:1.15rem;font-weight:700;color:var(--brown);}
    .table{margin:0;}
    .table th{font-size:.68rem;font-weight:600;letter-spacing:.12em;color:rgba(59,26,8,.5);border-bottom-width:1px;padding:.75rem 1.25rem;}
    .table td{font-size:.82rem;color:var(--brown);padding:.85rem 1.25rem;vertical-align:middle;}
    .badge-pending{background:rgba(232,168,62,.15);color:#b8860b;font-size:.65rem;font-weight:600;padding:.22rem .65rem;border-radius:9999px;}
    .btn-sm-action{font-size:.7rem;font-weight:600;padding:.3rem .8rem;border-radius:6px;border:none;cursor:pointer;transition:all .2s;}
    .btn-set-pw{background:rgba(196,85,26,.1);color:var(--orange);}
    .btn-set-pw:hover{background:var(--orange);color:#fff;}
    .alert-ok{background:rgba(28,120,14,.1);color:#27ae60;border:1px solid rgba(28,120,14,.2);border-radius:.5rem;padding:.75rem 1rem;font-size:.82rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:.5rem;}
    .alert-err{background:rgba(192,57,43,.1);color:#c0392b;border:1px solid rgba(192,57,43,.25);border-radius:.5rem;padding:.75rem 1rem;font-size:.82rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:.5rem;}
    .pw-input{background:#F3E4C6;border:1px solid rgba(59,26,8,.12);border-radius:.35rem;padding:.35rem .65rem;font-size:.78rem;color:var(--brown);outline:none;width:140px;}
    .pw-input:focus{border-color:var(--orange);box-shadow:0 0 0 2px rgba(196,85,26,.15);}
    .empty-row td{text-align:center;color:rgba(59,26,8,.38);font-style:italic;padding:2rem!important;}
    .mob-tog{display:none;background:none;border:none;font-size:1.4rem;color:var(--brown);cursor:pointer;padding:.25rem;}
    .sidebar-header{display:flex;align-items:center;justify-content:space-between;padding:1.5rem 1.5rem 1rem;border-bottom:1px solid rgba(232,168,62,.12);}
    .sidebar-close{display:none;background:none;border:none;color:rgba(251,240,220,.5);font-size:1.15rem;cursor:pointer;padding:0;line-height:1;}
    .sidebar-close:hover{color:var(--cream);}
    .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:150;}
    @media(max-width:767px){
      .sidebar{transform:translateX(-100%);transition:transform .3s ease;z-index:200;}
      .sidebar.open{transform:translateX(0);}
      .main{margin-left:0!important;}
      .mob-tog{display:flex!important;align-items:center;}
      .sidebar-close{display:block!important;}
      .sidebar-overlay.show{display:block;}
    }
  </style>
</head>
<body>


<div class="sidebar-overlay" id="sidebarOverlay"></div>
<aside class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <div><h2 style="font-family:var(--serif);font-size:1.3rem;font-weight:700;color:var(--cream);margin:0">DineLocal</h2><p style="font-size:.62rem;color:rgba(251,240,220,.4);letter-spacing:.12em;margin:0">ADMIN PANEL</p></div>
    <button class="sidebar-close" id="sidebarClose"><i class="bi bi-x-lg"></i></button>
  </div>
  <nav class="sidebar-nav">
    <a href="index.php"                class="nav-item"><i class="bi bi-grid"></i> Dashboard</a>
    <a href="manage-reservations.php"  class="nav-item"><i class="bi bi-calendar2-check"></i> Reservations</a>
    <a href="manage-menu.php"          class="nav-item"><i class="bi bi-card-list"></i> Menu Items</a>
    <a href="manage-users.php"         class="nav-item active"><i class="bi bi-people"></i> Users</a>
    <?php if (AdminController::hasRole('super_admin')): ?>
    <a href="manage-admins.php" class="nav-item"><i class="bi bi-shield-lock"></i> Admins</a>
    <?php endif; ?>
    <a href="../index.php" class="nav-item" target="_blank" rel="noopener"><i class="bi bi-arrow-left-circle"></i> View Site</a>
  </nav>
  <div class="sidebar-footer" style="display:flex;flex-direction:column;gap:.5rem;">
    <span style="font-size:.7rem;color:rgba(251,240,220,.35);padding-bottom:.25rem"><?= htmlspecialchars($_SESSION['admin_username'] ?? '') ?> &middot; <?= htmlspecialchars($_SESSION['admin_role'] ?? '') ?></span>
    <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Sign Out</a>
  </div>
</aside>

<div class="main">
  <div class="topbar">
    <div class="d-flex align-items-center gap-3">
      <button class="mob-tog" id="sidebarToggle"><i class="bi bi-list"></i></button>
      <h1>Manage Users</h1>
    </div>
    <span class="topbar-date">Logged in as: <?= htmlspecialchars($_SESSION['admin_username']) ?></span>
  </div>

  <div class="content">

    <?php if ($message): ?>
    <div class="<?= str_starts_with($message,'ERROR') ? 'alert-err' : 'alert-ok' ?>">
      <i class="bi bi-<?= str_starts_with($message,'ERROR') ? 'exclamation-circle' : 'check-circle' ?>-fill"></i>
      <?= htmlspecialchars($message) ?>
    </div>
    <?php endif; ?>

    <!-- Pending Reset Requests -->
    <div class="card-box">
      <div class="card-box-head">
        <h3><i class="bi bi-key me-2" style="color:var(--orange)"></i>Password Reset Requests</h3>
        <span class="badge-pending"><?= count($pendingResets) ?> pending</span>
      </div>
      <table class="table table-hover">
        <thead>
          <tr>
            <th>USER</th>
            <th>EMAIL</th>
            <th>REQUESTED</th>
            <th>SET TEMP PASSWORD</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($pendingResets)): ?>
          <tr class="empty-row"><td colspan="4">No pending password reset requests</td></tr>
          <?php else: foreach ($pendingResets as $req): ?>
          <tr>
            <td><?= htmlspecialchars($req['name'] ?? '—') ?></td>
            <td><?= htmlspecialchars($req['email']) ?></td>
            <td style="font-size:.75rem;color:rgba(59,26,8,.5)"><?= date('M j, Y g:ia', strtotime($req['requested_at'])) ?></td>
            <td>
              <form method="POST" style="display:flex;align-items:center;gap:.5rem" onsubmit="return confirm('Set this temporary password for the user?')">
                <input type="hidden" name="action"     value="set_temp_pw"/>
                <input type="hidden" name="user_id"    value="<?= (int)$req['user_id'] ?>"/>
                <input type="hidden" name="request_id" value="<?= (int)$req['id'] ?>"/>
                <input type="text" name="temp_password" class="pw-input" placeholder="Temp password" required minlength="6"/>
                <button type="submit" class="btn-sm-action btn-set-pw"><i class="bi bi-check"></i> Set &amp; Notify</button>
              </form>
            </td>
          </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>

    <!-- All Users -->
    <div class="card-box">
      <div class="card-box-head">
        <h3><i class="bi bi-people me-2" style="color:var(--orange)"></i>All Users</h3>
        <span style="font-size:.72rem;color:rgba(59,26,8,.4)"><?= count($allUsers) ?> registered</span>
      </div>
      <table class="table table-hover">
        <thead>
          <tr>
            <th>NAME</th>
            <th>EMAIL</th>
            <th>PHONE</th>
            <th>STATUS</th>
            <th>JOINED</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($allUsers)): ?>
          <tr class="empty-row"><td colspan="5">No users yet</td></tr>
          <?php else: foreach ($allUsers as $u): ?>
          <tr>
            <td><?= htmlspecialchars($u['name']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= htmlspecialchars($u['phone'] ?? '—') ?></td>
            <td>
              <?php if (!empty($u['force_password_change'])): ?>
              <span class="badge-pending">Temp Password</span>
              <?php else: ?>
              <span style="font-size:.65rem;font-weight:600;color:#27ae60">Active</span>
              <?php endif; ?>
            </td>
            <td style="font-size:.75rem;color:rgba(59,26,8,.5)"><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
          </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function(){
  var tog = document.getElementById('sidebarToggle');
  var cls = document.getElementById('sidebarClose');
  var ov  = document.getElementById('sidebarOverlay');
  var sb  = document.getElementById('sidebar');
  function openNav(){if(sb&&ov){sb.classList.add('open');ov.classList.add('show');}}
  function closeNav(){if(sb&&ov){sb.classList.remove('open');ov.classList.remove('show');}}
  if(tog) tog.addEventListener('click', openNav);
  if(cls) cls.addEventListener('click', closeNav);
  if(ov)  ov.addEventListener('click', closeNav);
})();
</script>
</body>
</html>
