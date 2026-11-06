<?php
$page_title = 'Servicios — Oase Friseur & Ästhetik';
require_once __DIR__ . '/config.php';
include 'includes/header.php';

$lang_col = 'nombre_' . $lang;

$stmt = $pdo->query('SELECT * FROM servicios WHERE activo = 1 ORDER BY categoria, id');
$servicios = $stmt->fetchAll();

$categorias = [];
foreach ($servicios as $s) {
    $categorias[$s['categoria']][] = $s;
}

$cat_labels = [
    'corte'       => ['de' => 'Schnitte',    'es' => 'Cortes',        'fr' => 'Coupes',    'en' => 'Cuts'],
    'color'       => ['de' => 'Farbe',       'es' => 'Color',         'fr' => 'Couleur',   'en' => 'Color'],
    'tratamiento' => ['de' => 'Pflege',      'es' => 'Tratamientos',  'fr' => 'Soins',     'en' => 'Treatments'],
    'estetica'    => ['de' => 'Ästhetik',    'es' => 'Estética',      'fr' => 'Esthétique','en' => 'Aesthetics'],
];
$cat_icons = [
    'corte'       => 'ti-cut',
    'color'       => 'ti-color-swatch',
    'tratamiento' => 'ti-droplet',
    'estetica'    => 'ti-sparkles',
];
?>

<div class="servicios-wrapper">

<!-- PAGE HEADER -->
<div class="page-header">
  <div class="page-header-inner">
    <div class="section-eyebrow" style="justify-content:center;">
      <span class="eyebrow-line"></span>
      <?= $t['nav_services'] ?? 'Leistungen' ?>
    </div>
    <h1 class="section-h2" style="text-align:center;">
      <?= $t['services_title_1'] ?? 'Für jeden Stil' ?>
      <br><em><?= $t['services_title_2'] ?? 'das Richtige' ?></em>
    </h1>
    <p class="body-text" style="text-align:center; max-width:500px; margin:1rem auto 0;">
      <?= $t['book_text'] ?? 'Professionelle Pflege für jeden Haartyp.' ?>
    </p>
  </div>
</div>

<!-- SERVICIOS POR CATEGORIA -->
<div class="servicios-page">
  <?php foreach ($categorias as $cat => $items): ?>
  <div class="servicios-cat reveal-up">
    <div class="servicios-cat-header">
      <div class="servicios-cat-icon"><i class="ti <?= $cat_icons[$cat] ?? 'ti-star' ?>"></i></div>
      <h2 class="servicios-cat-title"><?= $cat_labels[$cat][$lang] ?? ucfirst($cat) ?></h2>
    </div>
    <div class="servicios-list">
      <?php foreach ($items as $s):
        $nombre    = $s[$lang_col] ?? $s['nombre_de'];
        $precio_txt = $s['precio_texto'] ?? '';
        $precio    = 'CHF ' . number_format($s['precio'], 0);
      ?>
      <div class="servicio-row">
        <div class="servicio-nombre"><?= htmlspecialchars($nombre) ?></div>
        <div class="servicio-precio">
          <?php if ($precio_txt): ?>
          <span class="precio-desde"><?= htmlspecialchars($precio_txt) ?></span>
          <?php endif; ?>
          <span class="precio-num"><?= $precio ?></span>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- NOTA PRECIOS -->
<div class="precios-nota reveal-up">
  <i class="ti ti-info-circle"></i>
  <p><?= $t['prices_note'] ?? 'Die Preise können je nach Haarlänge, Haardichte und Arbeitsaufwand variieren. Gerne erstellen wir Ihnen ein individuelles Angebot.' ?></p>
</div>

<!-- CTA RESERVA -->
<div class="servicios-cta reveal-up">
  <h2 class="section-h2"><?= $t['book_title_1'] ?? 'Reserviere deinen' ?> <em><?= $t['book_title_2'] ?? 'Termin' ?></em></h2>
  <p class="body-text"><?= $t['book_text'] ?? 'Schnell und einfach online buchen.' ?></p>
  <a href="/booking.php" class="btn-primary" style="display:inline-block; text-decoration:none; margin-top:1.5rem;">
    <?= $t['btn_book'] ?? 'Termin buchen' ?>
  </a>
</div>

</div>

<?php include 'includes/footer.php'; ?>