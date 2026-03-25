<?php
session_start();
// Mark welcomed when returning from the welcome animation
if (isset($_GET['enter'])) {
    $_SESSION['welcomed'] = true;
}
// Redirect new visitors to the welcome page (once per session)
if (empty($_SESSION['welcomed'])) {
    header('Location: /welcome.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>DineLocal — Taste the Neighbourhood</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet"/>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
  <link rel="stylesheet" href="assets/css/style.css"/>
  <style>
    :root{--dl-orange:#C4551A;--dl-orange-d:#9E3A0E;--dl-cream:#FBF0DC;--dl-brown:#3B1A08;--dl-dark:#0d0702;--dl-gold:#E8A83E;--dl-serif:'Cormorant Garamond',Georgia,serif;}
    #hero{min-height:100svh;}
    #showcase{background:#0d0702;overflow:hidden;}
    #kinetic{background:var(--dl-orange);overflow:hidden;}
    #menu-sec{background:#0a0502;}
    #res-sec{background:#faf5ee;}
    footer{background:#0a0502;}
  </style>
</head>
<body>

<!-- ═══════════ NAV ═══════════ -->
<nav class="navbar navbar-expand-lg fixed-top" id="main-nav">
  <div class="container-xl">
    <a class="navbar-brand nav-logo me-auto" href="index.php">DineLocal</a>

    <button class="navbar-toggler me-2" type="button"
      data-bs-toggle="offcanvas" data-bs-target="#mobileNav" aria-label="Menu">
      <span class="tog-bar"></span><span class="tog-bar"></span><span class="tog-bar"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-center" id="desktopNav">
      <ul class="navbar-nav gap-1">
        <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="menu.php">Menu</a></li>
        <?php if (!empty($_SESSION['user_id'])): ?>
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
      <?php if (!empty($_SESSION['user_id'])): ?>
      <div class="dropdown">
        <button class="nav-user-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
          <span class="nav-avatar"><?= strtoupper(substr($_SESSION['user_name'],0,1)) ?></span>
          <span class="nav-username"><?= htmlspecialchars(explode(' ',$_SESSION['user_name'])[0]) ?></span>
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
    <nav class="d-flex flex-column gap-2 mb-4">
      <a href="index.php" class="mob-link">Home</a>
      <a href="menu.php" class="mob-link">Menu</a>
      <a href="reservations.php" class="mob-link">Reservations</a>
      <a href="about.php" class="mob-link">About</a>
    </nav>
    <?php if (!empty($_SESSION['user_id'])): ?>
    <div style="font-size:.7rem;font-weight:600;letter-spacing:.12em;color:rgba(251,240,220,.35);margin-bottom:.75rem">MY ACCOUNT</div>
    <a href="dashboard.php" class="mob-link" style="font-size:1.4rem">Dashboard</a>
    <a href="dashboard.php?tab=reservations" class="mob-link" style="font-size:1.2rem">My Reservations</a>
    <a href="logout.php" class="mob-link" style="font-size:1.1rem;color:rgba(192,57,43,.7)">Sign Out</a>
    <?php else: ?>
    <a href="choose-role.php" class="mob-link" style="font-size:1.5rem">Sign In</a>
    <a href="choose-role.php?action=signup" class="mob-link" style="font-size:1.2rem">Create Account</a>
    <?php endif; ?>
    <a href="reservations.php" class="btn-reserve mt-4 justify-content-center">
      <i class="bi bi-bag"></i> Reserve a Table
    </a>
  </div>
</div>

<!-- ═══════════ HERO ═══════════ -->
<section id="hero" class="position-relative d-flex flex-column overflow-hidden">
  <div class="position-absolute inset-0">
    <img id="hero-img"
      src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=1800&h=1200&fit=crop&crop=center"
      alt="DineLocal restaurant" class="w-100 h-100 object-fit-cover"/>
    <div class="hero-overlay position-absolute inset-0"></div>
  </div>

  <div class="flex-grow-1 d-flex align-items-center position-relative z-2">
    <div class="container-xl">
      <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8 text-center" style="padding-top:80px">
          <div class="hero-tag d-inline-flex align-items-center gap-2 mb-4">
            <span class="tag-dot"></span>
            <span>TORONTO · CANADA · EST. 2024</span>
          </div>
          <h1 class="hero-h1 mb-3">
            <span class="d-block">Taste the</span>
            <span class="d-block hero-italic">Neighbourhood</span>
          </h1>
          <p class="hero-sub mb-4">Farm-to-table dining in the heart of Toronto.<br class="d-none d-md-inline"/>Where every plate tells a story.</p>
          <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
            <a href="menu.php" class="btn-fill"><i class="bi bi-card-list"></i> Explore Menu</a>
            <a href="reservations.php" class="btn-line"><i class="bi bi-calendar2-check"></i> Book a Table</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="hero-bar position-relative z-2 d-none d-md-block">
    <div class="container-xl">
      <div class="d-flex align-items-center justify-content-center gap-4 py-3">
        <span class="hbar-item"><i class="bi bi-geo-alt"></i> 123 Queen St W, Toronto</span>
        <span class="hbar-div"></span>
        <span class="hbar-item"><i class="bi bi-clock"></i> Open Daily · 11am – 10pm</span>
        <span class="hbar-div"></span>
        <span class="hbar-item"><i class="bi bi-star-fill"></i> 4.9 · 1,200+ Reviews</span>
      </div>
    </div>
  </div>

  <div class="position-absolute bottom-0 start-50 translate-middle-x pb-4 z-2 text-center scroll-cue d-none d-md-flex flex-column align-items-center gap-2">
    <div class="scue-line"></div>
    <span>SCROLL</span>
  </div>
</section>

<!-- ═══════════ SHOWCASE ═══════════ -->
<section id="showcase">

  <div class="showcase-panel" id="sp1">
    <div class="sp-photo">
      <img src="https://images.unsplash.com/photo-1558030006-450675393462?w=900&h=1100&fit=crop&crop=center" alt="Striploin"/>
      <div class="sp-photo-overlay"></div>
    </div>
    <div class="sp-content">
      <div class="sp-inner">
        <span class="sp-num">01</span>
        <p class="sp-cat"><i class="bi bi-fork-knife"></i> SIGNATURE MAINS</p>
        <h2 class="sp-title">Ontario Beef<br><em>Striploin</em></h2>
        <p class="sp-desc">Dry-aged 28 days. Served with roasted marrow, heirloom carrots and a red wine reduction.</p>
        <div class="sp-meta">
          <span class="sp-price">$42</span>
          <span class="sp-avail"><i class="bi bi-circle-fill"></i> Available tonight</span>
        </div>
        <a href="menu.php" class="sp-btn">Order This <i class="bi bi-arrow-right"></i></a>
      </div>
    </div>
  </div>

  <div class="showcase-panel sp-reverse" id="sp2">
    <div class="sp-photo">
      <img src="https://images.unsplash.com/photo-1563379926898-05f4575a45d8?w=900&h=1100&fit=crop&crop=center" alt="Tagliatelle"/>
      <div class="sp-photo-overlay"></div>
    </div>
    <div class="sp-content">
      <div class="sp-inner">
        <span class="sp-num">02</span>
        <p class="sp-cat"><i class="bi bi-fork-knife"></i> HANDMADE PASTA</p>
        <h2 class="sp-title">Wild Mushroom<br><em>Tagliatelle</em></h2>
        <p class="sp-desc">Hand-cut pasta with foraged Ontario mushrooms, truffle cream and aged pecorino.</p>
        <div class="sp-meta">
          <span class="sp-price">$28</span>
          <span class="sp-avail"><i class="bi bi-circle-fill"></i> Available tonight</span>
        </div>
        <a href="menu.php" class="sp-btn">Order This <i class="bi bi-arrow-right"></i></a>
      </div>
    </div>
  </div>

  <div class="showcase-panel" id="sp3">
    <div class="sp-photo">
      <img src="https://images.unsplash.com/photo-1551024601-bec78aea704b?w=900&h=1100&fit=crop&crop=center" alt="Ganache"/>
      <div class="sp-photo-overlay"></div>
    </div>
    <div class="sp-content">
      <div class="sp-inner">
        <span class="sp-num">03</span>
        <p class="sp-cat"><i class="bi bi-cup-straw"></i> DESSERTS</p>
        <h2 class="sp-title">Dark Cocoa<br><em>Ganache</em></h2>
        <p class="sp-desc">70% Peruvian cocoa, salted caramel, maple honey gelato and a delicate vanilla tuile.</p>
        <div class="sp-meta">
          <span class="sp-price">$14</span>
          <span class="sp-avail"><i class="bi bi-circle-fill"></i> Available tonight</span>
        </div>
        <a href="menu.php" class="sp-btn">Order This <i class="bi bi-arrow-right"></i></a>
      </div>
    </div>
  </div>

</section>

<!-- ═══════════ STATS ═══════════ -->
<div class="stats-strip">
  <div class="container-xl">
    <div class="row g-0">
      <div class="col-6 col-md-3"><div class="stat-item"><strong>12+</strong><span>Years of Excellence</span></div></div>
      <div class="col-6 col-md-3"><div class="stat-item"><strong>100%</strong><span>Local Ingredients</span></div></div>
      <div class="col-6 col-md-3"><div class="stat-item"><strong>4.9★</strong><span>Average Rating</span></div></div>
      <div class="col-6 col-md-3"><div class="stat-item"><strong>50+</strong><span>Menu Items</span></div></div>
    </div>
  </div>
</div>

<!-- ═══════════ KINETIC ═══════════ -->
<section id="kinetic" class="py-5 py-md-6 text-center">
  <div class="container-fluid px-3 px-md-5">
    <div class="overflow-hidden"><span class="kw kl" id="k1">RESERVE</span></div>
    <div class="overflow-hidden"><span class="kw kr" id="k2">YOUR TABLE</span></div>
    <div class="overflow-hidden"><span class="kw kl" id="k3">ANYTIME.</span></div>
    <div class="k-meta d-flex flex-wrap align-items-center justify-content-center gap-3 mt-4">
      <span><i class="bi bi-clock"></i> OPEN 7 DAYS</span>
      <span class="kd d-none d-md-inline-block"></span>
      <span><i class="bi bi-globe2"></i> BOOK ONLINE 24/7</span>
      <span class="kd d-none d-md-inline-block"></span>
      <span><i class="bi bi-telephone"></i> (416) 555-0192</span>
    </div>
  </div>
</section>

<!-- ═══════════ MENU CARDS ═══════════ -->
<section id="menu-sec" class="py-5 py-md-6">
  <div class="container-xxl px-3 px-md-5">
    <div class="d-flex align-items-end justify-content-between flex-wrap gap-2 mb-4 mb-md-5">
      <div>
        <p class="eyebrow"><i class="bi bi-scissors"></i> THE MENU</p>
        <h2 class="sec-title">Chef's Selection</h2>
      </div>
      <a href="menu.php" class="see-all">Explore All <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="row g-4">
      <div class="col-12 col-md-6 col-lg-4">
        <article class="mcard h-100" id="mc1">
          <div class="mcard-img">
            <img src="https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=700&h=500&fit=crop" alt="Flatbread" loading="lazy"/>
            <span class="cprice">$24</span>
            <span class="ctag c-ok"><i class="bi bi-check-circle-fill"></i> Available</span>
          </div>
          <div class="mcard-body">
            <p class="ccat"><i class="bi bi-fork-knife"></i> Starters</p>
            <h3>Wood-Fired Flatbread</h3>
            <p>Ontario prosciutto, fig jam, arugula, shaved parmesan, aged balsamic.</p>
            <a href="menu.php" class="cbtn">Add to Order <i class="bi bi-plus-circle"></i></a>
          </div>
        </article>
      </div>
      <div class="col-12 col-md-6 col-lg-4">
        <article class="mcard h-100" id="mc2">
          <div class="mcard-img">
            <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=700&h=500&fit=crop" alt="Salmon" loading="lazy"/>
            <span class="cprice">$38</span>
            <span class="ctag c-ok"><i class="bi bi-check-circle-fill"></i> Available</span>
          </div>
          <div class="mcard-body">
            <p class="ccat"><i class="bi bi-fork-knife"></i> Mains</p>
            <h3>Pan-Seared Salmon</h3>
            <p>Atlantic salmon, saffron risotto, crispy capers, lemon beurre blanc.</p>
            <a href="menu.php" class="cbtn">Add to Order <i class="bi bi-plus-circle"></i></a>
          </div>
        </article>
      </div>
      <div class="col-12 col-md-6 col-lg-4">
        <article class="mcard h-100" id="mc3">
          <div class="mcard-img">
            <img src="https://images.unsplash.com/photo-1473093226795-af9932fe5856?w=700&h=500&fit=crop" alt="Dessert" loading="lazy"/>
            <span class="cprice">$12</span>
            <span class="ctag c-ok"><i class="bi bi-check-circle-fill"></i> Available</span>
          </div>
          <div class="mcard-body">
            <p class="ccat"><i class="bi bi-cup-straw"></i> Desserts</p>
            <h3>Crème Brûlée</h3>
            <p>Classic French vanilla custard, torched sugar, Ontario berry compote.</p>
            <a href="menu.php" class="cbtn">Add to Order <i class="bi bi-plus-circle"></i></a>
          </div>
        </article>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════ RESERVATION ═══════════ -->
<section id="res-sec">
  <div class="row g-0" style="min-height:100vh">
    <div class="col-12 col-lg-6 res-photo-col">
      <div class="position-relative h-100" style="min-height:350px">
        <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=900&h=1100&fit=crop"
             alt="Restaurant" class="w-100 h-100 object-fit-cover" loading="lazy"/>
        <div class="res-photo-ov position-absolute bottom-0 start-0 end-0 p-4 p-md-5">
          <i class="bi bi-quote res-qi d-block mb-2"></i>
          <blockquote class="res-quote">"Food is our common ground, a universal experience."</blockquote>
          <cite class="res-cite">— James Beard</cite>
        </div>
      </div>
    </div>
    <div class="col-12 col-lg-6 d-flex align-items-center" style="background:#faf5ee">
      <div class="w-100 px-4 px-md-5 py-5">
        <div style="max-width:500px;margin:0 auto">
          <p class="eyebrow"><i class="bi bi-shield-check"></i> SECURE YOUR EXPERIENCE</p>
          <h2 class="res-h2">Book a Table</h2>
          <p class="res-desc mb-4">Join us for an evening of locally sourced flavors.</p>
          <div class="d-flex gap-3 mt-3">
            <a href="reservations.php" class="btn-fill w-100 justify-content-center">
              <i class="bi bi-calendar2-check"></i> Make a Reservation
            </a>
          </div>
          <?php if (!empty($_SESSION['user_id'])): ?>
          <div class="text-center mt-3">
            <a href="dashboard.php?tab=reservations" style="color:var(--dl-orange);font-size:.84rem;font-weight:600;text-decoration:none">
              <i class="bi bi-clock-history me-1"></i> View My Previous Reservations
            </a>
          </div>
          <?php else: ?>
          <div class="text-center mt-3" style="font-size:.82rem;color:rgba(59,26,8,.55)">
            <a href="choose-role.php" style="color:var(--dl-orange);font-weight:600;text-decoration:none">Sign in</a>
            to track and manage your reservations.
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════ FOOTER ═══════════ -->
<footer class="py-5">
  <div class="container-xxl px-3 px-md-5">
    <div class="row g-4 pb-4 border-bottom" style="border-color:rgba(232,168,62,.12)!important">
      <div class="col-12 col-md-5 col-lg-4">
        <h3 class="ft-logo mb-3">DineLocal</h3>
        <p class="ft-text">Farm-to-table dining in the heart of Toronto. Every ingredient tells a story.</p>
        <div class="d-flex gap-2 mt-3">
          <a href="#" class="ft-soc"><i class="bi bi-instagram"></i></a>
          <a href="#" class="ft-soc"><i class="bi bi-facebook"></i></a>
          <a href="#" class="ft-soc"><i class="bi bi-twitter-x"></i></a>
        </div>
      </div>
      <div class="col-6 col-md-2">
        <h4 class="ft-head">NAVIGATE</h4>
        <div class="d-flex flex-column gap-2">
          <a href="index.php" class="ft-link"><i class="bi bi-house"></i> Home</a>
          <a href="menu.php" class="ft-link"><i class="bi bi-card-list"></i> Menu</a>
          <a href="reservations.php" class="ft-link"><i class="bi bi-calendar2-check"></i> Reservations</a>
          <a href="about.php" class="ft-link"><i class="bi bi-info-circle"></i> About</a>
        </div>
      </div>
      <div class="col-6 col-md-2">
        <h4 class="ft-head">ACCOUNT</h4>
        <div class="d-flex flex-column gap-2">
          <?php if (!empty($_SESSION['user_id'])): ?>
          <a href="dashboard.php" class="ft-link"><i class="bi bi-grid"></i> My Dashboard</a>
          <a href="dashboard.php?tab=reservations" class="ft-link"><i class="bi bi-calendar2-check"></i> My Reservations</a>
          <a href="logout.php" class="ft-link"><i class="bi bi-box-arrow-right"></i> Sign Out</a>
          <?php else: ?>
          <a href="choose-role.php" class="ft-link"><i class="bi bi-person"></i> Sign In</a>
          <a href="choose-role.php?action=signup" class="ft-link"><i class="bi bi-person-plus"></i> Create Account</a>
          <?php endif; ?>
          <a href="admin/login.php" class="ft-link" style="opacity:.35;font-size:.7rem"><i class="bi bi-shield-lock"></i> Staff Login</a>
        </div>
      </div>
      <div class="col-6 col-md-3 col-lg-2">
        <h4 class="ft-head">VISIT</h4>
        <div class="d-flex flex-column gap-2">
          <p class="ft-link mb-0"><i class="bi bi-geo-alt"></i> 123 Queen St W</p>
          <p class="ft-link mb-0"><i class="bi bi-telephone"></i> (416) 555-0192</p>
          <p class="ft-link mb-0"><i class="bi bi-clock"></i> <strong class="text-light">11am–10pm</strong></p>
        </div>
      </div>
    </div>
    <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-2 pt-4">
      <p class="ft-copy mb-0">© <?= date('Y') ?> DineLocal Toronto. All rights reserved.</p>
      <div class="d-flex gap-2 ft-icons"><i class="bi bi-fork-knife"></i><i class="bi bi-leaf"></i></div>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>
<script src="assets/js/validation.js"></script>
<script src="assets/js/animations.js"></script>
</body>
</html>
