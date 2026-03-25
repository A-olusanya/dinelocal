<?php
session_start();
require_once '../config/db.php';
require_once '../controllers/AdminController.php';
AdminController::requireAuth();
AdminController::requireRole('super_admin', 'menu_manager');
require_once '../models/Menu.php';
$model   = new Menu();
$message = '';
$editing = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id     = (int)($_POST['id'] ?? 0);
    if ($action === 'add' || $action === 'edit_save') {
        // Handle image: uploaded file takes priority over URL
        $imageUrl = htmlspecialchars(trim($_POST['image_url'] ?? ''));
        if (!empty($_FILES['image_file']['name']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['image/jpeg','image/png','image/webp','image/gif'];
            if (in_array($_FILES['image_file']['type'], $allowed)) {
                $ext      = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
                $filename = 'menu_' . uniqid() . '.' . strtolower($ext);
                $dest     = __DIR__ . '/../assets/images/menu/' . $filename;
                if (move_uploaded_file($_FILES['image_file']['tmp_name'], $dest)) {
                    $imageUrl = 'assets/images/menu/' . $filename;
                }
            }
        }
        $data = [
            'name'         => htmlspecialchars(trim($_POST['name'] ?? '')),
            'description'  => htmlspecialchars(trim($_POST['description'] ?? '')),
            'price'        => (float)($_POST['price'] ?? 0),
            'category'     => htmlspecialchars(trim($_POST['category'] ?? '')),
            'image_url'    => $imageUrl,
            'is_available' => isset($_POST['is_available']) ? 1 : 0,
            'is_featured'  => isset($_POST['is_featured'])  ? 1 : 0,
        ];
        if ($action === 'add')       { $model->create($data); $message = 'Menu item added successfully!'; }
        if ($action === 'edit_save') { $model->update($id, $data); $message = 'Menu item updated successfully!'; }
        header('Location: manage-menu.php?msg=' . urlencode($message));
        exit;
    }
    if ($action === 'toggle') { $model->toggleAvailability($id); header('Location: manage-menu.php'); exit; }
    if ($action === 'delete') { $model->delete($id); header('Location: manage-menu.php'); exit; }
}

