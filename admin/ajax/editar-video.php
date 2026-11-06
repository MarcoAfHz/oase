<?php
require_once __DIR__ . '/../../config.php';
session_start();
if (!isset($_SESSION['oase_admin'])) die(json_encode(['error' => 'No autorizado']));
header('Content-Type: application/json');

$id        = intval($_POST['id'] ?? 0);
$titulo_es = trim($_POST['titulo_es'] ?? '');
$desc_es   = trim($_POST['descripcion_es'] ?? '');
$categoria = trim($_POST['categoria'] ?? 'general');
$tags      = trim($_POST['tags'] ?? '');

if (!$id) die(json_encode(['error' => 'ID requerido']));

$titulo_de = $titulo_es ? deepLTranslate($titulo_es, 'de') : '';
$titulo_fr = $titulo_es ? deepLTranslate($titulo_es, 'fr') : '';
$titulo_en = $titulo_es ? deepLTranslate($titulo_es, 'en') : '';
$desc_de   = $desc_es   ? deepLTranslate($desc_es,   'de') : '';
$desc_fr   = $desc_es   ? deepLTranslate($desc_es,   'fr') : '';
$desc_en   = $desc_es   ? deepLTranslate($desc_es,   'en') : '';

$pdo->prepare('
    UPDATE videos SET
        titulo_es=?, titulo_de=?, titulo_fr=?, titulo_en=?,
        descripcion_es=?, descripcion_de=?, descripcion_fr=?, descripcion_en=?,
        categoria=?, tags=?
    WHERE id=?
')->execute([$titulo_es, $titulo_de, $titulo_fr, $titulo_en, $desc_es, $desc_de, $desc_fr, $desc_en, $categoria, $tags, $id]);

// Reemplazar video si se subió uno nuevo
if (!empty($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
    $file     = $_FILES['archivo'];
    $max_size = 50 * 1024 * 1024;
    $allowed  = ['mp4','mov','webm','quicktime'];
    $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($file['size'] <= $max_size && in_array($ext, $allowed)) {
        $upload_dir = __DIR__ . '/../../uploads/videos/';
        $nombre     = 'vid_' . uniqid() . '.' . $ext;
        if (move_uploaded_file($file['tmp_name'], $upload_dir . $nombre)) {
            // Borrar video anterior
            $old = $pdo->prepare('SELECT archivo FROM videos WHERE id = ?');
            $old->execute([$id]);
            $oldV = $old->fetch();
            if ($oldV) { $path = $upload_dir . $oldV['archivo']; if (file_exists($path)) unlink($path); }
            $pdo->prepare('UPDATE videos SET archivo = ? WHERE id = ?')->execute([$nombre, $id]);
        }
    }
}

echo json_encode(['success' => true]);  