<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['error' => 'Método no permitido']));
}

$nombre       = trim($_POST['nombre'] ?? '');
$email        = trim($_POST['email'] ?? '');
$email_conf   = trim($_POST['email_confirmacion'] ?? '');
$telefono     = trim($_POST['telefono'] ?? '');
$servicio_id  = intval($_POST['servicio_id'] ?? 0);
$fecha        = trim($_POST['fecha'] ?? '');
$hora         = trim($_POST['hora'] ?? '');
$lang         = in_array($_POST['lang'] ?? 'de', ['de','es','fr','en']) ? $_POST['lang'] : 'de';

// ===== VALIDACIONES =====
$errores = [];
if (empty($nombre))     $errores[] = 'nombre';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errores[] = 'email_invalido';
if ($email !== $email_conf) $errores[] = 'email_no_coincide';
if ($servicio_id <= 0)  $errores[] = 'servicio';
if (empty($fecha))      $errores[] = 'fecha';
if (empty($hora))       $errores[] = 'hora';

if (!empty($errores)) {
    http_response_code(400);
    die(json_encode(['error' => $errores[0], 'errores' => $errores]));
}

// Obtener servicio
$stmt = $pdo->prepare('SELECT * FROM servicios WHERE id = ? AND activo = 1');
$stmt->execute([$servicio_id]);
$servicio = $stmt->fetch();
if (!$servicio) {
    die(json_encode(['error' => 'servicio_no_encontrado']));
}

$duracion      = intval($servicio['duracion_min']);
$slots_needed  = ceil($duracion / 30);
$duracion_real = $slots_needed * 30;

// ===== VERIFICAR EMAIL DUPLICADO MISMO DÍA =====
$dup = $pdo->prepare('SELECT codigo FROM reservas WHERE email = ? AND fecha = ? AND estado != "cancelada"');
$dup->execute([$email, $fecha]);
$dup_reserva = $dup->fetch();
if ($dup_reserva) {
    die(json_encode([
        'error'  => 'email_duplicado',
        'codigo' => $dup_reserva['codigo'],
    ]));
}

// ===== VERIFICAR DISPONIBILIDAD (doble check) =====
$hora_inicio_min = timeToMinutes($hora);
$hora_fin_min    = $hora_inicio_min + $duracion_real;

