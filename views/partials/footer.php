<?php
// ============================================
// DINELOCAL · views/partials/footer.php
// Reusable site footer
// Include at the bottom of every page
// ITC 6355 | Arjun & Ayomide
// Usage: <?php require_once 'views/partials/footer.php'; ?>
// ============================================
?>

<footer id="site-footer" style="background:#0a0502; border-top:1px solid rgba(232,168,62,.08);">
  <div class="container-xxl px-3 px-md-5 py-5">

    <!-- Main footer grid -->
    <div class="row g-4 pb-4 border-bottom" style="border-color:rgba(232,168,62,.1)!important">

      <!-- Brand column -->
      <div class="col-12 col-md-5 col-lg-4">
        <h3 style="font-family:var(--dl-serif);font-size:1.4rem;font-weight:700;color:var(--dl-cream);margin-bottom:.6rem">
          DineLocal
        </h3>
        <p style="font-size:.76rem;color:rgba(251,240,220,.44);line-height:1.75;max-width:265px;margin-bottom:1.3rem">
          Farm-to-table dining in the heart of Toronto since 2012. Every ingredient tells a story.
        </p>
        <div class="d-flex gap-2">
          <a href="#" class="ft-soc"><i class="bi bi-instagram"></i></a>
          <a href="#" class="ft-soc"><i class="bi bi-facebook"></i></a>
          <a href="#" class="ft-soc"><i class="bi bi-twitter-x"></i></a>
        </div>
      </div>

      <!-- Connect column -->
      <div class="col-6 col-md-2">
        <h4 class="ft-head">CONNECT</h4>
        <div class="d-flex flex-column gap-2">
          <a href="#" class="ft-link"><i class="bi bi-instagram"></i> Instagram</a>
          <a href="#" class="ft-link"><i class="bi bi-facebook"></i> Facebook</a>
          <a href="#" class="ft-link"><i class="bi bi-twitter-x"></i> Twitter</a>
        </div>
      </div>

      <!-- Visit column -->
      <div class="col-6 col-md-2">
        <h4 class="ft-head">VISIT</h4>
        <div class="d-flex flex-column gap-2">
          <p class="ft-link mb-0"><i class="bi bi-geo-alt"></i> 123 Queen St W</p>
          <p class="ft-link mb-0"><i class="bi bi-telephone"></i> (416) 555-0192</p>
          <p class="ft-link mb-0"><i class="bi bi-clock"></i> <strong style="color:#fff">11am – 10pm</strong></p>
          <p class="ft-link mb-0"><i class="bi bi-envelope"></i> hello@dinelocal.ca</p>
        </div>
      </div>

      <!-- Navigate column -->
      <div class="col-6 col-md-3 col-lg-2">
        <h4 class="ft-head">NAVIGATE</h4>
        <div class="d-flex flex-column gap-2">
          <a href="/index.php"        class="ft-link"><i class="bi bi-house"></i> Home</a>
          <a href="/menu.php"         class="ft-link"><i class="bi bi-card-list"></i> Menu</a>
          <a href="/reservations.php" class="ft-link"><i class="bi bi-calendar2-check"></i> Reservations</a>
          <a href="/about.php"        class="ft-link"><i class="bi bi-info-circle"></i> About</a>
          <a href="/login.php"        class="ft-link"><i class="bi bi-person"></i> My Account</a>
        </div>
      </div>

    </div>

    <!-- Bottom bar -->
    <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-2 pt-4">
      <p style="font-size:.68rem;color:rgba(251,240,220,.26);margin:0">
        © <?= date('Y') ?> DineLocal Toronto. All rights reserved.
        &nbsp;·&nbsp;
        <a href="/admin/login.php" style="color:rgba(251,240,220,.18);text-decoration:none;font-size:.65rem">Staff Login</a>
      </p>
      <div class="d-flex gap-2" style="font-size:.88rem;color:rgba(251,240,220,.18)">
        <i class="bi bi-fork-knife"></i>
        <i class="bi bi-leaf"></i>
      </div>
    </div>

  </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Site JS -->
<script src="/assets/js/main.js"></script>
<script src="/assets/js/validation.js"></script>
<script src="/assets/js/animations.js"></script>
</body>
</html>