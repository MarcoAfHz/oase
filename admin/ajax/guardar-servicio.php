<?php
require_once __DIR__ . '/../../config.php';
session_start();
if (!isset($_SESSION['oase_admin'])) die(json_encode(['error' => 'No autorizado']));
header('Content-Type: application/json');
$id         = intval($_POST['id'] ?? 0);
$nombre_es  = trim($_POST['nombre_es'] ?? '');
$nombre_de  = trim($_POST['nombre_de'] ?? '');
$nombre_fr  = trim($_POST['nombre_fr'] ?? '');
$nombre_en  = trim($_POST['nombre_en'] ?? '');
$precio     = floatval($_POST['precio'] ?? 0);
$categoria  = trim($_POST['categoria'] ?? '');
if (!$id || !$nombre_es || $precio <= 0) die(json_encode(['error' => 'Datos inválidos']));
$pdo->prepare('UPDATE servicios SET nombre_es=?,nombre_de=?,nombre_fr=?,nombre_en=?,precio=?,categoria=? WHERE id=?')
    ->execute([$nombre_es,$nombre_de,$nombre_fr,$nombre_en,$precio,$categoria,$id]);
echo json_encode(['success' => true]);