<?php
require_once __DIR__ . '/../../config.php';
session_start();
if (!isset($_SESSION['oase_admin'])) die(json_encode(['error' => 'No autorizado']));
header('Content-Type: application/json');

$id = intval($_POST['id'] ?? 0);
if (!$id) die(json_encode(['error' => 'ID requerido']));

$foto = $pdo->prepare('SELECT archivo FROM galeria_fotos WHERE id = ?');
$foto->execute([$id]);
$f = $foto->fetch();
if (!$f) die(json_encode(['error' => 'Foto no encontrada']));

// Borrar archivo físico
$path = __DIR__ . '/../../uploads/' . $f['archivo'];
if (file_exists($path)) unlink($path);

$pdo->prepare('DELETE FROM galeria_fotos WHERE id = ?')->execute([$id]);
echo json_encode(['success' => true]);