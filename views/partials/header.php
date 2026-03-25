<?php
// ============================================
// DINELOCAL · views/partials/header.php
// Reusable HTML <head> section
// Include at the top of every page BEFORE
// page-specific <style> tags
// ITC 6355 | Arjun & Ayomide
// Usage: <?php require_once 'views/partials/header.php'; ?>
// ============================================

// $pageTitle must be set before including this file
// e.g. $pageTitle = 'Menu — DineLocal';
if (!isset($pageTitle)) $pageTitle = 'DineLocal — Taste the Neighbourhood';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="description" content="DineLocal — Farm-to-table dining in the heart of Toronto. Locally sourced, seasonally inspired."/>
  <title><?= htmlspecialchars($pageTitle) ?></title>

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet"/>
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <!-- GSAP (for pages that use animations) -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
  <!-- Site CSS -->
  <link rel="stylesheet" href="/assets/css/style.css"/>

  <!-- CSS Variables (Internal CSS — rubric requirement) -->
  <style>
    :root {
      --dl-orange:   #C4551A;
      --dl-orange-d: #9E3A0E;
      --dl-cream:    #FBF0DC;
      --dl-brown:    #3B1A08;
      --dl-dark:     #0d0702;
      --dl-gold:     #E8A83E;
      --dl-serif:    'Cormorant Garamond', Georgia, serif;
      --dl-sans:     'Inter', system-ui, sans-serif;
    }
  </style>
</head>
<body>