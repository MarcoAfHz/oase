<?php
require_once __DIR__ . '/../../config.php';
session_start();
if (!isset($_SESSION['oase_admin'])) die(json_encode(['error' => 'No autorizado']));
header('Content-Type: application/json');

$desde = trim($_GET['desde'] ?? '');
$hoy   = date('Y-m-d');

// Nuevas reservas desde el último check
$where = $desde ? 'AND r.created_at > ?' : '';
$params = $desde ? [$desde] : [];

$stmt = $pdo->prepare('
    SELECT r.id, r.nombre, r.email, r.telefono, r.fecha, r.hora, r.estado, r.lang, r.created_at,
           s.nombre_es as servicio
    FROM reservas r
    JOIN servicios s ON s.id = r.servicio_id
    WHERE 1=1 ' . $where . '
    ORDER BY r.created_at DESC
    LIMIT 50
');
$stmt->execute($params);
$reservas = $stmt->fetchAll();

// Badge — reservas de hoy
$hoy_count = $pdo->query("SELECT COUNT(*) FROM reservas WHERE fecha = '{$hoy}' AND estado = 'confirmada'")->fetchColumn();

echo json_encode([
    'reservas'   => $reservas,
    'hoy_count'  => $hoy_count,
    'timestamp'  => date('Y-m-d H:i:s'),
]);