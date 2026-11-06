<?php
$stmt_s = $pdo->query('SELECT * FROM servicios ORDER BY categoria, id');
$all_srv = $stmt_s->fetchAll();
$cats = ['corte'=>'Cortes','color'=>'Color','tratamiento'=>'Tratamientos','estetica'=>'Estética'];
$cat_icons = ['corte'=>'ti-cut','color'=>'ti-color-swatch','tratamiento'=>'ti-droplet','estetica'=>'ti-sparkles'];
?>

<style>
.srv-table { display: block; }
.srv-acordeon { display: none; }

.srv-row td input, .srv-row td select {
  padding: 4px 7px; font-size: 12px;
  border: 1px solid #e5e7eb; border-radius: 4px;
  width: 100%; outline: none;
}
.srv-row td input:focus, .srv-row td select:focus { border-color: #c9a84c; }

/* Modal nuevo servicio */
.modal-srv-overlay {
  display: none; position: fixed; inset: 0;
  background: rgba(0,0,0,0.5); z-index: 9999;
  align-items: center; justify-content: center;
}
.modal-srv-overlay.open { display: flex; }
.modal-srv-box {
  background: #fff; border-radius: 12px;
  padding: 1.5rem; width: 100%; max-width: 560px;
  max-height: 90vh; overflow-y: auto;
  margin: 1rem;
}
.modal-srv-title { font-size: 16px; font-weight: 600; color: #111827; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 8px; }
.modal-srv-title i { color: #c9a84c; }
.modal-field { margin-bottom: 1rem; }
.modal-field label { display: block; font-size: 10px; letter-spacing: 1.5px; text-transform: uppercase; color: #9ca3af; margin-bottom: 5px; }
.modal-field input, .modal-field select {
  width: 100%; padding: 9px 12px; font-size: 13px;
  border: 1px solid #e5e7eb; border-radius: 6px;
  outline: none; background: #fff; color: #111827; font-family: inherit;
}
.modal-field input:focus, .modal-field select:focus { border-color: #c9a84c; }
.modal-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }
.modal-btns { display: flex; gap: 8px; margin-top: 1.25rem; }
.modal-btns button { flex: 1; padding: 10px; font-size: 13px; font-weight: 600; border: none; border-radius: 6px; cursor: pointer; transition: all 0.2s; }
.btn-modal-cancel { background: #f3f4f6; color: #6b7280; }
.btn-modal-cancel:hover { background: #e5e7eb; }
.btn-modal-save { background: #c9a84c; color: #0f0f0f; }
.btn-modal-save:hover { background: #e0c06a; }
.btn-modal-traducir { background: rgba(201,168,76,0.12); color: #92681a; padding: 8px 14px !important; flex: none !important; font-size: 12px !important; }
.btn-modal-traducir:hover { background: #c9a84c; color: #0f0f0f; }

/* Acordeón mobile */
.srv-cat-group { margin-bottom: 1rem; }
.srv-cat-label { display: flex; align-items: center; gap: 8px; font-size: 11px; letter-spacing: 2px; text-transform: uppercase; color: #9ca3af; padding: 0 0.25rem 0.5rem; border-bottom: 1px solid #f3f4f6; margin-bottom: 0.5rem; }
.srv-cat-label i { color: #c9a84c; font-size: 14px; }
.srv-item { border: 1px solid #e5e7eb; border-radius: 8px; margin-bottom: 0.5rem; overflow: hidden; background: #fff; }
.srv-item-header { display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 1rem; cursor: pointer; user-select: none; }
.srv-item-header:active { background: #fafafa; }
.srv-item-nombre { font-size: 13px; font-weight: 600; color: #111827; flex: 1; margin-right: 8px; }
.srv-item-precio { font-size: 13px; font-weight: 700; color: #c9a84c; white-space: nowrap; margin-right: 8px; }
.srv-item-chevron { font-size: 16px; color: #9ca3af; transition: transform 0.2s; flex-shrink: 0; }
.srv-item.open .srv-item-chevron { transform: rotate(180deg); }
.srv-item-body { display: none; padding: 0 1rem 1rem; border-top: 1px solid #f3f4f6; }
.srv-item.open .srv-item-body { display: block; }
.srv-field { margin-bottom: 0.75rem; }
.srv-field label { display: block; font-size: 10px; letter-spacing: 1.5px; text-transform: uppercase; color: #9ca3af; margin-bottom: 4px; }
.srv-field input, .srv-field select { width: 100%; padding: 8px 10px; font-size: 13px; border: 1px solid #e5e7eb; border-radius: 6px; outline: none; background: #fff; color: #111827; font-family: inherit; }
.srv-field input:focus, .srv-field select:focus { border-color: #c9a84c; }
.srv-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; }
.srv-item-actions { display: flex; gap: 0.5rem; margin-top: 0.75rem; }
.srv-item-actions button { flex: 1; padding: 8px; font-size: 12px; font-weight: 600; border: none; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 5px; transition: all 0.2s; }
.btn-traducir-m { background: rgba(201,168,76,0.12); color: #92681a; }
.btn-traducir-m:hover { background: #c9a84c; color: #0f0f0f; }
.btn-guardar-m { background: #dcfce7; color: #166534; }
.btn-guardar-m:hover { background: #22c55e; color: #fff; }
.btn-eliminar-m { background: #fee2e2; color: #991b1b; flex: none !important; width: 38px; padding: 8px !important; }
.btn-eliminar-m:hover { background: #ef4444; color: #fff; }

@media (max-width: 900px) {
  .srv-table { display: none; }
  .srv-acordeon { display: block; }
  .modal-grid-2 { grid-template-columns: 1fr; }
}
</style>

<!-- MODAL NUEVO SERVICIO -->
<div class="modal-srv-overlay" id="modalNuevoServicio">
  <div class="modal-srv-box">
    <div class="modal-srv-title"><i class="ti ti-plus"></i> Nuevo servicio</div>
    <div class="modal-field">
      <label>🇪🇸 Nombre en español</label>
      <input type="text" id="new_es" placeholder="Ej: Corte de caballero">
    </div>
    <div class="modal-btns" style="margin-top:0; margin-bottom:1rem;">
      <button class="btn-modal-traducir" onclick="traducirNuevo()"><i class="ti ti-language"></i> Traducir automáticamente</button>
    </div>
    <div class="modal-field">
      <label>🇩🇪 Alemán</label>
      <input type="text" id="new_de" placeholder="Herrenhaarschnitt">
    </div>
    <div class="modal-field">
      <label>🇫🇷 Francés</label>
      <input type="text" id="new_fr" placeholder="Coupe homme">
    </div>
    <div class="modal-field">
      <label>🇬🇧 Inglés</label>
      <input type="text" id="new_en" placeholder="Men's haircut">
    </div>
    <div class="modal-grid-2">
      <div class="modal-field">
        <label>Precio CHF</label>
        <input type="number" id="new_precio" placeholder="40" min="0">
      </div>
      <div class="modal-field">
        <label>Duración (min)</label>
        <input type="number" id="new_duracion" placeholder="30" min="15" step="15">
      </div>
    </div>
    <div class="modal-grid-2">
      <div class="modal-field">
        <label>Categoría</label>
        <select id="new_categoria">
          <?php foreach ($cats as $k=>$v): ?>
          <option value="<?= $k ?>"><?= $v ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="modal-field">
        <label>Precio texto</label>
        <input type="text" id="new_precio_texto" placeholder="desde / ab" value="desde / ab">
      </div>
    </div>
    <div class="modal-btns">
      <button class="btn-modal-cancel" onclick="cerrarModalServicio()">Cancelar</button>
      <button class="btn-modal-save" onclick="crearServicio()"><i class="ti ti-check"></i> Crear servicio</button>
    </div>
  </div>
</div>

<div class="panel-card">
  <div class="panel-card-header">
    <h5><i class="ti ti-cut"></i> Servicios & Precios</h5>
    <button class="btn-guardar" onclick="abrirModalServicio()" style="display:flex;align-items:center;gap:5px;padding:6px 14px;">
      <i class="ti ti-plus"></i> Nuevo servicio
    </button>
  </div>
  <div class="p-3">
    <div id="msg-servicios"></div>

    <!-- TABLA DESKTOP -->
    <div class="srv-table">
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>Español</th>
              <th>Alemán</th>
              <th>Francés</th>
              <th>Inglés</th>
              <th>CHF</th>
              <th>Min</th>
              <th>Categoría</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody id="tbody-servicios">
            <?php foreach ($all_srv as $s): ?>
            <tr class="srv-row" id="srv-row-<?= $s['id'] ?>">
              <td><input type="text" id="es_<?= $s['id'] ?>" value="<?= htmlspecialchars($s['nombre_es']) ?>" style="min-width:150px"></td>
              <td><input type="text" id="de_<?= $s['id'] ?>" value="<?= htmlspecialchars($s['nombre_de']) ?>" style="min-width:150px"></td>
              <td><input type="text" id="fr_<?= $s['id'] ?>" value="<?= htmlspecialchars($s['nombre_fr']) ?>" style="min-width:120px"></td>
              <td><input type="text" id="en_<?= $s['id'] ?>" value="<?= htmlspecialchars($s['nombre_en']) ?>" style="min-width:120px"></td>
              <td><input type="number" id="precio_<?= $s['id'] ?>" value="<?= $s['precio'] ?>" style="width:70px"></td>
              <td><input type="number" id="dur_<?= $s['id'] ?>" value="<?= $s['duracion_min'] ?>" style="width:60px"></td>
              <td>
                <select id="cat_<?= $s['id'] ?>">
                  <?php foreach ($cats as $k=>$v): ?>
                  <option value="<?= $k ?>" <?= $s['categoria']===$k?'selected':'' ?>><?= $v ?></option>
                  <?php endforeach; ?>
                </select>
              </td>
              <td style="white-space:nowrap">
                <button class="btn-traducir me-1" onclick="traducirServicio(<?= $s['id'] ?>)"><i class="ti ti-language"></i></button>
                <button class="btn-guardar me-1" onclick="guardarServicio(<?= $s['id'] ?>)"><i class="ti ti-check"></i></button>
                <button class="btn-cancelar" onclick="eliminarServicio(<?= $s['id'] ?>)" title="Eliminar"><i class="ti ti-trash"></i></button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ACORDEÓN MOBILE -->
    <div class="srv-acordeon" id="srv-acordeon">
      <?php
      $por_cat = [];
      foreach ($all_srv as $s) { $por_cat[$s['categoria']][] = $s; }
      foreach ($por_cat as $cat => $items):
      ?>
      <div class="srv-cat-group">
        <div class="srv-cat-label">
          <i class="ti <?= $cat_icons[$cat] ?? 'ti-star' ?>"></i>
          <?= $cats[$cat] ?? ucfirst($cat) ?>
        </div>
        <?php foreach ($items as $s): ?>
        <div class="srv-item" id="srv-item-<?= $s['id'] ?>">
          <div class="srv-item-header" onclick="toggleSrv(<?= $s['id'] ?>)">
            <div class="srv-item-nombre"><?= htmlspecialchars($s['nombre_es']) ?></div>
            <div class="srv-item-precio">CHF <?= number_format($s['precio'],0) ?></div>
            <i class="ti ti-chevron-down srv-item-chevron"></i>
          </div>
          <div class="srv-item-body">
            <div class="srv-field" style="margin-top:0.75rem;">
              <label>🇪🇸 Español</label>
              <input type="text" id="m_es_<?= $s['id'] ?>" value="<?= htmlspecialchars($s['nombre_es']) ?>">
            </div>
            <div class="srv-field">
              <label>🇩🇪 Alemán</label>
              <input type="text" id="m_de_<?= $s['id'] ?>" value="<?= htmlspecialchars($s['nombre_de']) ?>">
            </div>
            <div class="srv-field">
              <label>🇫🇷 Francés</label>
              <input type="text" id="m_fr_<?= $s['id'] ?>" value="<?= htmlspecialchars($s['nombre_fr']) ?>">
            </div>
            <div class="srv-field">
              <label>🇬🇧 Inglés</label>
              <input type="text" id="m_en_<?= $s['id'] ?>" value="<?= htmlspecialchars($s['nombre_en']) ?>">
            </div>
            <div class="srv-row-2">
              <div class="srv-field">
                <label>Precio CHF</label>
                <input type="number" id="m_precio_<?= $s['id'] ?>" value="<?= $s['precio'] ?>">
              </div>
              <div class="srv-field">
                <label>Categoría</label>
                <select id="m_cat_<?= $s['id'] ?>">
                  <?php foreach ($cats as $k=>$v): ?>
                  <option value="<?= $k ?>" <?= $s['categoria']===$k?'selected':'' ?>><?= $v ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="srv-item-actions">
              <button class="btn-traducir-m" onclick="traducirServicioM(<?= $s['id'] ?>)"><i class="ti ti-language"></i> Traducir</button>
              <button class="btn-guardar-m" onclick="guardarServicioM(<?= $s['id'] ?>)"><i class="ti ti-check"></i> Guardar</button>
              <button class="btn-eliminar-m" onclick="eliminarServicio(<?= $s['id'] ?>)" title="Eliminar"><i class="ti ti-trash"></i></button>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endforeach; ?>
    </div>

  </div>
</div>

<script>
// ===== MODAL =====
function abrirModalServicio() {
  document.getElementById('modalNuevoServicio').classList.add('open');
  document.getElementById('new_es').focus();
}
function cerrarModalServicio() {
  document.getElementById('modalNuevoServicio').classList.remove('open');
  ['new_es','new_de','new_fr','new_en','new_precio','new_duracion','new_precio_texto'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.value = id === 'new_precio_texto' ? 'desde / ab' : '';
  });
}
document.getElementById('modalNuevoServicio').addEventListener('click', function(e) {
  if (e.target === this) cerrarModalServicio();
});

// ===== TRADUCIR NUEVO =====
function traducirNuevo() {
  const texto = document.getElementById('new_es').value.trim();
  if (!texto) return Swal.fire({icon:'warning',title:'Escribe el nombre en español primero',timer:2000,showConfirmButton:false});
  const data = new FormData(); data.append('texto', texto);
  fetch('ajax/deepl-translate.php', { method:'POST', body:data })
    .then(r => r.json())
    .then(res => {
      if (res.success) {
        document.getElementById('new_de').value = res.traducciones.de || '';
        document.getElementById('new_fr').value = res.traducciones.fr || '';
        document.getElementById('new_en').value = res.traducciones.en || '';
        Swal.fire({icon:'success',title:'Traducido',timer:1200,showConfirmButton:false});
      } else {
        Swal.fire({icon:'error',title:'Error al traducir',text:res.error});
      }
    });
}

// ===== CREAR SERVICIO =====
function crearServicio() {
  const es = document.getElementById('new_es').value.trim();
  const precio = document.getElementById('new_precio').value;
  if (!es || !precio) return Swal.fire({icon:'warning',title:'Nombre en español y precio son obligatorios',timer:2000,showConfirmButton:false});

  const data = new FormData();
  data.append('nombre_es',    es);
  data.append('nombre_de',    document.getElementById('new_de').value);
  data.append('nombre_fr',    document.getElementById('new_fr').value);
  data.append('nombre_en',    document.getElementById('new_en').value);
  data.append('precio',       precio);
  data.append('duracion_min', document.getElementById('new_duracion').value || 60);
  data.append('categoria',    document.getElementById('new_categoria').value);
  data.append('precio_texto', document.getElementById('new_precio_texto').value);

  fetch('ajax/crear-servicio.php', { method:'POST', body:data })
    .then(r => r.json())
    .then(res => {
      if (res.success) {
        cerrarModalServicio();
        Swal.fire({icon:'success',title:'Servicio creado',timer:1200,showConfirmButton:false});
        const tbody = document.getElementById('tbody-servicios');
        if (tbody && res.id) {
          const cat = data.get('categoria');
          const catNombres = {corte:'Cortes',color:'Color',tratamiento:'Tratamientos',estetica:'Estética'};
          const tr = document.createElement('tr');
          tr.className = 'srv-row';
          tr.id = 'srv-row-' + res.id;
          tr.innerHTML = `
            <td><input type="text" id="es_${res.id}" value="${data.get('nombre_es')}" style="min-width:150px"></td>
            <td><input type="text" id="de_${res.id}" value="${data.get('nombre_de')}" style="min-width:150px"></td>
            <td><input type="text" id="fr_${res.id}" value="${data.get('nombre_fr')}" style="min-width:120px"></td>
            <td><input type="text" id="en_${res.id}" value="${data.get('nombre_en')}" style="min-width:120px"></td>
            <td><input type="number" id="precio_${res.id}" value="${data.get('precio')}" style="width:70px"></td>
            <td><input type="number" id="dur_${res.id}" value="${data.get('duracion_min')}" style="width:60px"></td>
            <td><select id="cat_${res.id}">
              <option value="corte" ${cat==='corte'?'selected':''}>Cortes</option>
              <option value="color" ${cat==='color'?'selected':''}>Color</option>
              <option value="tratamiento" ${cat==='tratamiento'?'selected':''}>Tratamientos</option>
              <option value="estetica" ${cat==='estetica'?'selected':''}>Estética</option>
            </select></td>
            <td style="white-space:nowrap">
              <button class="btn-traducir me-1" onclick="traducirServicio(${res.id})"><i class="ti ti-language"></i></button>
              <button class="btn-guardar me-1" onclick="guardarServicio(${res.id})"><i class="ti ti-check"></i></button>
              <button class="btn-cancelar" onclick="eliminarServicio(${res.id})"><i class="ti ti-trash"></i></button>
            </td>`;
          tbody.appendChild(tr);
        }
      } else {
        Swal.fire({icon:'error',title:'Error al crear',text:res.error});
      }
    });
}

// ===== ELIMINAR =====
function eliminarServicio(id) {
  Swal.fire({
    title: '¿Eliminar este servicio?',
    text: 'Esta acción no se puede deshacer.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#6b7280',
  }).then(result => {
    if (!result.isConfirmed) return;
    fetch('ajax/eliminar-servicio.php', {
      method: 'POST',
      body: new URLSearchParams({id}),
      headers: {'Content-Type':'application/x-www-form-urlencoded'}
    })
    .then(r => r.json())
    .then(res => {
      if (res.success) {
        document.getElementById('srv-row-' + id)?.remove();
        document.getElementById('srv-item-' + id)?.remove();
        Swal.fire({icon:'success',title:'Eliminado',timer:1200,showConfirmButton:false});
      } else {
        Swal.fire({icon:'error',title:'Error',text: res.error || 'No se pudo eliminar'});
      }
    });
  });
}

// ===== TOGGLE ACORDEÓN =====
function toggleSrv(id) {
  document.getElementById('srv-item-' + id).classList.toggle('open');
}

// ===== DESKTOP =====
function traducirServicio(id) {
  const texto = document.getElementById('es_' + id).value.trim();
  if (!texto) return Swal.fire({icon:'warning',title:'Escribe el nombre en español primero',timer:2000,showConfirmButton:false});
  const data = new FormData(); data.append('texto', texto);
  fetch('ajax/deepl-translate.php', { method:'POST', body:data })
    .then(r => r.json())
    .then(res => {
      if (res.success) {
        document.getElementById('de_' + id).value = res.traducciones.de || '';
        document.getElementById('fr_' + id).value = res.traducciones.fr || '';
        document.getElementById('en_' + id).value = res.traducciones.en || '';
        Swal.fire({icon:'success',title:'Traducido',timer:1500,showConfirmButton:false});
      } else {
        Swal.fire({icon:'error',title:'Error al traducir',text:res.error});
      }
    });
}
function guardarServicio(id) {
  const data = new FormData();
  data.append('id',           id);
  data.append('nombre_es',    document.getElementById('es_' + id).value);
  data.append('nombre_de',    document.getElementById('de_' + id).value);
  data.append('nombre_fr',    document.getElementById('fr_' + id).value);
  data.append('nombre_en',    document.getElementById('en_' + id).value);
  data.append('precio',       document.getElementById('precio_' + id).value);
  data.append('duracion_min', document.getElementById('dur_' + id).value);
  data.append('categoria',    document.getElementById('cat_' + id).value);
  fetch('ajax/guardar-servicio.php', { method:'POST', body:data })
    .then(r => r.json())
    .then(res => {
      if (res.success) Swal.fire({icon:'success',title:'Guardado',timer:1200,showConfirmButton:false});
      else Swal.fire({icon:'error',title:'Error al guardar'});
    });
}

// ===== MOBILE =====
function traducirServicioM(id) {
  const texto = document.getElementById('m_es_' + id).value.trim();
  if (!texto) return Swal.fire({icon:'warning',title:'Escribe el nombre en español primero',timer:2000,showConfirmButton:false});
  const data = new FormData(); data.append('texto', texto);
  fetch('ajax/deepl-translate.php', { method:'POST', body:data })
    .then(r => r.json())
    .then(res => {
      if (res.success) {
        document.getElementById('m_de_' + id).value = res.traducciones.de || '';
        document.getElementById('m_fr_' + id).value = res.traducciones.fr || '';
        document.getElementById('m_en_' + id).value = res.traducciones.en || '';
        Swal.fire({icon:'success',title:'Traducido',timer:1500,showConfirmButton:false});
      } else {
        Swal.fire({icon:'error',title:'Error al traducir',text:res.error});
      }
    });
}
function guardarServicioM(id) {
  const data = new FormData();
  data.append('id',        id);
  data.append('nombre_es', document.getElementById('m_es_' + id).value);
  data.append('nombre_de', document.getElementById('m_de_' + id).value);
  data.append('nombre_fr', document.getElementById('m_fr_' + id).value);
  data.append('nombre_en', document.getElementById('m_en_' + id).value);
  data.append('precio',    document.getElementById('m_precio_' + id).value);
  data.append('categoria', document.getElementById('m_cat_' + id).value);
  fetch('ajax/guardar-servicio.php', { method:'POST', body:data })
    .then(r => r.json())
    .then(res => {
      if (res.success) {
        const header = document.querySelector('#srv-item-' + id + ' .srv-item-nombre');
        if (header) header.textContent = document.getElementById('m_es_' + id).value;
        const precio = document.querySelector('#srv-item-' + id + ' .srv-item-precio');
        if (precio) precio.textContent = 'CHF ' + parseInt(document.getElementById('m_precio_' + id).value);
        Swal.fire({icon:'success',title:'Guardado',timer:1200,showConfirmButton:false});
      } else {
        Swal.fire({icon:'error',title:'Error al guardar'});
      }
    });
}
</script>