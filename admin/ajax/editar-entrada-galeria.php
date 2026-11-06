<?php
require_once __DIR__ . '/../../config.php';
session_start();
if (!isset($_SESSION['oase_admin'])) die(json_encode(['error' => 'No autorizado']));
header('Content-Type: application/json');

$id        = intval($_POST['id'] ?? 0);
$titulo_es = trim($_POST['titulo'] ?? '');
$desc_es   = trim($_POST['descripcion'] ?? '');
$categoria = trim($_POST['categoria'] ?? 'general');
$tags      = trim($_POST['tags'] ?? '');

if (!$id) die(json_encode(['error' => 'ID requerido']));

// Obtener tipo del grupo
$grupo = $pdo->prepare('SELECT tipo FROM galeria_grupos WHERE id = ?');
$grupo->execute([$id]);
$g = $grupo->fetch();
if (!$g) die(json_encode(['error' => 'Entrada no encontrada']));
$tipo = $g['tipo'];

// Traducir con DeepL
$titulo_de = $titulo_es ? deepLTranslate($titulo_es, 'de') : '';
$titulo_fr = $titulo_es ? deepLTranslate($titulo_es, 'fr') : '';
$titulo_en = $titulo_es ? deepLTranslate($titulo_es, 'en') : '';
$desc_de   = $desc_es   ? deepLTranslate($desc_es,   'de') : '';
$desc_fr   = $desc_es   ? deepLTranslate($desc_es,   'fr') : '';
$desc_en   = $desc_es   ? deepLTranslate($desc_es,   'en') : '';

$pdo->prepare('
    UPDATE galeria_grupos SET
        titulo_es=?, titulo_de=?, titulo_fr=?, titulo_en=?,
        descripcion_es=?, descripcion_de=?, descripcion_fr=?, descripcion_en=?,
        categoria=?, tags=?
    WHERE id=?
')->execute([$titulo_es, $titulo_de, $titulo_fr, $titulo_en, $desc_es, $desc_de, $desc_fr, $desc_en, $categoria, $tags, $id]);

$upload_dir = __DIR__ . '/../../uploads/';
$allowed    = ['jpg','jpeg','png','webp','gif'];
$max_size   = 10 * 1024 * 1024;

function subirFoto($file, $grupo_id, $rol, $orden, $pdo, $upload_dir, $allowed, $max_size) {
    if ($file['error'] !== UPLOAD_ERR_OK) return false;
    if ($file['size'] > $max_size) return false;
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) return false;
    $nombre = 'gal_' . uniqid() . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $upload_dir . $nombre)) return false;
    $pdo->prepare('INSERT INTO galeria_fotos (grupo_id, archivo, rol, orden) VALUES (?,?,?,?)')
        ->execute([$grupo_id, $nombre, $rol, $orden]);
    return true;
}

if ($tipo === 'foto') {
    // Reemplazar foto única
    if (!empty($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        // Borrar foto anterior
        $old = $pdo->prepare('SELECT id, archivo FROM galeria_fotos WHERE grupo_id = ? LIMIT 1');
        $old->execute([$id]);
        $oldFoto = $old->fetch();
        if ($oldFoto) {
            $path = $upload_dir . $oldFoto['archivo'];
            if (file_exists($path)) unlink($path);
            $pdo->prepare('DELETE FROM galeria_fotos WHERE id = ?')->execute([$oldFoto['id']]);
        }
        subirFoto($_FILES['foto'], $id, 'normal', 0, $pdo, $upload_dir, $allowed, $max_size);
    }
} elseif ($tipo === 'antes_despues') {
    // Reemplazar antes y/o después por separado
    foreach (['antes', 'despues'] as $rol) {
        if (!empty($_FILES[$rol]) && $_FILES[$rol]['error'] === UPLOAD_ERR_OK) {
            // Borrar foto con ese rol
            $old = $pdo->prepare('SELECT id, archivo FROM galeria_fotos WHERE grupo_id = ? AND rol = ?');
            $old->execute([$id, $rol]);
            $oldFoto = $old->fetch();
            if ($oldFoto) {
                $path = $upload_dir . $oldFoto['archivo'];
                if (file_exists($path)) unlink($path);
                $pdo->prepare('DELETE FROM galeria_fotos WHERE id = ?')->execute([$oldFoto['id']]);
            }
            $orden = $rol === 'antes' ? 0 : 1;
            subirFoto($_FILES[$rol], $id, $rol, $orden, $pdo, $upload_dir, $allowed, $max_size);
        }
    }
} elseif ($tipo === 'carrusel') {
    // Añadir fotos nuevas al carrusel
    if (!empty($_FILES['fotos']['name'][0])) {
        $orden_actual = (int)($pdo->query("SELECT MAX(orden) FROM galeria_fotos WHERE grupo_id=$id")->fetchColumn() ?? 0);
        $fotos = $_FILES['fotos'];
        $count = count($fotos['name']);
        for ($i = 0; $i < $count; $i++) {
            $file = ['name'=>$fotos['name'][$i],'type'=>$fotos['type'][$i],'tmp_name'=>$fotos['tmp_name'][$i],'error'=>$fotos['error'][$i],'size'=>$fotos['size'][$i]];
            if (subirFoto($file, $id, 'normal', ++$orden_actual, $pdo, $upload_dir, $allowed, $max_size)) {}
        }
    }
}

echo json_encode(['success' => true]);