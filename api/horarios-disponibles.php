<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

$fecha       = $_GET['fecha'] ?? '';
$servicio_id = intval($_GET['servicio_id'] ?? 0);

if (!$fecha || !$servicio_id) {
    die(json_encode(['horas' => [], 'error' => 'Faltan datos']));
}

// Día de la semana (1=Lun...7=Dom)
$dia = date('N', strtotime($fecha));

// Domingo cerrado
if ($dia == 7) {
    die(json_encode(['horas' => [], 'msg' => 'closed']));
}

// Día bloqueado
$bloq = $pdo->prepare('SELECT id FROM dias_bloqueados WHERE fecha = ?');
$bloq->execute([$fecha]);
if ($bloq->fetch()) {
    die(json_encode(['horas' => [], 'msg' => 'blocked']));
}

// Horario del día
$stmt_h = $pdo->prepare('SELECT * FROM horarios WHERE dia_semana = ? AND activo = 1');
$stmt_h->execute([$dia]);
$horario = $stmt_h->fetch();
if (!$horario) {
    die(json_encode(['horas' => [], 'msg' => 'no_schedule']));
}

// Duración del servicio solicitado
$stmt_s = $pdo->prepare('SELECT duracion_min FROM servicios WHERE id = ? AND activo = 1');
$stmt_s->execute([$servicio_id]);
$servicio = $stmt_s->fetch();
if (!$servicio) {
    die(json_encode(['horas' => [], 'error' => 'Servicio no encontrado']));
}
$duracion = intval($servicio['duracion_min']);

// Redondear duración al múltiplo de 30 más cercano hacia arriba
// Ej: 45 min → 60 min (2 slots de 30), 30 min → 30 min (1 slot)
$slots_necesarios = ceil($duracion / 30);
$duracion_real    = $slots_necesarios * 30; // duración efectiva en minutos

// Obtener reservas confirmadas ese día con su duración
$stmt_r = $pdo->prepare('
    SELECT r.hora, s.duracion_min
    FROM reservas r
    JOIN servicios s ON s.id = r.servicio_id
    WHERE r.fecha = ? AND r.estado != "cancelada"
');
$stmt_r->execute([$fecha]);
$reservas_dia = $stmt_r->fetchAll();

// Construir array de minutos ocupados
$minutos_ocupados = [];
foreach ($reservas_dia as $r) {
    $inicio_min  = timeToMinutes($r['hora']);
    $dur_slots   = ceil(intval($r['duracion_min']) / 30);
    $dur_efectiva = $dur_slots * 30;
    for ($m = $inicio_min; $m < $inicio_min + $dur_efectiva; $m += 30) {
        $minutos_ocupados[] = $m;
    }
}

// Generar slots cada 30 min
$inicio_min = timeToMinutes($horario['hora_inicio']);
$fin_min    = timeToMinutes($horario['hora_fin']);
$ahora_min  = timeToMinutes(date('H:i:s'));
$es_hoy     = ($fecha === date('Y-m-d'));

$horas = [];
$slot = $inicio_min;

while ($slot + $duracion_real <= $fin_min) {
    // Si es hoy, no mostrar slots que ya pasaron
    if ($es_hoy && $slot <= $ahora_min) {
        $slot += 30;
        continue;
    }

    // Comprobar si algún slot necesario está ocupado
    $disponible = true;
    for ($m = $slot; $m < $slot + $duracion_real; $m += 30) {
        if (in_array($m, $minutos_ocupados)) {
            $disponible = false;
            break;
        }
    }

    if ($disponible) {
        $horas[] = minutesToTime($slot);
    }

    $slot += 30;
}

// Generar también slots que sobrepasan el cierre (Edwin decide)
$horas_limite = [];
$slot2 = $inicio_min;
while ($slot2 + 30 <= $fin_min) {
    if ($slot2 + $duracion_real > $fin_min) {
        // Este slot sobrepasa — comprobar si está libre
        $disponible2 = true;
        for ($m = $slot2; $m < $slot2 + $duracion_real; $m += 30) {
            if (in_array($m, $minutos_ocupados)) {
                $disponible2 = false;
                break;
            }
        }
        if ($disponible2) {
            if (!$es_hoy || $slot2 > $ahora_min) {
                $horas_limite[] = minutesToTime($slot2);
            }
        }
    }
    $slot2 += 30;
}
$tiene_limite = !empty($horas_limite);

echo json_encode([
    'horas'         => $horas,
    'horas_limite'  => $horas_limite,
    'duracion'      => $duracion,
    'duracion_real' => $duracion_real,
    'slots'         => $slots_necesarios,
    'tiene_limite'  => $tiene_limite,
    'cierre'        => substr($horario['hora_fin'], 0, 5),
]);

// ===== HELPERS =====
function timeToMinutes(string $time): int {
    $parts = explode(':', $time);
    return intval($parts[0]) * 60 + intval($parts[1]);
}
function minutesToTime(int $minutes): string {
    return str_pad(floor($minutes / 60), 2, '0', STR_PAD_LEFT) . ':' . str_pad($minutes % 60, 2, '0', STR_PAD_LEFT);
}