<?php
// =============================================
// Oase — Config local (XAMPP)
// =============================================

// Idiomas permitidos
$allowed_langs = ['de', 'es', 'fr', 'en'];

// Base de datos
define('DB_HOST',    'localhost');
define('DB_NAME',    'oase_db');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_CHARSET', 'utf8mb4');

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET,
        DB_USER, DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die(json_encode(['error' => 'Error de conexión: ' . $e->getMessage()]));
}

// WhatsApp Edwin
define('EDWIN_WHATSAPP', '34656514033');

// Email SMTP
define('SMTP_HOST',      'smtp.gmail.com');
define('SMTP_PORT',      587);
define('SMTP_USER',      '');
define('SMTP_PASS',      '');
define('SMTP_FROM',      'reservas@tudominio.com');
define('SMTP_FROM_NAME', 'Oase Friseur & Ästhetik');

// DeepL Free API
define('DEEPL_API_KEY', '');
define('DEEPL_API_URL', 'https://api-free.deepl.com/v2/translate');

// App
define('APP_URL',  'http://oase.test');
define('APP_NAME', 'Oase Friseur & Ästhetik');

date_default_timezone_set('Europe/Zurich');

// =============================================
// WhatsApp link para Edwin (con código)
// =============================================
function whatsappLink(array $r): string {
    $fecha = date('d/m/Y', strtotime($r['fecha']));
    $hora  = $r['hora'];
    $msg   = "*Nueva reserva - Oase*\n\n"
           . "Código: *{$r['codigo']}*\n"
           . "Cliente: {$r['nombre']}\n"
           . "Teléfono: " . ($r['telefono'] ?: '—') . "\n"
           . "Servicio: {$r['servicio_nombre']}\n"
           . "Fecha: {$fecha}\n"
           . "Hora: {$hora} – {$r['fin_hora']}\n";
    if (!empty($r['sobrepasa'])) {
        $msg .= "\n⚠️ Este servicio finaliza a las {$r['fin_hora']} (cierre: {$r['cierre']}). Por favor confirma si puedes atenderlo.";
    }
    return 'https://wa.me/' . EDWIN_WHATSAPP . '?text=' . urlencode($msg);
}

// =============================================
// Traducir con DeepL
// =============================================
function deepLTranslate(string $text, string $target_lang): string {
    $target = strtoupper($target_lang === 'en' ? 'EN-GB' : $target_lang);
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => DEEPL_API_URL,
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => ['Authorization: DeepL-Auth-Key ' . DEEPL_API_KEY, 'Content-Type: application/json'],
        CURLOPT_POSTFIELDS     => json_encode(['text' => [$text], 'target_lang' => $target]),
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response, true);
    return $data['translations'][0]['text'] ?? $text;
}

