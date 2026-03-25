<?php
session_start();
require_once '../config/db.php';
require_once '../controllers/AdminController.php';
AdminController::requireAuth();
AdminController::requireRole('super_admin'); // Only super admins

$ctrl    = new AdminController();
$message = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email']    ?? '');
        $password = $_POST['password']      ?? '';
        $role     = $_POST['role']          ?? 'menu_manager';
        if (!$username || !$email || strlen($password) < 6) {
            $error = 'Username, email and a password of at least 6 characters are required.';
        } else {
            try {
                $ctrl->create($username, $email, $password, $role);
                $message = "Admin '$username' added successfully.";
            } catch (Exception $e) {
                $error = 'That username or email is already in use.';
            }
        }
    }

    if ($action === 'change_role') {
        $id   = (int)($_POST['admin_id'] ?? 0);
        $role = $_POST['role'] ?? '';
        if ($id === (int)$_SESSION['admin_id']) {
            $error = 'You cannot change your own role.';
        } else {
            $ctrl->updateRole($id, $role);
            $message = 'Role updated.';
        }
    }

    if ($action === 'reset_password') {
        $id  = (int)($_POST['admin_id'] ?? 0);
        $pw  = $_POST['new_password'] ?? '';
        if (strlen($pw) < 6) {
            $error = 'Password must be at least 6 characters.';
        } else {
            $ctrl->resetPassword($id, $pw);
            $message = 'Password reset successfully.';
        }
    }

    if ($action === 'delete') {
        $id = (int)($_POST['admin_id'] ?? 0);
        if ($id === (int)$_SESSION['admin_id']) {
            $error = 'You cannot delete your own account.';
        } else {
            $ctrl->delete($id);
            $message = 'Admin account removed.';
        }
    }
}

$admins = $ctrl->getAll();