if (isset($_GET['edit'])) { $editing = $model->getById((int)$_GET['edit']); }
$message    = $_GET['msg'] ?? '';
$items      = $model->getAll();
$categories = ['Starters', 'Mains', 'Desserts', 'Drinks', 'Specials'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage Menu — DineLocal Admin</title>
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
    .nav-item i{font-size:1rem;width:18px;}
    .sidebar-footer{padding:1rem 1.5rem;border-top:1px solid rgba(232,168,62,.1);}
    .sidebar-footer a{display:flex;align-items:center;gap:.6rem;font-size:.78rem;color:rgba(251,240,220,.42);text-decoration:none;}
    .main{margin-left:240px;flex:1;}
    .topbar{background:#fff;border-bottom:1px solid rgba(59,26,8,.08);padding:1rem 1.5rem;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50;}
    .topbar h1{font-family:var(--serif);font-size:1.5rem;font-weight:700;color:var(--brown);}
    .topbar-date{font-size:.78rem;color:rgba(59,26,8,.5);}
    @media(max-width:767px){.topbar{padding:.75rem 1rem;}.topbar h1{font-size:1.2rem;}.topbar-date{display:none;}.admin-badge{font-size:.65rem;padding:.25rem .6rem;}}
    .content{padding:2rem;}
    /* Form card */
    .form-card{background:#fff;border-radius:1rem;border:1px solid rgba(59,26,8,.06);padding:1.75rem;margin-bottom:1.5rem;}
    .form-card h3{font-family:var(--serif);font-size:1.15rem;font-weight:700;color:var(--brown);margin-bottom:1.25rem;padding-bottom:.75rem;border-bottom:1px solid rgba(59,26,8,.07);}
    .form-label{font-size:.72rem;font-weight:600;color:rgba(59,26,8,.6);letter-spacing:.04em;}
    .form-control,.form-select{border:1px solid rgba(59,26,8,.12);border-radius:.45rem;font-size:.84rem;color:var(--brown);padding:.6rem .9rem;}
    .form-control:focus,.form-select:focus{border-color:var(--orange);box-shadow:0 0 0 2px rgba(196,85,26,.15);}
    .btn-add{background:linear-gradient(135deg,var(--orange),#9E3A0E);color:#fff;border:none;border-radius:.5rem;padding:.65rem 1.5rem;font-size:.82rem;font-weight:600;cursor:pointer;transition:all .2s;}
    .btn-add:hover{transform:translateY(-1px);box-shadow:0 4px 14px rgba(196,85,26,.4);}
    /* Table */
    .card-box{background:#fff;border-radius:1rem;border:1px solid rgba(59,26,8,.06);overflow:hidden;}
    .card-box-head{padding:1.25rem 1.5rem;border-bottom:1px solid rgba(59,26,8,.06);display:flex;align-items:center;justify-content:space-between;}
    .card-box-head h3{font-family:var(--serif);font-size:1.15rem;font-weight:700;color:var(--brown);}
    .table{margin:0;}
    .table th{font-size:.68rem;font-weight:600;letter-spacing:.12em;color:rgba(59,26,8,.5);border-bottom-width:1px;padding:.75rem 1.25rem;}
    .table td{font-size:.82rem;color:var(--brown);padding:.85rem 1.25rem;vertical-align:middle;}
    .avail-yes{color:#27ae60;font-weight:600;font-size:.75rem;}
    .avail-no{color:#c0392b;font-weight:600;font-size:.75rem;}
    .btn-sm-action{font-size:.7rem;font-weight:600;padding:.22rem .65rem;border-radius:6px;border:none;cursor:pointer;transition:all .2s;}
    .btn-edit{background:rgba(232,168,62,.15);color:#b8860b;}
    .btn-edit:hover{background:var(--gold);color:#fff;}
    .btn-toggle{background:rgba(59,26,8,.08);color:rgba(59,26,8,.6);}
    .btn-toggle:hover{background:var(--orange);color:#fff;}
    .btn-del{background:rgba(192,57,43,.1);color:#c0392b;}
    .btn-del:hover{background:#c0392b;color:#fff;}
    .alert-ok{background:rgba(28,120,14,.1);color:#27ae60;border:1px solid rgba(28,120,14,.2);border-radius:.5rem;padding:.75rem 1rem;font-size:.82rem;margin-bottom:1rem;}
    @media(max-width:767px){.sidebar{transform:translateX(-100%);transition:transform .3s;}.sidebar.open{transform:translateX(0);}.main{margin-left:0;}.mob-tog{display:flex!important;}}
    .mob-tog{display:none;background:none;border:none;font-size:1.3rem;color:var(--brown);cursor:pointer;}
    .img-tab{background:transparent;border:1px solid rgba(59,26,8,.15);border-radius:.4rem;padding:.3rem .85rem;font-size:.76rem;font-weight:600;color:rgba(59,26,8,.55);cursor:pointer;transition:all .2s;display:flex;align-items:center;gap:.35rem;}
    .img-tab.active{background:var(--orange);border-color:var(--orange);color:#fff;}
    .img-tab:hover:not(.active){background:rgba(59,26,8,.06);}
  </style>
</head>
<body>

<aside class="sidebar" id="sidebar">
  <div class="sidebar-logo"><h2>DineLocal</h2><p>ADMIN PANEL</p></div>
  <nav class="sidebar-nav">
    <a href="index.php" class="nav-item"><i class="bi bi-grid"></i> Dashboard</a>
    <a href="manage-reservations.php" class="nav-item"><i class="bi bi-calendar2-check"></i> Reservations</a>
    <a href="manage-menu.php" class="nav-item active"><i class="bi bi-card-list"></i> Menu Items</a>
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

<div class="main">
  <div class="topbar">
    <div class="d-flex align-items-center gap-3">
      <button class="mob-tog" onclick="document.getElementById('sidebar').classList.toggle('open')"><i class="bi bi-list"></i></button>
      <h1><?= $editing ? 'Edit Menu Item' : 'Manage Menu' ?></h1>
    </div>
    <span class="topbar-date"><?= count($items) ?> items</span>
  </div>

  <div class="content">
    <?php if ($message): ?>
    <div class="alert-ok"><i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Add / Edit Form -->
    <div class="form-card">
      <h3><?= $editing ? 'Edit: ' . htmlspecialchars($editing['name']) : 'Add New Menu Item' ?></h3>
      <form method="POST" action="manage-menu.php" enctype="multipart/form-data">
        <input type="hidden" name="action" value="<?= $editing ? 'edit_save' : 'add' ?>"/>
        <?php if ($editing): ?><input type="hidden" name="id" value="<?= $editing['id'] ?>"/><?php endif; ?>
        <div class="row g-3">
          <div class="col-12 col-md-6">
            <label class="form-label">Item Name *</label>
            <input type="text" name="name" class="form-control" required placeholder="e.g. Beef Striploin" value="<?= htmlspecialchars($editing['name'] ?? '') ?>"/>
          </div>
          <div class="col-6 col-md-3">
            <label class="form-label">Category *</label>
            <select name="category" class="form-select" required>
              <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat ?>" <?= ($editing['category'] ?? '') === $cat ? 'selected' : '' ?>><?= $cat ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-6 col-md-3">
            <label class="form-label">Price ($) *</label>
            <input type="number" name="price" class="form-control" step="0.01" min="0" required placeholder="24.00" value="<?= htmlspecialchars($editing['price'] ?? '') ?>"/>
          </div>
          <div class="col-12">
            <label class="form-label">Description *</label>
            <textarea name="description" class="form-control" rows="2" required placeholder="Describe the dish..."><?= htmlspecialchars($editing['description'] ?? '') ?></textarea>
          </div>
          <div class="col-12">
            <label class="form-label">Image</label>
            <div style="border:1px solid rgba(59,26,8,.12);border-radius:.45rem;padding:1rem;background:#faf8f5;">
              <!-- Upload tab toggle -->
              <div style="display:flex;gap:.5rem;margin-bottom:.85rem;">
                <button type="button" class="img-tab active" id="tab-upload" onclick="switchTab('upload')">
                  <i class="bi bi-upload"></i> Upload from device
                </button>
                <button type="button" class="img-tab" id="tab-url" onclick="switchTab('url')">
                  <i class="bi bi-link-45deg"></i> Paste URL
                </button>
              </div>
              <!-- Upload panel -->
              <div id="panel-upload">
                <input type="file" name="image_file" id="image_file" accept="image/*" class="form-control"
                  style="font-size:.82rem;" onchange="previewImg(this)"/>
                <div id="img-preview-wrap" style="margin-top:.75rem;display:none;">
                  <img id="img-preview" src="" alt="Preview"
                    style="max-height:140px;border-radius:.5rem;border:1px solid rgba(59,26,8,.1);object-fit:cover;"/>
                  <button type="button" onclick="clearFile()" style="display:block;margin-top:.35rem;font-size:.72rem;color:#c0392b;background:none;border:none;cursor:pointer;">
                    <i class="bi bi-x-circle"></i> Remove
                  </button>
                </div>
                <?php if (!empty($editing['image_url']) && !str_starts_with($editing['image_url'],'http')): ?>
                <p style="font-size:.72rem;color:rgba(59,26,8,.5);margin-top:.5rem">
                  Current: <strong><?= htmlspecialchars($editing['image_url']) ?></strong>
                  — upload a new file to replace it, or leave empty to keep.
                </p>
                <?php endif; ?>
              </div>
              <!-- URL panel -->
              <div id="panel-url" style="display:none;">
                <input type="url" name="image_url" id="image_url" class="form-control" placeholder="https://images.unsplash.com/..."
                  value="<?= htmlspecialchars($editing['image_url'] ?? '') ?>"
                  style="font-size:.84rem;"/>
                <p style="font-size:.7rem;color:rgba(59,26,8,.4);margin-top:.4rem">
                  Paste any public image URL (Unsplash, etc.)
                </p>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-4 d-flex align-items-end gap-3 pb-1">
            <label class="d-flex align-items-center gap-2" style="font-size:.82rem;font-weight:500;cursor:pointer">
              <input type="checkbox" name="is_available" <?= ($editing['is_available'] ?? 1) ? 'checked' : '' ?>/> Available
            </label>
            <label class="d-flex align-items-center gap-2" style="font-size:.82rem;font-weight:500;cursor:pointer">
              <input type="checkbox" name="is_featured" <?= ($editing['is_featured'] ?? 0) ? 'checked' : '' ?>/> Featured
            </label>
          </div>
          <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn-add"><i class="bi <?= $editing ? 'bi-save' : 'bi-plus-circle' ?> me-1"></i><?= $editing ? 'Save Changes' : 'Add Item' ?></button>
            <?php if ($editing): ?><a href="manage-menu.php" class="btn-add" style="background:#6c757d;text-decoration:none">Cancel</a><?php endif; ?>
          </div>
        </div>
      </form>
    </div>

    <!-- Items Table -->
    <div class="card-box">
      <div class="card-box-head">
        <h3>All Menu Items</h3>
        <input type="text" id="menuSearch" placeholder="Search..." style="border:1px solid rgba(59,26,8,.12);border-radius:.45rem;padding:.4rem .8rem;font-size:.8rem;outline:none;color:var(--brown);width:180px"/>
      </div>
      <div class="table-responsive">
        <table class="table" id="menuTable">
          <thead><tr><th>#</th><th>NAME</th><th>CATEGORY</th><th>PRICE</th><th>AVAILABLE</th><th>FEATURED</th><th>ACTIONS</th></tr></thead>
          <tbody>
            <?php foreach ($items as $item): ?>
            <tr class="menu-row">
              <td><?= $item['id'] ?></td>
              <td>
                <strong><?= htmlspecialchars($item['name']) ?></strong><br>
                <small style="color:rgba(59,26,8,.45);font-size:.72rem"><?= mb_strimwidth(htmlspecialchars($item['description']), 0, 55, '...') ?></small>
              </td>
              <td><?= htmlspecialchars($item['category']) ?></td>
              <td>$<?= number_format($item['price'], 2) ?></td>
              <td class="<?= $item['is_available'] ? 'avail-yes' : 'avail-no' ?>"><?= $item['is_available'] ? '✓ Yes' : '✗ No' ?></td>
              <td><?= $item['is_featured'] ? '<span class="avail-yes">★ Yes</span>' : '<span style="color:rgba(59,26,8,.35)">No</span>' ?></td>
              <td>
                <div class="d-flex gap-1 flex-nowrap">
                  <a href="?edit=<?= $item['id'] ?>" class="btn-sm-action btn-edit">Edit</a>
                  <form method="POST" style="display:inline">
                    <input type="hidden" name="id" value="<?= $item['id'] ?>"/>
                    <input type="hidden" name="action" value="toggle"/>
                    <button class="btn-sm-action btn-toggle" type="submit"><?= $item['is_available'] ? 'Disable' : 'Enable' ?></button>
                  </form>
                  <form method="POST" style="display:inline" onsubmit="return confirm('Delete this item?')">
                    <input type="hidden" name="id" value="<?= $item['id'] ?>"/>
                    <input type="hidden" name="action" value="delete"/>
                    <button class="btn-sm-action btn-del" type="submit">Delete</button>
                  </form>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('menuSearch')?.addEventListener('input', function() {
  const q = this.value.toLowerCase();
  document.querySelectorAll('.menu-row').forEach(row => {
    row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
  });
});
</script>
<script>
function switchTab(tab) {
  document.getElementById('panel-upload').style.display = tab === 'upload' ? '' : 'none';
  document.getElementById('panel-url').style.display    = tab === 'url'    ? '' : 'none';
  document.getElementById('tab-upload').classList.toggle('active', tab === 'upload');
  document.getElementById('tab-url').classList.toggle('active',    tab === 'url');
  // Clear the inactive input so it doesn't interfere
  if (tab === 'upload') document.getElementById('image_url').value = '';
  else clearFile();
}
function previewImg(input) {
  const wrap = document.getElementById('img-preview-wrap');
  const prev = document.getElementById('img-preview');
  if (input.files && input.files[0]) {
    prev.src = URL.createObjectURL(input.files[0]);
    wrap.style.display = '';
  }
}
function clearFile() {
  document.getElementById('image_file').value = '';
  document.getElementById('img-preview-wrap').style.display = 'none';
  document.getElementById('img-preview').src = '';
}
// On edit: if current image is a URL, default to URL tab
<?php if (!empty($editing['image_url']) && str_starts_with($editing['image_url'], 'http')): ?>
switchTab('url');
<?php endif; ?>
</script>
</body>
</html>