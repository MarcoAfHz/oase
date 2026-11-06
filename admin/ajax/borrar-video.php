<?php
require_once __DIR__ . '/../../config.php';
session_start();
if (!isset($_SESSION['oase_admin'])) die(json_encode(['error' => 'No autorizado']));
header('Content-Type: application/json');

$id = intval($_POST['id'] ?? 0);
if (!$id) die(json_encode(['error' => 'ID requerido']));

$stmt = $pdo->prepare('SELECT archivo FROM videos WHERE id = ?');
$stmt->execute([$id]);
$v = $stmt->fetch();
if (!$v) die(json_encode(['error' => 'Video no encontrado']));

$path = __DIR__ . '/../../uploads/videos/' . $v['archivo'];
if (file_exists($path)) unlink($path);

$pdo->prepare('DELETE FROM videos WHERE id = ?')->execute([$id]);
echo json_encode(['success' => true]);