<?php
session_start();
$_isLoggedIn = !empty($_SESSION['user_id']);
$_userName   = $_SESSION['user_name'] ?? '';
$_initials   = $_isLoggedIn ? strtoupper(substr($_userName, 0, 1)) : '';

// ── PHP form processing ──
require_once 'config/db.php';
require_once 'models/Reservation.php';
if ($_isLoggedIn) {
    require_once 'models/User.php';
    $_userModel = new User();
    $_userFull  = $_userModel->getById((int)$_SESSION['user_id']);
}

$success    = false;
$error      = false;
$formErrors = [];
$formData   = [];
$newId      = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $forSelf   = ($_POST['booking_for'] ?? 'self') === 'self';
    $autoName  = $forSelf && $_isLoggedIn ? ($_userFull['name'] ?? '') : '';
    $autoEmail = $forSelf && $_isLoggedIn ? ($_userFull['email'] ?? '') : '';

    $formData = [
        'fullName'   => $forSelf && $_isLoggedIn ? $autoName : htmlspecialchars(trim($_POST['fullName'] ?? '')),
        'email'      => $forSelf && $_isLoggedIn ? $autoEmail : filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL),
        'guests'     => htmlspecialchars(trim($_POST['guests'] ?? '')),
        'date'       => $_POST['date'] ?? '',
        'time'       => htmlspecialchars(trim($_POST['time'] ?? '')),
        'special'    => htmlspecialchars(trim($_POST['special'] ?? '')),
        'booking_for'=> $_POST['booking_for'] ?? 'self',
    ];

    if (strlen($formData['fullName']) < 2) $formErrors['fullName'] = 'Full name is required (min 2 characters).';
    if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) $formErrors['email'] = 'A valid email address is required.';
    if (empty($formData['guests'])) $formErrors['guests'] = 'Please select the number of guests.';
    if (empty($formData['date'])) { $formErrors['date'] = 'Please select a date.'; }
    elseif (strtotime($formData['date']) < strtotime('today')) { $formErrors['date'] = 'Date must be today or in the future.'; }
    if (empty($formData['time'])) $formErrors['time'] = 'Please select a preferred time.';

    if (empty($formErrors)) {
        try {
            $model = new Reservation();
            $newId = $model->create([
                'user_id'   => $_isLoggedIn ? (int)$_SESSION['user_id'] : null,
                'full_name' => $formData['fullName'],
                'email'     => $formData['email'],
                'guests'    => $formData['guests'],
                'date'      => $formData['date'],
                'time'      => $formData['time'],
                'special'   => $formData['special'],
            ]);
            $success = true;
            // Redirect logged-in users to their dashboard reservations tab
            if ($_isLoggedIn) {
                header('Location: dashboard.php?tab=reservations&booked=' . $newId);
                exit;
            }
        } catch (Exception $e) {
            $error = 'Database error. Please try again.';
        }
    } else {
        $error = 'Please correct the errors below.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Reservations — DineLocal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet"/>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="assets/css/style.css"/>
  <style>
    :root { --dl-orange:#C4551A; --dl-orange-d:#9E3A0E; --dl-cream:#FBF0DC; --dl-brown:#3B1A08; --dl-dark:#0d0702; --dl-gold:#E8A83E; --dl-serif:'Cormorant Garamond',Georgia,serif; }
    body { background:#0a0502; }
    .page-hero {
      min-height: 42vh; display:flex; align-items:flex-end;
      position:relative; overflow:hidden; padding-bottom:3rem;
    }
    .page-hero-bg { position:absolute; inset:0; z-index:0; }
    .page-hero-bg img { width:100%; height:100%; object-fit:cover; object-position:center 30%; }
    .page-hero-bg::after { content:''; position:absolute; inset:0; background:linear-gradient(to bottom,rgba(13,7,2,.3) 0%,rgba(13,7,2,.85) 100%); }
    .page-hero-content { position:relative; z-index:1; }
    .page-hero h1 { font-family:var(--dl-serif); font-size:clamp(2.5rem,6vw,5rem); font-weight:700; color:var(--dl-cream); letter-spacing:-.03em; }
    /* Success / Error banners */
    .banner { border-radius:1rem; padding:1.25rem 1.5rem; display:flex; align-items:flex-start; gap:1rem; margin-bottom:2rem; }
    .banner-success { background:rgba(28,70,14,.2); border:1px solid rgba(143,209,79,.3); color:#8FD14F; }
    .banner-error   { background:rgba(192,57,43,.15); border:1px solid rgba(192,57,43,.3); color:#e74c3c; }
    .banner i { font-size:1.4rem; flex-shrink:0; margin-top:.1rem; }
    /* Two-column layout */
    .res-wrap { display:grid; grid-template-columns:1fr 1fr; min-height:80vh; }
    @media(max-width:991px) { .res-wrap { grid-template-columns:1fr; } }
    /* Left: info + photo */
    .res-info { background:#1a0d05; padding:4rem 3rem; position:relative; overflow:hidden; }
    .res-info-img { position:absolute; inset:0; z-index:0; opacity:.18; }
    .res-info-img img { width:100%; height:100%; object-fit:cover; }
    .res-info-content { position:relative; z-index:1; }
    .info-block { display:flex; gap:1rem; align-items:flex-start; margin-bottom:1.75rem; }
    .info-icon { width:42px; height:42px; border-radius:50%; background:rgba(196,85,26,.15); border:1px solid rgba(196,85,26,.3); display:flex; align-items:center; justify-content:center; color:var(--dl-orange); font-size:1rem; flex-shrink:0; }
    .info-block h5 { font-family:var(--dl-serif); font-size:1.05rem; font-weight:700; color:var(--dl-cream); margin-bottom:.2rem; }
    .info-block p { font-size:.8rem; color:rgba(251,240,220,.52); margin:0; line-height:1.55; }
    /* Right: form */
    .res-form-wrap { background:#faf5ee; padding:4rem 3rem; }
    @media(max-width:991px) { .res-info { padding:3rem 1.5rem; } .res-form-wrap { padding:3rem 1.5rem; } }
    /* Mini bag */
    .mb-handles { display:flex; gap:14px; justify-content:center; margin-bottom:-2px; }
    .mb-handles span { display:block; width:21px; height:19px; border:2.5px solid #2C1200; border-radius:50% 50% 0 0; border-bottom:none; }
    #mb-body { background:#F3E4C6; border:1px solid rgba(59,26,8,.12); border-radius:10px; padding:1rem 1.6rem; min-width:170px; min-height:112px; display:flex; flex-direction:column; align-items:center; gap:.22rem; box-shadow:0 3px 16px rgba(59,26,8,.08); }
    #mb-body .bi-bag2 { font-size:1.55rem; color:var(--dl-orange); }
    #mb-body > p { font-family:var(--dl-serif); font-size:.85rem; font-weight:700; color:var(--dl-brown); margin:0; }
    #mb-items { width:100%; display:flex; flex-direction:column; gap:.2rem; margin-top:.28rem; }
    .mb-r { font-size:.6rem; font-weight:600; color:var(--dl-brown); background:rgba(196,85,26,.1); padding:.15rem .5rem; border-radius:9999px; display:flex; align-items:center; gap:.3rem; animation:mbi .4s cubic-bezier(.34,1.56,.64,1) both; }
    @keyframes mbi { from{transform:translateY(-14px) scale(.72);opacity:0} to{transform:none;opacity:1} }
    /* Form fields */
    .rf { display:flex; flex-direction:column; gap:.3rem; }
    .rf label { font-size:.72rem; font-weight:600; color:var(--dl-brown); display:flex; align-items:center; gap:.3rem; }
    .rf input, .rf select, .rf textarea { background:#F3E4C6; border:none; border-radius:.5rem; padding:.7rem 1rem; font-family:'Inter',sans-serif; font-size:.875rem; color:var(--dl-brown); outline:none; width:100%; transition:background .2s,box-shadow .2s; }
    .rf input:focus,.rf select:focus,.rf textarea:focus { background:#fff9f3; box-shadow:0 0 0 2px rgba(196,85,26,.28); }
    .rf input::placeholder,.rf textarea::placeholder { color:rgba(59,26,8,.3); }
    .rf textarea { resize:vertical; min-height:80px; }
    .rerr { font-size:.66rem; color:#c0392b; font-weight:500; min-height:.8rem; display:block; }
    .field-ok input,.field-ok select { box-shadow:0 0 0 2px rgba(30,74,14,.38); }
    .field-err input,.field-err select { box-shadow:0 0 0 2px rgba(192,57,43,.42); }
    #sub-btn { background:linear-gradient(135deg,var(--dl-orange),var(--dl-orange-d)); color:#fff; padding:.95rem; border-radius:9999px; font-size:.92rem; font-weight:600; border:none; width:100%; display:flex; align-items:center; justify-content:center; gap:.55rem; box-shadow:0 4px 16px rgba(196,85,26,.4); transition:transform .2s,box-shadow .2s; cursor:pointer; }
    #sub-btn:hover { transform:translateY(-2px); box-shadow:0 8px 26px rgba(196,85,26,.58); }
    /* Testimonials */
    .tcard { background:rgba(196,85,26,.08); border:1px solid rgba(196,85,26,.15); border-radius:.85rem; padding:1.25rem; }
    .tcard p { font-size:.8rem; color:rgba(251,240,220,.65); line-height:1.6; font-style:italic; margin-bottom:.75rem; }
    .tcard strong { font-size:.72rem; color:var(--dl-gold); font-weight:600; letter-spacing:.05em; }
    .stars { color:var(--dl-gold); font-size:.75rem; margin-bottom:.5rem; }
  </style>
</head>
<body>

<!-- Nav -->
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
        <?php if ($_isLoggedIn): ?>
        <li class="nav-item dropdown">
          <a class="nav-link active dropdown-toggle" href="#" data-bs-toggle="dropdown">Reservations</a>
          <ul class="dropdown-menu nav-dropdown">
            <li><a class="dropdown-item" href="dashboard.php?tab=reservations"><i class="bi bi-calendar2-check me-2"></i>View My Reservations</a></li>
            <li><a class="dropdown-item" href="reservations.php"><i class="bi bi-plus-circle me-2"></i>Book a New Table</a></li>
          </ul>
        </li>
        <?php else: ?>
        <li class="nav-item"><a class="nav-link active" href="reservations.php">Reservations</a></li>
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
    <a href="reservations.php" class="btn-reserve justify-content-center">Reserve a Table</a>
  </div>
</div>

<!-- Page Hero -->
<div class="page-hero pt-5">
  <div class="page-hero-bg">
    <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1600&h=800&fit=crop&crop=center" alt="Restaurant"/>
  </div>
  <div class="page-hero-content container-xl px-3 px-md-5 w-100">
    <p class="eyebrow mb-2"><i class="bi bi-calendar2-check"></i> BOOK YOUR TABLE</p>
    <h1>Reservations</h1>
    <p style="color:rgba(251,240,220,.62);font-size:.9rem" class="mt-1">Secure your dining experience — it only takes a minute.</p>
  </div>
</div>


<!-- Main Content -->
<main>

  <!-- Success Banner (shown only for guests without account) -->
  <?php if ($success): ?>
  <div class="container-xl px-3 px-md-5 pt-5">
    <div class="banner banner-success">
      <i class="bi bi-hourglass-split"></i>
      <div>
        <strong style="font-size:1rem;font-family:var(--dl-serif)">Reservation Received!</strong>
        <p class="mb-0 mt-1" style="font-size:.85rem">Thank you, <?= htmlspecialchars($formData['fullName']) ?>! Your reservation #<?= $newId ?> for <?= htmlspecialchars($formData['guests']) ?> on <?= date('F j, Y', strtotime($formData['date'])) ?> at <?= htmlspecialchars($formData['time']) ?> is <strong>pending</strong> — our team will confirm it shortly.</p>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- Error Banner -->
  <?php if ($error && !$success): ?>
  <div class="container-xl px-3 px-md-5 pt-5">
    <div class="banner banner-error">
      <i class="bi bi-exclamation-circle-fill"></i>
      <div><strong>Please fix the following:</strong> <?= htmlspecialchars($error) ?></div>
    </div>
  </div>
  <?php endif; ?>

  <div class="res-wrap">

    <!-- LEFT: Info + Testimonials -->
    <div class="res-info">
      <div class="res-info-img">
        <img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800&h=1200&fit=crop" alt=""/>
      </div>
      <div class="res-info-content">
        <p class="eyebrow mb-1"><i class="bi bi-info-circle"></i> WHAT TO EXPECT</p>
        <h2 style="font-family:var(--dl-serif);font-size:clamp(1.8rem,3vw,2.5rem);font-weight:700;color:var(--dl-cream);letter-spacing:-.02em;margin-bottom:2rem">Your Evening<br><em style="color:var(--dl-gold)">Awaits</em></h2>

        <div class="info-block">
          <div class="info-icon"><i class="bi bi-clock"></i></div>
          <div>
            <h5>Opening Hours</h5>
            <p>Monday – Sunday<br>11:00 AM – 10:00 PM<br>Kitchen closes at 9:30 PM</p>
          </div>
        </div>
        <div class="info-block">
          <div class="info-icon"><i class="bi bi-geo-alt"></i></div>
          <div>
            <h5>Location</h5>
            <p>123 Queen Street West<br>Toronto, ON M5H 2M9<br>Parking available on-site</p>
          </div>
        </div>
        <div class="info-block">
          <div class="info-icon"><i class="bi bi-telephone"></i></div>
          <div>
            <h5>Contact</h5>
            <p>(416) 555-0192<br>hello@dinelocal.ca</p>
          </div>
        </div>
        <div class="info-block">
          <div class="info-icon"><i class="bi bi-shield-check"></i></div>
          <div>
            <h5>Our Policy</h5>
            <p>Reservations held for 15 minutes.<br>Cancellations: 24 hours notice required.</p>
          </div>
        </div>

        <!-- Testimonials -->
        <div class="mt-4 d-flex flex-column gap-3">
          <div class="tcard">
            <div class="stars">★★★★★</div>
            <p>"An unforgettable evening. The striploin was the best I've had in Toronto."</p>
            <strong>— Marcus T., Google Reviews</strong>
          </div>
          <div class="tcard">
            <div class="stars">★★★★★</div>
            <p>"Impeccable service, stunning atmosphere, and food that tells a real story."</p>
            <strong>— Priya K., Yelp</strong>
          </div>
        </div>
      </div>
    </div>

    <!-- RIGHT: Form -->
    <div class="res-form-wrap">
      <div style="max-width:500px;margin:0 auto">
        <p class="eyebrow mb-1"><i class="bi bi-bag"></i> RESERVE YOUR TABLE</p>
        <h2 style="font-family:var(--dl-serif);font-size:clamp(2rem,3.5vw,2.8rem);font-weight:700;color:var(--dl-brown);letter-spacing:-.02em;margin-bottom:.5rem">Book a Table</h2>
        <p style="font-size:.84rem;color:rgba(59,26,8,.55);margin-bottom:1.5rem">All fields marked * are required.</p>

        <!-- Mini bag -->
        <div class="text-center mb-4">
          <div class="d-inline-flex flex-column align-items-center">
            <div class="mb-handles"><span></span><span></span></div>
            <div id="mb-body">
              <i class="bi bi-bag2"></i>
              <p>DineLocal</p>
              <div id="mb-items"></div>
            </div>
          </div>
          <p style="font-family:var(--dl-serif);font-size:.84rem;font-weight:700;color:var(--dl-brown);margin-top:.5rem;margin-bottom:2px">Your Reservation</p>
          <p style="font-size:.64rem;color:rgba(59,26,8,.45)">Details appear as you fill the form.</p>
        </div>

        <form id="resForm" method="POST" action="reservations.php" novalidate>
          <div class="row g-3">

            <?php if ($_isLoggedIn): ?>
            <!-- Who is this booking for? -->
            <div class="col-12">
              <div style="background:rgba(196,85,26,.07);border-radius:.6rem;padding:.85rem 1rem;margin-bottom:.25rem">
                <p style="font-size:.72rem;font-weight:600;color:var(--dl-brown);margin-bottom:.6rem"><i class="bi bi-person-check me-1"></i> WHO IS THIS BOOKING FOR?</p>
                <div class="d-flex gap-2 flex-wrap">
                  <label style="display:flex;align-items:center;gap:.45rem;cursor:pointer;font-size:.83rem;font-weight:500;color:var(--dl-brown)">
                    <input type="radio" name="booking_for" value="self" id="forSelf" <?= (($formData['booking_for'] ?? 'self') === 'self') ? 'checked' : '' ?> style="accent-color:var(--dl-orange)"/> Myself
                  </label>
                  <label style="display:flex;align-items:center;gap:.45rem;cursor:pointer;font-size:.83rem;font-weight:500;color:var(--dl-brown)">
                    <input type="radio" name="booking_for" value="other" id="forOther" <?= (($formData['booking_for'] ?? '') === 'other') ? 'checked' : '' ?> style="accent-color:var(--dl-orange)"/> Someone Else
                  </label>
                </div>
              </div>
            </div>
            <?php endif; ?>

            <div class="col-12 col-sm-6" id="nameField">
              <div class="rf <?= isset($formErrors['fullName']) ? 'field-err' : (isset($formData['fullName']) && !isset($formErrors['fullName']) && $formData['fullName'] ? 'field-ok' : '') ?>">
                <label for="rn"><i class="bi bi-person"></i> Full Name *</label>
                <input type="text" id="rn" name="fullName" placeholder="Julian Thorne" required
                  value="<?= htmlspecialchars($formData['fullName'] ?? ($_isLoggedIn ? ($_userFull['name'] ?? '') : '')) ?>"/>
                <span class="rerr" id="e1"><?= htmlspecialchars($formErrors['fullName'] ?? '') ?></span>
              </div>
            </div>
            <div class="col-12 col-sm-6" id="emailField">
              <div class="rf <?= isset($formErrors['email']) ? 'field-err' : '' ?>">
                <label for="re"><i class="bi bi-envelope"></i> Email *</label>
                <input type="email" id="re" name="email" placeholder="you@example.ca" required
                  value="<?= htmlspecialchars($formData['email'] ?? ($_isLoggedIn ? ($_userFull['email'] ?? '') : '')) ?>"/>
                <span class="rerr" id="e2"><?= htmlspecialchars($formErrors['email'] ?? '') ?></span>
              </div>
            </div>
            <div class="col-12 col-sm-6">
              <div class="rf <?= isset($formErrors['guests']) ? 'field-err' : '' ?>">
                <label for="rg"><i class="bi bi-people"></i> Guests *</label>
                <select id="rg" name="guests" required>
                  <option value="">Select guests</option>
                  <?php foreach (['1 Guest','2 Guests','3 Guests','4 Guests','5 Guests','6+ Guests'] as $g): ?>
                  <option <?= ($formData['guests'] ?? '') === $g ? 'selected' : '' ?>><?= $g ?></option>
                  <?php endforeach; ?>
                </select>
                <span class="rerr" id="e3"><?= htmlspecialchars($formErrors['guests'] ?? '') ?></span>
              </div>
            </div>
            <div class="col-12 col-sm-6">
              <div class="rf <?= isset($formErrors['date']) ? 'field-err' : '' ?>">
                <label for="rd"><i class="bi bi-calendar3"></i> Date *</label>
                <input type="date" id="rd" name="date" required value="<?= htmlspecialchars($formData['date'] ?? '') ?>"/>
                <span class="rerr" id="e4"><?= htmlspecialchars($formErrors['date'] ?? '') ?></span>
              </div>
            </div>
            <div class="col-12">
              <div class="rf <?= isset($formErrors['time']) ? 'field-err' : '' ?>">
                <label for="rt"><i class="bi bi-clock"></i> Preferred Time *</label>
                <select id="rt" name="time" required>
                  <option value="">Select time</option>
                  <?php foreach (['11:00 AM','12:00 PM','1:00 PM','6:00 PM','7:00 PM','8:00 PM','9:00 PM'] as $t): ?>
                  <option <?= ($formData['time'] ?? '') === $t ? 'selected' : '' ?>><?= $t ?></option>
                  <?php endforeach; ?>
                </select>
                <span class="rerr" id="e5"><?= htmlspecialchars($formErrors['time'] ?? '') ?></span>
              </div>
            </div>
            <div class="col-12">
              <div class="rf">
                <label for="rs"><i class="bi bi-chat-text"></i> Special Requests <small style="font-weight:400;color:rgba(59,26,8,.38)">(optional)</small></label>
                <textarea id="rs" name="special" rows="3" placeholder="Dietary needs, celebrations, seating preferences..."><?= htmlspecialchars($formData['special'] ?? '') ?></textarea>
              </div>
            </div>
            <div class="col-12">
              <button type="submit" id="sub-btn">
                <i class="bi bi-bag-check"></i> Reserve My Table
              </button>
            </div>
          </div>
        </form>
      </div>
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
        <a href="menu.php" class="ft-link">Menu</a>
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
// ── JS Validation (rubric: alert, confirm, prompt) ──
const $ = id => document.getElementById(id);
const rn=$('rn'),re=$('re'),rg=$('rg'),rd=$('rd'),rt=$('rt');
const mbi=$('mb-items'),mbb=$('mb-body');
const drop=(k,icon,text)=>{
  if(!mbi)return;
  const o=mbi.querySelector(`[data-k="${k}"]`);if(o)o.remove();
  const d=document.createElement('div');d.className='mb-r';d.dataset.k=k;
  d.innerHTML=`<i class="bi ${icon}"></i>${text}`;mbi.appendChild(d);
  if(mbb){mbb.style.transform='rotate(4deg) scale(1.04)';setTimeout(()=>{mbb.style.transform='rotate(-3deg)';},85);setTimeout(()=>{mbb.style.transform='';},190);}
};
const ve=v=>/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
const fd=v=>{const d=new Date(v),n=new Date();n.setHours(0,0,0,0);return d>=n;};
rn?.addEventListener('blur',()=>{ const v=rn.value.trim(); if(v.length>=2)drop('n','bi-person',v); });
re?.addEventListener('blur',()=>{ const v=re.value.trim(); if(ve(v))drop('e','bi-envelope',v); });
rg?.addEventListener('change',()=>{ if(rg.value)drop('g','bi-people',rg.value); });
rd?.addEventListener('change',()=>{ if(rd.value&&fd(rd.value))drop('d','bi-calendar3',rd.value); });
rt?.addEventListener('change',()=>{ if(rt.value)drop('t','bi-clock',rt.value); });
// Min date
if(rd){const t=new Date();rd.min=`${t.getFullYear()}-${String(t.getMonth()+1).padStart(2,'0')}-${String(t.getDate()).padStart(2,'0')}`;}

document.getElementById('resForm')?.addEventListener('submit',function(e){
  let ok=true;
  const errs=[];
  if(!rn||rn.value.trim().length<2){errs.push('Full name (min 2 chars)');ok=false;}
  if(!re||!ve(re.value.trim())){errs.push('Valid email address');ok=false;}
  if(!rg||!rg.value){errs.push('Number of guests');ok=false;}
  if(!rd||!rd.value){errs.push('Reservation date');ok=false;}
  else if(!fd(rd.value)){errs.push('Future date');ok=false;}
  if(!rt||!rt.value){errs.push('Preferred time');ok=false;}

  if(!ok){
    // ALERT — rubric requirement
    alert('Please complete the following fields:\n• ' + errs.join('\n• '));
    e.preventDefault(); return;
  }

  // CONFIRM — rubric requirement
  const go=confirm(
    `Confirm your reservation:\n\n` +
    `Name: ${rn.value.trim()}\n` +
    `Email: ${re.value.trim()}\n` +
    `Guests: ${rg.value}\n` +
    `Date: ${rd.value}\n` +
    `Time: ${rt.value}\n\n` +
    `Proceed?`
  );
  if(!go){
    e.preventDefault();
    // PROMPT — rubric requirement
    const note=prompt('What would you like to change? (Press Cancel to go back)');
    if(note) alert(`We noted: "${note}"\nPlease update your details and submit again.`);
  }
});

// "Booking for" toggle — show/hide name+email when logged in
<?php if ($_isLoggedIn): ?>
const selfName  = <?= json_encode($_userFull['name']  ?? '') ?>;
const selfEmail = <?= json_encode($_userFull['email'] ?? '') ?>;
document.querySelectorAll('input[name="booking_for"]').forEach(r => {
  r.addEventListener('change', function() {
    const isSelf = this.value === 'self';
    if (rn) { rn.value = isSelf ? selfName  : ''; rn.readOnly = isSelf; rn.style.opacity = isSelf ? '.65' : '1'; }
    if (re) { re.value = isSelf ? selfEmail : ''; re.readOnly = isSelf; re.style.opacity = isSelf ? '.65' : '1'; }
  });
});
// Apply on load
const initSelf = document.querySelector('input[name="booking_for"]:checked')?.value === 'self';
if (initSelf) {
  if (rn) { rn.readOnly = true; rn.style.opacity = '.65'; }
  if (re) { re.readOnly = true; re.style.opacity = '.65'; }
}
<?php endif; ?>

// Nav hide
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