$roleLabels = [
    'super_admin'            => ['label'=>'Super Admin',          'color'=>'#b8860b', 'bg'=>'rgba(232,168,62,.15)', 'desc'=>'Full access to everything including managing other admins.'],
    'menu_manager'           => ['label'=>'Menu Manager',         'color'=>'#27ae60', 'bg'=>'rgba(28,120,14,.1)',   'desc'=>'Can add, edit, and delete menu items only.'],
    'reservations_manager'   => ['label'=>'Reservations Manager', 'color'=>'#3498db', 'bg'=>'rgba(52,152,219,.1)', 'desc'=>'Can view and manage reservations only.'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage Admins — DineLocal</title>
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
    .sidebar-footer{padding:1rem 1.5rem;border-top:1px solid rgba(232,168,62,.1);display:flex;flex-direction:column;gap:.5rem;}
    .sidebar-footer a{display:flex;align-items:center;gap:.6rem;font-size:.78rem;color:rgba(251,240,220,.42);text-decoration:none;transition:color .2s;}
    .sidebar-footer a:hover{color:var(--cream);}
    .sidebar-footer .logout-btn{color:rgba(192,57,43,.7);}
    .sidebar-footer .logout-btn:hover{color:#e74c3c;}
    .main{margin-left:240px;flex:1;}
    .topbar{background:#fff;border-bottom:1px solid rgba(59,26,8,.08);padding:1rem 1.5rem;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50;}
    .topbar h1{font-family:var(--serif);font-size:1.5rem;font-weight:700;color:var(--brown);}
    .topbar-date{font-size:.78rem;color:rgba(59,26,8,.5);}
    @media(max-width:767px){.topbar{padding:.75rem 1rem;}.topbar h1{font-size:1.2rem;}.topbar-date{display:none;}.admin-badge{font-size:.65rem;padding:.25rem .6rem;}}
    .content{padding:2rem;}
    .card-box{background:#fff;border-radius:1rem;border:1px solid rgba(59,26,8,.06);overflow:hidden;margin-bottom:2rem;}
    .card-box-head{padding:1.25rem 1.5rem;border-bottom:1px solid rgba(59,26,8,.06);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.5rem;}
    .card-box-head h3{font-family:var(--serif);font-size:1.15rem;font-weight:700;color:var(--brown);}
    .form-card{background:#fff;border-radius:1rem;border:1px solid rgba(59,26,8,.06);padding:1.75rem;margin-bottom:2rem;}
    .form-card h3{font-family:var(--serif);font-size:1.15rem;font-weight:700;color:var(--brown);margin-bottom:1.25rem;padding-bottom:.75rem;border-bottom:1px solid rgba(59,26,8,.07);}
    .form-label{font-size:.72rem;font-weight:600;color:rgba(59,26,8,.6);letter-spacing:.04em;}
    .form-control,.form-select{border:1px solid rgba(59,26,8,.12);border-radius:.45rem;font-size:.84rem;color:var(--brown);padding:.6rem .9rem;}
    .form-control:focus,.form-select:focus{border-color:var(--orange);box-shadow:0 0 0 2px rgba(196,85,26,.15);outline:none;}
    .btn-add{background:linear-gradient(135deg,var(--orange),#9E3A0E);color:#fff;border:none;border-radius:.5rem;padding:.65rem 1.5rem;font-size:.82rem;font-weight:600;cursor:pointer;transition:all .2s;}
    .btn-add:hover{transform:translateY(-1px);box-shadow:0 4px 14px rgba(196,85,26,.4);}
    .table{margin:0;}
    .table th{font-size:.68rem;font-weight:600;letter-spacing:.12em;color:rgba(59,26,8,.5);border-bottom-width:1px;padding:.75rem 1.25rem;}
    .table td{font-size:.82rem;color:var(--brown);padding:.85rem 1.25rem;vertical-align:middle;}
    .role-badge{display:inline-flex;align-items:center;gap:.35rem;font-size:.65rem;font-weight:700;padding:.22rem .7rem;border-radius:9999px;}
    .btn-sm-action{font-size:.7rem;font-weight:600;padding:.25rem .65rem;border-radius:6px;border:none;cursor:pointer;transition:all .2s;}
    .btn-role{background:rgba(232,168,62,.12);color:#b8860b;}
    .btn-role:hover{background:var(--gold);color:#fff;}
    .btn-pw{background:rgba(52,152,219,.1);color:#3498db;}
    .btn-pw:hover{background:#3498db;color:#fff;}
    .btn-del{background:rgba(192,57,43,.1);color:#c0392b;}
    .btn-del:hover{background:#c0392b;color:#fff;}
    .you-badge{background:rgba(196,85,26,.1);color:var(--orange);font-size:.6rem;font-weight:700;padding:.15rem .5rem;border-radius:9999px;margin-left:.4rem;}
    .alert-ok{background:rgba(28,120,14,.1);color:#27ae60;border:1px solid rgba(28,120,14,.2);border-radius:.5rem;padding:.75rem 1rem;font-size:.82rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:.5rem;}
    .alert-err{background:rgba(192,57,43,.1);color:#c0392b;border:1px solid rgba(192,57,43,.25);border-radius:.5rem;padding:.75rem 1rem;font-size:.82rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:.5rem;}
    .role-info{background:#faf8f5;border:1px solid rgba(59,26,8,.07);border-radius:.75rem;padding:1.25rem;margin-bottom:1.5rem;}
    .role-info h4{font-size:.72rem;font-weight:700;letter-spacing:.1em;color:rgba(59,26,8,.5);margin-bottom:.85rem;}
    .ri-row{display:flex;align-items:flex-start;gap:.75rem;margin-bottom:.65rem;}
    .ri-row:last-child{margin-bottom:0;}
    .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:999;align-items:center;justify-content:center;}
    .modal-overlay.open{display:flex;}
    .modal-box{background:#fff;border-radius:1rem;padding:1.75rem;width:100%;max-width:400px;box-shadow:0 24px 64px rgba(0,0,0,.3);}
    .modal-box h4{font-family:var(--serif);font-size:1.2rem;font-weight:700;color:var(--brown);margin-bottom:1rem;}
    .pw-input{background:#F3E4C6;border:none;border-radius:.45rem;padding:.65rem .9rem;font-size:.875rem;color:var(--brown);outline:none;width:100%;transition:background .2s,box-shadow .2s;}
    .pw-input:focus{background:#fff9f3;box-shadow:0 0 0 2px rgba(196,85,26,.28);}
    .mob-tog{display:none;background:none;border:none;font-size:1.3rem;color:var(--brown);cursor:pointer;}
    .sidebar-header{display:flex;align-items:flex-start;justify-content:space-between;padding:1.5rem 1.5rem 1rem;border-bottom:1px solid rgba(232,168,62,.12);}
    .sidebar-close{display:none;background:none;border:none;color:rgba(251,240,220,.4);font-size:1.15rem;cursor:pointer;padding:0;line-height:1;flex-shrink:0;margin-top:.15rem;}
    .sidebar-close:hover{color:var(--cream);}
    .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:99;}
    .topbar-date{font-size:.78rem;color:rgba(59,26,8,.5);}
    @media(max-width:767px){
      .sidebar{transform:translateX(-100%);transition:transform .3s ease;}
      .sidebar.open{transform:translateX(0);}
      .main{margin-left:0;}
      .mob-tog{display:flex!important;}
      .sidebar-close{display:block;}
      .sidebar-overlay.show{display:block;}
      .topbar{padding:.75rem 1rem;}
      .topbar h1{font-size:1.2rem;}
      .topbar-date{display:none;}
    }
  </style>
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>
<aside class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <div><h2 style="font-family:var(--serif);font-size:1.3rem;font-weight:700;color:var(--cream);margin:0">DineLocal</h2><p style="font-size:.62rem;color:rgba(251,240,220,.4);letter-spacing:.12em;margin:0">ADMIN PANEL</p></div>
    <button class="sidebar-close" onclick="closeSidebar()"><i class="bi bi-x-lg"></i></button>
  </div>
  <nav class="sidebar-nav">
    <a href="index.php"               class="nav-item" onclick="closeSidebar()"><i class="bi bi-grid"></i> Dashboard</a>
    <a href="manage-reservations.php" class="nav-item" onclick="closeSidebar()"><i class="bi bi-calendar2-check"></i> Reservations</a>
    <a href="manage-menu.php"         class="nav-item" onclick="closeSidebar()"><i class="bi bi-card-list"></i> Menu Items</a>
    <a href="manage-users.php"        class="nav-item" onclick="closeSidebar()"><i class="bi bi-people"></i> Users</a>
    <a href="manage-admins.php"       class="nav-item active" onclick="closeSidebar()"><i class="bi bi-shield-lock"></i> Admins</a>
    <a href="../index.php"            class="nav-item" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> View Site</a>
  </nav>
  <div class="sidebar-footer">
    <div style="font-size:.7rem;color:rgba(251,240,220,.3);padding:.25rem 0">
      Signed in as <strong style="color:rgba(251,240,220,.6)"><?= htmlspecialchars($_SESSION['admin_username']) ?></strong>
    </div>
    <a href="logout.php" class="logout-btn"><i class="bi bi-box-arrow-right"></i> Sign Out</a>
  </div>
</aside>

<div class="main">
  <div class="topbar">
    <div class="d-flex align-items-center gap-3">
      <button class="mob-tog" onclick="openSidebar()"><i class="bi bi-list"></i></button>
      <h1>Manage Admins</h1>
    </div>
    <span class="topbar-date"><i class="bi bi-shield-check me-1" style="color:var(--orange)"></i><?= htmlspecialchars($_SESSION['admin_username']) ?></span>
  </div>

  <div class="content">

    <?php if ($message): ?><div class="alert-ok"><i class="bi bi-check-circle-fill"></i><?= htmlspecialchars($message) ?></div><?php endif; ?>
    <?php if ($error):   ?><div class="alert-err"><i class="bi bi-exclamation-circle-fill"></i><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <!-- Role descriptions -->
    <div class="role-info">
      <h4>ROLE PERMISSIONS</h4>
      <?php foreach ($roleLabels as $key => $r): ?>
      <div class="ri-row">
        <span class="role-badge" style="background:<?= $r['bg'] ?>;color:<?= $r['color'] ?>;flex-shrink:0"><?= $r['label'] ?></span>
        <span style="font-size:.78rem;color:rgba(59,26,8,.55)"><?= $r['desc'] ?></span>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Add new admin -->
    <div class="form-card">
      <h3><i class="bi bi-person-plus me-2" style="color:var(--orange)"></i>Add New Admin</h3>
      <form method="POST">
        <input type="hidden" name="action" value="add"/>
        <div class="row g-3">
          <div class="col-12 col-md-4">
            <label class="form-label">Username *</label>
            <input type="text" name="username" class="form-control" placeholder="e.g. john_staff" required/>
          </div>
          <div class="col-12 col-md-4">
            <label class="form-label">Email *</label>
            <input type="email" name="email" class="form-control" placeholder="john@restaurant.ca" required/>
          </div>
          <div class="col-12 col-md-4">
            <label class="form-label">Temporary Password *</label>
            <input type="text" name="password" class="form-control" placeholder="Min 6 characters" required minlength="6"/>
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label">Role *</label>
            <select name="role" class="form-select">
              <option value="menu_manager">Menu Manager — Manage menu items only</option>
              <option value="reservations_manager">Reservations Manager — Manage reservations only</option>
              <option value="super_admin">Super Admin — Full access</option>
            </select>
          </div>
          <div class="col-12 col-md-6 d-flex align-items-end">
            <button type="submit" class="btn-add"><i class="bi bi-person-plus-fill me-1"></i> Add Admin</button>
          </div>
        </div>
      </form>
    </div>

    <!-- All admins list -->
    <div class="card-box">
      <div class="card-box-head">
        <h3><i class="bi bi-shield-lock me-2" style="color:var(--orange)"></i>All Admin Accounts</h3>
        <span style="font-size:.72rem;color:rgba(59,26,8,.4)"><?= count($admins) ?> accounts</span>
      </div>
      <table class="table table-hover">
        <thead>
          <tr>
            <th>USERNAME</th>
            <th>EMAIL</th>
            <th>ROLE</th>
            <th>JOINED</th>
            <th>ACTIONS</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($admins as $a):
            $isYou = $a['id'] == $_SESSION['admin_id'];
            $rl    = $roleLabels[$a['role']] ?? $roleLabels['super_admin'];
          ?>
          <tr>
            <td>
              <?= htmlspecialchars($a['username']) ?>
              <?php if ($isYou): ?><span class="you-badge">YOU</span><?php endif; ?>
            </td>
            <td style="font-size:.78rem;color:rgba(59,26,8,.55)"><?= htmlspecialchars($a['email']) ?></td>
            <td>
              <span class="role-badge" style="background:<?= $rl['bg'] ?>;color:<?= $rl['color'] ?>">
                <?= $rl['label'] ?>
              </span>
            </td>
            <td style="font-size:.75rem;color:rgba(59,26,8,.4)"><?= date('M j, Y', strtotime($a['created_at'])) ?></td>
            <td>
              <?php if (!$isYou): ?>
              <div style="display:flex;gap:.35rem;flex-wrap:wrap;align-items:center;">
                <!-- Change role -->
                <form method="POST" style="display:flex;gap:.3rem;align-items:center">
                  <input type="hidden" name="action"   value="change_role"/>
                  <input type="hidden" name="admin_id" value="<?= $a['id'] ?>"/>
                  <select name="role" class="form-select form-select-sm" style="font-size:.72rem;padding:.22rem .65rem;width:auto">
                    <option value="menu_manager"          <?= $a['role']==='menu_manager'         ?'selected':'' ?>>Menu Manager</option>
                    <option value="reservations_manager"  <?= $a['role']==='reservations_manager' ?'selected':'' ?>>Reservations Mgr</option>
                    <option value="super_admin"           <?= $a['role']==='super_admin'           ?'selected':'' ?>>Super Admin</option>
                  </select>
                  <button type="submit" class="btn-sm-action btn-role">Set Role</button>
                </form>
                <!-- Reset password -->
                <button class="btn-sm-action btn-pw" onclick="openPwModal(<?= $a['id'] ?>, '<?= htmlspecialchars($a['username']) ?>')">
                  <i class="bi bi-key"></i> Reset PW
                </button>
                <!-- Delete -->
                <form method="POST" onsubmit="return confirm('Remove <?= htmlspecialchars($a['username']) ?> from admin panel?')">
                  <input type="hidden" name="action"   value="delete"/>
                  <input type="hidden" name="admin_id" value="<?= $a['id'] ?>"/>
                  <button type="submit" class="btn-sm-action btn-del"><i class="bi bi-trash"></i></button>
                </form>
              </div>
              <?php else: ?>
              <span style="font-size:.72rem;color:rgba(59,26,8,.35);font-style:italic">Your account</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

  </div>
</div>

<!-- Reset Password Modal -->
<div class="modal-overlay" id="pwModal">
  <div class="modal-box">
    <h4><i class="bi bi-key me-2" style="color:var(--orange)"></i>Reset Password for <span id="pwName"></span></h4>
    <form method="POST">
      <input type="hidden" name="action"   value="reset_password"/>
      <input type="hidden" name="admin_id" id="pwAdminId"/>
      <div style="margin-bottom:1rem">
        <label style="font-size:.72rem;font-weight:600;color:var(--brown);display:block;margin-bottom:.35rem">New Password</label>
        <input type="text" name="new_password" class="pw-input" placeholder="Min 6 characters" required minlength="6"/>
      </div>
      <div style="display:flex;gap:.75rem">
        <button type="submit" class="btn-add" style="flex:1">Set Password</button>
        <button type="button" onclick="closePwModal()" style="flex:1;border:1px solid rgba(59,26,8,.15);background:transparent;border-radius:.5rem;font-size:.84rem;font-weight:600;color:rgba(59,26,8,.55);cursor:pointer">Cancel</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openPwModal(id, name) {
  document.getElementById('pwAdminId').value = id;
  document.getElementById('pwName').textContent = name;
  document.getElementById('pwModal').classList.add('open');
}
function closePwModal() {
  document.getElementById('pwModal').classList.remove('open');
}
document.getElementById('pwModal').addEventListener('click', function(e) {
  if (e.target === this) closePwModal();
});
</script>
<script>
function openSidebar(){
  document.getElementById("sidebar").classList.add("open");
  document.getElementById("sidebarOverlay").classList.add("show");
}
function closeSidebar(){
  document.getElementById("sidebar").classList.remove("open");
  document.getElementById("sidebarOverlay").classList.remove("show");
}
</script>
</body>
</html>
