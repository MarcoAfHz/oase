<?php
require_once __DIR__ . '/../../config.php';
session_start();
if (!isset($_SESSION['oase_admin'])) die(json_encode(['error' => 'No autorizado']));
header('Content-Type: application/json');

$id     = intval($_POST['id'] ?? 0);
$motivo = trim($_POST['motivo'] ?? '');

if (!$id) die(json_encode(['error' => 'ID requerido']));

// Obtener reserva con datos del cliente y servicio
$stmt = $pdo->prepare('
    SELECT r.*, s.nombre_de, s.nombre_es, s.nombre_fr, s.nombre_en, s.precio
    FROM reservas r
    JOIN servicios s ON s.id = r.servicio_id
    WHERE r.id = ?
');
$stmt->execute([$id]);
$r = $stmt->fetch();

if (!$r) die(json_encode(['error' => 'Reserva no encontrada']));

// Actualizar estado
$pdo->prepare('UPDATE reservas SET estado = "cancelada", motivo_cancelacion = ? WHERE id = ?')
    ->execute([$motivo, $id]);

// Email cancelación al cliente en su idioma
try {
    require_once __DIR__ . '/../../vendor/autoload.php';
    $lang     = $r['lang'] ?? 'de';
    $lang_col = 'nombre_' . $lang;
    $tx       = getEmailTexts($lang);
    $fecha_fmt = date('d/m/Y', strtotime($r['fecha']));
    $hora_fmt  = substr($r['hora'], 0, 5);
    $srv_nombre = $r[$lang_col] ?? $r['nombre_de'];

    $contenido  = '<h2>' . $tx['cancelacion_titulo'] . '</h2>';
    $contenido .= '<p>' . $tx['cancelacion_saludo'] . ' <strong>' . htmlspecialchars($r['nombre']) . '</strong> ' . $tx['cancelacion_intro'] . '</p>';
    $contenido .= '<div class="card">';
    $contenido .= '<p><strong>' . $tx['confirmacion_servicio'] . ':</strong> ' . htmlspecialchars($srv_nombre) . '</p>';
    $contenido .= '<p><strong>' . $tx['confirmacion_fecha'] . ':</strong> ' . $fecha_fmt . '</p>';
    $contenido .= '<p><strong>' . $tx['confirmacion_hora'] . ':</strong> ' . $hora_fmt . '</p>';
    if ($motivo) {
        $contenido .= '<p><strong>' . $tx['cancelacion_motivo'] . ':</strong> ' . htmlspecialchars($motivo) . '</p>';
    }
    $contenido .= '</div>';
    $contenido .= '<p style="margin-top:16px;">' . $tx['cancelacion_nuevo'] . '</p>';
    $contenido .= '<p><a href="' . APP_URL . '/booking.php" style="color:#c9a84c">' . $tx['btn_book'] . '</a></p>';

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
    $mail->Subject = $tx['cancelacion_subject'];
    $mail->isHTML(true);
    $mail->Body = emailTemplate($tx['cancelacion_titulo'], $contenido, $lang);
    $mail->send();
} catch (Exception $e) {
    error_log('Error email cancelacion: ' . $e->getMessage());
}

echo json_encode(['success' => true]);