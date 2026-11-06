<?php
$page_title = 'Videos — Oase Friseur & Ästhetik';
require_once __DIR__ . '/config.php';
include 'includes/header.php';

$stmt = $pdo->query('SELECT * FROM videos WHERE activo = 1 ORDER BY orden ASC, id DESC');
$videos = $stmt->fetchAll();
$titulo_col = 'titulo_' . $lang;
$desc_col   = 'descripcion_' . $lang;
?>

<div class="videos-wrapper">

  <!-- HEADER -->
  <div class="page-header">
    <div class="page-header-inner">
      <div class="section-eyebrow" style="justify-content:center;">
        <span class="eyebrow-line"></span>
        <?= $t['videos_eyebrow'] ?? 'Videos' ?>
      </div>
      <h1 class="section-h2" style="text-align:center;">
        <?= $t['videos_title_1'] ?? 'Unsere' ?> <em><?= $t['videos_title_2'] ?? 'Transformationen' ?></em>
      </h1>
      <p class="body-text" style="text-align:center; max-width:500px; margin:1rem auto 0;">
        <?= $t['videos_desc'] ?? 'Entdecke unsere Arbeit in Bewegung.' ?>
      </p>
    </div>
  </div>

  <!-- VIDEOS -->
  <div class="videos-page">
    <?php if (empty($videos)): ?>
    <div class="videos-empty">
      <i class="ti ti-video-off" style="font-size:48px; color:#2a2318;"></i>
      <p class="body-text" style="margin-top:1.5rem;"><?= $t['videos_empty'] ?? 'Bald verfügbar — próximamente.' ?></p>
    </div>
    <?php else: ?>
    <div class="videos-grid">
      <?php foreach ($videos as $i => $v):
        $titulo = $v[$titulo_col] ?: ($v['titulo_de'] ?: '');
        $desc   = $v[$desc_col]   ?: ($v['descripcion_de'] ?: '');
      ?>
      <div class="video-card reveal-up" style="--delay:<?= ($i % 3) * 0.1 ?>s">
        <div class="video-wrap">
          <video src="/uploads/videos/<?= htmlspecialchars($v['archivo']) ?>"
                 loop muted playsinline preload="metadata">
          </video>
          <div class="video-play-btn" onclick="toggleVideo(this)">
            <i class="ti ti-player-play"></i>
          </div>
          <?php if ($v['categoria'] && $v['categoria'] !== 'general'): ?>
          <div class="video-badge"><?= htmlspecialchars(ucfirst($v['categoria'])) ?></div>
          <?php endif; ?>
        </div>
        <?php if ($titulo || $desc): ?>
        <div class="video-info">
          <?php if ($titulo): ?><div class="video-titulo"><?= htmlspecialchars($titulo) ?></div><?php endif; ?>
          <?php if ($desc): ?><div class="video-desc"><?= htmlspecialchars($desc) ?></div><?php endif; ?>
          <?php if ($v['tags']): ?>
          <div class="video-tags">
            <?php foreach (array_slice(explode(',', $v['tags']), 0, 3) as $tag): ?>
            <span><?= htmlspecialchars(trim($tag)) ?></span>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </div>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

</div>

<?php
$extra_js = '<script>
function toggleVideo(btn) {
  const video = btn.previousElementSibling;
  if (video.paused) {
    document.querySelectorAll(".video-wrap video").forEach(v => {
      v.pause();
      const b = v.nextElementSibling;
      if (b) { b.innerHTML = "<i class=\"ti ti-player-play\"></i>"; b.classList.remove("playing"); }
    });
    video.play();
    btn.innerHTML = "<i class=\"ti ti-player-pause\"></i>";
    btn.classList.add("playing");
  } else {
    video.pause();
    btn.innerHTML = "<i class=\"ti ti-player-play\"></i>";
    btn.classList.remove("playing");
  }
}
</script>';
?>

<?php include 'includes/footer.php'; ?>