// =============================================
// Textos de email por idioma
// =============================================
function getEmailTexts(string $lang): array {
    $texts = [
        'de' => [
            'confirmacion_subject'   => 'Terminbestätigung — Oase Friseur & Ästhetik',
            'confirmacion_titulo'    => 'Ihr Termin ist bestätigt',
            'confirmacion_saludo'    => 'Vielen Dank für Ihre Buchung,',
            'confirmacion_intro'     => 'Hier sind die Details Ihres Termins:',
            'confirmacion_servicio'  => 'Leistung',
            'confirmacion_fecha'     => 'Datum',
            'confirmacion_hora'      => 'Uhrzeit',
            'confirmacion_precio'    => 'Preis',
            'confirmacion_reminder'  => 'Wir senden Ihnen 2 Stunden vor Ihrem Termin eine Erinnerung.',
            'confirmacion_cancel'    => 'Um Ihren Termin zu stornieren, nutzen Sie Ihren Buchungscode oder kontaktieren Sie uns per WhatsApp.',
            'cancelacion_subject'    => 'Terminabsage — Oase Friseur & Ästhetik',
            'cancelacion_titulo'     => 'Ihr Termin wurde storniert',
            'cancelacion_saludo'     => 'Leider müssen wir Ihnen mitteilen,',
            'cancelacion_intro'      => 'dass Ihr Termin storniert wurde:',
            'cancelacion_motivo'     => 'Grund der Absage',
            'cancelacion_nuevo'      => 'Sie können gerne einen neuen Termin buchen.',
            'recordatorio_subject'   => 'Terminerinnerung — Oase Friseur & Ästhetik',
            'recordatorio_titulo'    => 'Ihr Termin ist in 2 Stunden',
            'recordatorio_saludo'    => 'Wir erinnern Sie daran,',
            'recordatorio_intro'     => 'dass Sie heute einen Termin haben:',
            'pendiente_subject'      => 'Buchungsanfrage erhalten — Oase Friseur & Ästhetik',
            'pendiente_titulo'       => 'Ihre Anfrage wird geprüft',
            'pendiente_intro'        => 'Wir haben Ihre Anfrage erhalten. Edwin wird sie prüfen und Sie in Kürze bestätigen.',
            'pendiente_aviso'        => 'Der Salon schließt um %s und Ihr Service würde um %s enden. Edwin prüft, ob dies möglich ist.',
            'btn_book'               => 'Neuen Termin buchen',
            'codigo_label'           => 'Ihr Buchungscode',
            'ver_reserva'            => 'Buchung ansehen',
            'cancelar_reserva'       => 'Termin stornieren',
            'politica_aviso'         => 'Sie können bis 2 Stunden vor dem Termin kostenfrei stornieren.',
        ],
        'es' => [
            'confirmacion_subject'   => 'Reserva confirmada — Oase Friseur & Ästhetik',
            'confirmacion_titulo'    => 'Tu cita está confirmada',
            'confirmacion_saludo'    => '¡Gracias por reservar con nosotros,',
            'confirmacion_intro'     => 'Aquí tienes los detalles de tu cita:',
            'confirmacion_servicio'  => 'Servicio',
            'confirmacion_fecha'     => 'Fecha',
            'confirmacion_hora'      => 'Hora',
            'confirmacion_precio'    => 'Precio',
            'confirmacion_reminder'  => 'Te enviaremos un recordatorio 2 horas antes de tu cita.',
            'confirmacion_cancel'    => 'Para cancelar, usa tu código de reserva o contáctanos por WhatsApp.',
            'cancelacion_subject'    => 'Cita cancelada — Oase Friseur & Ästhetik',
            'cancelacion_titulo'     => 'Tu cita ha sido cancelada',
            'cancelacion_saludo'     => 'Lamentamos informarte de que',
            'cancelacion_intro'      => 'tu cita ha sido cancelada:',
            'cancelacion_motivo'     => 'Motivo de la cancelación',
            'cancelacion_nuevo'      => 'Puedes reservar una nueva cita cuando quieras.',
            'recordatorio_subject'   => 'Recordatorio de cita — Oase Friseur & Ästhetik',
            'recordatorio_titulo'    => 'Tu cita es en 2 horas',
            'recordatorio_saludo'    => 'Te recordamos que',
            'recordatorio_intro'     => 'tienes una cita hoy:',
            'pendiente_subject'      => 'Solicitud recibida — Oase Friseur & Ästhetik',
            'pendiente_titulo'       => 'Tu solicitud está siendo revisada',
            'pendiente_intro'        => 'Hemos recibido tu solicitud. Edwin la revisará y te confirmará en breve.',
            'pendiente_aviso'        => 'El cierre es a las %s y tu servicio finalizaría a las %s. Edwin revisará si es posible.',
            'btn_book'               => 'Reservar nueva cita',
            'codigo_label'           => 'Tu código de reserva',
            'ver_reserva'            => 'Ver mi reserva',
            'cancelar_reserva'       => 'Cancelar cita',
            'politica_aviso'         => 'Puedes cancelar gratis hasta 2 horas antes de tu cita.',
        ],
        'fr' => [
            'confirmacion_subject'   => 'Rendez-vous confirmé — Oase Friseur & Ästhetik',
            'confirmacion_titulo'    => 'Votre rendez-vous est confirmé',
            'confirmacion_saludo'    => 'Merci pour votre réservation,',
            'confirmacion_intro'     => 'Voici les détails de votre rendez-vous :',
            'confirmacion_servicio'  => 'Service',
            'confirmacion_fecha'     => 'Date',
            'confirmacion_hora'      => 'Heure',
            'confirmacion_precio'    => 'Prix',
            'confirmacion_reminder'  => 'Nous vous enverrons un rappel 2 heures avant votre rendez-vous.',
            'confirmacion_cancel'    => 'Pour annuler, utilisez votre code de réservation ou contactez-nous via WhatsApp.',
            'cancelacion_subject'    => 'Rendez-vous annulé — Oase Friseur & Ästhetik',
            'cancelacion_titulo'     => 'Votre rendez-vous a été annulé',
            'cancelacion_saludo'     => 'Nous sommes désolés de vous informer que',
            'cancelacion_intro'      => 'votre rendez-vous a été annulé :',
            'cancelacion_motivo'     => 'Motif de l\'annulation',
            'cancelacion_nuevo'      => 'Vous pouvez réserver un nouveau rendez-vous quand vous le souhaitez.',
            'recordatorio_subject'   => 'Rappel de rendez-vous — Oase Friseur & Ästhetik',
            'recordatorio_titulo'    => 'Votre rendez-vous est dans 2 heures',
            'recordatorio_saludo'    => 'Nous vous rappelons que',
            'recordatorio_intro'     => 'vous avez un rendez-vous aujourd\'hui :',
            'pendiente_subject'      => 'Demande reçue — Oase Friseur & Ästhetik',
            'pendiente_titulo'       => 'Votre demande est en cours d\'examen',
            'pendiente_intro'        => 'Nous avons reçu votre demande. Edwin la vérifiera et vous confirmera sous peu.',
            'pendiente_aviso'        => 'La fermeture est à %s et votre service se terminerait à %s. Edwin vérifiera si c\'est possible.',
            'btn_book'               => 'Prendre un nouveau RDV',
            'codigo_label'           => 'Votre code de réservation',
            'ver_reserva'            => 'Voir mon RDV',
            'cancelar_reserva'       => 'Annuler le RDV',
            'politica_aviso'         => 'Vous pouvez annuler gratuitement jusqu\'à 2 heures avant votre rendez-vous.',
        ],
        'en' => [
            'confirmacion_subject'   => 'Booking confirmed — Oase Friseur & Ästhetik',
            'confirmacion_titulo'    => 'Your appointment is confirmed',
            'confirmacion_saludo'    => 'Thank you for booking with us,',
            'confirmacion_intro'     => 'Here are your appointment details:',
            'confirmacion_servicio'  => 'Service',
            'confirmacion_fecha'     => 'Date',
            'confirmacion_hora'      => 'Time',
            'confirmacion_precio'    => 'Price',
            'confirmacion_reminder'  => 'We will send you a reminder 2 hours before your appointment.',
            'confirmacion_cancel'    => 'To cancel, use your booking code or contact us via WhatsApp.',
            'cancelacion_subject'    => 'Appointment cancelled — Oase Friseur & Ästhetik',
            'cancelacion_titulo'     => 'Your appointment has been cancelled',
            'cancelacion_saludo'     => 'We regret to inform you that',
            'cancelacion_intro'      => 'your appointment has been cancelled:',
            'cancelacion_motivo'     => 'Reason for cancellation',
            'cancelacion_nuevo'      => 'You are welcome to book a new appointment.',
            'recordatorio_subject'   => 'Appointment reminder — Oase Friseur & Ästhetik',
            'recordatorio_titulo'    => 'Your appointment is in 2 hours',
            'recordatorio_saludo'    => 'This is a reminder that',
            'recordatorio_intro'     => 'you have an appointment today:',
            'pendiente_subject'      => 'Request received — Oase Friseur & Ästhetik',
            'pendiente_titulo'       => 'Your request is being reviewed',
            'pendiente_intro'        => 'We have received your request. Edwin will review it and confirm shortly.',
            'pendiente_aviso'        => 'The salon closes at %s and your service would end at %s. Edwin will check if this is possible.',
            'btn_book'               => 'Book a new appointment',
            'codigo_label'           => 'Your booking code',
            'ver_reserva'            => 'View my booking',
            'cancelar_reserva'       => 'Cancel appointment',
            'politica_aviso'         => 'You can cancel free of charge up to 2 hours before your appointment.',
        ],
    ];
    return $texts[$lang] ?? $texts['de'];
}

