<?php
require_once __DIR__ . '/../../config.php';
session_start();
if (!isset($_SESSION['oase_admin'])) die(json_encode(['error' => 'No autorizado']));
header('Content-Type: application/json');

$texto = trim($_POST['texto'] ?? '');
if (!$texto) die(json_encode(['error' => 'Texto requerido']));

$traducciones = [];
foreach (['de', 'fr', 'en'] as $lang) {
    $traducciones[$lang] = deepLTranslate($texto, $lang);
}

echo json_encode(['success' => true, 'traducciones' => $traducciones]);