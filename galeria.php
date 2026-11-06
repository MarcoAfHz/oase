<?php
$page_title = 'Galerie — Oase Friseur & Ästhetik';
require_once __DIR__ . '/config.php';
include 'includes/header.php';

$stmt = $pdo->query('
    SELECT g.*,
           (SELECT archivo FROM galeria_fotos WHERE grupo_id = g.id AND rol = "antes" LIMIT 1) as foto_antes,
           (SELECT archivo FROM galeria_fotos WHERE grupo_id = g.id AND rol = "despues" LIMIT 1) as foto_despues,
           (SELECT archivo FROM galeria_fotos WHERE grupo_id = g.id ORDER BY orden ASC, id ASC LIMIT 1) as foto_portada,
           (SELECT COUNT(*) FROM galeria_fotos WHERE grupo_id = g.id) as num_fotos
    FROM galeria_grupos g
    WHERE g.activo = 1
    ORDER BY g.orden ASC, g.id DESC
');
$grupos = $stmt->fetchAll();

$carrusel_fotos = [];
if (!empty($grupos)) {
    $ids = implode(',', array_column($grupos, 'id'));
    $stmt2 = $pdo->query("SELECT * FROM galeria_fotos WHERE grupo_id IN ($ids) ORDER BY orden ASC, id ASC");
    foreach ($stmt2->fetchAll() as $f) {
        $carrusel_fotos[$f['grupo_id']][] = $f;
    }
}

$cat_labels = [
    'corte'       => ['de'=>'Schnitt',   'es'=>'Corte',       'fr'=>'Coupe',       'en'=>'Cut'],
    'color'       => ['de'=>'Farbe',     'es'=>'Color',       'fr'=>'Couleur',     'en'=>'Color'],
    'tratamiento' => ['de'=>'Pflege',    'es'=>'Tratamiento', 'fr'=>'Soin',        'en'=>'Treatment'],
    'estetica'    => ['de'=>'Ästhetik',  'es'=>'Estética',    'fr'=>'Esthétique',  'en'=>'Aesthetics'],
    'general'     => ['de'=>'Allgemein', 'es'=>'General',     'fr'=>'Général',     'en'=>'General'],
];
?>

<div class="galeria-wrapper">

  <div class="page-header">
    <div class="page-header-inner">
      <div class="section-eyebrow" style="justify-content:center;">
        <span class="eyebrow-line"></span>
        <?= $t['gallery_eyebrow'] ?? 'Galerie' ?>
      </div>
      <h1 class="section-h2" style="text-align:center;">
        <?= $t['gallery_title_1'] ?? 'Unsere' ?> <em><?= $t['gallery_title_2'] ?? 'Arbeiten' ?></em>
      </h1>
    </div>
  </div>

  <?php if (!empty($grupos)): ?>

  <div class="gal-filtros">
    <button class="gal-filtro active" data-cat="all">
      <?= ['de'=>'Alle','es'=>'Todos','fr'=>'Tous','en'=>'All'][$lang] ?? 'Todos' ?>
    </button>
    <?php
    $cats_usadas = array_unique(array_column($grupos, 'categoria'));
    foreach ($cats_usadas as $cat):
      if (!isset($cat_labels[$cat])) continue;
    ?>
    <button class="gal-filtro" data-cat="<?= $cat ?>">
      <?= $cat_labels[$cat][$lang] ?? ucfirst($cat) ?>
    </button>
    <?php endforeach; ?>
    <button class="gal-filtro" data-cat="antes_despues">
      <?= ['de'=>'Vorher/Nachher','es'=>'Antes & Después','fr'=>'Avant/Après','en'=>'Before & After'][$lang] ?? 'Antes & Después' ?>
    </button>
  </div>

  <div class="gal-masonry-wrap">
    <div class="gal-masonry" id="galMasonry">
      <?php foreach ($grupos as $g):
        $portada   = $g['tipo'] === 'antes_despues' ? ($g['foto_antes'] ?? $g['foto_portada']) : $g['foto_portada'];
        if (!$portada) continue;
        $titulo    = $g['titulo_' . $lang] ?: ($g['titulo_de'] ?: '');
        $desc      = $g['descripcion_' . $lang] ?: ($g['descripcion_de'] ?: '');
        $cat_label = $cat_labels[$g['categoria']][$lang] ?? $g['categoria'];
        $tags_arr  = $g['tags'] ? explode(',', $g['tags']) : [];
      ?>
      <div class="gal-item reveal-up"
           data-cat="<?= $g['categoria'] ?>"
           data-tipo="<?= $g['tipo'] ?>"
           data-id="<?= $g['id'] ?>"
           onclick="abrirGrupo(<?= $g['id'] ?>)">

        <div class="gal-item-inner">
          <img class="gal-img-principal"
               src="/uploads/<?= htmlspecialchars($portada) ?>"
               alt="<?= htmlspecialchars($titulo ?: 'Oase') ?>"
               loading="lazy">

          <?php if ($g['tipo'] === 'antes_despues' && $g['foto_antes']): ?>
          <img class="gal-img-antes"
               src="/uploads/<?= htmlspecialchars($g['foto_despues'] ?? $g['foto_portada']) ?>"
               alt="Después" loading="lazy">
          <?php endif; ?>

          <?php if ($g['tipo'] === 'carrusel'):
            $siguientes = array_slice($carrusel_fotos[$g['id']] ?? [], 1, 4);
            foreach ($siguientes as $sf): ?>
          <img class="gal-img-carrusel"
               src="/uploads/<?= htmlspecialchars($sf['archivo']) ?>"
               alt="" loading="lazy">
          <?php endforeach; endif; ?>

          <div class="gal-overlay">
            <?php if ($g['tipo'] === 'antes_despues'): ?>
            <div class="gal-overlay-icon"><i class="ti ti-arrows-left-right"></i></div>
            <?php elseif ($g['tipo'] === 'carrusel'): ?>
            <div class="gal-overlay-icon"><i class="ti ti-layout-grid"></i></div>
            <?php else: ?>
            <div class="gal-overlay-icon"><i class="ti ti-zoom-in"></i></div>
            <?php endif; ?>
            <?php if ($titulo): ?>
            <div class="gal-overlay-titulo"><?= htmlspecialchars($titulo) ?></div>
            <?php endif; ?>
            <?php if (!empty($tags_arr)): ?>
            <div class="gal-overlay-tags">
              <?php foreach (array_slice($tags_arr, 0, 3) as $tag): ?>
              <span><?= htmlspecialchars(trim($tag)) ?></span>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
          </div>

          <div class="gal-badges">
            <span class="gal-badge-cat"><?= $cat_label ?></span>
            <?php if ($g['tipo'] === 'antes_despues'): ?>
            <span class="gal-badge-tipo tipo-ad">↔</span>
            <?php elseif ($g['tipo'] === 'carrusel'): ?>
            <span class="gal-badge-tipo tipo-car"><?= $g['num_fotos'] ?></span>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <?php else: ?>
  <div style="text-align:center;padding:6rem 2rem;">
    <i class="ti ti-photo-off" style="font-size:48px;color:#2a2318;display:block;margin-bottom:1rem;"></i>
    <p class="body-text"><?= ['de'=>'Bald verfügbar.','es'=>'Próximamente.','fr'=>'Bientôt disponible.','en'=>'Coming soon.'][$lang] ?? 'Próximamente.' ?></p>
  </div>
  <?php endif; ?>

</div>

<!-- MODAL -->
<div class="gal-modal" id="galModal">
  <button class="gal-modal-close" onclick="cerrarModal()">✕</button>

  <div class="gal-modal-foto" id="modalFoto">
    <img id="modalFotoImg" src="" alt="">
    <div class="gal-modal-info" id="modalFotoInfo"></div>
  </div>

  <div class="gal-modal-ad" id="modalAD">
    <div class="gal-ad-comparador" id="adComparador">
      <img class="ad-img-antes" id="adAntes" src="" alt="Antes">
      <img class="ad-img-despues" id="adDespues" src="" alt="Después">
      <div class="ad-slider-line" id="adLine">
        <div class="ad-slider-handle"><i class="ti ti-arrows-left-right"></i></div>
      </div>
      <div class="ad-label-antes">ANTES</div>
      <div class="ad-label-despues">DESPUÉS</div>
    </div>
    <div class="gal-modal-info" id="modalADInfo"></div>
  </div>

  <div class="gal-modal-car" id="modalCar">
    <button class="car-btn car-prev" onclick="carNav(-1)">&#8592;</button>
    <button class="car-btn car-next" onclick="carNav(1)">&#8594;</button>
    <img id="carImg" src="" alt="">
    <div class="car-dots" id="carDots"></div>
    <div class="gal-modal-info" id="modalCarInfo"></div>
  </div>
</div>

<?php
$titulo_col = 'titulo_' . $lang;
$desc_col   = 'descripcion_' . $lang;
$extra_js = '<script>
const grupos = ' . json_encode(array_map(function($g) use ($carrusel_fotos, $titulo_col, $desc_col) {
    return [
        'id'          => $g['id'],
        'tipo'        => $g['tipo'],
        'titulo'      => $g[$titulo_col] ?: ($g['titulo_de'] ?? ''),
        'descripcion' => $g[$desc_col]   ?: ($g['descripcion_de'] ?? ''),
        'foto_antes'  => $g['foto_antes']  ? '/uploads/' . $g['foto_antes']  : null,
        'foto_despues'=> ($g['foto_despues'] ?? $g['foto_portada']) ? '/uploads/' . ($g['foto_despues'] ?? $g['foto_portada']) : null,
        'portada'     => $g['foto_portada'] ? '/uploads/' . $g['foto_portada'] : null,
        'fotos'       => array_map(fn($f) => '/uploads/' . $f['archivo'], $carrusel_fotos[$g['id']] ?? []),
    ];
}, $grupos)) . ';

