<?php
$page_title = 'Oase — Friseur & Ästhetik Zürich';
$content_file = __DIR__ . '/data/content.json';
$content = file_exists($content_file) ? json_decode(file_get_contents($content_file), true) : [];
require_once __DIR__ . '/config.php';
include 'includes/header.php';

$lang_col = 'nombre_' . $lang;
$stmt_srv = $pdo->query('SELECT * FROM servicios WHERE activo = 1 ORDER BY id');
$servicios_all = $stmt_srv->fetchAll();
?>

<div class="page-wrap">

<!-- ===================== HERO ===================== -->
<section class="hero" id="home">
  <div class="hero-bg">
    <video autoplay muted loop playsinline class="hero-video hero-video-desktop">
      <source src="/assets/video/salon-hero-horizontal.mp4" type="video/mp4">
    </video>
    <video autoplay muted loop playsinline class="hero-video hero-video-mobile">
      <source src="/assets/video/salon-hero-vertical.mp4" type="video/mp4">
    </video>
  </div>
  <div class="hero-overlay"></div>
  <div class="hero-content">
    <div class="hero-left reveal-left">
      <div class="hero-eyebrow"><span class="eyebrow-line"></span>Zürich — Friseur &amp; Ästhetik</div>
      <h1 class="hero-title">
        <?= $t['hero_title_1'] ?? 'Schönheit,' ?>
        <br><em><?= $t['hero_title_2'] ?? 'die man spürt' ?></em>
      </h1>
      <p class="hero-subtitle"><?= $t['hero_subtitle'] ?? 'Premium Haarpflege & Ästhetik im Herzen von Zürich.' ?></p>
      <div class="hero-btns">
        <button class="btn-primary" onclick="smoothScroll('buchen')"><?= $t['btn_book'] ?? 'Termin buchen' ?></button>
        <button class="btn-secondary" onclick="smoothScroll('leistungen')"><?= $t['btn_services'] ?? 'Leistungen' ?> &#8594;</button>
      </div>
    </div>
    <div class="hero-right reveal-right">
      <div class="hero-stat-card">
        <div class="hero-stat-big" data-count="20">0</div>
        <div class="hero-stat-label">+ <?= $t['stat_years'] ?? 'Jahre Erfahrung' ?></div>
      </div>
      <div class="hero-stat-card" style="margin-top:1.5rem;">
        <div class="hero-rating-stars">★★★★★</div>
        <div class="hero-rating-text">4.9 · <?= $t['stat_reviews'] ?? 'über 320 Bewertungen' ?></div>
      </div>
      <div class="hero-stat-card" style="margin-top:1.5rem;">
        <div class="hero-stat-big" data-count="3200">0</div>
        <div class="hero-stat-label"><?= $t['stat_clients'] ?? 'zufriedene Kunden' ?></div>
      </div>
    </div>
  </div>
  <div class="scroll-indicator"><div class="scroll-dot"></div></div>
</section>

<!-- ===================== TRUST STRIP ===================== -->
<div class="trust-strip">
  <div class="trust-item reveal-up" style="--delay:0s">
    <div class="trust-icon"><i class="ti ti-leaf"></i></div>
    <div class="trust-title"><?= $t['trust_1_title'] ?? 'Premium-Produkte' ?></div>
    <div class="trust-sub"><?= $t['trust_1_sub'] ?? 'Professionelle Marken' ?></div>
  </div>
  <div class="trust-item reveal-up" style="--delay:0.1s">
    <div class="trust-icon"><i class="ti ti-award"></i></div>
    <div class="trust-title"><?= $t['trust_2_title'] ?? 'Experten-Team' ?></div>
    <div class="trust-sub"><?= $t['trust_2_sub'] ?? 'Zertifizierte Spezialisten' ?></div>
  </div>
  <div class="trust-item reveal-up" style="--delay:0.2s">
    <div class="trust-icon"><i class="ti ti-calendar-event"></i></div>
    <div class="trust-title"><?= $t['trust_3_title'] ?? 'Online buchen' ?></div>
    <div class="trust-sub"><?= $t['trust_3_sub'] ?? 'Einfach & schnell' ?></div>
  </div>
  <div class="trust-item reveal-up" style="--delay:0.3s">
    <div class="trust-icon"><i class="ti ti-mood-smile"></i></div>
    <div class="trust-title"><?= $t['trust_4_title'] ?? 'Zufriedenheitsgarantie' ?></div>
    <div class="trust-sub"><?= $t['trust_4_sub'] ?? '100% Qualität' ?></div>
  </div>
</div>

