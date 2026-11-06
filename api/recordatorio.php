<?php
// Cron job — ejecutar cada 15 minutos en Hostinger
// Comando: */15 * * * * php /home/.../public_html/oase/api/recordatorio.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../vendor/autoload.php';

$ahora = new DateTime('now', new DateTimeZone('Europe/Zurich'));
$en2h  = (clone $ahora)->modify('+2 hours');
$desde = (clone $en2h)->modify('-8 minutes')->format('H:i:s');
$hasta = (clone $en2h)->modify('+8 minutes')->format('H:i:s');
$hoy   = $ahora->format('Y-m-d');

$stmt = $pdo->prepare('
    SELECT r.*, s.nombre_de, s.nombre_es, s.nombre_fr, s.nombre_en
    FROM reservas r
    JOIN servicios s ON s.id = r.servicio_id
    WHERE r.fecha = ?
      AND r.hora BETWEEN ? AND ?
      AND r.estado = "confirmada"
      AND r.recordatorio_enviado = 0
');
$stmt->execute([$hoy, $desde, $hasta]);
$reservas = $stmt->fetchAll();

$enviados = 0;
foreach ($reservas as $r) {
    $lang     = $r['lang'] ?? 'de';
    $lang_col = 'nombre_' . $lang;
    $srv      = $r[$lang_col] ?? $r['nombre_de'];
    $fecha_f  = date('d/m/Y', strtotime($r['fecha']));
    $hora_f   = substr($r['hora'], 0, 5);
    $tx       = getEmailTexts($lang);
    $url_reserva = APP_URL . '/mi-reserva.php?codigo=' . $r['codigo'];

    $contenido  = '<h2>' . $tx['recordatorio_titulo'] . '</h2>';
    $contenido .= '<p>' . $tx['recordatorio_saludo'] . ' <strong>' . htmlspecialchars($r['nombre']) . '</strong>, ' . $tx['recordatorio_intro'] . '</p>';
    $contenido .= '<div class="card">';
    $contenido .= '<p><strong>' . $tx['confirmacion_servicio'] . ':</strong> ' . htmlspecialchars($srv) . '</p>';
    $contenido .= '<p><strong>' . $tx['confirmacion_fecha'] . ':</strong> ' . $fecha_f . '</p>';
    $contenido .= '<p><strong>' . $tx['confirmacion_hora'] . ':</strong> ' . $hora_f . '</p>';
    if (!empty($r['codigo'])) {
        $contenido .= '<p><strong>' . ($tx['codigo_label'] ?? 'Código') . ':</strong> <span style="color:#c9a84c;font-weight:700">' . $r['codigo'] . '</span></p>';
    }
    $contenido .= '</div>';
    $contenido .= '<p style="margin-top:16px;">' . $tx['confirmacion_cancel'] . '</p>';
    $contenido .= '<p style="margin-top:8px;"><a href="' . $url_reserva . '" style="color:#c9a84c">' . ($tx['ver_reserva'] ?? 'Ver mi reserva') . '</a></p>';

    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';
        $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
        $mail->addAddress($r['email'], $r['nombre']);
        $mail->Subject = $tx['recordatorio_subject'];
        $mail->isHTML(true);
        $mail->Body = emailTemplate($tx['recordatorio_titulo'], $contenido, $lang);
        $mail->send();

        $pdo->prepare('UPDATE reservas SET recordatorio_enviado = 1 WHERE id = ?')->execute([$r['id']]);
        $enviados++;
        error_log("Recordatorio enviado a {$r['email']} reserva #{$r['id']} ({$r['codigo']})");
    } catch (Exception $e) {
        error_log("Error recordatorio #{$r['id']}: " . $e->getMessage());
    }
}

echo "Recordatorios enviados: {$enviados}";