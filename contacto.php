<?php
$page_title = 'Kontakt — Oase Friseur & Ästhetik';
require_once __DIR__ . '/config.php';
include 'includes/header.php';

$wa_number = '41767133341';
$wa_msg = urlencode($t['contact_wa_msg'] ?? 'Hola, me gustaría obtener más información sobre vuestros servicios.');
$wa_url = "https://wa.me/{$wa_number}?text={$wa_msg}";
?>

<!-- CONTACTO -->
<div class="contacto-wrapper">

  <!-- HEADER -->
  <div class="page-header">
    <div class="page-header-inner">
      <div class="section-eyebrow" style="justify-content:center;">
        <span class="eyebrow-line"></span>
        <?= $t['nav_contact'] ?? 'Kontakt' ?>
      </div>
      <h1 class="section-h2" style="text-align:center;">
        <?= $t['contact_title_1'] ?? 'Nehmen Sie' ?> <em><?= $t['contact_title_2'] ?? 'Kontakt auf' ?></em>
      </h1>
    </div>
  </div>

  <!-- GRID -->
  <div class="contacto-page">
    <div class="contacto-grid">

      <!-- INFO -->
      <div class="reveal-left">
        <div class="contacto-block">
          <div class="contacto-icon"><i class="ti ti-map-pin"></i></div>
          <div>
            <div class="contacto-label"><?= $t['contact_address'] ?? 'Adresse' ?></div>
            <div class="contacto-value">Musterstrasse 12<br>8001 Zürich</div>
          </div>
        </div>
        <div class="contacto-block">
          <div class="contacto-icon"><i class="ti ti-clock"></i></div>
          <div>
            <div class="contacto-label"><?= $t['footer_hours'] ?? 'Öffnungszeiten' ?></div>
            <div class="contacto-value">
              Mo – Fr: 9:00 – 19:00<br>
              Sa: 9:00 – 17:00<br>
              So: <?= $t['closed'] ?? 'geschlossen' ?>
            </div>
          </div>
        </div>
        <div class="contacto-block">
          <div class="contacto-icon"><i class="ti ti-phone"></i></div>
          <div>
            <div class="contacto-label">WhatsApp</div>
            <div class="contacto-value">+41 76 713 3341</div>
          </div>
        </div>
        <div class="contacto-block">
          <div class="contacto-icon"><i class="ti ti-brand-instagram"></i></div>
          <div>
            <div class="contacto-label">Instagram</div>
            <div class="contacto-value">@friseuroase</div>
          </div>
        </div>
      </div>

      <!-- WHATSAPP + RESERVA -->
      <div class="reveal-right">
        <div class="wa-card">
          <div class="wa-icon"><i class="ti ti-brand-whatsapp"></i></div>
          <h2 class="wa-title"><?= $t['contact_wa_title'] ?? 'Schreib uns direkt' ?></h2>
          <p class="wa-desc"><?= $t['contact_wa_desc'] ?? 'Für Fragen, Terminanfragen oder persönliche Beratung — wir antworten schnell auf WhatsApp.' ?></p>
          <a href="<?= $wa_url ?>" target="_blank" class="btn-wa">
            <i class="ti ti-brand-whatsapp"></i>
            <?= $t['contact_wa_btn'] ?? 'WhatsApp schreiben' ?>
          </a>
          <p class="wa-number">+41 76 713 3341</p>
        </div>
        <div class="contacto-booking-cta">
          <p class="body-text" style="margin-bottom:1rem;"><?= $t['contact_book_text'] ?? 'Oder buche deinen Termin direkt online:' ?></p>
          <a href="/booking.php" class="btn-primary" style="display:inline-block; text-decoration:none;">
            <?= $t['btn_book'] ?? 'Termin buchen' ?>
          </a>
        </div>
      </div>

    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>