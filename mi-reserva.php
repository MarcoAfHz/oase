<?php
require_once __DIR__ . '/config.php';

// Detectar idioma
$allowed_langs = ['de','es','fr','en'];
$lang = 'de';
if (isset($_GET['lang']) && in_array($_GET['lang'], $allowed_langs)) {
    $lang = $_GET['lang'];
} elseif (isset($_COOKIE['oase_lang']) && in_array($_COOKIE['oase_lang'], $allowed_langs)) {
    $lang = $_COOKIE['oase_lang'];
}
$t = [];
$lang_file = __DIR__ . '/lang/' . $lang . '.php';
if (file_exists($lang_file)) include $lang_file;

$codigo = strtoupper(trim($_GET['codigo'] ?? ''));
$accion = $_GET['accion'] ?? '';
$msg    = '';
$msg_tipo = '';
$reserva = null;

if ($codigo) {
    $stmt = $pdo->prepare('
        SELECT r.*, s.nombre_es, s.nombre_de, s.nombre_fr, s.nombre_en, s.duracion_min
        FROM reservas r
        JOIN servicios s ON s.id = r.servicio_id
        WHERE r.codigo = ?
    ');
    $stmt->execute([$codigo]);
    $reserva = $stmt->fetch();
}

// Procesar cancelación
if ($reserva && $accion === 'cancelar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $ahora      = new DateTime('now', new DateTimeZone('Europe/Zurich'));
    $cita       = new DateTime($reserva['fecha'] . ' ' . $reserva['hora'], new DateTimeZone('Europe/Zurich'));
    $diff_mins  = ($cita->getTimestamp() - $ahora->getTimestamp()) / 60;

    if ($diff_mins < 120) {
        $msg      = 'cancel_too_late';
        $msg_tipo = 'error';
    } elseif ($reserva['estado'] === 'cancelada') {
        $msg      = 'already_cancelled';
        $msg_tipo = 'error';
    } else {
        $pdo->prepare('UPDATE reservas SET estado = "cancelada", motivo_cancelacion = ? WHERE codigo = ?')
            ->execute(['Cancelado por el cliente', $codigo]);
        $reserva['estado'] = 'cancelada';

        // Email confirmación cancelación al cliente
        try {
            require_once __DIR__ . '/vendor/autoload.php';
            $tx       = getEmailTexts($reserva['lang'] ?? $lang);
            $lang_col = 'nombre_' . ($reserva['lang'] ?? $lang);
            $srv      = $reserva[$lang_col] ?? $reserva['nombre_es'];
            $fecha_f  = date('d/m/Y', strtotime($reserva['fecha']));
            $hora_f   = substr($reserva['hora'], 0, 5);

            $contenido  = '<h2>' . $tx['cancelacion_titulo'] . '</h2>';
            $contenido .= '<p>' . $tx['cancelacion_saludo'] . ' <strong>' . htmlspecialchars($reserva['nombre']) . '</strong>.</p>';
            $contenido .= '<div class="card">';
            $contenido .= '<p><strong>' . $tx['confirmacion_servicio'] . ':</strong> ' . htmlspecialchars($srv) . '</p>';
            $contenido .= '<p><strong>' . $tx['confirmacion_fecha'] . ':</strong> ' . $fecha_f . '</p>';
            $contenido .= '<p><strong>' . $tx['confirmacion_hora'] . ':</strong> ' . $hora_f . '</p>';
            $contenido .= '<p><strong>' . ($tx['cancelacion_motivo'] ?? 'Motivo') . ':</strong> Cancelado por el cliente</p>';
            $contenido .= '</div>';
            $contenido .= '<p style="margin-top:16px;"><a href="' . APP_URL . '/booking.php" style="color:#c9a84c">' . ($tx['btn_book'] ?? 'Reservar nueva cita') . '</a></p>';

            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = SMTP_HOST; $mail->SMTPAuth = true;
            $mail->Username = SMTP_USER; $mail->Password = SMTP_PASS;
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = SMTP_PORT; $mail->CharSet = 'UTF-8';
            $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
            $mail->addAddress($reserva['email'], $reserva['nombre']);
            $mail->Subject = $tx['cancelacion_subject'];
            $mail->isHTML(true);
            $mail->Body = emailTemplate($tx['cancelacion_titulo'], $contenido, $reserva['lang'] ?? $lang);
            $mail->send();
        } catch (Exception $e) { error_log('Email cancel cliente: ' . $e->getMessage()); }

        $msg      = 'cancelled_ok';
        $msg_tipo = 'success';
    }
}

// Procesar modificación (cambio de fecha/hora)
if ($reserva && $accion === 'modificar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nueva_fecha = trim($_POST['nueva_fecha'] ?? '');
    $nueva_hora  = trim($_POST['nueva_hora'] ?? '');
    if ($nueva_fecha && $nueva_hora) {
        $pdo->prepare('UPDATE reservas SET fecha = ?, hora = ? WHERE codigo = ?')
            ->execute([$nueva_fecha, $nueva_hora, $codigo]);
        $reserva['fecha'] = $nueva_fecha;
        $reserva['hora']  = $nueva_hora;
        $msg      = 'modified_ok';
        $msg_tipo = 'success';
    }
}

$page_title = 'Mi reserva — Oase';
include 'includes/header.php';

// Textos UI según idioma
$ui = [
    'de' => ['titulo'=>'Meine Buchung','no_encontrada'=>'Buchung nicht gefunden','codigo_label'=>'Buchungscode eingeben','btn_buscar'=>'Suchen','servicio'=>'Leistung','fecha'=>'Datum','hora'=>'Uhrzeit','estado'=>'Status','cancel_btn'=>'Termin stornieren','modify_btn'=>'Termin ändern','cancel_confirm'=>'Möchten Sie diesen Termin wirklich stornieren?','cancel_too_late'=>'Stornierung nicht mehr möglich. Weniger als 2 Stunden bis zum Termin. Bitte kontaktieren Sie uns per WhatsApp.','already_cancelled'=>'Dieser Termin wurde bereits storniert.','cancelled_ok'=>'Ihr Termin wurde erfolgreich storniert.','modified_ok'=>'Ihr Termin wurde erfolgreich geändert.','whatsapp_btn'=>'Per WhatsApp kontaktieren','nueva_fecha'=>'Neues Datum','nueva_hora'=>'Neue Uhrzeit','confirmada'=>'Bestätigt','cancelada'=>'Storniert','pendiente_confirmacion'=>'Wird geprüft'],
    'es' => ['titulo'=>'Mi reserva','no_encontrada'=>'Reserva no encontrada','codigo_label'=>'Introduce tu código de reserva','btn_buscar'=>'Buscar','servicio'=>'Servicio','fecha'=>'Fecha','hora'=>'Hora','estado'=>'Estado','cancel_btn'=>'Cancelar cita','modify_btn'=>'Modificar cita','cancel_confirm'=>'¿Seguro que quieres cancelar esta cita?','cancel_too_late'=>'No puedes cancelar online. Faltan menos de 2 horas para tu cita. Contacta por WhatsApp.','already_cancelled'=>'Esta reserva ya fue cancelada.','cancelled_ok'=>'Tu cita ha sido cancelada correctamente.','modified_ok'=>'Tu cita ha sido modificada correctamente.','whatsapp_btn'=>'Contactar por WhatsApp','nueva_fecha'=>'Nueva fecha','nueva_hora'=>'Nueva hora','confirmada'=>'Confirmada','cancelada'=>'Cancelada','pendiente_confirmacion'=>'Pendiente de confirmación'],
    'fr' => ['titulo'=>'Mon RDV','no_encontrada'=>'Réservation introuvable','codigo_label'=>'Entrez votre code de réservation','btn_buscar'=>'Rechercher','servicio'=>'Service','fecha'=>'Date','hora'=>'Heure','estado'=>'Statut','cancel_btn'=>'Annuler le RDV','modify_btn'=>'Modifier le RDV','cancel_confirm'=>'Voulez-vous vraiment annuler ce rendez-vous ?','cancel_too_late'=>'Annulation impossible. Moins de 2 heures avant le RDV. Contactez-nous par WhatsApp.','already_cancelled'=>'Ce RDV a déjà été annulé.','cancelled_ok'=>'Votre RDV a été annulé avec succès.','modified_ok'=>'Votre RDV a été modifié avec succès.','whatsapp_btn'=>'Contacter par WhatsApp','nueva_fecha'=>'Nouvelle date','nueva_hora'=>'Nouvelle heure','confirmada'=>'Confirmé','cancelada'=>'Annulé','pendiente_confirmacion'=>'En attente de confirmation'],
    'en' => ['titulo'=>'My booking','no_encontrada'=>'Booking not found','codigo_label'=>'Enter your booking code','btn_buscar'=>'Search','servicio'=>'Service','fecha'=>'Date','hora'=>'Time','estado'=>'Status','cancel_btn'=>'Cancel appointment','modify_btn'=>'Modify appointment','cancel_confirm'=>'Are you sure you want to cancel this appointment?','cancel_too_late'=>'Cannot cancel online. Less than 2 hours until your appointment. Please contact us via WhatsApp.','already_cancelled'=>'This booking has already been cancelled.','cancelled_ok'=>'Your appointment has been successfully cancelled.','modified_ok'=>'Your appointment has been successfully modified.','whatsapp_btn'=>'Contact via WhatsApp','nueva_fecha'=>'New date','nueva_hora'=>'New time','confirmada'=>'Confirmed','cancelada'=>'Cancelled','pendiente_confirmacion'=>'Pending confirmation'],
];
$u = $ui[$lang] ?? $ui['es'];
?>

<div class="mi-reserva-wrapper">
  <div class="page-header">
    <div class="page-header-inner">
      <div class="section-eyebrow" style="justify-content:center;"><span class="eyebrow-line"></span><?= $u['titulo'] ?></div>
      <h1 class="section-h2" style="text-align:center;"><?= $u['titulo'] ?></h1>
    </div>
  </div>

  <div class="mi-reserva-inner">

    <!-- BUSCAR POR CÓDIGO -->
    <?php if (!$codigo): ?>
    <div class="reserva-search">
      <label class="reserva-search-label"><?= $u['codigo_label'] ?></label>
      <form method="GET" class="reserva-search-form">
        <input type="text" name="codigo" placeholder="OASE-XXXXXX" class="reserva-search-input" maxlength="11" style="text-transform:uppercase">
        <button type="submit" class="btn-primary"><?= $u['btn_buscar'] ?></button>
      </form>
    </div>
    <?php elseif (!$reserva): ?>
    <div class="reserva-notfound">
      <i class="ti ti-calendar-x"></i>
      <p><?= $u['no_encontrada'] ?></p>
      <a href="/mi-reserva.php" class="btn-secondary" style="display:inline-flex;margin-top:1rem;"><?= $u['btn_buscar'] ?> →</a>
    </div>
    <?php else: ?>

    <!-- MENSAJES -->
    <?php if ($msg): ?>
    <div class="reserva-msg reserva-msg-<?= $msg_tipo ?>">
      <?php
      $msg_texts = ['cancel_too_late'=>$u['cancel_too_late'],'already_cancelled'=>$u['already_cancelled'],'cancelled_ok'=>$u['cancelled_ok'],'modified_ok'=>$u['modified_ok']];
      echo htmlspecialchars($msg_texts[$msg] ?? $msg);
      ?>
    </div>
    <?php endif; ?>

    <!-- DETALLES -->
    <?php
    $lang_col   = 'nombre_' . ($reserva['lang'] ?? $lang);
    $srv_nombre = $reserva[$lang_col] ?? $reserva['nombre_es'];
    $fecha_f    = date('d/m/Y', strtotime($reserva['fecha']));
    $hora_f     = substr($reserva['hora'], 0, 5);
    $ahora      = new DateTime('now', new DateTimeZone('Europe/Zurich'));
    $cita       = new DateTime($reserva['fecha'] . ' ' . $reserva['hora'], new DateTimeZone('Europe/Zurich'));
    $diff_mins  = ($cita->getTimestamp() - $ahora->getTimestamp()) / 60;
    $puede_cancelar = ($reserva['estado'] !== 'cancelada' && $diff_mins >= 120);
    $puede_modificar = $puede_cancelar;
    ?>
    <div class="reserva-card-detail">
      <div class="reserva-codigo">
        <span><?= $codigo ?></span>
        <button onclick="copiarCodigo()" class="btn-copy" title="Copiar"><i class="ti ti-copy"></i></button>
      </div>
      <div class="reserva-estado estado-<?= $reserva['estado'] ?>"><?= $u[$reserva['estado']] ?? $reserva['estado'] ?></div>

      <div class="reserva-info-grid">
        <div class="reserva-info-item">
          <div class="reserva-info-label"><?= $u['servicio'] ?></div>
          <div class="reserva-info-val"><?= htmlspecialchars($srv_nombre) ?></div>
        </div>
        <div class="reserva-info-item">
          <div class="reserva-info-label"><?= $u['fecha'] ?></div>
          <div class="reserva-info-val"><?= $fecha_f ?></div>
        </div>
        <div class="reserva-info-item">
          <div class="reserva-info-label"><?= $u['hora'] ?></div>
          <div class="reserva-info-val"><?= $hora_f ?></div>
        </div>
        <div class="reserva-info-item">
          <div class="reserva-info-label">Cliente</div>
          <div class="reserva-info-val"><?= htmlspecialchars($reserva['nombre']) ?></div>
        </div>
      </div>

      <?php if ($reserva['motivo_cancelacion']): ?>
      <div class="reserva-motivo">
        <strong><?= ($ui[$lang]['cancel_btn'] ?? 'Motivo') ?>:</strong>
        <?= htmlspecialchars($reserva['motivo_cancelacion']) ?>
      </div>
      <?php endif; ?>

      <!-- ACCIONES -->
      <div class="reserva-acciones">
        <?php if ($puede_cancelar): ?>
        <button class="btn-cancelar-reserva" onclick="confirmarCancelacion()">
          <i class="ti ti-x"></i> <?= $u['cancel_btn'] ?>
        </button>
        <?php endif; ?>
        <?php if ($puede_modificar): ?>
        <button class="btn-modificar-reserva" onclick="toggleModificar()">
          <i class="ti ti-edit"></i> <?= $u['modify_btn'] ?>
        </button>
        <?php endif; ?>
        <a href="https://wa.me/<?= EDWIN_WHATSAPP ?>" target="_blank" class="btn-wa-reserva">
          <i class="ti ti-brand-whatsapp"></i> <?= $u['whatsapp_btn'] ?>
        </a>
      </div>

      <!-- FORM MODIFICAR -->
      <div id="form-modificar" style="display:none; margin-top:1.5rem;">
        <form method="POST" action="?codigo=<?= $codigo ?>&accion=modificar">
          <div class="mod-grid">
            <div class="form-group">
              <label><?= $u['nueva_fecha'] ?></label>
              <input type="date" name="nueva_fecha" value="<?= $reserva['fecha'] ?>" min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
            </div>
            <div class="form-group">
              <label><?= $u['nueva_hora'] ?></label>
              <input type="time" name="nueva_hora" value="<?= substr($reserva['hora'],0,5) ?>">
            </div>
          </div>
          <button type="submit" class="btn-primary" style="margin-top:1rem;width:100%;"><?= $u['modify_btn'] ?></button>
        </form>
      </div>

      <!-- FORM CANCELAR -->
      <form id="form-cancelar" method="POST" action="?codigo=<?= $codigo ?>&accion=cancelar" style="display:none;">
        <button type="submit" class="btn-cancelar-reserva" style="width:100%;margin-top:0.5rem;">
          <i class="ti ti-check"></i> <?= $u['cancel_btn'] ?>
        </button>
      </form>

    </div>
    <?php endif; ?>
  </div>
</div>

<style>
.mi-reserva-wrapper { overflow-x: hidden; }
.page-header { background: #080603; padding: 8rem 2rem 4rem; border-bottom: 0.5px solid #1e1a13; text-align: center; }
.page-header-inner { max-width: 600px; margin: 0 auto; }
.mi-reserva-inner { max-width: 500px; margin: 3rem auto; padding: 0 1.25rem 4rem; }
.reserva-search { text-align: center; }
.reserva-search-label { display: block; font-family: -apple-system, sans-serif; font-size: 14px; color: #f0ebe3; margin-bottom: 1rem; }
.reserva-search-form { display: flex; gap: 8px; max-width: 380px; margin: 0 auto; }
.reserva-search-input { flex: 1; padding: 12px 14px; background: #1a1510; border: 0.5px solid #2a2318; color: #f0ebe3; font-size: 14px; outline: none; font-family: monospace; letter-spacing: 2px; }
.reserva-search-input:focus { border-color: #c9a84c; }
.reserva-notfound { text-align: center; padding: 3rem 1rem; }
.reserva-notfound i { font-size: 48px; color: #2a2318; display: block; margin-bottom: 1rem; }
.reserva-notfound p { color: #5a5246; font-family: -apple-system, sans-serif; font-size: 14px; }
.reserva-msg { padding: 12px 16px; border-radius: 4px; font-family: -apple-system, sans-serif; font-size: 13px; margin-bottom: 1.5rem; }
.reserva-msg-success { background: rgba(34,197,94,0.1); border: 0.5px solid #22c55e; color: #22c55e; }
.reserva-msg-error { background: rgba(239,68,68,0.1); border: 0.5px solid #ef4444; color: #ef4444; }
.reserva-card-detail { background: #1a1510; border: 0.5px solid #2a2318; padding: 1.5rem; border-radius: 4px; }
.reserva-codigo { display: flex; align-items: center; gap: 10px; margin-bottom: 0.75rem; }
.reserva-codigo span { font-family: monospace; font-size: 20px; font-weight: 700; color: #c9a84c; letter-spacing: 2px; }
.btn-copy { background: none; border: 0.5px solid #2a2318; color: #5a5246; padding: 4px 8px; cursor: pointer; border-radius: 4px; transition: all 0.2s; }
.btn-copy:hover { border-color: #c9a84c; color: #c9a84c; }
.reserva-estado { display: inline-block; padding: 4px 12px; border-radius: 20px; font-family: -apple-system, sans-serif; font-size: 12px; font-weight: 600; margin-bottom: 1.25rem; }
.estado-confirmada { background: rgba(34,197,94,0.1); color: #22c55e; }
.estado-cancelada { background: rgba(239,68,68,0.1); color: #ef4444; }
.estado-pendiente_confirmacion { background: rgba(249,115,22,0.1); color: #f97316; }
.reserva-info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem; }
.reserva-info-label { font-family: -apple-system, sans-serif; font-size: 10px; letter-spacing: 2px; text-transform: uppercase; color: #4a4035; margin-bottom: 4px; }
.reserva-info-val { font-family: -apple-system, sans-serif; font-size: 14px; color: #f0ebe3; }
.reserva-motivo { background: rgba(249,115,22,0.08); border: 0.5px solid rgba(249,115,22,0.3); padding: 10px 14px; font-family: -apple-system, sans-serif; font-size: 13px; color: #f97316; margin-bottom: 1.25rem; border-radius: 4px; }
.reserva-acciones { display: flex; flex-direction: column; gap: 8px; }
.btn-cancelar-reserva { padding: 12px; font-family: -apple-system, sans-serif; font-size: 12px; font-weight: 600; border: none; border-radius: 4px; cursor: pointer; background: rgba(239,68,68,0.1); color: #ef4444; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.2s; }
.btn-cancelar-reserva:hover { background: #ef4444; color: #fff; }
.btn-modificar-reserva { padding: 12px; font-family: -apple-system, sans-serif; font-size: 12px; font-weight: 600; border: 0.5px solid #2a2318; border-radius: 4px; cursor: pointer; background: transparent; color: #c9a84c; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.2s; }
.btn-modificar-reserva:hover { background: rgba(201,168,76,0.08); }
.btn-wa-reserva { padding: 12px; font-family: -apple-system, sans-serif; font-size: 12px; font-weight: 600; border-radius: 4px; background: rgba(37,211,102,0.1); color: #25d366; display: flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none; transition: all 0.2s; }
.btn-wa-reserva:hover { background: #25d366; color: #fff; }
.mod-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.form-group label { display: block; font-family: -apple-system, sans-serif; font-size: 10px; letter-spacing: 2px; text-transform: uppercase; color: #4a4035; margin-bottom: 6px; }
.form-group input { width: 100%; padding: 10px 12px; background: #0f0f0f; border: 0.5px solid #2a2318; color: #f0ebe3; font-size: 13px; outline: none; }
.form-group input:focus { border-color: #c9a84c; }
@media (max-width: 480px) {
  .reserva-search-form { flex-direction: column; }
  .reserva-info-grid { grid-template-columns: 1fr; }
  .mod-grid { grid-template-columns: 1fr; }
}
</style>

<script>
function copiarCodigo() {
  navigator.clipboard.writeText('<?= $codigo ?>').then(() => {
    const btn = document.querySelector('.btn-copy');
    btn.innerHTML = '<i class="ti ti-check"></i>';
    btn.style.color = '#22c55e';
    setTimeout(() => { btn.innerHTML = '<i class="ti ti-copy"></i>'; btn.style.color = ''; }, 2000);
  });
}
function confirmarCancelacion() {
  if (confirm('<?= addslashes($u["cancel_confirm"]) ?>')) {
    document.getElementById('form-cancelar').style.display = 'block';
    document.getElementById('form-cancelar').submit();
  }
}
function toggleModificar() {
  const f = document.getElementById('form-modificar');
  f.style.display = f.style.display === 'none' ? 'block' : 'none';
}
</script>

<?php include 'includes/footer.php'; ?>