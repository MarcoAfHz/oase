<?php
require_once __DIR__ . '/../../config.php';
session_start();
if (!isset($_SESSION['oase_admin'])) die(json_encode(['error' => 'No autorizado']));
header('Content-Type: application/json');

$nombre_es   = trim($_POST['nombre_es'] ?? '');
$nombre_de   = trim($_POST['nombre_de'] ?? '');
$nombre_fr   = trim($_POST['nombre_fr'] ?? '');
$nombre_en   = trim($_POST['nombre_en'] ?? '');
$precio      = floatval($_POST['precio'] ?? 0);
$duracion    = intval($_POST['duracion_min'] ?? 60);
$categoria   = trim($_POST['categoria'] ?? 'corte');
$precio_txt  = trim($_POST['precio_texto'] ?? 'desde / ab');

if (!$nombre_es || $precio <= 0) {
    die(json_encode(['error' => 'Nombre en español y precio son obligatorios']));
}

$pdo->prepare('
    INSERT INTO servicios (nombre_es, nombre_de, nombre_fr, nombre_en, precio, precio_texto, duracion_min, categoria, activo)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)
')->execute([$nombre_es, $nombre_de ?: $nombre_es, $nombre_fr ?: $nombre_es, $nombre_en ?: $nombre_es, $precio, $precio_txt, $duracion, $categoria]);

echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);