let carActual = 0, carFotos = [];

document.querySelectorAll(".gal-filtro").forEach(btn => {
    btn.addEventListener("click", function() {
        document.querySelectorAll(".gal-filtro").forEach(b => b.classList.remove("active"));
        this.classList.add("active");
        const cat = this.dataset.cat;
        document.querySelectorAll(".gal-item").forEach(item => {
            if (cat === "all") item.classList.remove("hidden");
            else if (cat === "antes_despues") item.classList.toggle("hidden", item.dataset.tipo !== "antes_despues");
            else item.classList.toggle("hidden", item.dataset.cat !== cat);
        });
    });
});

document.querySelectorAll(".gal-item[data-tipo=carrusel]").forEach(item => {
    const imgs = item.querySelectorAll(".gal-img-carrusel");
    if (!imgs.length) return;
    let idx = 0, timer = null;
    item.addEventListener("mouseenter", () => {
        timer = setInterval(() => {
            imgs.forEach(i => i.style.opacity = 0);
            idx = (idx + 1) % imgs.length;
            imgs[idx].style.opacity = 1;
        }, 800);
    });
    item.addEventListener("mouseleave", () => {
        clearInterval(timer);
        imgs.forEach(i => i.style.opacity = 0);
        idx = 0;
    });
});

function abrirGrupo(id) {
    const g = grupos.find(x => x.id == id);
    if (!g) return;
    document.getElementById("galModal").classList.add("open");
    document.body.style.overflow = "hidden";
    ["modalFoto","modalAD","modalCar"].forEach(id => document.getElementById(id).classList.remove("active"));
    if (g.tipo === "foto") {
        document.getElementById("modalFoto").classList.add("active");
        document.getElementById("modalFotoImg").src = g.portada;
        document.getElementById("modalFotoInfo").innerHTML = g.titulo ? "<h3>" + g.titulo + "</h3>" + (g.descripcion ? "<p>" + g.descripcion + "</p>" : "") : "";
    } else if (g.tipo === "antes_despues") {
    document.getElementById("modalAD").classList.add("active");
    document.getElementById("adAntes").src = g.foto_antes || g.portada;
    document.getElementById("adDespues").src = g.foto_despues || g.portada;
    document.getElementById("modalADInfo").innerHTML = g.titulo ? "<h3>" + g.titulo + "</h3>" + (g.descripcion ? "<p>" + g.descripcion + "</p>" : "") : "";
    initADSlider();
} else if (g.tipo === "carrusel") {
    carFotos = g.fotos; carActual = 0;
    document.getElementById("modalCar").classList.add("active");
    renderCarrusel();
    document.getElementById("modalCarInfo").innerHTML = g.titulo ? "<h3>" + g.titulo + "</h3>" + (g.descripcion ? "<p>" + g.descripcion + "</p>" : "") : "";
}
}