<!-- ===================== ABOUT ===================== -->
<section class="about" id="ueber">
  <div class="about-images reveal-left">
    <div class="about-images-grid">
      <img src="assets/img/view1.png" alt="Salon 1" class="img-hover">
      <img src="assets/img/view4.png" alt="Salon 2" class="img-hover">
      <img src="assets/img/view3.png" alt="Salon 3" class="img-hover">
      <img src="assets/img/view2.png" alt="Salon 4" class="img-hover">
    </div>
  </div>
  <div class="about-text reveal-right">
    <div class="section-eyebrow"><span class="eyebrow-line"></span><?= $t['about_eyebrow'] ?? 'Über uns' ?></div>
    <h2 class="section-h2"><?= $t['about_title_1'] ?? 'Die Kunst des' ?><br><em><?= $t['about_title_2'] ?? 'perfekten Stils' ?></em></h2>
    <div class="gold-line"></div>
    <p class="body-text"><?= $t['about_text'] ?? 'Im Oase verbinden wir Handwerk, Kreativität und erstklassige Produkte.' ?></p>
    <div class="tag-row">
      <span class="tag"><?= $t['tag_cut'] ?? 'Schnitt' ?></span>
      <span class="tag"><?= $t['tag_color'] ?? 'Farbe' ?></span>
      <span class="tag"><?= $t['tag_care'] ?? 'Pflege' ?></span>
      <span class="tag"><?= $t['tag_aesthetic'] ?? 'Ästhetik' ?></span>
    </div>
  </div>
</section>


<!-- ===================== GALERÍA CARRUSEL ===================== -->
<section class="galeria-section" id="galeria">
  <div class="galeria-header reveal-up">
    <div class="section-eyebrow" style="justify-content:center;"><span class="eyebrow-line"></span><?= $t['gallery_eyebrow'] ?? 'Galerie' ?></div>
    <h2 class="section-h2" style="text-align:center;"><?= $t['gallery_title_1'] ?? 'Unsere' ?> <em><?= $t['gallery_title_2'] ?? 'Arbeiten' ?></em></h2>
  </div>
  <div class="galeria-wrap">
    <div class="galeria-track" id="galeriaTrack">
      <?php
