<?php
if (!isset($lang)) {
    $allowed_langs = ['de', 'es', 'fr', 'en'];
    $lang = 'de';
    if (isset($_GET['lang']) && in_array($_GET['lang'], $allowed_langs)) {
        $lang = $_GET['lang'];
        setcookie('oase_lang', $lang, time() + 60*60*24*30, '/');
    } elseif (isset($_COOKIE['oase_lang']) && in_array($_COOKIE['oase_lang'], $allowed_langs)) {
        $lang = $_COOKIE['oase_lang'];
    }
}
if (!isset($t)) {
    $t = [];
    $lang_file = __DIR__ . '/../lang/' . $lang . '.php';
    if (file_exists($lang_file)) include $lang_file;
}
$current = basename($_SERVER['PHP_SELF'], '.php');

// CSS por página — solo carga el que corresponde
$page_css_map = [
    'servicios'   => '/assets/css/servicios.css',
    'sobre-edwin' => '/assets/css/edwin.css',
    'contacto'    => '/assets/css/contacto.css',
    'galeria'     => '/assets/css/galeria.css',
    'videos'      => '/assets/css/videos.css',
    'booking'     => '/assets/css/booking.css',
];
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $page_title ?? 'Oase — Friseur & Ästhetik Zürich' ?></title>
<meta name="description" content="<?= $page_desc ?? ($t['meta_desc'] ?? 'Oase Friseur & Ästhetik in Zürich') ?>">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">
<link rel="stylesheet" href="/assets/css/style.css">
<?php if (isset($page_css_map[$current])): ?>
<link rel="stylesheet" href="<?= $page_css_map[$current] ?>">
<?php endif; ?>
<?= $extra_css ?? '' ?>
</head>
<body>

<!-- RADIAL MENU (mobile/tablet only) -->
<div class="radial-overlay" id="radialOverlay">

  <div class="radial-logo">
    <img src="/assets/img/logo.png" alt="Oase" class="logo-img" style="height:44px; mix-blend-mode:lighten;">
  </div>

  <button class="radial-close" id="radialClose">&#10005;</button>

  <div class="radial-center"><div class="radial-center-dot"></div></div>

  <a href="/servicios.php" class="radial-item" id="rItem0">
    <div class="radial-circle">✂</div>
    <span class="radial-label"><?= $t['nav_services'] ?? 'Leistungen' ?></span>
  </a>
  <a href="/sobre-edwin.php" class="radial-item" id="rItem1">
    <div class="radial-circle">♟</div>
    <span class="radial-label"><?= $t['nav_about'] ?? 'Über uns' ?></span>
  </a>
  <a href="/galeria.php" class="radial-item" id="rItem2">
    <div class="radial-circle">⊞</div>
    <span class="radial-label"><?= $t['gallery_eyebrow'] ?? 'Galerie' ?></span>
  </a>
  
  <a href="/contacto.php" class="radial-item" id="rItem3">
    <div class="radial-circle">✆</div>
    <span class="radial-label"><?= $t['nav_contact'] ?? 'Kontakt' ?></span>
  </a>
  <a href="/videos.php" class="radial-item" id="rItem5">
  <div class="radial-circle">▶</div>
  <span class="radial-label"><?= $t['videos_eyebrow'] ?? 'Videos' ?></span>
</a>
  <a href="/booking.php" class="radial-item" id="rItem4">
    <div class="radial-circle">◇</div>
    <span class="radial-label"><?= $t['nav_book'] ?? 'Termin' ?></span>
  </a>

  <div class="radial-bottom">
    <div class="radial-lang">
      <a href="?lang=de" class="<?= $lang==='de'?'active':'' ?>">DE</a>
      <span>|</span>
      <a href="?lang=es" class="<?= $lang==='es'?'active':'' ?>">ES</a>
      <span>|</span>
      <a href="?lang=fr" class="<?= $lang==='fr'?'active':'' ?>">FR</a>
      <span>|</span>
      <a href="?lang=en" class="<?= $lang==='en'?'active':'' ?>">EN</a>
    </div>
  </div>
</div>

<!-- NAV -->
<nav id="navbar">
  <div class="nav-inner">
    <a href="/index.php" class="logo">
      <img src="/assets/img/logo.png" alt="Oase" class="logo-img">
      <div class="logo-text">
        <div class="logo-main">Oase</div>
        <div class="logo-sub">Friseur &amp; Ästhetik</div>
      </div>
    </a>
    <div class="nav-links">
      <a href="/servicios.php"   class="<?= $current==='servicios'   ?'nav-active':'' ?>"><?= $t['nav_services'] ?? 'Leistungen' ?></a>
      <a href="/sobre-edwin.php" class="<?= $current==='sobre-edwin' ?'nav-active':'' ?>"><?= $t['nav_about']   ?? 'Über uns'   ?></a>
      <a href="/galeria.php"     class="<?= $current==='galeria'     ?'nav-active':'' ?>"><?= $t['gallery_eyebrow'] ?? 'Galerie' ?></a>
      <a href="/videos.php" class="<?= $current==='videos' ?'nav-active':'' ?>"><?= $t['videos_eyebrow'] ?? 'Videos' ?></a>
      <a href="/contacto.php"    class="<?= $current==='contacto'    ?'nav-active':'' ?>"><?= $t['nav_contact'] ?? 'Kontakt'    ?></a>
    </div>
    <div class="nav-right">
      <div class="lang-switch">
        <a href="?lang=de" class="<?= $lang==='de'?'active':'' ?>">DE</a>
        <span>|</span>
        <a href="?lang=es" class="<?= $lang==='es'?'active':'' ?>">ES</a>
        <span>|</span>
        <a href="?lang=fr" class="<?= $lang==='fr'?'active':'' ?>">FR</a>
        <span>|</span>
        <a href="?lang=en" class="<?= $lang==='en'?'active':'' ?>">EN</a>
      </div>
      <a href="/booking.php" class="nav-cta"><?= $t['nav_book'] ?? 'Termin buchen' ?></a>
      <button class="nav-burger" id="navBurger">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</nav>