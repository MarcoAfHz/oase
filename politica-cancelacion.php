<?php
$page_title = 'Política de cancelación — Oase Friseur & Ästhetik';
require_once __DIR__ . '/config.php';
include 'includes/header.php';

$ui = [
    'de' => [
        'titulo'    => 'Stornierungsbedingungen',
        'subtitulo' => 'Kostenlos stornieren bis 2 Stunden vor dem Termin',
        'libre_title'  => '✅ Kostenlose Stornierung',
        'libre_desc'   => 'Sie können Ihren Termin bis zu 2 Stunden vor der gebuchten Zeit kostenlos stornieren — online mit Ihrem Buchungscode oder per WhatsApp.',
        'tarde_title'  => '⚠️ Weniger als 2 Stunden vorher',
        'tarde_desc'   => 'Bei Stornierungen weniger als 2 Stunden vor dem Termin bitten wir Sie, uns direkt per WhatsApp zu kontaktieren. Edwin prüft dann die Situation individuell.',
        'como_title'   => 'Wie stornieren?',
        'como_1'       => 'Online: Gehen Sie zu "Meine Buchung" und geben Sie Ihren Buchungscode ein.',
        'como_2'       => 'Per WhatsApp: Schreiben Sie uns mit Ihrem Namen und Buchungscode.',
        'modif_title'  => 'Termin ändern',
        'modif_desc'   => 'Sie können Ihren Termin über "Meine Buchung" mit Ihrem Code ändern, sofern noch mehr als 2 Stunden bis zum Termin verbleiben.',
        'codigo_title' => 'Ihren Buchungscode finden',
        'codigo_desc'  => 'Ihr Buchungscode (Format OASE-XXXXXX) wurde Ihnen per E-Mail nach der Buchung zugesandt.',
        'btn_reserva'  => 'Meine Buchung ansehen',
        'btn_wa'       => 'Per WhatsApp kontaktieren',
    ],
    'es' => [
        'titulo'    => 'Política de cancelación',
        'subtitulo' => 'Cancela gratis hasta 2 horas antes de tu cita',
        'libre_title'  => '✅ Cancelación gratuita',
        'libre_desc'   => 'Puedes cancelar tu cita hasta 2 horas antes de la hora reservada de forma gratuita — online con tu código de reserva o por WhatsApp.',
        'tarde_title'  => '⚠️ Menos de 2 horas antes',
        'tarde_desc'   => 'Si necesitas cancelar con menos de 2 horas de antelación, contáctanos directamente por WhatsApp. Edwin valorará la situación de forma individual.',
        'como_title'   => '¿Cómo cancelar?',
        'como_1'       => 'Online: Ve a "Mi reserva" e introduce tu código de reserva.',
        'como_2'       => 'Por WhatsApp: Escríbenos con tu nombre y código de reserva.',
        'modif_title'  => 'Modificar la cita',
        'modif_desc'   => 'Puedes modificar tu cita desde "Mi reserva" con tu código, siempre que falten más de 2 horas para la cita.',
        'codigo_title' => 'Dónde encontrar tu código',
        'codigo_desc'  => 'Tu código de reserva (formato OASE-XXXXXX) te fue enviado por email tras realizar la reserva.',
        'btn_reserva'  => 'Ver mi reserva',
        'btn_wa'       => 'Contactar por WhatsApp',
    ],
    'fr' => [
        'titulo'    => "Politique d'annulation",
        'subtitulo' => 'Annulez gratuitement jusqu\'à 2 heures avant votre RDV',
        'libre_title'  => '✅ Annulation gratuite',
        'libre_desc'   => 'Vous pouvez annuler votre rendez-vous jusqu\'à 2 heures avant l\'heure réservée gratuitement — en ligne avec votre code de réservation ou par WhatsApp.',
        'tarde_title'  => '⚠️ Moins de 2 heures avant',
        'tarde_desc'   => 'Pour les annulations à moins de 2 heures du rendez-vous, veuillez nous contacter directement par WhatsApp. Edwin évaluera la situation individuellement.',
        'como_title'   => 'Comment annuler ?',
        'como_1'       => 'En ligne : Accédez à "Mon RDV" et entrez votre code de réservation.',
        'como_2'       => 'Par WhatsApp : Écrivez-nous avec votre nom et votre code de réservation.',
        'modif_title'  => 'Modifier le rendez-vous',
        'modif_desc'   => 'Vous pouvez modifier votre rendez-vous depuis "Mon RDV" avec votre code, à condition qu\'il reste plus de 2 heures avant le rendez-vous.',
        'codigo_title' => 'Où trouver votre code',
        'codigo_desc'  => 'Votre code de réservation (format OASE-XXXXXX) vous a été envoyé par email après la réservation.',
        'btn_reserva'  => 'Voir mon RDV',
        'btn_wa'       => 'Contacter par WhatsApp',
    ],
    'en' => [
        'titulo'    => 'Cancellation policy',
        'subtitulo' => 'Cancel for free up to 2 hours before your appointment',
        'libre_title'  => '✅ Free cancellation',
        'libre_desc'   => 'You can cancel your appointment up to 2 hours before the booked time for free — online with your booking code or via WhatsApp.',
        'tarde_title'  => '⚠️ Less than 2 hours before',
        'tarde_desc'   => 'For cancellations less than 2 hours before the appointment, please contact us directly via WhatsApp. Edwin will assess the situation individually.',
        'como_title'   => 'How to cancel?',
        'como_1'       => 'Online: Go to "My booking" and enter your booking code.',
        'como_2'       => 'Via WhatsApp: Write to us with your name and booking code.',
        'modif_title'  => 'Modify appointment',
        'modif_desc'   => 'You can modify your appointment from "My booking" with your code, as long as more than 2 hours remain before the appointment.',
        'codigo_title' => 'Where to find your code',
        'codigo_desc'  => 'Your booking code (format OASE-XXXXXX) was sent to you by email after booking.',
        'btn_reserva'  => 'View my booking',
        'btn_wa'       => 'Contact via WhatsApp',
    ],
];
$u = $ui[$lang] ?? $ui['de'];
$wa_url = 'https://wa.me/41767133341';
?>

