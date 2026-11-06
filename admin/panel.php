<?php
require_once __DIR__ . '/auth/check-session.php';
require_once __DIR__ . '/../config.php';

$hoy        = date('Y-m-d');
$hoy_count  = $pdo->query("SELECT COUNT(*) FROM reservas WHERE fecha = '{$hoy}' AND estado = 'confirmada'")->fetchColumn();
$pendientes = $pdo->query('SELECT COUNT(*) FROM reservas WHERE estado = "pendiente_confirmacion"')->fetchColumn();
$total      = $pdo->query('SELECT COUNT(*) FROM reservas WHERE estado = "confirmada"')->fetchColumn();
$canceladas = $pdo->query('SELECT COUNT(*) FROM reservas WHERE estado = "cancelada"')->fetchColumn();
$mes        = date('Y-m');
$este_mes   = $pdo->query("SELECT COUNT(*) FROM reservas WHERE DATE_FORMAT(fecha,'%Y-%m') = '{$mes}' AND estado = 'confirmada'")->fetchColumn();

$stmt = $pdo->query('SELECT r.*, s.nombre_es as servicio FROM reservas r JOIN servicios s ON s.id = r.servicio_id ORDER BY r.created_at DESC LIMIT 50');
$reservas = $stmt->fetchAll();
$nombre_admin = $_SESSION['oase_admin_nombre'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Panel — Oase Admin</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html, body { overflow-x: hidden; max-width: 100%; }
body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f0f2f5; }

