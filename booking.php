<?php
$page_title = 'Reservar cita — Oase Friseur & Ästhetik';
require_once __DIR__ . '/config.php';
include 'includes/header.php';

$lang_col = 'nombre_' . $lang;
$stmt_srv = $pdo->query('SELECT * FROM servicios WHERE activo = 1 ORDER BY id');
$servicios = $stmt_srv->fetchAll();

$titles = [
    'de' => ['Termin buchen', 'Wähle deinen Wunschtermin', 'Schnell & einfach online buchen'],
    'es' => ['Reservar cita', 'Elige tu cita ideal', 'Reserva rápida y fácil online'],
    'fr' => ['Prendre RDV', 'Choisissez votre rendez-vous', 'Réservation rapide et facile en ligne'],
    'en' => ['Book now', 'Choose your ideal appointment', 'Quick & easy online booking'],
];
$title    = $titles[$lang][0] ?? $titles['de'][0];
$subtitle = $titles[$lang][1] ?? $titles['de'][1];
$desc     = $titles[$lang][2] ?? $titles['de'][2];
?>

<div style="padding-top: 80px; min-height: 100vh; background: #0f0f0f;">

  <!-- HEADER PÁGINA -->
  <div style="background: #080603; padding: 4rem 3rem 3rem; border-bottom: 0.5px solid #1e1a13; text-align:center;">
    <div class="section-eyebrow" style="justify-content:center;">
      <span class="eyebrow-line"></span>
      <?= $t['book_eyebrow'] ?? 'Online buchen' ?>
    </div>
    <h1 class="section-h2" style="margin-bottom:0.5rem;"><?= $t['book_title_1'] ?? 'Reserviere deinen' ?> <em><?= $t['book_title_2'] ?? 'Termin' ?></em></h1>
    <p class="body-text" style="max-width:500px; margin:0.5rem auto 0;"><?= $t['book_text'] ?? 'Schnell, einfach und in weniger als einer Minute.' ?></p>
  </div>

  <!-- BOOKING GRID -->
  <div style="max-width: 960px; margin: 4rem auto; padding: 0 2rem;">
    <div class="booking-grid">

      <!-- INFO -->
      <div>
        <div class="contact-info" style="margin-top:0;">
          <div class="contact-row"><i class="ti ti-map-pin"></i> Musterstrasse 12, 8001 Zürich</div>
          <div class="contact-row"><i class="ti ti-phone"></i> +41 44 000 00 00</div>
          <div class="contact-row"><i class="ti ti-clock"></i> Mo–Fr 9:00–19:00 · Sa 9:00–17:00</div>
        </div>
        <div style="margin-top: 3rem;">
          <div class="section-eyebrow" style="margin-bottom:1rem;">
            <span class="eyebrow-line"></span>
            <?= $t['nav_services'] ?? 'Leistungen' ?>
          </div>
          <?php foreach ($servicios as $s): ?>
          <div style="display:flex; justify-content:space-between; align-items:center; padding:0.75rem 0; border-bottom:0.5px solid #1e1a13;">
            <span style="font-family:-apple-system,sans-serif; font-size:13px; color:#f0ebe3;"><?= htmlspecialchars($s[$lang_col] ?? $s['nombre_de']) ?></span>
            <span style="font-family:-apple-system,sans-serif; font-size:13px; color:#c9a84c;">CHF <?= number_format($s['precio'],2) ?></span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- FORMULARIO PASOS -->
      <div class="booking-form" id="bookingForm">

        <!-- Paso 1: Servicio -->
        <div class="form-step" id="step1">
          <div class="form-group">
            <label><?= $t['form_service'] ?? 'Leistung' ?></label>
            <select id="selectServicio" onchange="nextStep(1)">
              <option value="">— <?= $t['form_service'] ?? 'Leistung wählen' ?> —</option>
              <?php foreach ($servicios as $s): ?>
              <option value="<?= $s['id'] ?>" data-duracion="<?= $s['duracion_min'] ?>">
                <?= htmlspecialchars($s[$lang_col] ?? $s['nombre_de']) ?> — CHF <?= number_format($s['precio'],2) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <!-- Paso 2: Fecha -->
        <div class="form-step" id="step2" style="display:none;">
          <div class="form-group">
            <label><?= $t['form_date'] ?? 'Datum' ?></label>
            <input type="date" id="selectFecha" min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
          </div>
        </div>

        <!-- Paso 3: Hora -->
        <div class="form-step" id="step3" style="display:none;">
          <div class="form-group">
            <label><?= $t['form_time'] ?? 'Uhrzeit' ?></label>
            <!-- Aviso slots al límite -->
            <div id="avisoLimite" style="display:none; background:rgba(249,115,22,0.08); border:0.5px solid #f97316; color:#f97316; padding:10px 12px; font-family:-apple-system,sans-serif; font-size:12px; margin-bottom:12px; border-radius:4px; line-height:1.6;">
              <i class="ti ti-alert-triangle"></i> <span id="textoAvisoLimite"></span>
            </div>
            <div class="horas-grid" id="horasGrid"></div>
          </div>
        </div>

        <!-- Paso 4: Datos -->
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
            <label><?= $t['form_email_conf'] ?? 'E-Mail bestätigen' ?></label>
            <input type="email" id="inputEmailConf" placeholder="deine@email.com">
          </div>
          <div class="form-group">
            <label><?= $t['form_phone'] ?? 'Telefon' ?></label>
            <input type="tel" id="inputTelefono" placeholder="+41 79 000 00 00">
          </div>
        </div>

        <!-- Resumen -->
        <div class="booking-resumen" id="bookingResumen" style="display:none;">
          <div class="resumen-row"><span><?= $t['form_service'] ?? 'Leistung' ?>:</span><strong id="rServicio">—</strong></div>
          <div class="resumen-row"><span><?= $t['form_date'] ?? 'Datum' ?>:</span><strong id="rFecha">—</strong></div>
          <div class="resumen-row"><span><?= $t['form_time'] ?? 'Uhrzeit' ?>:</span><strong id="rHora">—</strong></div>
          <div class="resumen-row"><span><?= $t['form_name'] ?? 'Name' ?>:</span><strong id="rNombre">—</strong></div>
        </div>

        <!-- Aviso WhatsApp -->
        <div id="avisoWhatsApp" style="display:none; background:rgba(37,211,102,0.06); border:0.5px solid rgba(37,211,102,0.3); padding:12px 14px; font-family:-apple-system,sans-serif; font-size:12px; color:#25d366; margin-bottom:1rem; border-radius:4px; line-height:1.6;">
          <i class="ti ti-brand-whatsapp"></i> <span id="textoAvisoWa"></span>
        </div>

        <!-- Código de reserva -->
        <div id="codigoWrap" style="display:none; background:rgba(201,168,76,0.08); border:0.5px solid #c9a84c; padding:1rem 1.25rem; margin-bottom:1rem; border-radius:4px;">
          <div style="font-family:-apple-system,sans-serif; font-size:10px; letter-spacing:2px; text-transform:uppercase; color:#c9a84c; margin-bottom:6px;" id="codigoLabel"></div>
          <div style="display:flex; align-items:center; gap:10px;">
            <span id="codigoReserva" style="font-family:monospace; font-size:22px; font-weight:700; color:#c9a84c; letter-spacing:2px;"></span>
            <button id="btnCopiarCodigo" onclick="copiarCodigoReserva()" style="background:none; border:0.5px solid #c9a84c; color:#c9a84c; padding:4px 8px; cursor:pointer; border-radius:4px; transition:all 0.2s;">
              <i class="ti ti-copy"></i>
            </button>
          </div>
          <a id="linkMiReserva" href="#" style="font-family:-apple-system,sans-serif; font-size:12px; color:#c9a84c; display:inline-block; margin-top:8px;"></a>
        </div>

        <!-- Mensaje -->
        <div id="bookingMsg" style="display:none; margin-bottom:1rem; font-family:-apple-system,sans-serif; font-size:13px; padding:12px; border-radius:4px; line-height:1.5;"></div>

        <!-- Botones -->
        <div class="booking-btns">
          <button class="btn-secondary" id="btnBack" onclick="prevStep()" style="display:none; font-size:11px; padding:12px 20px;">&#8592; <?= $t['btn_back'] ?? 'Zurück' ?></button>
          <button class="btn-primary" id="btnConfirm" onclick="confirmarReserva()" style="width:100%; padding:15px; font-size:12px; letter-spacing:2.5px; display:none;"><?= $t['form_submit'] ?? 'Termin bestätigen' ?></button>
        </div>

        <!-- Indicador pasos -->
        <div class="step-indicator">
          <div class="step-dot active" id="dot1"></div>
          <div class="step-dot" id="dot2"></div>
          <div class="step-dot" id="dot3"></div>
          <div class="step-dot" id="dot4"></div>
        </div>

      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>