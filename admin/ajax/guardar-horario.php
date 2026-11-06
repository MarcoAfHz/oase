<?php
require_once __DIR__ . '/../../config.php';
session_start();
if (!isset($_SESSION['oase_admin'])) { die(json_encode(['error' => 'No autorizado'])); }
header('Content-Type: application/json');
$id     = intval($_POST['id'] ?? 0);
$ini    = trim($_POST['hora_inicio'] ?? '');
$fin    = trim($_POST['hora_fin'] ?? '');
$activo = intval($_POST['activo'] ?? 0);
if (!$id || !$ini || !$fin) { die(json_encode(['error' => 'Datos inválidos'])); }
$pdo->prepare('UPDATE horarios SET hora_inicio=?, hora_fin=?, activo=? WHERE id=?')->execute([$ini, $fin, $activo, $id]);
echo json_encode(['success' => true]);