<div class="politica-wrapper">
  <div class="page-header">
    <div class="page-header-inner">
      <div class="section-eyebrow" style="justify-content:center;">
        <span class="eyebrow-line"></span>
        <?= $u['titulo'] ?>
      </div>
      <h1 class="section-h2" style="text-align:center;"><?= $u['titulo'] ?></h1>
      <p class="body-text" style="text-align:center; margin-top:1rem;"><?= $u['subtitulo'] ?></p>
    </div>
  </div>

  <div class="politica-inner">

    <div class="politica-grid">

      <!-- Cancelación libre -->
      <div class="politica-card politica-card-green reveal-up">
        <h2 class="politica-card-title"><?= $u['libre_title'] ?></h2>
        <p class="politica-card-desc"><?= $u['libre_desc'] ?></p>
      </div>

      <!-- Menos de 2h -->
      <div class="politica-card politica-card-orange reveal-up" style="--delay:0.1s">
        <h2 class="politica-card-title"><?= $u['tarde_title'] ?></h2>
        <p class="politica-card-desc"><?= $u['tarde_desc'] ?></p>
      </div>

    </div>

    <!-- Cómo cancelar -->
    <div class="politica-section reveal-up" style="--delay:0.2s">
      <h3 class="politica-section-title"><?= $u['como_title'] ?></h3>
      <div class="politica-steps">
        <div class="politica-step">
          <div class="politica-step-num">1</div>
          <div class="politica-step-text"><?= $u['como_1'] ?></div>
        </div>
        <div class="politica-step">
          <div class="politica-step-num">2</div>
          <div class="politica-step-text"><?= $u['como_2'] ?></div>
        </div>
      </div>
    </div>

    <!-- Modificar -->
    <div class="politica-section reveal-up" style="--delay:0.3s">
      <h3 class="politica-section-title"><?= $u['modif_title'] ?></h3>
      <p class="politica-section-desc"><?= $u['modif_desc'] ?></p>
    </div>

    <!-- Código -->
    <div class="politica-section reveal-up" style="--delay:0.4s">
      <h3 class="politica-section-title"><?= $u['codigo_title'] ?></h3>
      <p class="politica-section-desc"><?= $u['codigo_desc'] ?></p>
      <div class="codigo-ejemplo">OASE-X7K2PQ</div>
    </div>

    <!-- Botones -->
    <div class="politica-btns reveal-up" style="--delay:0.5s">
      <a href="/mi-reserva.php" class="btn-primary" style="display:inline-block; text-decoration:none;">
        <?= $u['btn_reserva'] ?>
      </a>
      <a href="<?= $wa_url ?>" target="_blank" class="btn-wa-politica">
        <i class="ti ti-brand-whatsapp"></i>
        <?= $u['btn_wa'] ?>
      </a>
    </div>

  </div>