// =============================================
// Plantilla email
// =============================================
function emailTemplate(string $titulo, string $contenido, string $lang = 'de'): string {
    $url = APP_URL;
    return '<!DOCTYPE html><html><head><meta charset="UTF-8">
<style>
body{font-family:Arial,sans-serif;background:#f5f5f5;margin:0;padding:0}
.wrap{max-width:560px;margin:40px auto;background:#fff;border-radius:8px;overflow:hidden}
.header{background:#0f0f0f;padding:32px;text-align:center}
.header h1{color:#c9a84c;font-size:22px;margin:0;letter-spacing:4px}
.header p{color:#888;font-size:11px;margin:6px 0 0;letter-spacing:2px}
.body{padding:36px 32px}
.body h2{color:#1a1a1a;font-size:18px;margin-bottom:8px}
.body p{color:#555;font-size:14px;line-height:1.7;margin:0 0 12px}
.card{background:#faf8f4;border-left:3px solid #c9a84c;padding:20px 24px;margin:20px 0;border-radius:4px}
.card p{margin:6px 0;font-size:14px;color:#333}
.card strong{color:#0f0f0f}
.reminder{background:#fff8e6;border:1px solid #f0d080;padding:14px 18px;border-radius:4px;margin-top:16px;font-size:13px;color:#8a6a00}
.footer{background:#0f0f0f;padding:20px;text-align:center}
.footer p{color:#555;font-size:11px;margin:0}
.footer a{color:#c9a84c;text-decoration:none}
</style></head><body>
<div class="wrap">
  <div class="header"><h1>OASE</h1><p>FRISEUR &amp; ÄSTHETIK · ZÜRICH</p></div>
  <div class="body">' . $contenido . '</div>
  <div class="footer"><p>Oase Friseur &amp; Ästhetik · Zürich<br><a href="' . $url . '">' . $url . '</a></p></div>
</div></body></html>';
}