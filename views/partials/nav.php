<?php
// ============================================
// DINELOCAL · views/partials/nav.php
// Reusable navbar with user auth state
// ITC 6355 | Arjun & Ayomide
// ============================================
if (session_status() === PHP_SESSION_NONE) session_start();
$isLoggedIn = !empty($_SESSION['user_id']);
$userName   = $_SESSION['user_name'] ?? '';
$initials   = $isLoggedIn ? strtoupper(substr($userName, 0, 1)) : '';
?>
<nav class="navbar navbar-expand-lg fixed-top" id="main-nav">
  <div class="container-xl">
    <a class="navbar-brand nav-logo me-auto" href="index.php">DineLocal</a>

    <button class="navbar-toggler me-2" type="button"
      data-bs-toggle="offcanvas" data-bs-target="#mobileNav" aria-label="Menu">
      <span class="tog-bar"></span><span class="tog-bar"></span><span class="tog-bar"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-center" id="desktopNav">
      <ul class="navbar-nav gap-1">
        <li class="nav-item"><a class="nav-link <?= basename($_SERVER['PHP_SELF'])==='index.php'?'active':'' ?>" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link <?= basename($_SERVER['PHP_SELF'])==='menu.php'?'active':'' ?>" href="menu.php">Menu</a></li>
        <li class="nav-item"><a class="nav-link <?= basename($_SERVER['PHP_SELF'])==='reservations.php'?'active':'' ?>" href="reservations.php">Reservations</a></li>
        <li class="nav-item"><a class="nav-link <?= basename($_SERVER['PHP_SELF'])==='about.php'?'active':'' ?>" href="about.php">About</a></li>
      </ul>
    </div>

    <div class="d-none d-lg-flex align-items-center gap-2 ms-auto">
      <?php if ($isLoggedIn): ?>
        <!-- Logged in: show avatar + dropdown -->
        <div class="dropdown">
          <button class="nav-user-btn dropdown-toggle" data-bs-toggle="dropdown">
            <span class="nav-avatar"><?= htmlspecialchars($initials) ?></span>
            <span class="nav-username"><?= htmlspecialchars(explode(' ',$userName)[0]) ?></span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end nav-dropdown">
            <li><a class="dropdown-item" href="dashboard.php"><i class="bi bi-grid me-2"></i>My Dashboard</a></li>
            <li><a class="dropdown-item" href="dashboard.php?tab=reservations"><i class="bi bi-calendar2-check me-2"></i>My Reservations</a></li>
            <li><a class="dropdown-item" href="dashboard.php?tab=profile"><i class="bi bi-person-gear me-2"></i>Edit Profile</a></li>
            <li><hr class="dropdown-divider" style="border-color:rgba(59,26,8,.08)"/></li>
            <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</a></li>
          </ul>
        </div>
      <?php else: ?>
        <a href="login.php" class="nav-signin">Sign In</a>
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
      <a href="index.php"        class="mob-link">Home</a>
      <a href="menu.php"         class="mob-link">Menu</a>
      <a href="reservations.php" class="mob-link">Reservations</a>
      <a href="about.php"        class="mob-link">About</a>
    </nav>
    <?php if ($isLoggedIn): ?>
      <div style="font-size:.7rem;font-weight:600;letter-spacing:.12em;color:rgba(251,240,220,.38);margin-bottom:.75rem">MY ACCOUNT</div>
      <a href="dashboard.php"           class="mob-link" style="font-size:1.2rem">Dashboard</a>
      <a href="dashboard.php?tab=reservations" class="mob-link" style="font-size:1.2rem">My Reservations</a>
      <a href="logout.php"              class="mob-link" style="font-size:1.2rem;color:rgba(192,57,43,.7)">Sign Out</a>
    <?php else: ?>
      <a href="login.php"    class="mob-link" style="font-size:1.4rem">Sign In</a>
      <a href="register.php" class="mob-link" style="font-size:1.4rem">Create Account</a>
    <?php endif; ?>
    <a href="reservations.php" class="btn-reserve justify-content-center mt-4">
      <i class="bi bi-bag"></i> Reserve a Table
    </a>
  </div>
</div>