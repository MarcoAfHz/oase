<?php
require_once __DIR__ . '/../../config.php';
session_start();
if (!isset($_SESSION['oase_admin'])) { die(json_encode(['error' => 'No autorizado'])); }
header('Content-Type: application/json');
$fecha  = trim($_POST['fecha'] ?? '');
$motivo = trim($_POST['motivo'] ?? '');
if (!$fecha) { die(json_encode(['error' => 'Fecha requerida'])); }
try {
    $pdo->prepare('INSERT INTO dias_bloqueados (fecha, motivo) VALUES (?, ?)')->execute([$fecha, $motivo]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['error' => 'Fecha ya bloqueada']);
}