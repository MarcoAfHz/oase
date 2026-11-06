<?php
require_once __DIR__ . '/../../config.php';
session_start();
if (!isset($_SESSION['oase_admin'])) { die(json_encode(['error' => 'No autorizado'])); }
header('Content-Type: application/json');
$id = intval($_POST['id'] ?? 0);
if (!$id) { die(json_encode(['error' => 'ID requerido'])); }
$pdo->prepare('DELETE FROM dias_bloqueados WHERE id = ?')->execute([$id]);
echo json_encode(['success' => true]);