<?php
require_once __DIR__ . '/../../config.php';
session_start();
if (!isset($_SESSION['oase_admin'])) die(json_encode(['error' => 'No autorizado']));
header('Content-Type: application/json');

$id = intval($_POST['id'] ?? 0);
if (!$id) die(json_encode(['error' => 'ID requerido']));

// Borrar archivos físicos
$fotos = $pdo->prepare('SELECT archivo FROM galeria_fotos WHERE grupo_id = ?');
$fotos->execute([$id]);
foreach ($fotos->fetchAll() as $f) {
    $path = __DIR__ . '/../../uploads/' . $f['archivo'];
    if (file_exists($path)) unlink($path);
}

// Borrar de BD (CASCADE borra galeria_fotos)
$pdo->prepare('DELETE FROM galeria_grupos WHERE id = ?')->execute([$id]);
echo json_encode(['success' => true]);