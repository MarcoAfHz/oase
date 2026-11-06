<?php
session_start();

if (isset($_SESSION['oase_admin'])) {
    header('Location: panel.php');
    exit;
}

require_once __DIR__ . '/../config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario  = trim($_POST['usuario'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($usuario && $password) {
        $stmt = $pdo->prepare('SELECT * FROM admin_users WHERE usuario = ? AND activo = 1');
        $stmt->execute([$usuario]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['oase_admin']        = true;
            $_SESSION['oase_admin_user']   = $user['usuario'];
            $_SESSION['oase_admin_nombre'] = $user['nombre'];
            header('Location: panel.php');
            exit;
        } else {
            $error = 'Usuario o contraseña incorrectos.';
        }
    } else {
        $error = 'Completa todos los campos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin — Oase Friseur & Ästhetik</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tabler-icons/2.47.0/tabler-icons.min.css">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #080603;
  position: relative;
  overflow: hidden;
}

/* ===== FONDO DECORATIVO ===== */
.bg-deco {
  position: fixed;
  inset: 0;
  z-index: 0;
  overflow: hidden;
}

/* Círculos dorados difusos */
.bg-circle {
  position: absolute;
  border-radius: 50%;
  filter: blur(80px);
  opacity: 0.12;
}
.bg-circle-1 {
  width: 600px; height: 600px;
  background: radial-gradient(circle, #c9a84c, transparent);
  top: -200px; left: -200px;
}
.bg-circle-2 {
  width: 400px; height: 400px;
  background: radial-gradient(circle, #c9a84c, transparent);
  bottom: -150px; right: -100px;
}
.bg-circle-3 {
  width: 300px; height: 300px;
  background: radial-gradient(circle, #8a6a20, transparent);
  top: 50%; left: 60%;
  opacity: 0.08;
}

/* Grid de líneas tipo plano arquitectónico */
.bg-grid {
  position: absolute;
  inset: 0;
  background-image:
    linear-gradient(rgba(201,168,76,0.04) 1px, transparent 1px),
    linear-gradient(90deg, rgba(201,168,76,0.04) 1px, transparent 1px);
  background-size: 60px 60px;
}

/* Líneas diagonales decorativas */
.bg-lines {
  position: absolute;
  inset: 0;
  overflow: hidden;
}
.bg-line {
  position: absolute;
  background: linear-gradient(90deg, transparent, rgba(201,168,76,0.08), transparent);
  height: 0.5px;
  width: 100%;
  transform-origin: left center;
}
.bg-line:nth-child(1) { top: 20%; transform: rotate(-15deg) scaleX(2); }
.bg-line:nth-child(2) { top: 50%; transform: rotate(-15deg) scaleX(2); }
.bg-line:nth-child(3) { top: 80%; transform: rotate(-15deg) scaleX(2); }

/* Esquinas decorativas */
.bg-corner {
  position: absolute;
  width: 120px; height: 120px;
  border-color: rgba(201,168,76,0.12);
  border-style: solid;
}
.bg-corner-tl { top: 2rem; left: 2rem; border-width: 1px 0 0 1px; }
.bg-corner-tr { top: 2rem; right: 2rem; border-width: 1px 1px 0 0; }
.bg-corner-bl { bottom: 2rem; left: 2rem; border-width: 0 0 1px 1px; }
.bg-corner-br { bottom: 2rem; right: 2rem; border-width: 0 1px 1px 0; }

/* Puntos decorativos en las esquinas */
.bg-corner::before {
  content: '';
  position: absolute;
  width: 5px; height: 5px;
  background: #c9a84c;
  border-radius: 50%;
  opacity: 0.4;
}
.bg-corner-tl::before { top: -3px; left: -3px; }
.bg-corner-tr::before { top: -3px; right: -3px; }
.bg-corner-bl::before { bottom: -3px; left: -3px; }
.bg-corner-br::before { bottom: -3px; right: -3px; }

/* ===== LOGO ARRIBA ===== */
.login-logo {
  position: fixed;
  top: 2.5rem;
  left: 50%;
  transform: translateX(-50%);
  z-index: 3;
  text-align: center;
}
.login-logo-main {
  font-size: 20px;
  font-weight: 700;
  letter-spacing: 8px;
  color: #c9a84c;
  text-transform: uppercase;
}
.login-logo-sub {
  font-size: 9px;
  letter-spacing: 3px;
  color: #4a4035;
  text-transform: uppercase;
  margin-top: 3px;
}

/* ===== CARD ===== */
.login-card {
  position: relative;
  z-index: 3;
  width: 100%;
  max-width: 380px;
  padding: 2.5rem 2rem;
  background: rgba(15, 12, 8, 0.9);
  border: 0.5px solid rgba(201,168,76,0.2);
  margin: 1rem;
}

/* Esquinitas doradas en la card */
.login-card::before,
.login-card::after {
  content: '';
  position: absolute;
  width: 20px; height: 20px;
  border-color: #c9a84c;
  border-style: solid;
  opacity: 0.5;
}
.login-card::before { top: -1px; left: -1px; border-width: 2px 0 0 2px; }
.login-card::after  { bottom: -1px; right: -1px; border-width: 0 2px 2px 0; }

.login-welcome {
  margin-bottom: 2rem;
  text-align: center;
}
.login-welcome-tag {
  font-size: 9px;
  letter-spacing: 3px;
  text-transform: uppercase;
  color: #c9a84c;
  margin-bottom: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
}
.login-welcome-tag::before,
.login-welcome-tag::after {
  content: '';
  display: inline-block;
  width: 20px; height: 0.5px;
  background: #c9a84c;
}
.login-welcome h1 {
  font-size: 26px;
  font-weight: 400;
  color: #f0ebe3;
  line-height: 1.2;
}
.login-welcome h1 em { font-style: italic; color: #c9a84c; }
.login-welcome p {
  font-size: 12px;
  color: #4a4035;
  margin-top: 0.5rem;
}

/* FORM */
.form-field { margin-bottom: 1.1rem; }
.form-field label {
  display: block;
  font-size: 9px;
  letter-spacing: 2px;
  text-transform: uppercase;
  color: #4a4035;
  margin-bottom: 7px;
}
.form-field-input { position: relative; }
.form-field-input i.icon-left {
  position: absolute;
  left: 13px; top: 50%;
  transform: translateY(-50%);
  font-size: 16px;
  color: #3a3020;
  transition: color 0.2s;
  pointer-events: none;
}
.form-field input {
  width: 100%;
  padding: 12px 13px 12px 42px;
  background: rgba(255,255,255,0.03);
  border: 0.5px solid #2a2318;
  color: #f0ebe3;
  font-size: 14px;
  outline: none;
  transition: all 0.2s;
  font-family: inherit;
}
.form-field input:focus { border-color: #c9a84c; background: rgba(201,168,76,0.03); }
.form-field-input:focus-within i.icon-left { color: #c9a84c; }
.form-field input::placeholder { color: #2a2318; }

.toggle-pass {
  position: absolute;
  right: 13px; top: 50%;
  transform: translateY(-50%);
  background: none; border: none;
  color: #3a3020; cursor: pointer;
  font-size: 16px; padding: 0;
  transition: color 0.2s;
}
.toggle-pass:hover { color: #c9a84c; }

.error-box {
  display: flex; align-items: center; gap: 10px;
  background: rgba(180,60,60,0.08);
  border: 0.5px solid rgba(180,60,60,0.4);
  color: #e07070;
  padding: 10px 13px;
  font-size: 12px;
  margin-bottom: 1.1rem;
}
.error-box i { font-size: 16px; flex-shrink: 0; }

.btn-login {
  width: 100%; padding: 13px;
  background: #c9a84c; color: #0a0805;
  border: none;
  font-size: 11px; font-weight: 700;
  letter-spacing: 3px; text-transform: uppercase;
  cursor: pointer; transition: all 0.25s;
  font-family: inherit;
  display: flex; align-items: center;
  justify-content: center; gap: 8px;
  margin-top: 0.25rem;
}
.btn-login:hover { background: #e0c06a; transform: translateY(-1px); }

.login-footer {
  margin-top: 1.75rem;
  display: flex; align-items: center;
  justify-content: space-between;
  border-top: 0.5px solid #1a1510;
  padding-top: 1.25rem;
}
.login-footer a {
  font-size: 11px; color: #3a3020;
  text-decoration: none; transition: color 0.2s;
  display: flex; align-items: center; gap: 5px;
}
.login-footer a:hover { color: #c9a84c; }
.login-footer-copy { font-size: 11px; color: #2a2318; }

@media (max-width: 480px) {
  .bg-corner { display: none; }
  .login-logo { top: 1.5rem; }
  .login-card { padding: 2rem 1.5rem; }
}
</style>
</head>
<body>

<!-- FONDO -->
<div class="bg-deco">
  <div class="bg-grid"></div>
  <div class="bg-circle bg-circle-1"></div>
  <div class="bg-circle bg-circle-2"></div>
  <div class="bg-circle bg-circle-3"></div>
  <div class="bg-lines">
    <div class="bg-line"></div>
    <div class="bg-line"></div>
    <div class="bg-line"></div>
  </div>
  <div class="bg-corner bg-corner-tl"></div>
  <div class="bg-corner bg-corner-tr"></div>
  <div class="bg-corner bg-corner-bl"></div>
  <div class="bg-corner bg-corner-br"></div>
</div>

<!-- LOGO -->
<div class="login-logo">
  <div class="login-logo-main">Oase</div>
  <div class="login-logo-sub">Friseur & Ästhetik · Zürich</div>
</div>

<!-- CARD -->
<div class="login-card">
  <div class="login-welcome">
    <div class="login-welcome-tag">Acceso privado</div>
    <h1>Bienvenido <em>de vuelta</em></h1>
    <p>Gestiona reservas, servicios y galería.</p>
  </div>

  <?php if ($error): ?>
  <div class="error-box">
    <i class="ti ti-alert-circle"></i>
    <?= htmlspecialchars($error) ?>
  </div>
  <?php endif; ?>

  <form method="POST">
    <div class="form-field">
      <label>Usuario</label>
      <div class="form-field-input">
        <input type="text" name="usuario" placeholder="tu usuario" autocomplete="username" required>
        <i class="ti ti-user icon-left"></i>
      </div>
    </div>
    <div class="form-field">
      <label>Contraseña</label>
      <div class="form-field-input">
        <input type="password" name="password" id="passInput" placeholder="••••••••" autocomplete="current-password" required>
        <i class="ti ti-lock icon-left"></i>
        <button type="button" class="toggle-pass" onclick="togglePass()">
          <i class="ti ti-eye" id="eyeIcon"></i>
        </button>
      </div>
    </div>
    <button type="submit" class="btn-login">
      Entrar <i class="ti ti-arrow-right"></i>
    </button>
  </form>

  <div class="login-footer">
    <a href="/index.php">
      <i class="ti ti-arrow-left"></i> Volver a la web
    </a>
    <span class="login-footer-copy">© <?= date('Y') ?> Oase</span>
  </div>
</div>

<script>
function togglePass() {
  const input = document.getElementById('passInput');
  const icon  = document.getElementById('eyeIcon');
  input.type = input.type === 'password' ? 'text' : 'password';
  icon.className = input.type === 'password' ? 'ti ti-eye' : 'ti ti-eye-off';
}
</script>
</body>
</html>