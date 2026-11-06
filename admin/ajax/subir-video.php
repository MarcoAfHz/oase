<?php
require_once __DIR__ . '/../../config.php';
session_start();
if (!isset($_SESSION['oase_admin'])) die(json_encode(['error' => 'No autorizado']));
header('Content-Type: application/json');

$titulo_es = trim($_POST['titulo_es'] ?? '');
$desc_es   = trim($_POST['descripcion_es'] ?? '');
$categoria = trim($_POST['categoria'] ?? 'general');
$tags      = trim($_POST['tags'] ?? '');

if (empty($_FILES['archivo'])) die(json_encode(['error' => 'No se recibió el video']));

$file     = $_FILES['archivo'];
$max_size = 50 * 1024 * 1024;
$allowed  = ['mp4','mov','webm','quicktime'];
$ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if ($file['error'] !== UPLOAD_ERR_OK) die(json_encode(['error' => 'Error al subir']));
if ($file['size'] > $max_size)        die(json_encode(['error' => 'El video supera 50MB']));
if (!in_array($ext, $allowed))        die(json_encode(['error' => 'Formato no permitido']));

$upload_dir = __DIR__ . '/../../uploads/videos/';
if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

$nombre  = 'vid_' . uniqid() . '.' . $ext;
$destino = $upload_dir . $nombre;
if (!move_uploaded_file($file['tmp_name'], $destino)) die(json_encode(['error' => 'No se pudo guardar el archivo']));

$titulo_de = $titulo_es ? deepLTranslate($titulo_es, 'de') : '';
$titulo_fr = $titulo_es ? deepLTranslate($titulo_es, 'fr') : '';
$titulo_en = $titulo_es ? deepLTranslate($titulo_es, 'en') : '';
$desc_de   = $desc_es   ? deepLTranslate($desc_es,   'de') : '';
$desc_fr   = $desc_es   ? deepLTranslate($desc_es,   'fr') : '';
$desc_en   = $desc_es   ? deepLTranslate($desc_es,   'en') : '';

$pdo->prepare('
    INSERT INTO videos (archivo, titulo_es, titulo_de, titulo_fr, titulo_en, descripcion_es, descripcion_de, descripcion_fr, descripcion_en, categoria, tags)
    VALUES (?,?,?,?,?,?,?,?,?,?,?)
')->execute([$nombre, $titulo_es, $titulo_de, $titulo_fr, $titulo_en, $desc_es, $desc_de, $desc_fr, $desc_en, $categoria, $tags]);

echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);