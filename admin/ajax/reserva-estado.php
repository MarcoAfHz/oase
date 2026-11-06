<?php
require_once __DIR__ . '/../../config.php';
session_start();
if (!isset($_SESSION['oase_admin'])) { die(json_encode(['error' => 'No autorizado'])); }
header('Content-Type: application/json');
$id     = intval($_POST['id'] ?? 0);
$estado = $_POST['estado'] ?? '';
if (!$id || !in_array($estado, ['pendiente','confirmada','cancelada'])) {
    die(json_encode(['error' => 'Datos inválidos']));
}
$pdo->prepare('UPDATE reservas SET estado = ? WHERE id = ?')->execute([$estado, $id]);
echo json_encode(['success' => true]);