<?php
session_start();
require_once '../config/db.php';
require_once '../controllers/AdminController.php';
AdminController::requireAuth();
require_once '../models/Reservation.php';
require_once '../models/Menu.php';

$resModel  = new Reservation();
$menuModel = new Menu();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $id = (int)($_POST['res_id'] ?? 0);
    if ($_POST['action'] === 'confirm')   $resModel->updateStatus($id, 'confirmed');
    if ($_POST['action'] === 'cancel')    $resModel->updateStatus($id, 'cancelled');
    header('Location: index.php');
    exit;
}

$counts     = $resModel->countByStatus();
$todayRes   = $resModel->getToday();
$allRes     = $resModel->getAll();
$allMenu    = $menuModel->getAll();
$totalRes   = array_sum($counts);
$totalItems = count($allMenu);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard — DineLocal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,700;1,700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <style>
    :root{--orange:#C4551A;--gold:#E8A83E;--dark:#0d0702;--cream:#FBF0DC;--brown:#3B1A08;--serif:'Cormorant Garamond',serif;}
    *{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:'Inter',sans-serif;background:#f4f1ec;color:var(--brown);display:flex;min-height:100vh;}
    /* Sidebar */
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
    /* Main */
    .main{margin-left:240px;flex:1;padding:0;}
    .topbar{background:#fff;border-bottom:1px solid rgba(59,26,8,.08);padding:1rem 1.5rem;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50;}
    .topbar h1{font-family:var(--serif);font-size:1.5rem;font-weight:700;color:var(--brown);}
    .topbar-right{display:flex;align-items:center;gap:.75rem;}
    .admin-badge{background:rgba(196,85,26,.1);color:var(--orange);padding:.32rem .85rem;border-radius:9999px;font-size:.72rem;font-weight:600;}
    .topbar-date{font-size:.78rem;color:rgba(59,26,8,.5);}
    @media(max-width:767px){
      .topbar{padding:.75rem 1rem;}
      .topbar h1{font-size:1.2rem;}
      .topbar-date{display:none;}
      .admin-badge{font-size:.65rem;padding:.25rem .6rem;}
    }
    .content{padding:2rem;}
    /* Stat cards */
    .stat-card{background:#fff;border-radius:1rem;padding:1.5rem;border:1px solid rgba(59,26,8,.06);display:flex;align-items:flex-start;gap:1rem;}
    .stat-icon{width:48px;height:48px;border-radius:.75rem;display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;}
    .si-orange{background:rgba(196,85,26,.12);color:var(--orange);}
    .si-gold{background:rgba(232,168,62,.12);color:var(--gold);}
    .si-green{background:rgba(28,120,14,.1);color:#2ecc71;}
    .si-blue{background:rgba(52,152,219,.1);color:#3498db;}
    .stat-val{font-family:var(--serif);font-size:2rem;font-weight:700;color:var(--brown);line-height:1;}
    .stat-lbl{font-size:.72rem;font-weight:600;color:rgba(59,26,8,.5);letter-spacing:.08em;margin-top:.2rem;}
    /* Table */
    .card-box{background:#fff;border-radius:1rem;border:1px solid rgba(59,26,8,.06);overflow:hidden;}
    .card-box-head{padding:1.25rem 1.5rem;border-bottom:1px solid rgba(59,26,8,.06);display:flex;align-items:center;justify-content:space-between;}
    .card-box-head h3{font-family:var(--serif);font-size:1.15rem;font-weight:700;color:var(--brown);}
    .table{margin:0;}
    .table th{font-size:.68rem;font-weight:600;letter-spacing:.12em;color:rgba(59,26,8,.5);border-bottom-width:1px;padding:.75rem 1.25rem;}
    .table td{font-size:.82rem;color:var(--brown);padding:.85rem 1.25rem;vertical-align:middle;}
    .badge-pending{background:rgba(232,168,62,.15);color:#b8860b;font-size:.65rem;font-weight:600;padding:.22rem .65rem;border-radius:9999px;}
    .badge-confirmed{background:rgba(28,120,14,.1);color:#27ae60;font-size:.65rem;font-weight:600;padding:.22rem .65rem;border-radius:9999px;}
    .badge-cancelled{background:rgba(192,57,43,.1);color:#c0392b;font-size:.65rem;font-weight:600;padding:.22rem .65rem;border-radius:9999px;}
    .btn-sm-action{font-size:.7rem;font-weight:600;padding:.22rem .65rem;border-radius:6px;border:none;cursor:pointer;transition:all .2s;}
    .btn-confirm{background:rgba(28,120,14,.1);color:#27ae60;}
    .btn-confirm:hover{background:#27ae60;color:#fff;}
    .btn-cancel{background:rgba(192,57,43,.1);color:#c0392b;}
    .btn-cancel:hover{background:#c0392b;color:#fff;}
    /* Mobile sidebar */
    .mob-tog{display:none;background:none;border:none;font-size:1.3rem;color:var(--brown);cursor:pointer;}
    .sidebar-header{display:flex;align-items:flex-start;justify-content:space-between;padding:1.5rem 1.5rem 1rem;border-bottom:1px solid rgba(232,168,62,.12);}
    .sidebar-close{display:none;background:none;border:none;color:rgba(251,240,220,.4);font-size:1.15rem;cursor:pointer;padding:0;line-height:1;flex-shrink:0;margin-top:.15rem;}
    .sidebar-close:hover{color:var(--cream);}
    @media(max-width:767px){
      .sidebar{transform:translateX(-100%);transition:transform .3s ease;}
      .sidebar.open{transform:translateX(0);}
      .main{margin-left:0;}
      .mob-tog{display:flex!important;}
      .sidebar-close{display:block;}
      .sidebar-overlay.show{display:block;}
    }
    /* Desktop: sidebar always visible, overrides Bootstrap offcanvas hide */
    @media(min-width:768px){
      .sidebar{position:fixed!important;transform:none!important;visibility:visible!important;display:flex!important;}
      .main{margin-left:240px;}
      .mob-tog{display:none!important;}
    }
  </style>
</head>
<body>




<!-- Sidebar -->
<aside class="sidebar offcanvas offcanvas-start" id="sidebar" tabindex="-1">
  <div class="sidebar-header">
    <div><h2 style="font-family:var(--serif);font-size:1.3rem;font-weight:700;color:var(--cream);margin:0">DineLocal</h2><p style="font-size:.62rem;color:rgba(251,240,220,.4);letter-spacing:.12em;margin:0">ADMIN PANEL</p></div>
    <button class="sidebar-close" data-bs-dismiss="offcanvas"><i class="bi bi-x-lg"></i></button>
  </div>
  <nav class="sidebar-nav">
    <a href="index.php" class="nav-item active"><i class="bi bi-grid"></i> Dashboard</a>
    <a href="manage-reservations.php" class="nav-item"><i class="bi bi-calendar2-check"></i> Reservations</a>
    <a href="manage-menu.php" class="nav-item"><i class="bi bi-card-list"></i> Menu Items</a>
    <a href="manage-users.php" class="nav-item"><i class="bi bi-people"></i> Users</a>
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

<!-- Main -->
<div class="main">
  <div class="topbar">
    <div class="d-flex align-items-center gap-3">
      <button class="mob-tog" data-bs-toggle="offcanvas" data-bs-target="#sidebar">
        <i class="bi bi-list"></i>
      </button>
      <h1>Dashboard</h1>
    </div>
    <div class="topbar-right">
      <span class="admin-badge"><i class="bi bi-shield-check"></i> Admin</span>
      <span class="topbar-date"><?= date('D, M j Y') ?></span>
    </div>
  </div>

  <div class="content">

    <!-- Stats -->
    <div class="row g-3 mb-4">
      <div class="col-12 col-md-3">
        <div class="stat-card">
          <div class="stat-icon si-orange"><i class="bi bi-calendar2-check"></i></div>
          <div>
            <div class="stat-val"><?= $totalRes ?></div>
            <div class="stat-lbl">TOTAL RESERVATIONS</div>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-3">
        <div class="stat-card">
          <div class="stat-icon si-gold"><i class="bi bi-hourglass-split"></i></div>
          <div>
            <div class="stat-val"><?= $counts['pending'] ?></div>
            <div class="stat-lbl">PENDING</div>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-3">
        <div class="stat-card">
          <div class="stat-icon si-green"><i class="bi bi-check-circle"></i></div>
          <div>
            <div class="stat-val"><?= $counts['confirmed'] ?></div>
            <div class="stat-lbl">CONFIRMED</div>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-3">
        <div class="stat-card">
          <div class="stat-icon si-blue"><i class="bi bi-card-list"></i></div>
          <div>
            <div class="stat-val"><?= $totalItems ?></div>
            <div class="stat-lbl">MENU ITEMS</div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-4">

      <!-- Today's Reservations -->
      <div class="col-12 col-lg-7">
        <div class="card-box">
          <div class="card-box-head">
            <h3>Today's Reservations</h3>
            <span style="font-size:.72rem;color:rgba(59,26,8,.45)"><?= date('F j, Y') ?></span>
          </div>
          <?php if (empty($todayRes)): ?>
          <div class="text-center py-5" style="color:rgba(59,26,8,.38);font-size:.85rem">
            <i class="bi bi-calendar-x" style="font-size:2rem;display:block;margin-bottom:.5rem"></i>
            No reservations today.
          </div>
          <?php else: ?>
          <div class="table-responsive">
            <table class="table">
              <thead><tr><th>NAME</th><th>TIME</th><th>GUESTS</th><th>STATUS</th><th>ACTION</th></tr></thead>
              <tbody>
                <?php foreach ($todayRes as $r): ?>
                <tr>
                  <td><strong><?= htmlspecialchars($r['full_name']) ?></strong><br><small style="color:rgba(59,26,8,.45)"><?= htmlspecialchars($r['email']) ?></small></td>
                  <td><?= htmlspecialchars($r['time']) ?></td>
                  <td><?= htmlspecialchars($r['guests']) ?></td>
                  <td><span class="badge-<?= $r['status'] ?>"><?= ucfirst($r['status']) ?></span></td>
                  <td>
                    <div class="d-flex gap-1">
                      <?php if ($r['status'] === 'pending'): ?>
                      <form method="POST" style="display:inline">
                        <input type="hidden" name="res_id" value="<?= $r['id'] ?>"/>
                        <input type="hidden" name="action" value="confirm"/>
                        <button class="btn-sm-action btn-confirm" type="submit">Confirm</button>
                      </form>
                      <?php endif; ?>
                      <?php if ($r['status'] !== 'cancelled'): ?>
                      <form method="POST" style="display:inline">
                        <input type="hidden" name="res_id" value="<?= $r['id'] ?>"/>
                        <input type="hidden" name="action" value="cancel"/>
                        <button class="btn-sm-action btn-cancel" type="submit">Cancel</button>
                      </form>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Quick Links + Recent -->
      <div class="col-12 col-lg-5">
        <div class="card-box mb-4">
          <div class="card-box-head"><h3>Quick Actions</h3></div>
          <div class="p-3 d-flex flex-column gap-2">
            <a href="manage-reservations.php" class="btn btn-sm" style="background:rgba(196,85,26,.1);color:var(--orange);border:none;font-size:.8rem;font-weight:600;text-align:left;padding:.65rem 1rem;border-radius:.5rem">
              <i class="bi bi-calendar2-check me-2"></i> Manage All Reservations
            </a>
            <a href="manage-menu.php" class="btn btn-sm" style="background:rgba(196,85,26,.1);color:var(--orange);border:none;font-size:.8rem;font-weight:600;text-align:left;padding:.65rem 1rem;border-radius:.5rem">
              <i class="bi bi-plus-circle me-2"></i> Add Menu Item
            </a>
            <a href="../index.php" target="_blank" class="btn btn-sm" style="background:rgba(59,26,8,.06);color:var(--brown);border:none;font-size:.8rem;font-weight:600;text-align:left;padding:.65rem 1rem;border-radius:.5rem">
              <i class="bi bi-box-arrow-up-right me-2"></i> View Live Website
            </a>
          </div>
        </div>
        <div class="card-box">
          <div class="card-box-head"><h3>Reservation Status</h3></div>
          <div class="p-3">
            <?php
            $total = max($totalRes, 1);
            foreach ([
              ['pending',   $counts['pending'],   '#f39c12'],
              ['confirmed', $counts['confirmed'],  '#27ae60'],
              ['cancelled', $counts['cancelled'],  '#c0392b'],
            ] as [$label, $count, $color]):
            $pct = round(($count/$total)*100);
            ?>
            <div class="mb-3">
              <div class="d-flex justify-content-between mb-1">
                <span style="font-size:.75rem;font-weight:600;color:var(--brown)"><?= ucfirst($label) ?></span>
                <span style="font-size:.75rem;color:rgba(59,26,8,.5)"><?= $count ?></span>
              </div>
              <div style="height:6px;background:rgba(59,26,8,.08);border-radius:9999px;overflow:hidden">
                <div style="height:100%;width:<?= $pct ?>%;background:<?= $color ?>;border-radius:9999px"></div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
</script>
</body>
</html>