$titulo_col_idx = 'titulo_' . $lang;
$stmt_gal = $pdo->query('
    SELECT gf.archivo, gg.titulo_de, gg.titulo_es, gg.titulo_fr, gg.titulo_en, gg.categoria
    FROM galeria_grupos gg
    JOIN galeria_fotos gf ON gf.id = (
        SELECT id FROM galeria_fotos
        WHERE grupo_id = gg.id
        AND rol IN ("normal","despues")
        ORDER BY orden ASC, id ASC
        LIMIT 1
    )
    WHERE gg.activo = 1
    ORDER BY gg.orden ASC, gg.id DESC
    LIMIT 10
');
$fotos_index = $stmt_gal->fetchAll();
if (!empty($fotos_index)):
  foreach ($fotos_index as $foto):
    $titulo_idx = $foto[$titulo_col_idx] ?: ($foto['titulo_de'] ?: '');
?>
      <div class="galeria-slide">
        <div class="galeria-img-wrap">
          <img src="/uploads/<?= htmlspecialchars($foto['archivo']) ?>"
               alt="<?= htmlspecialchars($titulo_idx ?: 'Oase') ?>"
               class="img-hover">
          <?php if ($titulo_idx): ?>
          <div class="galeria-caption"><?= htmlspecialchars($titulo_idx) ?></div>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach;
      else: ?>
      <div class="galeria-slide">
        <div class="galeria-img-wrap" style="background:#1a1510;min-height:300px;display:flex;align-items:center;justify-content:center;">
          <p style="color:#4a4035;font-family:-apple-system,sans-serif;font-size:13px;"><?= ['de'=>'Bald verfügbar','es'=>'Próximamente','fr'=>'Bientôt disponible','en'=>'Coming soon'][$lang] ?? 'Próximamente' ?></p>
        </div>
      </div>
      <?php endif; ?>
    </div>
    <div class="galeria-controls">
      <button class="slider-btn" id="galeriaPrev">&#8592;</button>
      <div class="slider-dots" id="galeriaDots"></div>
      <button class="slider-btn" id="galeriaNext">&#8594;</button>
    </div>
  </div>
  <div style="text-align:center; margin-top:2.5rem;">
    <a href="/galeria.php" class="btn-secondary" style="display:inline-flex;"><?= $t['gallery_more'] ?? 'Alle Fotos ansehen' ?> &#8594;</a>
  </div>
</section>

<!-- ===================== SOBRE EDWIN ===================== -->
<section class="edwin-section" id="edwin">
  <div class="edwin-inner">
    <div class="edwin-img reveal-left">
      <div class="edwin-img-wrap">
        <img src="assets/img/perfilEdwin.png" alt="Edwin">
        <div class="edwin-img-badge">
          <span class="badge-num" data-count="20">0</span>
          <span class="badge-label"><?= $t['stat_years'] ?? 'Jahre' ?></span>
        </div>
      </div>
    </div>
    <div class="edwin-text reveal-right">
      <div class="section-eyebrow"><span class="eyebrow-line"></span><?= $t['edwin_eyebrow'] ?? 'Der Meister' ?></div>
      <h2 class="section-h2">Edwin<br><em><?= $t['edwin_title'] ?? 'Friseurmeister & Ästhetiker' ?></em></h2>
      <div class="gold-line"></div>
      <p class="body-text"><?= $t['edwin_bio'] ?? 'Mit über 20 Jahren Erfahrung hat sich Edwin als Spezialist für krauses, lockiges und Afro-Haar einen Namen gemacht. Zertifizierter Kolorist der L\'Oréal Akademie Paris.' ?></p>
      <div class="edwin-skills">
        <div class="edwin-skill">
          <div class="skill-icon"><i class="ti ti-wave-sine"></i></div>
          <div class="skill-info">
            <div class="skill-name"><?= ['de'=>'Krauses & Afro-Haar','es'=>'Cabello crespo & afro','fr'=>'Cheveux crépus & afro','en'=>'Curly & afro hair'][$lang] ?? 'Cabello crespo & afro' ?></div>
            <div class="skill-desc"><?= ['de'=>'Spezialist für krauses, lockiges Haar jeder Textur','es'=>'Especialista en cabello crespo, rizado y voluminoso','fr'=>'Spécialiste cheveux crépus & bouclés','en'=>'Specialist in curly & voluminous hair'][$lang] ?? '' ?></div>
          </div>
        </div>
        <div class="edwin-skill">
          <div class="skill-icon"><i class="ti ti-color-swatch"></i></div>
          <div class="skill-info">
            <div class="skill-name"><?= $t['skill_1'] ?? 'Farbspezialist' ?></div>
            <div class="skill-desc"><?= ['de'=>'Zertifizierter Kolorist — L\'Oréal Akademie Paris','es'=>'Colorimetrista — Academia L\'Oréal París','fr'=>'Coloriste — Académie L\'Oréal Paris','en'=>'Colourist — L\'Oréal Academy Paris'][$lang] ?? '' ?></div>
          </div>
        </div>
        <div class="edwin-skill">
          <div class="skill-icon"><i class="ti ti-heart"></i></div>
          <div class="skill-info">
            <div class="skill-name"><?= ['de'=>'Brautfrisuren & Extensions','es'=>'Peinados de novia & extensiones','fr'=>'Coiffures mariée & extensions','en'=>'Bridal styles & extensions'][$lang] ?? 'Peinados de novia' ?></div>
            <div class="skill-desc"><?= ['de'=>'Hochsteckfrisuren, Moños & Haarverkauf','es'=>'Recogidos, moños & venta de extensiones','fr'=>'Chignons & vente extensions','en'=>'Updos, buns & hair extensions'][$lang] ?? '' ?></div>
          </div>
        </div>
        <div class="edwin-skill">
          <div class="skill-icon"><i class="ti ti-world"></i></div>
          <div class="skill-info">
            <div class="skill-name"><?= ['de'=>'Internationale Erfahrung','es'=>'Experiencia internacional','fr'=>'Expérience internationale','en'=>'International experience'][$lang] ?? 'Experiencia internacional' ?></div>
            <div class="skill-desc">Panamá · Chile · España · Zürich</div>
          </div>
        </div>
      </div>
      <a href="/sobre-edwin.php" class="btn-secondary" style="display:inline-block; margin-top:2rem; text-decoration:none; margin-right:1rem;"><?= ['de'=>'Mehr über Edwin','es'=>'Más sobre Edwin','fr'=>'En savoir plus','en'=>'More about Edwin'][$lang] ?? 'Más sobre Edwin' ?> &#8594;</a>
      <a href="/booking.php" class="btn-primary" style="display:inline-block; margin-top:2rem; text-decoration:none;"><?= $t['btn_book'] ?? 'Termin buchen' ?></a>
    </div>
  </div>
</section>

<!-- ===================== BOOKING ===================== -->
<section class="booking-section" id="buchen">
  <div class="booking-inner reveal-up">
    <div class="booking-grid">
      <div>
        <div class="section-eyebrow"><span class="eyebrow-line"></span><?= $t['book_eyebrow'] ?? 'Online buchen' ?></div>
        <h2 class="section-h2"><?= $t['book_title_1'] ?? 'Reserviere deinen' ?><br><em><?= $t['book_title_2'] ?? 'Termin' ?></em></h2>
        <p class="body-text" style="margin-top:1rem;"><?= $t['book_text'] ?? 'Schnell, einfach und in weniger als einer Minute.' ?></p>
        <div class="contact-info">
          <div class="contact-row"><i class="ti ti-map-pin"></i> Thurgauerstrasse 117, 8152 Glattpark (Opfikon)</div>
          <div class="contact-row"><i class="ti ti-phone"></i> +41 76 713 33 41</div>
          <div class="contact-row"><i class="ti ti-clock"></i> Mo–Fr 9:00–20:00 · Sa 9:00–17:00</div>
        </div>
      </div>
      <div class="booking-form" id="bookingForm">
        <div class="form-step" id="step1">
          <div class="form-group">
            <label><?= $t['form_service'] ?? 'Leistung' ?></label>
            <select id="selectServicio" onchange="nextStep(1)">
              <option value="">— <?= $t['form_service'] ?? 'Leistung wählen' ?> —</option>
              <?php foreach ($servicios_all as $s): ?>
              <option value="<?= $s['id'] ?>" data-duracion="<?= $s['duracion_min'] ?>">
                <?= htmlspecialchars($s[$lang_col] ?? $s['nombre_de']) ?> — CHF <?= number_format($s['precio'],2) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-step" id="step2" style="display:none;">
          <div class="form-group">
            <label><?= $t['form_date'] ?? 'Datum' ?></label>
            <input type="date" id="selectFecha" min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
          </div>
        </div>
        <div class="form-step" id="step3" style="display:none;">
          <div class="form-group">
            <label><?= $t['form_time'] ?? 'Uhrzeit' ?></label>
            <div class="horas-grid" id="horasGrid"></div>
          </div>
        </div>
        <div class="form-step" id="step4" style="display:none;">
          <div class="form-group">
            <label><?= $t['form_name'] ?? 'Name' ?></label>
            <input type="text" id="inputNombre" placeholder="<?= $t['form_name_ph'] ?? 'Dein Name' ?>">
          </div>
          <div class="form-group">
            <label>E-Mail</label>
            <input type="email" id="inputEmail" placeholder="deine@email.com">
          </div>
          <div class="form-group">
            <label><?= $t['form_phone'] ?? 'Telefon' ?></label>
            <input type="tel" id="inputTelefono" placeholder="+41 79 000 00 00">
          </div>
        </div>
        <div class="booking-resumen" id="bookingResumen" style="display:none;">
          <div class="resumen-row"><span><?= $t['form_service'] ?? 'Leistung' ?>:</span><strong id="rServicio">—</strong></div>
          <div class="resumen-row"><span><?= $t['form_date'] ?? 'Datum' ?>:</span><strong id="rFecha">—</strong></div>
          <div class="resumen-row"><span><?= $t['form_time'] ?? 'Uhrzeit' ?>:</span><strong id="rHora">—</strong></div>
          <div class="resumen-row"><span><?= $t['form_name'] ?? 'Name' ?>:</span><strong id="rNombre">—</strong></div>
        </div>
        <div id="bookingMsg" style="display:none; margin-bottom:1rem; font-family:-apple-system,sans-serif; font-size:13px; padding:12px; border-radius:4px;"></div>
        <div class="booking-btns">
          <button class="btn-secondary" id="btnBack" onclick="prevStep()" style="display:none; font-size:11px; padding:12px 20px;">&#8592; <?= $t['btn_back'] ?? 'Zurück' ?></button>
          <button class="btn-primary" id="btnConfirm" onclick="confirmarReserva()" style="width:100%; padding:15px; font-size:12px; letter-spacing:2.5px; display:none;"><?= $t['form_submit'] ?? 'Termin bestätigen' ?></button>
        </div>
        <div class="step-indicator">
          <div class="step-dot active" id="dot1"></div>
          <div class="step-dot" id="dot2"></div>
          <div class="step-dot" id="dot3"></div>
          <div class="step-dot" id="dot4"></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ===================== PRESS ===================== -->
<div class="press-strip reveal-up">
  <span class="press-label"><?= $t['press_label'] ?? 'Bekannt aus' ?></span>
  <div class="press-divider"></div>
  <div class="press-logos">
    <span class="press-item">Vogue Suisse</span>
    <span class="press-item">Zürich Magazin</span>
    <span class="press-item">Beauty Insider</span>
    <span class="press-item">Der Spiegel</span>
    <span class="press-item">Elle Deutschland</span>
  </div>
</div>

</div><!-- /page-wrap -->

<?php include 'includes/footer.php'; ?>