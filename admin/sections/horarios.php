<?php
$stmt_h = $pdo->query('SELECT * FROM horarios ORDER BY dia_semana');
$horarios = $stmt_h->fetchAll();
$dias = ['','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];
$stmt_b = $pdo->query('SELECT * FROM dias_bloqueados ORDER BY fecha DESC LIMIT 30');
$bloqueados = $stmt_b->fetchAll();
?>
<div class="row g-3">
  <div class="col-12 col-lg-7">
    <div class="panel-card">
      <div class="panel-card-header">
        <h5><i class="ti ti-clock"></i> Horarios semanales</h5>
      </div>
      <div class="p-3">
        <div class="row g-2">
          <?php foreach ($horarios as $h): ?>
          <div class="col-12">
            <div class="horario-card">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="horario-dia"><?= $dias[$h['dia_semana']] ?></div>
                <div class="form-check form-switch mb-0">
                  <input class="form-check-input" type="checkbox" id="act_<?= $h['id'] ?>" <?= $h['activo'] ? 'checked' : '' ?>>
                  <label class="form-check-label" style="font-size:12px;color:#6b7280" for="act_<?= $h['id'] ?>">Activo</label>
                </div>
              </div>
              <div class="horario-input">
                <div>
                  <label style="font-size:10px;color:#9ca3af;letter-spacing:1px;text-transform:uppercase">Apertura</label>
                  <input type="time" id="ini_<?= $h['id'] ?>" value="<?= $h['hora_inicio'] ?>">
                </div>
                <div>
                  <label style="font-size:10px;color:#9ca3af;letter-spacing:1px;text-transform:uppercase">Cierre</label>
                  <input type="time" id="fin_<?= $h['id'] ?>" value="<?= $h['hora_fin'] ?>">
                </div>
                <button class="btn-guardar ms-auto" onclick="guardarHorario(<?= $h['id'] ?>)">
                  <i class="ti ti-check"></i> Guardar
                </button>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 col-lg-5">
    <div class="panel-card">
      <div class="panel-card-header">
        <h5><i class="ti ti-calendar-off"></i> Días bloqueados</h5>
      </div>
      <div class="p-3">
        <div class="d-flex gap-2 mb-3 flex-wrap">
          <input type="date" id="nueva-fecha" class="form-control form-control-sm" min="<?= date('Y-m-d') ?>" style="max-width:160px">
          <input type="text" id="nuevo-motivo" class="form-control form-control-sm" placeholder="Motivo (opcional)" style="max-width:180px">
          <button class="btn btn-sm btn-dark" onclick="bloquearDia()"><i class="ti ti-plus"></i> Bloquear</button>
        </div>
        <div id="lista-bloqueados">
          <?php foreach ($bloqueados as $b): ?>
          <div class="d-flex align-items-center justify-content-between py-2 border-bottom" id="blq-<?= $b['id'] ?>">
            <div>
              <div style="font-size:13px;font-weight:600;color:#111827"><?= date('d/m/Y', strtotime($b['fecha'])) ?></div>
              <?php if ($b['motivo']): ?>
              <div style="font-size:11px;color:#9ca3af"><?= htmlspecialchars($b['motivo']) ?></div>
              <?php endif; ?>
            </div>
            <button onclick="desbloquear(<?= $b['id'] ?>)" style="background:#fee2e2;border:none;color:#991b1b;padding:4px 10px;border-radius:6px;font-size:11px;cursor:pointer">
              <i class="ti ti-x"></i>
            </button>
          </div>
          <?php endforeach; ?>
          <?php if (empty($bloqueados)): ?>
          <div style="text-align:center;color:#9ca3af;padding:2rem;font-size:13px">Sin días bloqueados</div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
function guardarHorario(id) {
  const data = new FormData();
  data.append('id', id);
  data.append('hora_inicio', document.getElementById('ini_' + id).value);
  data.append('hora_fin', document.getElementById('fin_' + id).value);
  data.append('activo', document.getElementById('act_' + id).checked ? 1 : 0);
  fetch('ajax/guardar-horario.php', { method: 'POST', body: data })
    .then(r => r.json())
    .then(res => {
      if (res.success) Swal.fire({icon:'success',title:'Horario guardado',timer:1200,showConfirmButton:false});
      else Swal.fire({icon:'error',title:'Error al guardar'});
    });
}
function bloquearDia() {
  const fecha = document.getElementById('nueva-fecha').value;
  const motivo = document.getElementById('nuevo-motivo').value;
  if (!fecha) return Swal.fire({icon:'warning',title:'Selecciona una fecha',timer:1500,showConfirmButton:false});
  const data = new FormData();
  data.append('fecha', fecha);
  data.append('motivo', motivo);
  fetch('ajax/bloquear-dia.php', { method: 'POST', body: data })
    .then(r => r.json())
    .then(res => {
      if (res.success) {
        Swal.fire({icon:'success',title:'Día bloqueado',timer:1200,showConfirmButton:false});
        setTimeout(() => location.reload(), 1200);
      } else {
        Swal.fire({icon:'error',title:'Error',text:res.error || 'No se pudo bloquear'});
      }
    });
}
function desbloquear(id) {
  Swal.fire({
    title: '¿Desbloquear este día?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Sí, desbloquear',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#c9a84c',
  }).then(r => {
    if (r.isConfirmed) {
      fetch('ajax/desbloquear-dia.php', { method:'POST', body:'id='+id, headers:{'Content-Type':'application/x-www-form-urlencoded'} })
        .then(r => r.json())
        .then(res => {
          if (res.success) {
            document.getElementById('blq-' + id).remove();
            Swal.fire({icon:'success',title:'Día desbloqueado',timer:1200,showConfirmButton:false});
          }
        });
    }
  });
}
</script>