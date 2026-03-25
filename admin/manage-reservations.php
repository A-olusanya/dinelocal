<?php
session_start();
require_once '../config/db.php';
require_once '../controllers/AdminController.php';
AdminController::requireAuth();
AdminController::requireRole('super_admin', 'reservations_manager');
require_once '../models/Reservation.php';
$model = new Reservation();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $id = (int)($_POST['res_id'] ?? 0);
    if ($_POST['action'] === 'confirm')  $model->updateStatus($id, 'confirmed');
    if ($_POST['action'] === 'cancel')   $model->updateStatus($id, 'cancelled');
    if ($_POST['action'] === 'delete')   $model->delete($id);
    header('Location: manage-reservations.php');
    exit;
}

$filter = $_GET['status'] ?? 'all';
$all    = $filter === 'all' ? $model->getAll() : $model->getByStatus($filter);
$counts = $model->countByStatus();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage Reservations — DineLocal Admin</title>
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
    .sidebar-footer a{display:flex;align-items:center;gap:.6rem;font-size:.78rem;color:rgba(251,240,220,.42);text-decoration:none;}
    .main{margin-left:240px;flex:1;}
    .topbar{background:#fff;border-bottom:1px solid rgba(59,26,8,.08);padding:1rem 1.5rem;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50;}
    .topbar h1{font-family:var(--serif);font-size:1.5rem;font-weight:700;color:var(--brown);}
    .topbar-date{font-size:.78rem;color:rgba(59,26,8,.5);}
    @media(max-width:767px){.topbar{padding:.75rem 1rem;}.topbar h1{font-size:1.2rem;}.topbar-date{display:none;}.admin-badge{font-size:.65rem;padding:.25rem .6rem;}}
    .content{padding:2rem;}
    .card-box{background:#fff;border-radius:1rem;border:1px solid rgba(59,26,8,.06);overflow:hidden;}
    .card-box-head{padding:1.25rem 1.5rem;border-bottom:1px solid rgba(59,26,8,.06);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;}
    .card-box-head h3{font-family:var(--serif);font-size:1.15rem;font-weight:700;color:var(--brown);}
    .filter-pills{display:flex;gap:.5rem;flex-wrap:wrap;}
    .fpill{border:1px solid rgba(59,26,8,.15);background:transparent;color:rgba(59,26,8,.6);padding:.28rem .85rem;border-radius:9999px;font-size:.72rem;font-weight:600;cursor:pointer;transition:all .2s;}
    .fpill:hover,.fpill.active{background:var(--orange);border-color:var(--orange);color:#fff;}
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
    .btn-delete{background:rgba(192,57,43,.06);color:#c0392b;}
    .btn-delete:hover{background:#c0392b;color:#fff;}
    .search-box{border:1px solid rgba(59,26,8,.15);border-radius:.5rem;padding:.5rem .9rem;font-size:.82rem;color:var(--brown);outline:none;background:#f9f6f2;width:220px;}
    .search-box:focus{border-color:var(--orange);box-shadow:0 0 0 2px rgba(196,85,26,.15);}
    @media(max-width:767px){.sidebar{transform:translateX(-100%);transition:transform .3s;}.sidebar.open{transform:translateX(0);}.main{margin-left:0;}.mob-tog{display:flex!important;}}
    .mob-tog{display:none;background:none;border:none;font-size:1.3rem;color:var(--brown);cursor:pointer;}
    .sidebar-header{display:flex;align-items:flex-start;justify-content:space-between;padding:1.5rem 1.5rem 1rem;border-bottom:1px solid rgba(232,168,62,.12);}
    
    .sidebar-close{display:none;background:none;border:none;color:rgba(251,240,220,.4);font-size:1.15rem;cursor:pointer;padding:0;line-height:1;flex-shrink:0;margin-top:.15rem;}
    .sidebar-close:hover{color:var(--cream);}
    .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:99;}
    @media(max-width:767px){.sidebar-close{display:block;}.sidebar-overlay.show{display:block;}}
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
    <a href="index.php" class="nav-item" onclick="closeSidebar()"><i class="bi bi-grid"></i> Dashboard</a>
    <a href="manage-reservations.php" class="nav-item active" onclick="closeSidebar()"><i class="bi bi-calendar2-check"></i> Reservations</a>
    <a href="manage-menu.php" class="nav-item" onclick="closeSidebar()"><i class="bi bi-card-list"></i> Menu Items</a>
    <a href="manage-users.php" class="nav-item" onclick="closeSidebar()"><i class="bi bi-people"></i> Users</a>
    <?php if (AdminController::hasRole('super_admin')): ?>
    <a href="manage-admins.php" class="nav-item" onclick="closeSidebar()"><i class="bi bi-shield-lock"></i> Admins</a>
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
      <button class="mob-tog" onclick="openSidebar()"><i class="bi bi-list"></i></button>
      <h1>Reservations</h1>
    </div>
    <div class="d-flex align-items-center gap-2">
      <span class="topbar-date"><?= array_sum($counts) ?> total</span>
    </div>
  </div>

  <div class="content">
    <div class="card-box">
      <div class="card-box-head">
        <h3>All Reservations</h3>
        <div class="d-flex align-items-center gap-3 flex-wrap">
          <input type="text" class="search-box" id="searchBox" placeholder="Search name or email..."/>
          <div class="filter-pills">
            <a href="?status=all"       class="fpill <?= $filter==='all'?'active':'' ?>">All (<?= array_sum($counts) ?>)</a>
            <a href="?status=pending"   class="fpill <?= $filter==='pending'?'active':'' ?>">Pending (<?= $counts['pending'] ?>)</a>
            <a href="?status=confirmed" class="fpill <?= $filter==='confirmed'?'active':'' ?>">Confirmed (<?= $counts['confirmed'] ?>)</a>
            <a href="?status=cancelled" class="fpill <?= $filter==='cancelled'?'active':'' ?>">Cancelled (<?= $counts['cancelled'] ?>)</a>
          </div>
        </div>
      </div>

      <?php if (empty($all)): ?>
      <div class="text-center py-5" style="color:rgba(59,26,8,.38);font-size:.85rem">
        <i class="bi bi-calendar-x" style="font-size:2rem;display:block;margin-bottom:.5rem"></i>
        No reservations found.
      </div>
      <?php else: ?>
      <div class="table-responsive">
        <table class="table" id="resTable">
          <thead>
            <tr>
              <th>#</th><th>NAME</th><th>EMAIL</th><th>GUESTS</th>
              <th>DATE</th><th>TIME</th><th>STATUS</th><th>SPECIAL</th><th>ACTIONS</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($all as $r): ?>
            <tr class="res-row">
              <td><?= $r['id'] ?></td>
              <td><strong><?= htmlspecialchars($r['full_name']) ?></strong></td>
              <td><?= htmlspecialchars($r['email']) ?></td>
              <td><?= htmlspecialchars($r['guests']) ?></td>
              <td><?= date('M j, Y', strtotime($r['date'])) ?></td>
              <td><?= htmlspecialchars($r['time']) ?></td>
              <td><span class="badge-<?= $r['status'] ?>"><?= ucfirst($r['status']) ?></span></td>
              <td style="max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                <?= $r['special'] ? htmlspecialchars($r['special']) : '<span style="color:rgba(59,26,8,.3)">—</span>' ?>
              </td>
              <td>
                <div class="d-flex gap-1 flex-nowrap">
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
                  <form method="POST" style="display:inline" onsubmit="return confirm('Delete this reservation?')">
                    <input type="hidden" name="res_id" value="<?= $r['id'] ?>"/>
                    <input type="hidden" name="action" value="delete"/>
                    <button class="btn-sm-action btn-delete" type="submit">Delete</button>
                  </form>
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
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Live search
document.getElementById('searchBox')?.addEventListener('input', function() {
  const q = this.value.toLowerCase();
  document.querySelectorAll('.res-row').forEach(row => {
    const text = row.textContent.toLowerCase();
    row.style.display = text.includes(q) ? '' : 'none';
  });
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