function cerrarModal() {
    document.getElementById("galModal").classList.remove("open");
    document.body.style.overflow = "";
}
function renderCarrusel() {
    document.getElementById("carImg").src = carFotos[carActual];
    const dots = document.getElementById("carDots");
    dots.innerHTML = "";
    carFotos.forEach((_, i) => {
        const d = document.createElement("div");
        d.className = "car-dot" + (i === carActual ? " active" : "");
        d.onclick = () => { carActual = i; renderCarrusel(); };
        dots.appendChild(d);
    });
}
function carNav(dir) { carActual = (carActual + dir + carFotos.length) % carFotos.length; renderCarrusel(); }
function initADSlider() {
    const line = document.getElementById("adLine");
    const despues = document.getElementById("adDespues");
    const comp = document.getElementById("adComparador");
    let dragging = false;
    function setPos(x) {
        const rect = comp.getBoundingClientRect();
        let pct = Math.max(0, Math.min(100, ((x - rect.left) / rect.width) * 100));
        line.style.left = pct + "%";
        despues.style.clipPath = "inset(0 0 0 " + pct + "%)";
    }
    line.addEventListener("mousedown", e => { dragging = true; e.preventDefault(); });
    document.addEventListener("mousemove", e => { if (dragging) setPos(e.clientX); });
    document.addEventListener("mouseup", () => { dragging = false; });
    line.addEventListener("touchstart", () => { dragging = true; });
    document.addEventListener("touchmove", e => { if (dragging) setPos(e.touches[0].clientX); });
    document.addEventListener("touchend", () => { dragging = false; });
}
document.addEventListener("keydown", e => {
    if (e.key === "Escape") cerrarModal();
    if (e.key === "ArrowRight") carNav(1);
    if (e.key === "ArrowLeft") carNav(-1);
});
document.getElementById("galModal").addEventListener("click", e => {
    if (e.target === document.getElementById("galModal")) cerrarModal();
});
</script>';
?>
<?php include 'includes/footer.php'; ?>