$stmt_r = $pdo->prepare('
    SELECT r.hora, s.duracion_min
    FROM reservas r
    JOIN servicios s ON s.id = r.servicio_id
    WHERE r.fecha = ? AND r.estado != "cancelada"
');
$stmt_r->execute([$fecha]);
$reservas_dia = $stmt_r->fetchAll();

$minutos_ocupados = [];
foreach ($reservas_dia as $r) {
    $ini  = timeToMinutes($r['hora']);
    $dur  = ceil(intval($r['duracion_min']) / 30) * 30;
    for ($m = $ini; $m < $ini + $dur; $m += 30) {
        $minutos_ocupados[] = $m;
    }
}

for ($m = $hora_inicio_min; $m < $hora_fin_min; $m += 30) {
    if (in_array($m, $minutos_ocupados)) {
        die(json_encode(['error' => 'slot_ocupado']));
    }
}

// ===== VERIFICAR SI SOBREPASA HORARIO =====
$dia     = date('N', strtotime($fecha));
$stmt_h  = $pdo->prepare('SELECT hora_fin FROM horarios WHERE dia_semana = ? AND activo = 1');
$stmt_h->execute([$dia]);
$horario = $stmt_h->fetch();
$cierre_min = $horario ? timeToMinutes($horario['hora_fin']) : 19*60;

$sobrepasa_horario = ($hora_fin_min > $cierre_min);
$estado = $sobrepasa_horario ? 'pendiente_confirmacion' : 'confirmada';

// ===== GENERAR CÓDIGO ÚNICO =====
do {
    $codigo = 'OASE-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
    $check_codigo = $pdo->prepare('SELECT id FROM reservas WHERE codigo = ?');
    $check_codigo->execute([$codigo]);
} while ($check_codigo->fetch());

// ===== GUARDAR RESERVA =====
$insert = $pdo->prepare('
    INSERT INTO reservas (codigo, nombre, email, email_confirmacion, telefono, servicio_id, fecha, hora, estado, lang)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
');
$insert->execute([$codigo, $nombre, $email, $email_conf, $telefono, $servicio_id, $fecha, $hora, $estado, $lang]);
$reserva_id = $pdo->lastInsertId();

$fecha_fmt  = date('d/m/Y', strtotime($fecha));
$hora_fmt   = $hora;
$lang_col   = 'nombre_' . $lang;
$srv_nombre = $servicio[$lang_col] ?? $servicio['nombre_de'];
$precio     = 'CHF ' . number_format($servicio['precio'], 2);
$cierre_fmt = floor($cierre_min/60) . ':' . str_pad($cierre_min%60, 2, '0', STR_PAD_LEFT);
$fin_fmt    = floor($hora_fin_min/60) . ':' . str_pad($hora_fin_min%60, 2, '0', STR_PAD_LEFT);

// ===== ENVIAR EMAIL AL CLIENTE =====
$email_enviado = false;
try {
    require_once __DIR__ . '/../vendor/autoload.php';
    $tx = getEmailTexts($lang);

    $url_reserva = APP_URL . '/mi-reserva.php?codigo=' . $codigo;

    if ($sobrepasa_horario) {
        // Email de reserva pendiente de confirmación
        $contenido  = '<h2>' . ($tx['pendiente_titulo'] ?? 'Tu solicitud está siendo revisada') . '</h2>';
        $contenido .= '<p>' . ($tx['pendiente_intro'] ?? 'Hemos recibido tu solicitud. Edwin la revisará y te confirmará en breve.') . '</p>';
        $contenido .= '<div class="card">';
        $contenido .= '<p><strong>' . $tx['confirmacion_servicio'] . ':</strong> ' . htmlspecialchars($srv_nombre) . '</p>';
        $contenido .= '<p><strong>' . $tx['confirmacion_fecha'] . ':</strong> ' . $fecha_fmt . '</p>';
        $contenido .= '<p><strong>' . $tx['confirmacion_hora'] . ':</strong> ' . $hora_fmt . ' – ' . $fin_fmt . '</p>';
        $contenido .= '<p><strong>' . $tx['confirmacion_precio'] . ':</strong> ' . $precio . '</p>';
        $contenido .= '</div>';
        $contenido .= '<div class="reminder">' . sprintf($tx['pendiente_aviso'] ?? 'El cierre es a las %s y tu servicio finalizaría a las %s. Edwin revisará si es posible.', $cierre_fmt, $fin_fmt) . '</div>';
        $contenido .= '<p style="margin-top:16px;"><strong>' . ($tx['codigo_label'] ?? 'Tu código de reserva') . ':</strong> <span style="font-size:18px;color:#c9a84c;font-weight:700">' . $codigo . '</span></p>';
        $contenido .= '<p style="margin-top:12px;"><a href="' . $url_reserva . '" style="color:#c9a84c">' . ($tx['ver_reserva'] ?? 'Ver mi reserva') . '</a></p>';
        $subject = $tx['pendiente_subject'] ?? 'Solicitud recibida — Oase';
    } else {
        // Email de confirmación normal
        $contenido  = '<h2>' . $tx['confirmacion_titulo'] . '</h2>';
        $contenido .= '<p>' . $tx['confirmacion_saludo'] . ' <strong>' . htmlspecialchars($nombre) . '</strong>!</p>';
        $contenido .= '<p>' . $tx['confirmacion_intro'] . '</p>';
        $contenido .= '<div class="card">';
        $contenido .= '<p><strong>' . $tx['confirmacion_servicio'] . ':</strong> ' . htmlspecialchars($srv_nombre) . '</p>';
        $contenido .= '<p><strong>' . $tx['confirmacion_fecha'] . ':</strong> ' . $fecha_fmt . '</p>';
        $contenido .= '<p><strong>' . $tx['confirmacion_hora'] . ':</strong> ' . $hora_fmt . '</p>';
        $contenido .= '<p><strong>' . $tx['confirmacion_precio'] . ':</strong> ' . $precio . '</p>';
        $contenido .= '</div>';
        $contenido .= '<p style="margin-top:16px;"><strong>' . ($tx['codigo_label'] ?? 'Tu código de reserva') . ':</strong> <span style="font-size:18px;color:#c9a84c;font-weight:700">' . $codigo . '</span></p>';
        $contenido .= '<div class="reminder">' . $tx['confirmacion_reminder'] . '</div>';
        $contenido .= '<p style="margin-top:12px;">';
        $contenido .= '<a href="' . $url_reserva . '" style="display:inline-block;background:#c9a84c;color:#0f0f0f;padding:10px 20px;text-decoration:none;font-weight:700;margin-right:8px">' . ($tx['ver_reserva'] ?? 'Ver mi reserva') . '</a>';
        $contenido .= '<a href="' . $url_reserva . '&accion=cancelar" style="display:inline-block;background:#fee2e2;color:#991b1b;padding:10px 20px;text-decoration:none;font-weight:700">' . ($tx['cancelar_reserva'] ?? 'Cancelar') . '</a>';
        $contenido .= '</p>';
        $contenido .= '<p style="margin-top:16px;font-size:12px;color:#888">' . ($tx['politica_aviso'] ?? 'Puedes cancelar hasta 2 horas antes de tu cita.') . '</p>';
        $contenido .= '<p style="margin-top:8px;">' . $tx['confirmacion_cancel'] . '</p>';
        $subject = $tx['confirmacion_subject'];
    }

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
    $mail->addAddress($email, $nombre);
    $mail->Subject = $subject;
    $mail->isHTML(true);
    $mail->Body = emailTemplate($tx['confirmacion_titulo'], $contenido, $lang);
    $mail->send();
    $email_enviado = true;
} catch (Exception $e) {
    error_log('Error email: ' . $e->getMessage());
}

// ===== EMAIL RESUMEN A EDWIN =====
try {
    $mail2 = new PHPMailer\PHPMailer\PHPMailer(true);
    $mail2->isSMTP();
    $mail2->Host       = SMTP_HOST;
    $mail2->SMTPAuth   = true;
    $mail2->Username   = SMTP_USER;
    $mail2->Password   = SMTP_PASS;
    $mail2->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail2->Port       = SMTP_PORT;
    $mail2->CharSet    = 'UTF-8';
    $mail2->setFrom(SMTP_FROM, SMTP_FROM_NAME);
    $mail2->addAddress(SMTP_USER); // email de Edwin — cambiar en producción
    $mail2->Subject = ($sobrepasa_horario ? '⚠️ REVISAR — ' : '✅ Nueva reserva — ') . $nombre . ' · ' . $fecha_fmt . ' ' . $hora_fmt;
    $mail2->isHTML(true);
    $contenido_edwin  = '<h2>' . ($sobrepasa_horario ? '⚠️ Reserva pendiente de confirmación' : '✅ Nueva reserva') . '</h2>';
    $contenido_edwin .= '<div class="card">';
    $contenido_edwin .= '<p><strong>Código:</strong> ' . $codigo . '</p>';
    $contenido_edwin .= '<p><strong>Cliente:</strong> ' . htmlspecialchars($nombre) . '</p>';
    $contenido_edwin .= '<p><strong>Email:</strong> ' . htmlspecialchars($email) . '</p>';
    $contenido_edwin .= '<p><strong>Teléfono:</strong> ' . htmlspecialchars($telefono ?: '—') . '</p>';
    $contenido_edwin .= '<p><strong>Servicio:</strong> ' . htmlspecialchars($srv_nombre) . ' (' . $duracion . ' min)</p>';
    $contenido_edwin .= '<p><strong>Fecha:</strong> ' . $fecha_fmt . '</p>';
    $contenido_edwin .= '<p><strong>Hora:</strong> ' . $hora_fmt . ' – ' . $fin_fmt . '</p>';
    $contenido_edwin .= '<p><strong>Idioma cliente:</strong> ' . strtoupper($lang) . '</p>';
    if ($sobrepasa_horario) {
        $contenido_edwin .= '<p style="color:#c44"><strong>⚠️ Este servicio finaliza a las ' . $fin_fmt . ' (cierre: ' . $cierre_fmt . '). Confirma o cancela desde el panel.</strong></p>';
    }
    $contenido_edwin .= '</div>';
    $mail2->Body = emailTemplate('Nueva reserva', $contenido_edwin, 'es');
    $mail2->send();
} catch (Exception $e) {
    error_log('Error email Edwin: ' . $e->getMessage());
}

// ===== WHATSAPP LINK =====
$wa_data = [
    'nombre'          => $nombre,
    'telefono'        => $telefono,
    'servicio_nombre' => $srv_nombre,
    'fecha'           => $fecha,
    'hora'            => $hora,
    'codigo'          => $codigo,
    'sobrepasa'       => $sobrepasa_horario,
    'fin_hora'        => $fin_fmt,
    'cierre'          => $cierre_fmt,
];
$wa_link = whatsappLink($wa_data);

echo json_encode([
    'success'      => true,
    'reserva_id'   => $reserva_id,
    'codigo'       => $codigo,
    'estado'       => $estado,
    'email_ok'     => $email_enviado,
    'whatsapp_url' => $wa_link,
    'sobrepasa'    => $sobrepasa_horario,
]);

function timeToMinutes(string $time): int {
    $parts = explode(':', $time);
    return intval($parts[0]) * 60 + intval($parts[1]);
}