<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>About — DineLocal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet"/>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="assets/css/style.css"/>
  <style>
    :root{--dl-orange:#C4551A;--dl-orange-d:#9E3A0E;--dl-cream:#FBF0DC;--dl-brown:#3B1A08;--dl-dark:#0d0702;--dl-gold:#E8A83E;--dl-serif:'Cormorant Garamond',Georgia,serif;}
    body{background:#0a0502;}
    .page-hero{min-height:55vh;display:flex;align-items:flex-end;position:relative;overflow:hidden;padding-bottom:4rem;}
    .page-hero-bg{position:absolute;inset:0;z-index:0;}
    .page-hero-bg img{width:100%;height:100%;object-fit:cover;object-position:center 40%;}
    .page-hero-bg::after{content:'';position:absolute;inset:0;background:linear-gradient(to bottom,rgba(13,7,2,.25) 0%,rgba(13,7,2,.88) 100%);}
    .page-hero-content{position:relative;z-index:1;}
    .page-hero h1{font-family:var(--dl-serif);font-size:clamp(3rem,7vw,6rem);font-weight:700;color:var(--dl-cream);letter-spacing:-.03em;line-height:.9;}
    .story-grid{display:grid;grid-template-columns:1fr 1fr;gap:5rem;align-items:center;padding:6rem 0;}
    @media(max-width:767px){.story-grid{grid-template-columns:1fr;gap:2.5rem;padding:3.5rem 0;}}
    .story-photo{position:relative;}
    .story-photo img{width:100%;aspect-ratio:3/4;object-fit:cover;border-radius:1.5rem;}
    .story-badge{position:absolute;bottom:-1.5rem;right:-1.5rem;background:var(--dl-orange);color:#fff;border-radius:1rem;padding:1.25rem 1.5rem;text-align:center;box-shadow:0 8px 32px rgba(196,85,26,.4);}
    .story-badge strong{font-family:var(--dl-serif);font-size:2rem;font-weight:700;display:block;line-height:1;}
    .story-badge span{font-size:.65rem;font-weight:600;letter-spacing:.1em;opacity:.85;}
    @media(max-width:767px){.story-badge{right:-.5rem;bottom:-.5rem;padding:.85rem 1rem;}.story-badge strong{font-size:1.5rem;}}
    .story-content h2{font-family:var(--dl-serif);font-size:clamp(2rem,4vw,3.5rem);font-weight:700;color:var(--dl-cream);letter-spacing:-.025em;line-height:1.05;margin-bottom:1.5rem;}
    .story-content p{font-size:.9rem;color:rgba(251,240,220,.58);line-height:1.8;margin-bottom:1.25rem;}
    .values-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1.5rem;}
    @media(max-width:767px){.values-grid{grid-template-columns:1fr;}}
    .value-card{background:#1a0d05;border:1px solid rgba(232,168,62,.1);border-radius:1.25rem;padding:2rem 1.5rem;transition:transform .25s,box-shadow .25s;}
    .value-card:hover{transform:translateY(-6px);box-shadow:0 20px 48px rgba(0,0,0,.4);}
    .value-icon{width:52px;height:52px;border-radius:50%;background:rgba(196,85,26,.12);border:1px solid rgba(196,85,26,.25);display:flex;align-items:center;justify-content:center;color:var(--dl-orange);font-size:1.3rem;margin-bottom:1.25rem;}
    .value-card h3{font-family:var(--dl-serif);font-size:1.25rem;font-weight:700;color:var(--dl-cream);margin-bottom:.6rem;}
    .value-card p{font-size:.8rem;color:rgba(251,240,220,.48);line-height:1.7;margin:0;}
    .team-card{text-align:center;}
    .team-photo{width:140px;height:140px;border-radius:50%;object-fit:cover;margin:0 auto 1.25rem;border:3px solid rgba(232,168,62,.25);}
    .team-card h4{font-family:var(--dl-serif);font-size:1.2rem;font-weight:700;color:var(--dl-cream);margin-bottom:.2rem;}
    .team-card p{font-size:.75rem;color:var(--dl-orange);font-weight:600;letter-spacing:.08em;margin-bottom:.6rem;}
    .team-card small{font-size:.76rem;color:rgba(251,240,220,.45);line-height:1.6;}
    .timeline{position:relative;padding-left:2rem;}
    .timeline::before{content:'';position:absolute;left:7px;top:0;bottom:0;width:2px;background:rgba(196,85,26,.2);}
    .tl-item{position:relative;margin-bottom:2rem;}
    .tl-dot{position:absolute;left:-2rem;top:.2rem;width:16px;height:16px;border-radius:50%;background:var(--dl-orange);border:2px solid rgba(196,85,26,.4);}
    .tl-year{font-size:.65rem;font-weight:700;color:var(--dl-orange);letter-spacing:.15em;margin-bottom:.25rem;}
    .tl-title{font-family:var(--dl-serif);font-size:1.1rem;font-weight:700;color:var(--dl-cream);margin-bottom:.3rem;}
    .tl-desc{font-size:.8rem;color:rgba(251,240,220,.5);line-height:1.6;}
    .dark-sec{background:#0d0702;}
    .cta-wrap{background:linear-gradient(135deg,var(--dl-orange),var(--dl-orange-d));border-radius:1.5rem;padding:4rem 3rem;text-align:center;position:relative;overflow:hidden;}
    .cta-wrap::before{content:'';position:absolute;inset:0;background:url("https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=1200&h=400&fit=crop") center/cover;opacity:.15;}
    .cta-wrap>*{position:relative;z-index:1;}
    .cta-wrap h2{font-family:var(--dl-serif);font-size:clamp(2rem,4vw,3rem);font-weight:700;color:#fff;letter-spacing:-.025em;margin-bottom:1rem;}
    .cta-wrap p{color:rgba(255,255,255,.8);font-size:.9rem;max-width:460px;margin:0 auto 2rem;}
    .btn-white{background:#fff;color:var(--dl-orange);padding:.85rem 2rem;border-radius:9999px;font-size:.875rem;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:.5rem;transition:transform .2s,box-shadow .2s;}
    .btn-white:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,0,0,.3);color:var(--dl-orange-d);}
    .py-md-6{padding-top:6rem!important;padding-bottom:6rem!important;}
    @media(max-width:767px){.py-md-6{padding-top:4rem!important;padding-bottom:4rem!important;}}
  </style>
</head>
<body>

<!-- NAV -->
<nav class="navbar navbar-expand-lg fixed-top" id="main-nav">
  <div class="container-xl">
    <a class="navbar-brand nav-logo me-auto" href="index.php">DineLocal</a>
    <button class="navbar-toggler me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNav" aria-label="Menu">
      <span class="tog-bar"></span><span class="tog-bar"></span><span class="tog-bar"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-center" id="desktopNav">
      <ul class="navbar-nav gap-1">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
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
        <li class="nav-item"><a class="nav-link active" href="about.php">About</a></li>
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

<!-- Hero -->
<div class="page-hero pt-5">
  <div class="page-hero-bg">
    <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=1600&h=900&fit=crop&crop=center" alt="Kitchen"/>
  </div>
  <div class="page-hero-content container-xl px-3 px-md-5 w-100">
    <p class="eyebrow mb-2"><i class="bi bi-info-circle"></i> OUR STORY</p>
    <h1>Where Toronto<br><em style="color:var(--dl-gold)">Comes to Dine</em></h1>
  </div>
</div>

<main>
  <!-- Story -->
  <section class="dark-sec">
    <div class="container-xl px-3 px-md-5">
      <div class="story-grid">
        <div class="story-photo">
          <img src="https://images.unsplash.com/photo-1577219491135-ce391730fb2c?w=700&h=900&fit=crop" alt="Chef"/>
          <div class="story-badge"><strong>12+</strong><span>YEARS IN<br>TORONTO</span></div>
        </div>
        <div class="story-content">
          <p class="eyebrow mb-2"><i class="bi bi-book"></i> OUR BEGINNING</p>
          <h2>A Kitchen Born From Community</h2>
          <p>DineLocal was founded in 2012 with a single belief: the best ingredients grow within 100 kilometres of your table. What began as a 30-seat neighbourhood bistro on Queen Street West has grown into one of Toronto's most celebrated dining destinations.</p>
          <p>Our Executive Chef sources directly from over 40 Ontario farms, building relationships with farmers, foragers and artisans who share our commitment to quality and sustainability.</p>
          <p>We are proud to have received recognition from Toronto Life, the Globe and Mail, and Canada's 100 Best Restaurants.</p>
          <div class="d-flex gap-4 mt-4">
            <div><div style="font-family:var(--dl-serif);font-size:2.5rem;font-weight:700;color:var(--dl-gold);line-height:1">40+</div><div style="font-size:.7rem;font-weight:600;color:rgba(251,240,220,.48);letter-spacing:.1em">LOCAL FARMS</div></div>
            <div><div style="font-family:var(--dl-serif);font-size:2.5rem;font-weight:700;color:var(--dl-gold);line-height:1">50K+</div><div style="font-size:.7rem;font-weight:600;color:rgba(251,240,220,.48);letter-spacing:.1em">GUESTS SERVED</div></div>
            <div><div style="font-family:var(--dl-serif);font-size:2.5rem;font-weight:700;color:var(--dl-gold);line-height:1">4.9★</div><div style="font-size:.7rem;font-weight:600;color:rgba(251,240,220,.48);letter-spacing:.1em">AVERAGE RATING</div></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Values -->
  <section class="py-5 py-md-6" style="background:#120900">
    <div class="container-xl px-3 px-md-5">
      <div class="text-center mb-5">
        <p class="eyebrow justify-content-center"><i class="bi bi-stars"></i> WHAT WE STAND FOR</p>
        <h2 style="font-family:var(--dl-serif);font-size:clamp(2rem,4vw,3rem);font-weight:700;color:var(--dl-cream);letter-spacing:-.02em">Our Core Values</h2>
      </div>
      <div class="values-grid">
        <div class="value-card"><div class="value-icon"><i class="bi bi-leaf"></i></div><h3>Local First</h3><p>100% of our produce is sourced from Ontario farms within 100km. We know our farmers by name and visit their fields every season.</p></div>
        <div class="value-card"><div class="value-icon"><i class="bi bi-recycle"></i></div><h3>Zero Waste</h3><p>Our kitchen composts every scrap and partners with FoodShare Toronto to redirect surplus food to those who need it.</p></div>
        <div class="value-card"><div class="value-icon"><i class="bi bi-people"></i></div><h3>Community</h3><p>We employ from the neighbourhood, mentor young chefs, and host monthly community dinners where everyone is welcome.</p></div>
      </div>
    </div>
  </section>

  <!-- Team -->
  <section class="py-5 py-md-6 dark-sec">
    <div class="container-xl px-3 px-md-5">
      <div class="text-center mb-5">
        <p class="eyebrow justify-content-center"><i class="bi bi-people"></i> THE PEOPLE</p>
        <h2 style="font-family:var(--dl-serif);font-size:clamp(2rem,4vw,3rem);font-weight:700;color:var(--dl-cream);letter-spacing:-.02em">Meet the Team</h2>
      </div>
      <div class="row g-4 justify-content-center">
        <div class="col-12 col-sm-6 col-lg-3"><div class="team-card"><img src="https://randomuser.me/api/portraits/men/32.jpg" class="team-photo" alt="Chef Marcus"/><h4>Chef Marcus Webb</h4><p>EXECUTIVE CHEF</p><small>Trained at Le Cordon Bleu, Paris. 18 years crafting Ontario-inspired cuisine with European technique.</small></div></div>
        <div class="col-12 col-sm-6 col-lg-3"><div class="team-card"><img src="https://randomuser.me/api/portraits/women/68.jpg" class="team-photo" alt="Priya"/><h4>Priya Nair</h4><p>PASTRY CHEF</p><small>Michelin-trained specialist. Blends South Asian spice traditions with classical French pastry technique.</small></div></div>
        <div class="col-12 col-sm-6 col-lg-3"><div class="team-card"><img src="https://randomuser.me/api/portraits/men/75.jpg" class="team-photo" alt="James"/><h4>James Okafor</h4><p>GENERAL MANAGER</p><small>15 years in Toronto's hospitality scene. James ensures every guest experience exceeds expectations.</small></div></div>
        <div class="col-12 col-sm-6 col-lg-3"><div class="team-card"><img src="https://randomuser.me/api/portraits/women/44.jpg" class="team-photo" alt="Sofia"/><h4>Sofia Marchetti</h4><p>SOMMELIER</p><small>WSET Level 4. Curates our rotating wine list with a focus on emerging Ontario VQA producers.</small></div></div>
      </div>
    </div>
  </section>

  <!-- Timeline -->
  <section class="py-5 py-md-6" style="background:#120900">
    <div class="container-xl px-3 px-md-5">
      <div class="row g-5 align-items-center">
        <div class="col-12 col-md-5">
          <p class="eyebrow mb-2"><i class="bi bi-clock-history"></i> OUR JOURNEY</p>
          <h2 style="font-family:var(--dl-serif);font-size:clamp(2rem,4vw,3rem);font-weight:700;color:var(--dl-cream);letter-spacing:-.02em;margin-bottom:1.5rem">12 Years of<br><em style="color:var(--dl-gold)">Flavour</em></h2>
          <p style="font-size:.88rem;color:rgba(251,240,220,.52);line-height:1.8">From a humble 30-seat bistro to one of Toronto's most celebrated restaurants.</p>
        </div>
        <div class="col-12 col-md-7">
          <div class="timeline">
            <div class="tl-item"><div class="tl-dot"></div><div class="tl-year">2012</div><div class="tl-title">DineLocal Opens</div><div class="tl-desc">30-seat bistro opens on Queen St W with a farm-to-table concept and 6 local farm partners.</div></div>
            <div class="tl-item"><div class="tl-dot"></div><div class="tl-year">2015</div><div class="tl-title">Toronto Life's Best New Restaurant</div><div class="tl-desc">Expanded to 80 seats and opened the private dining room.</div></div>
            <div class="tl-item"><div class="tl-dot"></div><div class="tl-year">2018</div><div class="tl-title">Zero Waste Kitchen Initiative</div><div class="tl-desc">Launched full zero-waste kitchen program. Partnered with 40+ Ontario farms.</div></div>
            <div class="tl-item"><div class="tl-dot"></div><div class="tl-year">2021</div><div class="tl-title">Canada's 100 Best Restaurants</div><div class="tl-desc">Ranked in Canada's 100 Best. Launched community dinner program.</div></div>
            <div class="tl-item"><div class="tl-dot"></div><div class="tl-year">2024</div><div class="tl-title">12th Anniversary</div><div class="tl-desc">Launching our online reservation platform and digital tasting menu experience.</div></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="py-5 py-md-6 dark-sec">
    <div class="container-xl px-3 px-md-5">
      <div class="cta-wrap">
        <p style="font-size:.62rem;font-weight:600;letter-spacing:.28em;color:rgba(255,255,255,.7)" class="mb-3"><i class="bi bi-calendar2-check"></i> JOIN US</p>
        <h2>Ready to Taste the Neighbourhood?</h2>
        <p>Reserve your table today and experience the warmth, flavour and community that defines DineLocal.</p>
        <a href="reservations.php" class="btn-white"><i class="bi bi-calendar2-check"></i> Book a Table</a>
      </div>
    </div>
  </section>
</main>

<footer class="py-5" style="background:#0a0502;border-top:1px solid rgba(232,168,62,.08)">
  <div class="container-xl px-3 px-md-5">
    <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3">
      <span class="ft-logo">DineLocal</span>
      <p class="ft-copy mb-0">© <?= date('Y') ?> DineLocal Toronto. All rights reserved.</p>
      <div class="d-flex gap-3">
        <a href="index.php" class="ft-link">Home</a>
        <a href="menu.php" class="ft-link">Menu</a>
        <a href="reservations.php" class="ft-link">Reservations</a>
        <?php if (!empty($_SESSION['user_id'])): ?>
        <a href="dashboard.php" class="ft-link">My Account</a>
        <?php else: ?>
        <a href="choose-role.php" class="ft-link">Sign In</a>
        <?php endif; ?>
        <a href="admin/login.php" class="ft-link" style="opacity:.3;font-size:.7rem">Staff</a>
      </div>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
let lastY=0;
window.addEventListener('scroll',()=>{
  const nav=document.getElementById('main-nav');
  const sy=window.scrollY;
  nav.style.transition='transform .35s ease';
  nav.style.transform=sy>80&&sy>lastY?'translateY(-110%)':'translateY(0)';
  lastY=sy;
},{passive:true});
</script>
</body>
</html>