<?php
require_once __DIR__ . '/config.php';

$msg = '';
$tipo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario  = trim($_POST['usuario'] ?? '');
    $nombre   = trim($_POST['nombre'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$usuario || !$nombre || !$password) {
        $msg  = 'Todos los campos son obligatorios.';
        $tipo = 'error';
    } elseif (strlen($password) < 6) {
        $msg  = 'La contraseña debe tener al menos 6 caracteres.';
        $tipo = 'error';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $pdo->prepare('INSERT INTO admin_users (usuario, password, nombre, activo) VALUES (?,?,?,1)')
                ->execute([$usuario, $hash, $nombre]);
            $msg  = '✓ Usuario creado. Borra este archivo ahora.';
            $tipo = 'ok';
        } catch (Exception $e) {
            $msg  = 'Error: el usuario ya existe.';
            $tipo = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Crear admin</title>
<style>
*{box-sizing:border-box;}
body{font-family:sans-serif;background:#0f0f0f;color:#f0ebe3;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;padding:20px;}
.box{width:100%;max-width:400px;background:#1a1510;border:0.5px solid #2a2318;padding:2rem;}
h2{color:#c9a84c;margin-top:0;margin-bottom:1.5rem;text-align:center;}
label{display:block;font-size:12px;letter-spacing:1px;text-transform:uppercase;color:#8a8070;margin-bottom:6px;}
input{width:100%;background:#0f0f0f;color:#f0ebe3;border:1px solid #2a2318;padding:12px;margin-bottom:16px;outline:none;font-size:14px;}
input:focus{border-color:#c9a84c;}
button{width:100%;background:#c9a84c;color:#0f0f0f;border:none;padding:13px;font-weight:bold;cursor:pointer;font-size:14px;}
button:hover{background:#e0c06a;}
.msg-ok{background:rgba(34,197,94,0.1);border:1px solid #22c55e;color:#22c55e;padding:12px;margin-bottom:1rem;font-size:13px;}
.msg-error{background:rgba(239,68,68,0.1);border:1px solid #ef4444;color:#ef4444;padding:12px;margin-bottom:1rem;font-size:13px;}
.warn{background:rgba(249,115,22,0.08);border:1px solid #f97316;color:#f97316;padding:10px;margin-top:1rem;font-size:12px;}
</style>
</head>
<body>
<div class="box">
  <h2>Crear usuario admin</h2>
  <?php if ($msg): ?>
  <div class="msg-<?= $tipo ?>"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>
  <?php if ($tipo !== 'ok'): ?>
  <form method="POST">
    <label>Nombre</label>
    <input type="text" name="nombre" placeholder="Edwin" required>
    <label>Usuario</label>
    <input type="text" name="usuario" placeholder="edwin" required>
    <label>Contraseña</label>
    <input type="password" name="password" placeholder="mínimo 6 caracteres" required>
    <button type="submit">Crear usuario</button>
  </form>
  <?php endif; ?>
  <div class="warn">⚠️ Borra este archivo después de usarlo.</div>
</div>
</body>
</html>