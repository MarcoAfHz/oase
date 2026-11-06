<?php
require_once __DIR__ . '/../../config.php';
session_start();
if (!isset($_SESSION['oase_admin'])) die(json_encode(['error' => 'No autorizado']));
header('Content-Type: application/json');

$id = intval($_POST['id'] ?? 0);
if (!$id) die(json_encode(['error' => 'ID requerido']));

$stmt = $pdo->prepare('
    SELECT r.*, s.nombre_de, s.nombre_es, s.nombre_fr, s.nombre_en, s.precio
    FROM reservas r
    JOIN servicios s ON s.id = r.servicio_id
    WHERE r.id = ? AND r.estado = "pendiente_confirmacion"
');
$stmt->execute([$id]);
$r = $stmt->fetch();
if (!$r) die(json_encode(['error' => 'Reserva no encontrada o ya procesada']));

// Confirmar
$pdo->prepare('UPDATE reservas SET estado = "confirmada" WHERE id = ?')->execute([$id]);

// Email confirmación al cliente en su idioma
try {
    require_once __DIR__ . '/../../vendor/autoload.php';
    $lang     = $r['lang'] ?? 'de';
    $lang_col = 'nombre_' . $lang;
    $tx       = getEmailTexts($lang);
    $fecha_f  = date('d/m/Y', strtotime($r['fecha']));
    $hora_f   = substr($r['hora'], 0, 5);
    $srv      = $r[$lang_col] ?? $r['nombre_de'];
    $precio   = 'CHF ' . number_format($r['precio'], 2);
    $url_reserva = APP_URL . '/mi-reserva.php?codigo=' . $r['codigo'];

    $contenido  = '<h2>' . $tx['confirmacion_titulo'] . '</h2>';
    $contenido .= '<p>' . $tx['confirmacion_saludo'] . ' <strong>' . htmlspecialchars($r['nombre']) . '</strong>!</p>';
    $contenido .= '<p>' . $tx['confirmacion_intro'] . '</p>';
    $contenido .= '<div class="card">';
    $contenido .= '<p><strong>' . $tx['confirmacion_servicio'] . ':</strong> ' . htmlspecialchars($srv) . '</p>';
    $contenido .= '<p><strong>' . $tx['confirmacion_fecha'] . ':</strong> ' . $fecha_f . '</p>';
    $contenido .= '<p><strong>' . $tx['confirmacion_hora'] . ':</strong> ' . $hora_f . '</p>';
    $contenido .= '<p><strong>' . $tx['confirmacion_precio'] . ':</strong> ' . $precio . '</p>';
    $contenido .= '</div>';
    $contenido .= '<p style="margin-top:16px;"><strong>' . ($tx['codigo_label'] ?? 'Código') . ':</strong> <span style="font-size:18px;color:#c9a84c;font-weight:700">' . $r['codigo'] . '</span></p>';
    $contenido .= '<div class="reminder">' . $tx['confirmacion_reminder'] . '</div>';
    $contenido .= '<p style="margin-top:12px;"><a href="' . $url_reserva . '" style="color:#c9a84c">' . ($tx['ver_reserva'] ?? 'Ver mi reserva') . '</a></p>';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = SMTP_HOST; $mail->SMTPAuth = true;
    $mail->Username = SMTP_USER; $mail->Password = SMTP_PASS;
    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = SMTP_PORT; $mail->CharSet = 'UTF-8';
    $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
    $mail->addAddress($r['email'], $r['nombre']);
    $mail->Subject = $tx['confirmacion_subject'];
    $mail->isHTML(true);
    $mail->Body = emailTemplate($tx['confirmacion_titulo'], $contenido, $lang);
    $mail->send();
} catch (Exception $e) {
    error_log('Email confirmar: ' . $e->getMessage());
}

echo json_encode(['success' => true]);