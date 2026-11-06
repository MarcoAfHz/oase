<?php
require_once __DIR__ . '/../../config.php';
session_start();
if (!isset($_SESSION['oase_admin'])) die(json_encode(['error' => 'No autorizado']));
header('Content-Type: application/json');

$id = intval($_POST['id'] ?? 0);
if (!$id) die(json_encode(['error' => 'ID requerido']));

// Verificar que no tenga reservas activas
$check = $pdo->prepare('SELECT COUNT(*) FROM reservas WHERE servicio_id = ? AND estado = "confirmada"');
$check->execute([$id]);
if ($check->fetchColumn() > 0) {
    die(json_encode(['error' => 'Este servicio tiene reservas activas. No se puede eliminar.']));
}

$pdo->prepare('DELETE FROM servicios WHERE id = ?')->execute([$id]);
echo json_encode(['success' => true]);