.sidebar { width: 250px; height: 100vh; background: #0f0f0f; position: fixed; top: 0; left: 0; z-index: 1001; display: flex; flex-direction: column; border-right: 0.5px solid #2a2318; transition: transform 0.3s ease; }
.sidebar-logo { padding: 1.25rem 1.5rem; border-bottom: 0.5px solid #2a2318; }
.sidebar-logo-main { font-size: 18px; font-weight: 700; letter-spacing: 5px; color: #c9a84c; }
.sidebar-logo-sub { font-size: 9px; letter-spacing: 2px; color: #3a3020; text-transform: uppercase; margin-top: 2px; }
.sidebar-nav { padding: 0.75rem 0; flex: 1; overflow-y: auto; }
.nav-section { font-size: 9px; letter-spacing: 2px; text-transform: uppercase; color: #2a2318; padding: 0.75rem 1.25rem 0.35rem; }
.nav-item { display: flex; align-items: center; gap: 10px; padding: 0.65rem 1.25rem; color: #7a7060; font-size: 13px; text-decoration: none; transition: all 0.2s; cursor: pointer; border: none; background: none; width: 100%; border-left: 2px solid transparent; }
.nav-item:hover { color: #c4b99e; background: rgba(201,168,76,0.06); }
.nav-item.active { color: #c9a84c; background: rgba(201,168,76,0.1); border-left-color: #c9a84c; }
.nav-item i { font-size: 17px; flex-shrink: 0; }
.nav-badge { background: #c9a84c; color: #0f0f0f; font-size: 10px; font-weight: 700; padding: 1px 7px; border-radius: 10px; margin-left: auto; }
.nav-badge-red { background: #ef4444; color: #fff; font-size: 10px; font-weight: 700; padding: 1px 7px; border-radius: 10px; margin-left: auto; }
.sidebar-footer { padding: 1rem 1.25rem; border-top: 0.5px solid #2a2318; }
.sidebar-footer a { display: flex; align-items: center; gap: 8px; color: #4a4035; font-size: 12px; text-decoration: none; transition: color 0.2s; }
.sidebar-footer a:hover { color: #ef4444; }

.sb-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.55); z-index: 1000; }

.topbar { position: fixed; top: 0; left: 250px; right: 0; height: 58px; background: #fff; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; padding: 0 1.25rem; gap: 12px; z-index: 999; transition: left 0.3s; }
.topbar-burger { display: none; background: none; border: none; color: #111827; cursor: pointer; padding: 2px; line-height: 1; align-items: center; }
.topbar-title { font-size: 15px; font-weight: 600; color: #111827; flex: 1; }
.topbar-badge { display: flex; align-items: center; gap: 5px; background: rgba(201,168,76,0.1); border: 1px solid rgba(201,168,76,0.3); padding: 4px 10px; border-radius: 20px; font-size: 12px; color: #92681a; font-weight: 600; white-space: nowrap; }
.topbar-badge-red { display: flex; align-items: center; gap: 5px; background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); padding: 4px 10px; border-radius: 20px; font-size: 12px; color: #991b1b; font-weight: 600; white-space: nowrap; }
.topbar-avatar { width: 32px; height: 32px; border-radius: 50%; background: #c9a84c; color: #0f0f0f; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; flex-shrink: 0; }

.main { margin-left: 250px; padding: 72px 1.25rem 2rem; min-height: 100vh; }

.stat-card { background: #fff; border-radius: 10px; padding: 1.1rem; border: 1px solid #e5e7eb; display: flex; align-items: center; gap: 0.85rem; height: 100%; }
.stat-icon { width: 44px; height: 44px; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
.stat-num { font-size: 24px; font-weight: 700; color: #111827; line-height: 1; }
.stat-label { font-size: 11px; color: #6b7280; margin-top: 2px; }
.icon-gold { background: rgba(201,168,76,0.12); color: #c9a84c; }
.icon-blue { background: rgba(59,130,246,0.12); color: #3b82f6; }
.icon-green { background: rgba(34,197,94,0.12); color: #22c55e; }
.icon-red { background: rgba(239,68,68,0.12); color: #ef4444; }
.icon-orange { background: rgba(249,115,22,0.12); color: #f97316; }

.panel-card { background: #fff; border-radius: 10px; border: 1px solid #e5e7eb; overflow: hidden; }
.panel-card-header { padding: 0.9rem 1.1rem; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; flex-wrap: wrap; }
.panel-card-header h5 { font-size: 14px; font-weight: 600; color: #111827; margin: 0; display: flex; align-items: center; gap: 7px; }
.panel-card-header h5 i { color: #c9a84c; }

.table th { font-size: 11px; letter-spacing: 0.5px; text-transform: uppercase; color: #6b7280; background: #f9fafb; border-bottom: 1px solid #e5e7eb; padding: 0.65rem 0.85rem; white-space: nowrap; }
.table td { font-size: 13px; padding: 0.65rem 0.85rem; vertical-align: middle; color: #374151; }
.table tbody tr:hover { background: #fafafa; }
.tr-pendiente { background: rgba(249,115,22,0.04) !important; border-left: 3px solid #f97316; }

.badge-confirmada { background: #dcfce7; color: #166534; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; white-space: nowrap; }
.badge-cancelada { background: #fee2e2; color: #991b1b; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; white-space: nowrap; }
.badge-pendiente_confirmacion { background: #fff3cd; color: #856404; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; white-space: nowrap; }

.btn-cancelar { padding: 4px 10px; font-size: 11px; font-weight: 600; border-radius: 6px; border: none; cursor: pointer; background: #fee2e2; color: #991b1b; transition: all 0.2s; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap; }
.btn-cancelar:hover { background: #ef4444; color: #fff; }
.btn-confirmar { padding: 4px 10px; font-size: 11px; font-weight: 600; border-radius: 6px; border: none; cursor: pointer; background: rgba(201,168,76,0.15); color: #92681a; transition: all 0.2s; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap; }
.btn-confirmar:hover { background: #c9a84c; color: #0f0f0f; }
.btn-traducir { padding: 4px 10px; font-size: 11px; font-weight: 600; border-radius: 6px; border: none; cursor: pointer; background: rgba(201,168,76,0.15); color: #92681a; transition: all 0.2s; display: inline-flex; align-items: center; gap: 4px; }
.btn-traducir:hover { background: #c9a84c; color: #0f0f0f; }
.btn-guardar { padding: 4px 10px; font-size: 11px; font-weight: 600; border-radius: 6px; border: none; cursor: pointer; background: #dcfce7; color: #166534; transition: all 0.2s; }
.btn-guardar:hover { background: #22c55e; color: #fff; }

.filtros { display: flex; gap: 6px; flex-wrap: wrap; }
.filtros select, .filtros input[type=date] { padding: 5px 8px; font-size: 12px; border: 1px solid #e5e7eb; border-radius: 6px; background: #fff; color: #374151; outline: none; }
.filtros select:focus, .filtros input:focus { border-color: #c9a84c; }

/* Cards mobile reservas */
.reserva-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 1rem; margin-bottom: 0.75rem; }
.reserva-card.pendiente-card { border-left: 3px solid #f97316; background: rgba(249,115,22,0.02); }
.reserva-card-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 0.75rem; gap: 8px; }
.reserva-card-nombre { font-size: 14px; font-weight: 600; color: #111827; }
.reserva-card-codigo { font-family: monospace; font-size: 11px; color: #c9a84c; letter-spacing: 1px; }
.reserva-card-body { display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 0.75rem; }
.reserva-card-row { font-size: 11px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px; }
.reserva-card-row span { display: block; font-size: 13px; font-weight: 600; color: #374151; text-transform: none; letter-spacing: 0; margin-top: 2px; }
.reserva-card-actions { display: flex; gap: 6px; }

/* Horarios */
.horario-card { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 0.9rem; }
.horario-dia { font-size: 13px; font-weight: 600; color: #111827; margin-bottom: 8px; }
.horario-input { display: flex; gap: 8px; align-items: flex-end; flex-wrap: wrap; }
.horario-input label { font-size: 10px; color: #9ca3af; letter-spacing: 1px; text-transform: uppercase; display: block; margin-bottom: 3px; }
.horario-input input[type=time] { padding: 5px 8px; font-size: 12px; border: 1px solid #e5e7eb; border-radius: 6px; outline: none; background: #fff; }
.horario-input input[type=time]:focus { border-color: #c9a84c; }

.section-content { display: none; }
.section-content.active { display: block; }

.srv-row td input, .srv-row td select { padding: 4px 7px; font-size: 12px; border: 1px solid #e5e7eb; border-radius: 4px; width: 100%; outline: none; }
.srv-row td input:focus, .srv-row td select:focus { border-color: #c9a84c; }

@media (max-width: 900px) {
  .sidebar { transform: translateX(-250px); }
  .sidebar.open { transform: translateX(0); }
  .sb-overlay.show { display: block; }
  .topbar { left: 0; right: 0; }
  .topbar-burger { display: flex; }
  .main { margin-left: 0; padding: 66px 0.85rem 2rem; }
  .table-desktop { display: none !important; }
  .cards-mobile { display: block !important; }
}
@media (min-width: 901px) {
  .table-desktop { display: block; }
  .cards-mobile { display: none; }
}
</style>
</head>
<body>

<div class="sb-overlay" id="sbOverlay" onclick="closeSidebar()"></div>

<div class="sidebar" id="sidebar">
  <div class="sidebar-logo">
    <div class="sidebar-logo-main">Oase</div>
    <div class="sidebar-logo-sub">Panel Admin</div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section">Principal</div>
    <button class="nav-item active" onclick="showSection('reservas',this)">
      <i class="ti ti-calendar"></i> Reservas
      <?php if ($pendientes > 0): ?>
      <span class="nav-badge-red"><?= $pendientes ?></span>
      <?php elseif ($hoy_count > 0): ?>
      <span class="nav-badge"><?= $hoy_count ?></span>
      <?php endif; ?>
    </button>
    <button class="nav-item" onclick="showSection('servicios',this)"><i class="ti ti-cut"></i> Servicios & Precios</button>
    <button class="nav-item" onclick="showSection('horarios',this)"><i class="ti ti-clock"></i> Horarios</button>
    <button class="nav-item" onclick="showSection('galeria',this)"><i class="ti ti-photo"></i> Galería</button>
    <button class="nav-item" onclick="showSection('videos_sec',this)"><i class="ti ti-video"></i> Videos</button>
    <div class="nav-section">Web</div>
    <a class="nav-item" href="/index.php" target="_blank"><i class="ti ti-external-link"></i> Ver web</a>
  </nav>
  <div class="sidebar-footer">
    <a href="logout.php"><i class="ti ti-logout"></i> Cerrar sesión</a>
  </div>
</div>

<div class="topbar">
  <button class="topbar-burger" id="burgerBtn" onclick="toggleSidebar()">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
    </svg>
  </button>
  <div class="topbar-title" id="topbar-title">Reservas</div>
  <?php if ($pendientes > 0): ?>
  <div class="topbar-badge-red"><i class="ti ti-alert-circle"></i><span><?= $pendientes ?></span> pendientes</div>
  <?php endif; ?>
  <div class="topbar-badge"><i class="ti ti-calendar-due"></i><span id="badge-hoy-top"><?= $hoy_count ?></span> hoy</div>
  <div class="topbar-avatar"><?= strtoupper(substr($nombre_admin,0,1)) ?></div>
</div>

<div class="main">

  <!-- RESERVAS -->
  <div id="sec-reservas" class="section-content active">
    <!-- STATS -->
  <div class="row g-2 mb-3">
    <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-icon icon-gold"><i class="ti ti-calendar-event"></i></div><div><div class="stat-num"><?= $total ?></div><div class="stat-label">Confirmadas</div></div></div></div>
    <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-icon icon-blue"><i class="ti ti-calendar-due"></i></div><div><div class="stat-num" id="stat-hoy"><?= $hoy_count ?></div><div class="stat-label">Hoy</div></div></div></div>
    <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-icon icon-green"><i class="ti ti-calendar-stats"></i></div><div><div class="stat-num"><?= $este_mes ?></div><div class="stat-label">Este mes</div></div></div></div>
    <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-icon icon-orange"><i class="ti ti-clock-pause"></i></div><div><div class="stat-num" id="stat-pendientes"><?= $pendientes ?></div><div class="stat-label">Pendientes</div></div></div></div>
  </div>
    <div class="panel-card">
      <div class="panel-card-header">
        <h5><i class="ti ti-calendar"></i> Reservas</h5>
        <div class="filtros">
          <input type="date" id="filtro-fecha" onchange="filtrarReservas()">
          <select id="filtro-estado" onchange="filtrarReservas()">
            <option value="">Todos</option>
            <option value="confirmada">Confirmadas</option>
            <option value="pendiente_confirmacion">Pendientes</option>
            <option value="cancelada">Canceladas</option>
          </select>
        </div>
      </div>

      <!-- TABLA DESKTOP -->
      <div class="table-desktop">
        <div class="table-responsive">
          <table class="table mb-0">
            <thead><tr><th>#</th><th>Código</th><th>Cliente</th><th>Servicio</th><th>Fecha</th><th>Hora</th><th>Idioma</th><th>Estado</th><th>Acción</th></tr></thead>
            <tbody id="tbody-reservas">
              <?php foreach ($reservas as $r): ?>
              <tr id="fila-<?= $r['id'] ?>" data-estado="<?= $r['estado'] ?>" data-fecha="<?= $r['fecha'] ?>" class="<?= $r['estado']==='pendiente_confirmacion' ? 'tr-pendiente' : '' ?>">
                <td style="color:#9ca3af">#<?= $r['id'] ?></td>
                <td><span style="font-family:monospace;font-size:11px;color:#c9a84c;letter-spacing:1px"><?= htmlspecialchars($r['codigo'] ?? '—') ?></span></td>
                <td><div style="font-weight:600"><?= htmlspecialchars($r['nombre']) ?></div><div style="font-size:11px;color:#9ca3af"><?= htmlspecialchars($r['email']) ?></div></td>
                <td><?= htmlspecialchars($r['servicio']) ?></td>
                <td><?= date('d/m/Y', strtotime($r['fecha'])) ?></td>
                <td><?= substr($r['hora'],0,5) ?></td>
                <td><span style="font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase"><?= $r['lang'] ?? 'de' ?></span></td>
                <td><span class="badge-<?= $r['estado'] ?>"><?= $r['estado']==='pendiente_confirmacion'?'⚠️ Pendiente':ucfirst($r['estado']) ?></span></td>
                <td style="white-space:nowrap">
                  <?php if($r['estado']==='pendiente_confirmacion'): ?>
                  <button class="btn-confirmar me-1" onclick="confirmarReserva(<?= $r['id'] ?>)"><i class="ti ti-check"></i> Confirmar</button>
                  <button class="btn-cancelar" onclick="cancelarReserva(<?= $r['id'] ?>)"><i class="ti ti-x"></i> Cancelar</button>
                  <?php elseif($r['estado']==='confirmada'): ?>
                  <button class="btn-cancelar" onclick="cancelarReserva(<?= $r['id'] ?>)"><i class="ti ti-x"></i> Cancelar</button>
                  <?php else: ?>
                  <span style="font-size:11px;color:#9ca3af;font-style:italic"><?= $r['motivo_cancelacion'] ? htmlspecialchars(substr($r['motivo_cancelacion'],0,25)).'...' : '—' ?></span>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php if(empty($reservas)): ?><tr><td colspan="9" class="text-center py-4" style="color:#9ca3af">No hay reservas aún</td></tr><?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- CARDS MOBILE -->
      <div class="cards-mobile p-2" id="cards-reservas">
        <?php foreach ($reservas as $r): ?>
        <div class="reserva-card <?= $r['estado']==='pendiente_confirmacion'?'pendiente-card':'' ?>" id="card-<?= $r['id'] ?>" data-estado="<?= $r['estado'] ?>" data-fecha="<?= $r['fecha'] ?>">
          <div class="reserva-card-header">
            <div>
              <div class="reserva-card-nombre"><?= htmlspecialchars($r['nombre']) ?></div>
              <div class="reserva-card-codigo"><?= htmlspecialchars($r['codigo'] ?? '—') ?></div>
              <div style="font-size:11px;color:#9ca3af"><?= htmlspecialchars($r['email']) ?></div>
            </div>
            <span class="badge-<?= $r['estado'] ?>"><?= $r['estado']==='pendiente_confirmacion'?'⚠️ Pendiente':ucfirst($r['estado']) ?></span>
          </div>
          <div class="reserva-card-body">
            <div class="reserva-card-row">Servicio<span><?= htmlspecialchars($r['servicio']) ?></span></div>
            <div class="reserva-card-row">Fecha<span><?= date('d/m/Y', strtotime($r['fecha'])) ?></span></div>
            <div class="reserva-card-row">Hora<span><?= substr($r['hora'],0,5) ?></span></div>
            <div class="reserva-card-row">Idioma<span style="text-transform:uppercase"><?= $r['lang'] ?? 'de' ?></span></div>
          </div>
          <div class="reserva-card-actions">
            <?php if($r['estado']==='pendiente_confirmacion'): ?>
            <button class="btn-confirmar flex-fill justify-content-center" onclick="confirmarReserva(<?= $r['id'] ?>)"><i class="ti ti-check"></i> Confirmar</button>
            <button class="btn-cancelar flex-fill justify-content-center" onclick="cancelarReserva(<?= $r['id'] ?>)"><i class="ti ti-x"></i> Cancelar</button>
            <?php elseif($r['estado']==='confirmada'): ?>
            <button class="btn-cancelar w-100 justify-content-center" onclick="cancelarReserva(<?= $r['id'] ?>)"><i class="ti ti-x"></i> Cancelar reserva</button>
            <?php elseif($r['motivo_cancelacion']): ?>
            <div style="font-size:11px;color:#9ca3af;font-style:italic;background:#fafafa;padding:6px 8px;border-radius:4px;width:100%"><?= htmlspecialchars($r['motivo_cancelacion']) ?></div>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
        <?php if(empty($reservas)): ?>
        <div class="text-center py-4" style="color:#9ca3af;font-size:13px">No hay reservas aún</div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div id="sec-servicios" class="section-content"><?php include __DIR__ . '/sections/servicios.php'; ?></div>
  <div id="sec-horarios" class="section-content"><?php include __DIR__ . '/sections/horarios.php'; ?></div>
  <div id="sec-galeria" class="section-content"><?php include __DIR__ . '/sections/galeria.php'; ?></div>
  <div id="sec-videos_sec" class="section-content"><?php include __DIR__ . '/sections/videos.php'; ?></div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const sectionTitles = { reservas:'Reservas', servicios:'Servicios & Precios', horarios:'Horarios', galeria:'Galería', videos_sec:'Videos' };

function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('open');
  document.getElementById('sbOverlay').classList.toggle('show');
}
function closeSidebar() {
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('sbOverlay').classList.remove('show');
}
function showSection(name, el) {
  document.querySelectorAll('.section-content').forEach(s => s.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  document.getElementById('sec-' + name).classList.add('active');
  el.classList.add('active');
  document.getElementById('topbar-title').textContent = sectionTitles[name] || name;
  closeSidebar();
}

function confirmarReserva(id) {
  Swal.fire({
    title: '¿Confirmar esta reserva?',
    text: 'El cliente recibirá un email de confirmación.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: '✓ Confirmar',
    cancelButtonText: 'Volver',
    confirmButtonColor: '#c9a84c',
    cancelButtonColor: '#6b7280',
  }).then(result => {
    if (!result.isConfirmed) return;
    fetch('ajax/confirmar-reserva.php', { method:'POST', body: new URLSearchParams({id}), headers:{'Content-Type':'application/x-www-form-urlencoded'} })
      .then(r => r.json())
      .then(res => {
        if (res.success) {
          // Actualizar tabla
          const fila = document.getElementById('fila-' + id);
          if (fila) {
            fila.classList.remove('tr-pendiente');
            fila.dataset.estado = 'confirmada';
            const badge = fila.querySelector('[class^="badge-"]');
            if (badge) { badge.className = 'badge-confirmada'; badge.textContent = 'Confirmada'; }
            const btns = fila.querySelectorAll('.btn-confirmar');
            btns.forEach(b => b.remove());
          }
          // Actualizar card
          const card = document.getElementById('card-' + id);
          if (card) {
            card.classList.remove('pendiente-card');
            card.dataset.estado = 'confirmada';
            const badge = card.querySelector('[class^="badge-"]');
            if (badge) { badge.className = 'badge-confirmada'; badge.textContent = 'Confirmada'; }
            const actions = card.querySelector('.reserva-card-actions');
            if (actions) actions.innerHTML = '<button class="btn-cancelar w-100 justify-content-center" onclick="cancelarReserva(' + id + ')"><i class="ti ti-x"></i> Cancelar reserva</button>';
          }
          Swal.fire({ icon:'success', title:'Reserva confirmada', text:'El cliente ha sido notificado.', timer:2000, showConfirmButton:false });
        } else {
          Swal.fire({ icon:'error', title:'Error', text: res.error || 'No se pudo confirmar' });
        }
      });
  });
}

function cancelarReserva(id) {
  Swal.fire({
    title: '¿Cancelar esta reserva?',
    text: 'El cliente recibirá un email con el motivo.',
    input: 'textarea',
    inputLabel: 'Motivo de la cancelación',
    inputPlaceholder: 'Escribe el motivo...',
    inputAttributes: { rows: 3 },
    showCancelButton: true,
    confirmButtonText: 'Cancelar reserva',
    cancelButtonText: 'Volver',
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#6b7280',
    inputValidator: v => { if (!v) return 'Escribe un motivo'; }
  }).then(result => {
    if (!result.isConfirmed) return;
    const data = new FormData();
    data.append('id', id);
    data.append('motivo', result.value);
    fetch('ajax/cancelar-reserva.php', { method:'POST', body:data })
      .then(r => r.json())
      .then(res => {
        if (res.success) {
          [document.getElementById('fila-' + id), document.getElementById('card-' + id)].forEach(el => {
            if (!el) return;
            el.classList.remove('tr-pendiente','pendiente-card');
            el.dataset.estado = 'cancelada';
            const badge = el.querySelector('[class^="badge-"]');
            if (badge) { badge.className = 'badge-cancelada'; badge.textContent = 'Cancelada'; }
            const btns = el.querySelectorAll('.btn-cancelar, .btn-confirmar');
            btns.forEach(b => b.remove());
          });
          Swal.fire({ icon:'success', title:'Reserva cancelada', text:'El cliente ha sido notificado.', timer:2000, showConfirmButton:false });
        } else {
          Swal.fire({ icon:'error', title:'Error', text: res.error || 'No se pudo cancelar' });
        }
      });
  });
}

function filtrarReservas() {
  const fecha  = document.getElementById('filtro-fecha').value;
  const estado = document.getElementById('filtro-estado').value;
  document.querySelectorAll('#tbody-reservas tr').forEach(tr => {
    tr.style.display = (!fecha || tr.dataset.fecha === fecha) && (!estado || tr.dataset.estado === estado) ? '' : 'none';
  });
  document.querySelectorAll('#cards-reservas .reserva-card').forEach(card => {
    card.style.display = (!fecha || card.dataset.fecha === fecha) && (!estado || card.dataset.estado === estado) ? '' : 'none';
  });
}

let lastTs = '<?= date('Y-m-d H:i:s') ?>';
function pollReservas() {
  fetch('ajax/polling-reservas.php?desde=' + encodeURIComponent(lastTs))
    .then(r => r.json())
    .then(data => {
      if (!data.reservas) return;
      const el_hoy = document.getElementById('badge-hoy-top'); if(el_hoy) el_hoy.textContent = data.hoy_count;
      const sh = document.getElementById('stat-hoy'); if(sh) sh.textContent = data.hoy_count;

      data.reservas.forEach(r => {
        if (document.getElementById('fila-' + r.id)) return;
        const fd = new Date(r.fecha + 'T00:00:00').toLocaleDateString('es-ES',{day:'2-digit',month:'2-digit',year:'numeric'});
        const fh = r.hora.substring(0,5);
        const esPendiente = r.estado === 'pendiente_confirmacion';
        const badgeClass = esPendiente ? 'badge-pendiente_confirmacion' : 'badge-confirmada';
        const badgeText  = esPendiente ? '⚠️ Pendiente' : 'Confirmada';
        const accionesTd = esPendiente
          ? `<button class="btn-confirmar me-1" onclick="confirmarReserva(${r.id})"><i class="ti ti-check"></i> Confirmar</button><button class="btn-cancelar" onclick="cancelarReserva(${r.id})"><i class="ti ti-x"></i> Cancelar</button>`
          : `<button class="btn-cancelar" onclick="cancelarReserva(${r.id})"><i class="ti ti-x"></i> Cancelar</button>`;

        const tbody = document.getElementById('tbody-reservas');
        if (tbody) {
          const tr = document.createElement('tr');
          tr.id = 'fila-' + r.id; tr.dataset.estado = r.estado; tr.dataset.fecha = r.fecha;
          if (esPendiente) tr.className = 'tr-pendiente';
          tr.innerHTML = `<td style="color:#9ca3af">#${r.id}</td><td><span style="font-family:monospace;font-size:11px;color:#c9a84c;letter-spacing:1px">${r.codigo||'—'}</span></td><td><div style="font-weight:600">${r.nombre}</div><div style="font-size:11px;color:#9ca3af">${r.email}</div></td><td>${r.servicio}</td><td>${fd}</td><td>${fh}</td><td><span style="font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase">${r.lang||'de'}</span></td><td><span class="${badgeClass}">${badgeText}</span></td><td style="white-space:nowrap">${accionesTd}</td>`;
          tbody.insertBefore(tr, tbody.firstChild);
        }

        const cards = document.getElementById('cards-reservas');
        if (cards) {
          const div = document.createElement('div');
          div.className = 'reserva-card' + (esPendiente ? ' pendiente-card' : '');
          div.id = 'card-' + r.id; div.dataset.estado = r.estado; div.dataset.fecha = r.fecha;
          div.innerHTML = `<div class="reserva-card-header"><div><div class="reserva-card-nombre">${r.nombre}</div><div class="reserva-card-codigo">${r.codigo||'—'}</div><div style="font-size:11px;color:#9ca3af">${r.email}</div></div><span class="${badgeClass}">${badgeText}</span></div><div class="reserva-card-body"><div class="reserva-card-row">Servicio<span>${r.servicio}</span></div><div class="reserva-card-row">Fecha<span>${fd}</span></div><div class="reserva-card-row">Hora<span>${fh}</span></div><div class="reserva-card-row">Idioma<span style="text-transform:uppercase">${r.lang||'de'}</span></div></div><div class="reserva-card-actions">${esPendiente ? `<button class="btn-confirmar flex-fill" onclick="confirmarReserva(${r.id})"><i class="ti ti-check"></i> Confirmar</button><button class="btn-cancelar flex-fill" onclick="cancelarReserva(${r.id})"><i class="ti ti-x"></i> Cancelar</button>` : `<button class="btn-cancelar w-100" onclick="cancelarReserva(${r.id})"><i class="ti ti-x"></i> Cancelar reserva</button>`}</div>`;
          cards.insertBefore(div, cards.firstChild);
        }

        Swal.fire({ toast:true, position:'top-end', icon: esPendiente ? 'warning' : 'success', title:(esPendiente ? '⚠️ Revisar — ' : '¡Nueva reserva! — ') + r.nombre, showConfirmButton:false, timer:5000, timerProgressBar:true });
      });
      lastTs = data.timestamp;
    }).catch(() => {});
}
setInterval(pollReservas, 30000);
</script>
</body>
</html>