<?php
require_once __DIR__ . '/../../config.php';
session_start();
if (!isset($_SESSION['oase_admin'])) die(json_encode(['error' => 'No autorizado']));
header('Content-Type: application/json');

$tipo        = $_POST['tipo'] ?? 'foto';
$titulo_es   = trim($_POST['titulo'] ?? '');
$desc_es     = trim($_POST['descripcion'] ?? '');
$categoria   = trim($_POST['categoria'] ?? 'general');
$tags        = trim($_POST['tags'] ?? '');

$tipos_validos = ['foto', 'antes_despues', 'carrusel'];
if (!in_array($tipo, $tipos_validos)) die(json_encode(['error' => 'Tipo inválido']));

// Traducir título con DeepL
$titulo_de = $titulo_es ? deepLTranslate($titulo_es, 'de') : '';
$titulo_fr = $titulo_es ? deepLTranslate($titulo_es, 'fr') : '';
$titulo_en = $titulo_es ? deepLTranslate($titulo_es, 'en') : '';

// Traducir descripción con DeepL
$desc_de = $desc_es ? deepLTranslate($desc_es, 'de') : '';
$desc_fr = $desc_es ? deepLTranslate($desc_es, 'fr') : '';
$desc_en = $desc_es ? deepLTranslate($desc_es, 'en') : '';

// Crear grupo
$pdo->prepare('
    INSERT INTO galeria_grupos 
    (tipo, titulo_de, titulo_es, titulo_fr, titulo_en, descripcion_de, descripcion_es, descripcion_fr, descripcion_en, categoria, tags)
    VALUES (?,?,?,?,?,?,?,?,?,?,?)
')->execute([$tipo, $titulo_de, $titulo_es, $titulo_fr, $titulo_en, $desc_de, $desc_es, $desc_fr, $desc_en, $categoria, $tags]);

$grupo_id = $pdo->lastInsertId();

$upload_dir = __DIR__ . '/../../uploads/';
$allowed    = ['jpg','jpeg','png','webp','gif'];
$max_size   = 10 * 1024 * 1024;

function subirFotoGaleria($file, $grupo_id, $rol, $orden, $pdo, $upload_dir, $allowed, $max_size) {
    if ($file['error'] !== UPLOAD_ERR_OK) return false;
    if ($file['size'] > $max_size) return false;
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) return false;
    $nombre  = 'gal_' . uniqid() . '.' . $ext;
    $destino = $upload_dir . $nombre;
    if (!move_uploaded_file($file['tmp_name'], $destino)) return false;
    $pdo->prepare('INSERT INTO galeria_fotos (grupo_id, archivo, rol, orden) VALUES (?,?,?,?)')
        ->execute([$grupo_id, $nombre, $rol, $orden]);
    return true;
}

if ($tipo === 'foto') {
    if (empty($_FILES['foto'])) { $pdo->prepare('DELETE FROM galeria_grupos WHERE id=?')->execute([$grupo_id]); die(json_encode(['error'=>'No se recibió la foto'])); }
    subirFotoGaleria($_FILES['foto'], $grupo_id, 'normal', 0, $pdo, $upload_dir, $allowed, $max_size);
} elseif ($tipo === 'antes_despues') {
    if (empty($_FILES['foto_antes']) || empty($_FILES['foto_despues'])) { $pdo->prepare('DELETE FROM galeria_grupos WHERE id=?')->execute([$grupo_id]); die(json_encode(['error'=>'Faltan fotos'])); }
    subirFotoGaleria($_FILES['foto_antes'],   $grupo_id, 'antes',   0, $pdo, $upload_dir, $allowed, $max_size);
    subirFotoGaleria($_FILES['foto_despues'], $grupo_id, 'despues', 1, $pdo, $upload_dir, $allowed, $max_size);
} elseif ($tipo === 'carrusel') {
    if (empty($_FILES['fotos'])) { $pdo->prepare('DELETE FROM galeria_grupos WHERE id=?')->execute([$grupo_id]); die(json_encode(['error'=>'No se recibieron fotos'])); }
    $fotos = $_FILES['fotos'];
    $count = count($fotos['name']);
    for ($i = 0; $i < $count; $i++) {
        $file = ['name'=>$fotos['name'][$i],'type'=>$fotos['type'][$i],'tmp_name'=>$fotos['tmp_name'][$i],'error'=>$fotos['error'][$i],'size'=>$fotos['size'][$i]];
        subirFotoGaleria($file, $grupo_id, 'normal', $i, $pdo, $upload_dir, $allowed, $max_size);
    }
}

echo json_encode(['success' => true, 'grupo_id' => $grupo_id]);