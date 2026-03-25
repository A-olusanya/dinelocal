<?php
session_start();
$_isLoggedIn = !empty($_SESSION['user_id']);
$_userName   = $_SESSION['user_name'] ?? '';
$_initials   = $_isLoggedIn ? strtoupper(substr($_userName, 0, 1)) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Menu — DineLocal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet"/>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="assets/css/style.css"/>
  <style>
    :root { --dl-orange:#C4551A; --dl-orange-d:#9E3A0E; --dl-cream:#FBF0DC; --dl-brown:#3B1A08; --dl-dark:#0d0702; --dl-gold:#E8A83E; --dl-serif:'Cormorant Garamond',Georgia,serif; }
    body { background: #0a0502; }
    /* Page hero */
    .page-hero { background: linear-gradient(135deg,#1a0800 0%,#0d0702 100%); padding: 130px 0 60px; border-bottom:1px solid rgba(232,168,62,.1); }
    .page-hero h1 { font-family:var(--dl-serif); font-size:clamp(2.5rem,6vw,5rem); font-weight:700; color:var(--dl-cream); letter-spacing:-.03em; }
    .page-hero p { color:rgba(251,240,220,.58); font-size:.9rem; }
    /* Category filter pills */
    .filter-bar { background:#0d0702; border-bottom:1px solid rgba(232,168,62,.08); padding:1rem 0; position:sticky; top:68px; z-index:100; }
    .filter-pill { background:transparent; border:1px solid rgba(232,168,62,.2); color:rgba(251,240,220,.55); padding:.38rem 1.1rem; border-radius:9999px; font-size:.78rem; font-weight:500; cursor:pointer; transition:all .2s; white-space:nowrap; }
    .filter-pill:hover, .filter-pill.active { background:var(--dl-orange); border-color:var(--dl-orange); color:#fff; }
    /* Category headings */
    .cat-heading { font-family:var(--dl-serif); font-size:clamp(1.8rem,3vw,2.5rem); font-weight:700; color:var(--dl-cream); letter-spacing:-.02em; border-left:3px solid var(--dl-orange); padding-left:1rem; }
    /* Menu item card */
    .menu-item { background:#1a0d05; border:1px solid rgba(232,168,62,.08); border-radius:1rem; overflow:hidden; transition:transform .25s ease, box-shadow .25s; height:100%; display:flex; flex-direction:column; }
    .menu-item:hover { transform:translateY(-6px); box-shadow:0 20px 48px rgba(0,0,0,.5); }
    .menu-item-img { aspect-ratio:16/10; overflow:hidden; position:relative; flex-shrink:0; }
    .menu-item-img img { width:100%; height:100%; object-fit:cover; transition:transform .5s ease; }
    .menu-item:hover .menu-item-img img { transform:scale(1.05); }
    .mi-price { position:absolute; top:.75rem; right:.75rem; background:rgba(13,7,2,.88); color:var(--dl-gold); font-family:var(--dl-serif); font-size:.95rem; font-weight:700; padding:.2rem .7rem; border-radius:9999px; border:1px solid rgba(232,168,62,.2); }
    .mi-badge { position:absolute; bottom:.75rem; left:.75rem; font-size:.58rem; font-weight:600; padding:.18rem .6rem; border-radius:9999px; display:flex; align-items:center; gap:.25rem; }
    .mi-available { background:rgba(28,70,14,.9); color:#8FD14F; }
    .mi-unavailable { background:rgba(60,60,60,.88); color:#aaa; }
    .menu-item-body { padding:1rem; flex:1; display:flex; flex-direction:column; }
    .mi-cat { font-size:.58rem; font-weight:600; letter-spacing:.2em; color:var(--dl-orange); margin-bottom:.35rem; }
    .menu-item-body h3 { font-family:var(--dl-serif); font-size:1.1rem; font-weight:700; color:var(--dl-cream); margin-bottom:.35rem; line-height:1.2; }
    .menu-item-body p { font-size:.76rem; color:rgba(251,240,220,.45); line-height:1.6; flex:1; margin-bottom:.75rem; }
    .mi-btn { display:inline-flex; align-items:center; gap:.35rem; font-size:.74rem; font-weight:600; color:var(--dl-orange); border:1px solid rgba(196,85,26,.3); padding:.32rem .8rem; border-radius:9999px; text-decoration:none; transition:all .2s; width:fit-content; margin-top:auto; }
    .mi-btn:hover { background:var(--dl-orange); color:#fff; border-color:var(--dl-orange); }
    /* Placeholder image */
    .mi-placeholder { width:100%; height:100%; background:linear-gradient(135deg,#2a1100,#1a0800); display:flex; align-items:center; justify-content:center; }
    .mi-placeholder i { font-size:2.5rem; color:rgba(232,168,62,.25); }
  </style>
</head>
<body>

<!-- Nav (same as index) -->
<nav class="navbar navbar-expand-lg fixed-top" id="main-nav">
  <div class="container-xl">
    <a class="navbar-brand nav-logo me-auto" href="index.php">DineLocal</a>
    <button class="navbar-toggler me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNav" aria-label="Menu">
      <span class="tog-bar"></span><span class="tog-bar"></span><span class="tog-bar"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-center" id="desktopNav">
      <ul class="navbar-nav gap-1">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link active" href="menu.php">Menu</a></li>
        <?php if ($_isLoggedIn): ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Reservations</a>
          <ul class="dropdown-menu nav-dropdown">
            <li><a class="dropdown-item" href="dashboard.php?tab=reservations"><i class="bi bi-calendar2-check me-2"></i>View My Reservations</a></li>
            <li><a class="dropdown-item" href="reservations.php"><i class="bi bi-plus-circle me-2"></i>Book a New Table</a></li>
          </ul>
        </li>
        <?php else: ?>
        <li class="nav-item"><a class="nav-link" href="reservations.php">Reservations</a></li>
        <?php endif; ?>
        <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
      </ul>
    </div>
    <div class="d-none d-lg-flex align-items-center gap-2 ms-auto">
      <?php if ($_isLoggedIn): ?>
      <div class="dropdown">
        <button class="nav-user-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
          <span class="nav-avatar"><?= htmlspecialchars($_initials) ?></span>
          <span class="nav-username"><?= htmlspecialchars(explode(' ',$_userName)[0]) ?></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end nav-dropdown">
          <li><a class="dropdown-item" href="dashboard.php"><i class="bi bi-grid me-2"></i>My Dashboard</a></li>
          <li><a class="dropdown-item" href="dashboard.php?tab=reservations"><i class="bi bi-calendar2-check me-2"></i>My Reservations</a></li>
          <li><a class="dropdown-item" href="dashboard.php?tab=profile"><i class="bi bi-person-gear me-2"></i>Edit Profile</a></li>
          <li><hr class="dropdown-divider"/></li>
          <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</a></li>
        </ul>
      </div>
      <?php else: ?>
      <a href="choose-role.php" class="nav-signin">Sign In</a>
      <a href="choose-role.php?action=signup" class="nav-signin" style="background:rgba(196,85,26,.12);border-radius:9999px;padding:.38rem 1rem;">Sign Up</a>
      <?php endif; ?>
      <a href="reservations.php" class="btn-reserve d-flex align-items-center gap-2">
        <i class="bi bi-bag"></i> Reserve
      </a>
    </div>
  </div>
</nav>

<!-- Mobile offcanvas -->
<div class="offcanvas offcanvas-end" id="mobileNav" tabindex="-1">
  <div class="offcanvas-header border-0 pb-0">
    <span class="nav-logo">DineLocal</span>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body d-flex flex-column justify-content-center">
    <nav class="d-flex flex-column gap-2 mb-5">
      <a href="index.php" class="mob-link">Home</a>
      <a href="menu.php" class="mob-link">Menu</a>
      <a href="reservations.php" class="mob-link">Reservations</a>
      <a href="about.php" class="mob-link">About</a>
    </nav>
    <a href="choose-role.php" class="mob-link" style="font-size:1.5rem">Sign In</a>
    <a href="choose-role.php?action=signup" class="mob-link" style="font-size:1.2rem">Create Account</a>
    <a href="reservations.php" class="btn-reserve justify-content-center mt-3">Reserve a Table</a>
  </div>
</div>

<!-- Page Hero -->
<div class="page-hero">
  <div class="container-xl px-3 px-md-5">
    <p class="eyebrow mb-2"><i class="bi bi-card-list"></i> OUR MENU</p>
    <h1>Chef's Selection</h1>
    <p class="mt-2">Locally sourced. Seasonally inspired. Always exceptional.</p>
  </div>
</div>

<!-- Category Filter -->
<div class="filter-bar">
  <div class="container-xl px-3 px-md-5">
    <div class="d-flex gap-2 overflow-auto pb-1" style="scrollbar-width:none">
      <button class="filter-pill active" onclick="filterMenu('all', this)">All Items</button>
      <button class="filter-pill" onclick="filterMenu('Starters', this)">Starters</button>
      <button class="filter-pill" onclick="filterMenu('Mains', this)">Mains</button>
      <button class="filter-pill" onclick="filterMenu('Desserts', this)">Desserts</button>
      <button class="filter-pill" onclick="filterMenu('Drinks', this)">Drinks</button>
    </div>
  </div>
</div>

<!-- Menu Content -->
<main class="py-5" style="background:#0a0502">
  <div class="container-xl px-3 px-md-5">

    <?php
    require_once 'config/db.php';
    require_once 'models/Menu.php';
    $menuModel  = new Menu();
    $categories = $menuModel->getCategories();
    $allItems   = $menuModel->getAll();
    $grouped    = [];
    foreach ($allItems as $item) { $grouped[$item['category']][] = $item; }
    ?>

    <?php foreach ($grouped as $cat => $items): ?>
    <div class="menu-category mb-5" data-category="<?= htmlspecialchars($cat) ?>">
      <div class="d-flex align-items-center gap-3 mb-4">
        <h2 class="cat-heading mb-0"><?= htmlspecialchars($cat) ?></h2>
        <span style="font-size:.72rem;color:rgba(251,240,220,.35);font-weight:500"><?= count($items) ?> items</span>
      </div>
      <div class="row g-3 g-md-4">
        <?php foreach ($items as $item): ?>
        <div class="col-12 col-sm-6 col-lg-4 menu-item-col">
          <article class="menu-item">
            <div class="menu-item-img">
              <?php if (!empty($item['image_url'])): ?>
                <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" loading="lazy"/>
              <?php else: ?>
                <div class="mi-placeholder"><i class="bi bi-image"></i></div>
              <?php endif; ?>
              <span class="mi-price">$<?= number_format($item['price'], 2) ?></span>
              <span class="mi-badge <?= $item['is_available'] ? 'mi-available' : 'mi-unavailable' ?>">
                <i class="bi bi-circle-fill"></i>
                <?= $item['is_available'] ? 'Available' : 'Unavailable' ?>
              </span>
            </div>
            <div class="menu-item-body">
              <p class="mi-cat"><?= htmlspecialchars($cat) ?></p>
              <h3><?= htmlspecialchars($item['name']) ?></h3>
              <p><?= htmlspecialchars($item['description']) ?></p>
              <?php if ($item['is_available']): ?>
              <a href="reservations.php" class="mi-btn">
                <i class="bi bi-plus-circle"></i> Order Tonight
              </a>
              <?php endif; ?>
            </div>
          </article>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endforeach; ?>

    <!-- CTA Banner -->
    <div class="text-center py-5 mt-4" style="background:rgba(232,168,62,.05);border-radius:1.5rem;border:1px solid rgba(232,168,62,.1)">
      <p style="font-size:.65rem;font-weight:600;letter-spacing:.28em;color:var(--dl-orange)" class="mb-2"><i class="bi bi-calendar2-check"></i> BOOK YOUR TABLE</p>
      <h3 style="font-family:var(--dl-serif);font-size:clamp(1.8rem,3vw,2.5rem);color:var(--dl-cream);font-weight:700" class="mb-3">Ready to Dine?</h3>
      <p style="color:rgba(251,240,220,.55);font-size:.88rem;max-width:400px;margin:0 auto 1.5rem">Reserve your table and enjoy these dishes fresh from our kitchen.</p>
      <a href="reservations.php" class="btn-fill"><i class="bi bi-calendar2-check"></i> Reserve a Table</a>
    </div>

  </div>
</main>

<!-- Footer -->
<footer class="py-5" style="background:#0a0502;border-top:1px solid rgba(232,168,62,.08)">
  <div class="container-xl px-3 px-md-5">
    <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3">
      <span class="ft-logo">DineLocal</span>
      <p class="ft-copy mb-0">© 2024 DineLocal Toronto. All rights reserved.</p>
      <div class="d-flex gap-3">
        <a href="index.php" class="ft-link">Home</a>
        <a href="reservations.php" class="ft-link">Reservations</a>
        <a href="about.php" class="ft-link">About</a>
      </div>
    </div>
  </div>
</footer>

<div id="cursor"></div>
<div id="progress-bar"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>
<script>
// JS form validation (rubric) - inline
function filterMenu(cat, btn) {
  // Filter categories
  document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
  btn.classList.add('active');
  document.querySelectorAll('.menu-category').forEach(section => {
    if (cat === 'all' || section.dataset.category === cat) {
      section.style.display = '';
    } else {
      section.style.display = 'none';
    }
  });
}
// Nav hide on scroll
let lastY = 0;
window.addEventListener('scroll', () => {
  const nav = document.getElementById('main-nav');
  const sy = window.scrollY;
  nav.style.transition = 'transform .35s ease';
  if (sy > 80 && sy > lastY) nav.style.transform = 'translateY(-110%)';
  else nav.style.transform = 'translateY(0)';
  lastY = sy;
}, {passive:true});
</script>
</body>
</html>