</div>

<style>
.politica-wrapper { overflow-x: hidden; }
.page-header { background: #080603; padding: 8rem 2rem 4rem; border-bottom: 0.5px solid #1e1a13; text-align: center; overflow: hidden; }
.page-header-inner { max-width: 600px; margin: 0 auto; }
.politica-inner { max-width: 760px; margin: 0 auto; padding: 4rem 1.25rem; }
.politica-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 3rem; }
.politica-card { padding: 2rem; border-radius: 4px; }
.politica-card-green { background: rgba(34,197,94,0.06); border: 0.5px solid rgba(34,197,94,0.3); }
.politica-card-orange { background: rgba(249,115,22,0.06); border: 0.5px solid rgba(249,115,22,0.3); }
.politica-card-title { font-family: -apple-system, sans-serif; font-size: 15px; font-weight: 600; color: #f0ebe3; margin-bottom: 0.75rem; }
.politica-card-desc { font-family: -apple-system, sans-serif; font-size: 13px; color: #6a6055; line-height: 1.7; }
.politica-section { margin-bottom: 2.5rem; padding-bottom: 2.5rem; border-bottom: 0.5px solid #1e1a13; }
.politica-section:last-of-type { border-bottom: none; }
.politica-section-title { font-family: -apple-system, sans-serif; font-size: 15px; font-weight: 600; color: #c9a84c; margin-bottom: 1rem; letter-spacing: 0.5px; }
.politica-section-desc { font-family: -apple-system, sans-serif; font-size: 13px; color: #6a6055; line-height: 1.7; }
.politica-steps { display: flex; flex-direction: column; gap: 1rem; }
.politica-step { display: flex; align-items: flex-start; gap: 14px; }
.politica-step-num { width: 28px; height: 28px; border-radius: 50%; background: rgba(201,168,76,0.12); border: 0.5px solid #c9a84c; display: flex; align-items: center; justify-content: center; font-family: -apple-system, sans-serif; font-size: 12px; font-weight: 700; color: #c9a84c; flex-shrink: 0; }
.politica-step-text { font-family: -apple-system, sans-serif; font-size: 13px; color: #6a6055; line-height: 1.7; padding-top: 4px; }
.codigo-ejemplo { font-family: monospace; font-size: 22px; font-weight: 700; color: #c9a84c; letter-spacing: 3px; margin-top: 1rem; padding: 0.75rem 1.25rem; background: rgba(201,168,76,0.06); border: 0.5px solid #2a2318; display: inline-block; border-radius: 4px; }
.politica-btns { display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 1rem; }
.btn-wa-politica { display: inline-flex; align-items: center; gap: 8px; background: rgba(37,211,102,0.1); color: #25d366; border: 0.5px solid rgba(37,211,102,0.3); padding: 14px 24px; font-family: -apple-system, sans-serif; font-size: 12px; font-weight: 600; text-decoration: none; transition: all 0.2s; }
.btn-wa-politica:hover { background: #25d366; color: #fff; }
@media (max-width: 600px) {
  .politica-grid { grid-template-columns: 1fr; }
  .politica-btns { flex-direction: column; }
}
</style>

<?php include 'includes/footer.php'; ?>