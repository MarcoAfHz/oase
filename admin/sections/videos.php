<?php
$stmt_v = $pdo->query('SELECT * FROM videos WHERE activo = 1 ORDER BY orden ASC, id DESC');
$videos_admin = $stmt_v->fetchAll();
$categorias = ['corte'=>'Corte','color'=>'Color','tratamiento'=>'Tratamiento','estetica'=>'Estética','general'=>'General'];
?>

<style>
.vid-upload-area {
  border: 2px dashed #e5e7eb; border-radius: 10px;
  padding: 2rem; text-align: center; cursor: pointer;
  transition: all 0.2s; background: #fafafa; position: relative;
  margin-bottom: 1.25rem;
}
.vid-upload-area:hover { border-color: #c9a84c; background: rgba(201,168,76,0.04); }
.vid-upload-area input[type=file] { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; }
.vid-upload-icon { font-size: 36px; color: #d1d5db; margin-bottom: 0.5rem; }
.vid-upload-text { font-size: 13px; color: #6b7280; font-weight: 600; }
.vid-upload-sub { font-size: 11px; color: #9ca3af; margin-top: 4px; }

.vid-selected-preview { background: rgba(201,168,76,0.06); border: 0.5px solid rgba(201,168,76,0.3); border-radius: 8px; padding: 10px 14px; margin-bottom: 1rem; font-size: 12px; color: #92681a; display: none; align-items: center; gap: 8px; }
.vid-selected-preview i { font-size: 18px; }

.vid-info-form { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 1.25rem; margin-bottom: 1.25rem; }
.vid-field { margin-bottom: 1rem; }
.vid-field label { display: block; font-size: 10px; letter-spacing: 1.5px; text-transform: uppercase; color: #9ca3af; margin-bottom: 5px; }
.vid-field input, .vid-field select, .vid-field textarea { width: 100%; padding: 9px 12px; font-size: 13px; border: 1px solid #e5e7eb; border-radius: 6px; outline: none; background: #fff; color: #111827; font-family: inherit; }
.vid-field input:focus, .vid-field select:focus, .vid-field textarea:focus { border-color: #c9a84c; }
.vid-field textarea { resize: vertical; min-height: 70px; }
.vid-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }

.vid-tags-wrap { display: flex; flex-wrap: wrap; gap: 6px; padding: 8px; border: 1px solid #e5e7eb; border-radius: 6px; background: #fff; min-height: 40px; cursor: text; }
.vid-tag-item { background: rgba(201,168,76,0.12); color: #92681a; padding: 2px 10px; border-radius: 20px; font-size: 12px; display: flex; align-items: center; gap: 5px; }
.vid-tag-item button { background: none; border: none; color: #92681a; cursor: pointer; font-size: 12px; padding: 0; }
.vid-tag-input { border: none; outline: none; font-size: 13px; color: #111827; flex: 1; min-width: 80px; padding: 2px 4px; }

.vid-progress { display: none; height: 4px; background: #f3f4f6; border-radius: 2px; margin-bottom: 1rem; overflow: hidden; }
.vid-progress-bar { height: 100%; background: #c9a84c; width: 0%; transition: width 0.3s; border-radius: 2px; }

.vid-btn-publish { width: 100%; padding: 12px; background: #c9a84c; color: #0f0f0f; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 700; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 6px; }
.vid-btn-publish:hover { background: #e0c06a; }
.vid-btn-publish:disabled { opacity: 0.6; cursor: not-allowed; }

.vid-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-top: 1.5rem; }
.vid-card { border-radius: 10px; overflow: hidden; border: 1px solid #e5e7eb; background: #fff; transition: all 0.2s; }
.vid-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
.vid-thumb { position: relative; aspect-ratio: 9/16; overflow: hidden; background: #0f0f0f; cursor: pointer; }
.vid-thumb video { width: 100%; height: 100%; object-fit: cover; }
.vid-thumb-overlay { position: absolute; inset: 0; background: rgba(8,6,3,0.4); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.2s; }
.vid-thumb:hover .vid-thumb-overlay { opacity: 1; }
.vid-thumb-overlay i { font-size: 36px; color: #c9a84c; }
.vid-card-info { padding: 0.75rem; }
.vid-card-titulo { font-size: 12px; font-weight: 600; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 2px; }
.vid-card-cat { font-size: 10px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px; }
.vid-card-actions { display: flex; gap: 5px; margin-top: 6px; }
.btn-vid-del { background: none; border: none; color: #9ca3af; cursor: pointer; font-size: 15px; padding: 2px; transition: color 0.2s; }
.btn-vid-del:hover { color: #ef4444; }
.btn-vid-edit { background: none; border: none; color: #9ca3af; cursor: pointer; font-size: 15px; padding: 2px; transition: color 0.2s; }
.btn-vid-edit:hover { color: #c9a84c; }

/* Modal reproducir */
.vid-modal { position: fixed; inset: 0; z-index: 5000; background: rgba(8,6,3,0.97); display: none; align-items: center; justify-content: center; flex-direction: column; gap: 1rem; }
.vid-modal.open { display: flex; }
.vid-modal-close { position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: 1px solid #2a2318; color: #c9a84c; cursor: pointer; font-size: 18px; width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.2s; z-index: 10; }
.vid-modal-close:hover { background: #c9a84c; color: #0f0f0f; }
.vid-modal video { max-width: 90vw; max-height: 80vh; border-radius: 4px; }
.vid-modal-info { text-align: center; max-width: 400px; }
.vid-modal-info h3 { font-family: 'Georgia', serif; font-size: 16px; color: #f0ebe3; margin-bottom: 4px; }
.vid-modal-info p { font-family: -apple-system, sans-serif; font-size: 12px; color: #5a5246; }

/* Modal editar */
.vid-edit-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 9000; align-items: center; justify-content: center; }
.vid-edit-overlay.open { display: flex; }
.vid-edit-box { background: #fff; border-radius: 12px; width: 100%; max-width: 580px; max-height: 90vh; overflow-y: auto; margin: 1rem; }
.vid-edit-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; justify-content: space-between; }
.vid-edit-header h3 { font-size: 15px; font-weight: 600; color: #111827; margin: 0; display: flex; align-items: center; gap: 8px; }
.vid-edit-close { background: none; border: none; font-size: 20px; color: #9ca3af; cursor: pointer; }
.vid-edit-body { padding: 1.5rem; }

@media (max-width: 900px) { .vid-grid { grid-template-columns: repeat(2, 1fr); } .vid-grid-2 { grid-template-columns: 1fr; } }
@media (max-width: 480px) { .vid-grid { grid-template-columns: repeat(2, 1fr); gap: 0.5rem; } }
</style>

<!-- Modal reproducir -->
<div class="vid-modal" id="vidModal">
  <button class="vid-modal-close" onclick="cerrarVidModal()">✕</button>
  <video id="vidModalPlayer" controls style="max-width:90vw;max-height:80vh;border-radius:4px;"></video>
  <div class="vid-modal-info" id="vidModalInfo"></div>
</div>

<!-- Modal editar -->
<div class="vid-edit-overlay" id="vidEditOverlay">
  <div class="vid-edit-box">
    <div class="vid-edit-header">
      <h3><i class="ti ti-pencil" style="color:#c9a84c"></i> Editar video</h3>
      <button class="vid-edit-close" onclick="cerrarVidEditar()">✕</button>
    </div>
    <div class="vid-edit-body">
      <input type="hidden" id="edit-vid-id">

      <!-- Video actual -->
<div class="vid-field">
  <label>Video actual</label>
  <video id="edit-vid-preview" src="" muted style="width:100%;max-height:180px;object-fit:cover;border-radius:8px;background:#0f0f0f;"></video>
</div>

<!-- Reemplazar video -->
<div class="vid-field">
  <label>Reemplazar video (opcional)</label>
  <div class="vid-upload-area" style="padding:1rem;">
    <input type="file" id="edit-vid-file" accept="video/mp4,video/mov,video/webm,video/quicktime" onchange="previewEditVid(this)">
    <div style="font-size:22px;color:#d1d5db;"><i class="ti ti-video-plus"></i></div>
    <div style="font-size:12px;color:#6b7280;margin-top:4px;">Clic para reemplazar el video</div>
  </div>
  <div id="edit-vid-nuevo-nombre" style="display:none;font-size:11px;color:#92681a;margin-top:6px;"></div>
</div>

      <!-- Info -->
      <div class="vid-grid-2">
        <div class="vid-field">
          <label>Título (español)</label>
          <input type="text" id="edit-vid-titulo" placeholder="Ej: Transformación balayage">
        </div>
        <div class="vid-field">
          <label>Categoría</label>
          <select id="edit-vid-categoria">
            <?php foreach ($categorias as $k=>$v): ?>
            <option value="<?= $k ?>"><?= $v ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="vid-field">
        <label>Descripción (opcional)</label>
        <textarea id="edit-vid-desc" placeholder="Describe el trabajo..."></textarea>
      </div>
      <div class="vid-field">
        <label>Tags (pulsa Enter para añadir)</label>
        <div class="vid-tags-wrap" id="tags-vid-edit-wrap" onclick="document.getElementById('tag-vid-edit-input').focus()">
          <input type="text" class="vid-tag-input" id="tag-vid-edit-input" placeholder="#balayage" onkeydown="addVidEditTag(event)">
        </div>
        <input type="hidden" id="edit-vid-tags">
      </div>
      <div style="display:flex;gap:0.75rem;margin-top:0.5rem;">
        <button class="btn-gal-cancel" style="flex:1;padding:10px;background:#f3f4f6;color:#6b7280;border:none;border-radius:6px;cursor:pointer;font-size:13px;font-weight:600;" onclick="cerrarVidEditar()">Cancelar</button>
        <button class="vid-btn-publish" style="flex:1;" onclick="guardarVidEdicion()"><i class="ti ti-check"></i> Guardar</button>
      </div>
    </div>
  </div>
</div>

<div class="panel-card">
  <div class="panel-card-header">
    <h5><i class="ti ti-video"></i> Videos & Shorts</h5>
  </div>
  <div class="p-3">

    <!-- Zona subir -->
    <div class="vid-upload-area">
      <input type="file" id="video-file" accept="video/mp4,video/mov,video/webm,video/quicktime" onchange="seleccionarVideo(this)">
      <div class="vid-upload-icon"><i class="ti ti-video-plus"></i></div>
      <div class="vid-upload-text">Arrastra el video aquí o haz clic</div>
      <div class="vid-upload-sub">MP4, MOV, WebM — máx. 50MB · Formato vertical 9:16 recomendado</div>
    </div>

    <!-- Preview seleccionado -->
    <div class="vid-selected-preview" id="vidSelectedPreview">
      <i class="ti ti-file-video"></i>
      <span id="vidSelectedName"></span>
    </div>

    <!-- Info publicación -->
    <div class="vid-info-form">
      <div class="vid-grid-2">
        <div class="vid-field">
          <label>Título (español)</label>
          <input type="text" id="video-titulo" placeholder="Ej: Transformación balayage">
        </div>
        <div class="vid-field">
          <label>Categoría</label>
          <select id="video-categoria">
            <?php foreach ($categorias as $k=>$v): ?>
            <option value="<?= $k ?>"><?= $v ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="vid-field">
        <label>Descripción (opcional)</label>
        <textarea id="video-desc" placeholder="Describe el trabajo realizado..."></textarea>
      </div>
      <div class="vid-field">
        <label>Tags (pulsa Enter para añadir)</label>
        <div class="vid-tags-wrap" id="tags-vid-wrap" onclick="document.getElementById('tag-vid-input').focus()">
          <input type="text" class="vid-tag-input" id="tag-vid-input" placeholder="#balayage" onkeydown="addVidTag(event)">
        </div>
        <input type="hidden" id="video-tags">
      </div>
    </div>

    <!-- Progreso -->
    <div class="vid-progress" id="vidProgress">
      <div class="vid-progress-bar" id="vidProgressBar"></div>
    </div>

    <!-- Botón publicar -->
    <button class="vid-btn-publish" id="vidBtnPublish" onclick="subirVideo()">
      <i class="ti ti-upload"></i> Publicar video
    </button>

    <!-- Grid videos -->
    <?php if (!empty($videos_admin)): ?>
    <div style="font-size:13px;font-weight:600;color:#111827;margin:1.5rem 0 0.75rem;">
      Videos publicados (<?= count($videos_admin) ?>)
    </div>
    <div class="vid-grid" id="grid-videos">
      <?php foreach ($videos_admin as $v): ?>
      <div class="vid-card" id="video-<?= $v['id'] ?>">
        <div class="vid-thumb" onclick="abrirVidModal('/uploads/videos/<?= htmlspecialchars($v['archivo']) ?>','<?= htmlspecialchars(addslashes($v['titulo_es'] ?: ($v['titulo_de'] ?: ''))) ?>','<?= htmlspecialchars(addslashes($v['descripcion_es'] ?: ($v['descripcion_de'] ?: ''))) ?>')">
          <video src="/uploads/videos/<?= htmlspecialchars($v['archivo']) ?>" muted preload="metadata"></video>
          <div class="vid-thumb-overlay"><i class="ti ti-player-play-filled"></i></div>
        </div>
        <div class="vid-card-info">
          <div class="vid-card-titulo"><?= htmlspecialchars($v['titulo_es'] ?: ($v['titulo_de'] ?: 'Sin título')) ?></div>
          <div class="vid-card-cat"><?= $categorias[$v['categoria']] ?? $v['categoria'] ?></div>
          <div class="vid-card-actions">
            <button class="btn-vid-edit" onclick="abrirVidEditar(<?= $v['id'] ?>,'<?= htmlspecialchars(addslashes($v['titulo_es']??'')) ?>','<?= htmlspecialchars(addslashes($v['descripcion_es']??'')) ?>','<?= $v['categoria'] ?>','<?= htmlspecialchars(addslashes($v['tags']??'')) ?>','/uploads/videos/<?= htmlspecialchars($v['archivo']) ?>')"><i class="ti ti-pencil"></i></button>
            <button class="btn-vid-del" onclick="borrarVideo(<?= $v['id'] ?>)"><i class="ti ti-trash"></i></button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div style="text-align:center;padding:3rem 1rem;color:#9ca3af;font-size:13px;margin-top:1.5rem;">
      <i class="ti ti-video-off" style="font-size:40px;display:block;margin-bottom:0.75rem;"></i>
      No hay videos aún. Sube el primero.
    </div>
    <?php endif; ?>

  </div>
</div>

<script>
var vidFileSeleccionado = null;
var vidTags = [];
var vidEditTags = [];

function seleccionarVideo(input) {
  const file = input.files[0];
  if (!file) return;
  vidFileSeleccionado = file;
  document.getElementById('vidSelectedName').textContent = file.name + ' (' + (file.size/1024/1024).toFixed(1) + ' MB)';
  document.getElementById('vidSelectedPreview').style.display = 'flex';
}

function addVidTag(event) {
  if (event.key !== 'Enter') return;
  event.preventDefault();
  let val = event.target.value.trim().replace(/\s+/g,'');
  if (!val) return;
  if (!val.startsWith('#')) val = '#' + val;
  if (vidTags.includes(val)) { event.target.value=''; return; }
  vidTags.push(val);
  renderVidTags('tags-vid-wrap','tag-vid-input','video-tags',vidTags,'vid');
  event.target.value='';
}
function addVidEditTag(event) {
  if (event.key !== 'Enter') return;
  event.preventDefault();
  let val = event.target.value.trim().replace(/\s+/g,'');
  if (!val) return;
  if (!val.startsWith('#')) val = '#' + val;
  if (vidEditTags.includes(val)) { event.target.value=''; return; }
  vidEditTags.push(val);
  renderVidTags('tags-vid-edit-wrap','tag-vid-edit-input','edit-vid-tags',vidEditTags,'vid-edit');
  event.target.value='';
}
function renderVidTags(wrapId, inputId, hiddenId, arr, tipo) {
  const wrap = document.getElementById(wrapId);
  const inputEl = document.getElementById(inputId);
  wrap.innerHTML = '';
  arr.forEach((tag, i) => {
    const span = document.createElement('span');
    span.className = 'vid-tag-item';
    span.innerHTML = `${tag} <button onclick="removeVidTag('${tipo}',${i})">✕</button>`;
    wrap.appendChild(span);
  });
  wrap.appendChild(inputEl);
  document.getElementById(hiddenId).value = arr.join(',');
}
function removeVidTag(tipo, i) {
  if (tipo === 'vid') { vidTags.splice(i,1); renderVidTags('tags-vid-wrap','tag-vid-input','video-tags',vidTags,'vid'); }
  else { vidEditTags.splice(i,1); renderVidTags('tags-vid-edit-wrap','tag-vid-edit-input','edit-vid-tags',vidEditTags,'vid-edit'); }
}

function subirVideo() {
  if (!vidFileSeleccionado) return Swal.fire({icon:'warning',title:'Selecciona un video primero',timer:2000,showConfirmButton:false});
  const data = new FormData();
  data.append('archivo',        vidFileSeleccionado);
  data.append('titulo_es',      document.getElementById('video-titulo').value);
  data.append('descripcion_es', document.getElementById('video-desc').value);
  data.append('categoria',      document.getElementById('video-categoria').value);
  data.append('tags',           document.getElementById('video-tags').value);
  const btn=document.getElementById('vidBtnPublish');
  const progress=document.getElementById('vidProgress');
  const bar=document.getElementById('vidProgressBar');
  btn.disabled=true; btn.innerHTML='<i class="ti ti-loader"></i> Subiendo...';
  progress.style.display='block';
  const xhr = new XMLHttpRequest();
  xhr.upload.addEventListener('progress', e=>{ if(e.lengthComputable) bar.style.width=Math.round(e.loaded/e.total*100)+'%'; });
  xhr.addEventListener('load', ()=>{
    try {
      const res = JSON.parse(xhr.responseText);
      if(res.success) Swal.fire({icon:'success',title:'Video publicado',timer:1500,showConfirmButton:false}).then(()=>location.reload());
      else { Swal.fire({icon:'error',title:'Error',text:res.error||'No se pudo subir'}); btn.disabled=false; btn.innerHTML='<i class="ti ti-upload"></i> Publicar video'; progress.style.display='none'; }
    } catch(e) { Swal.fire({icon:'error',title:'Error de respuesta'}); btn.disabled=false; btn.innerHTML='<i class="ti ti-upload"></i> Publicar video'; }
  });
  xhr.addEventListener('error', ()=>{ Swal.fire({icon:'error',title:'Error de conexión'}); btn.disabled=false; btn.innerHTML='<i class="ti ti-upload"></i> Publicar video'; });
  xhr.open('POST','ajax/subir-video.php');
  xhr.send(data);
}

function abrirVidModal(src, titulo, desc) {
  const player = document.getElementById('vidModalPlayer');
  player.src=src; player.play();
  document.getElementById('vidModalInfo').innerHTML = titulo ? '<h3>'+titulo+'</h3>'+(desc?'<p>'+desc+'</p>':'') : '';
  document.getElementById('vidModal').classList.add('open');
  document.body.style.overflow='hidden';
}
function cerrarVidModal() {
  const player=document.getElementById('vidModalPlayer');
  player.pause(); player.src='';
  document.getElementById('vidModal').classList.remove('open');
  document.body.style.overflow='';
}

function abrirVidEditar(id, titulo, desc, cat, tags, src) {
  document.getElementById('edit-vid-id').value = id;
  document.getElementById('edit-vid-titulo').value = titulo;
  document.getElementById('edit-vid-desc').value = desc;
  document.getElementById('edit-vid-categoria').value = cat;
  document.getElementById('edit-vid-preview').src = src;
  vidEditTags = tags ? tags.split(',').map(t=>t.trim()).filter(Boolean) : [];
  document.getElementById('edit-vid-tags').value = tags || '';
  renderVidTags('tags-vid-edit-wrap','tag-vid-edit-input','edit-vid-tags',vidEditTags,'vid-edit');
  document.getElementById('vidEditOverlay').classList.add('open');
  document.body.style.overflow='hidden';
}
function cerrarVidEditar() {
  document.getElementById('vidEditOverlay').classList.remove('open');
  document.body.style.overflow='';
}
function guardarVidEdicion() {
  const id = document.getElementById('edit-vid-id').value;
  const data = new FormData();
  data.append('id',             id);
  data.append('titulo_es',      document.getElementById('edit-vid-titulo').value);
  data.append('descripcion_es', document.getElementById('edit-vid-desc').value);
  data.append('categoria',      document.getElementById('edit-vid-categoria').value);
  data.append('tags',           document.getElementById('edit-vid-tags').value);
  
  // Añadir nuevo video si se seleccionó
  const fileNuevo = document.getElementById('edit-vid-file').files[0];
  if (fileNuevo) data.append('archivo', fileNuevo);

  Swal.fire({title:'Guardando...',allowOutsideClick:false,didOpen:()=>Swal.showLoading()});
  fetch('ajax/editar-video.php',{method:'POST',body:data})
    .then(r=>r.json())
    .then(res=>{
      if(res.success){ cerrarVidEditar(); Swal.fire({icon:'success',title:'Guardado',timer:1500,showConfirmButton:false}).then(()=>location.reload()); }
      else Swal.fire({icon:'error',title:'Error',text:res.error||'No se pudo guardar'});
    })
    .catch(()=>Swal.fire({icon:'error',title:'Error de conexión'}));
}

function previewEditVid(input) {
  const file = input.files[0];
  if (!file) return;
  const preview = document.getElementById('edit-vid-preview');
  preview.src = URL.createObjectURL(file);
  const nombre = document.getElementById('edit-vid-nuevo-nombre');
  nombre.textContent = '✓ ' + file.name + ' (' + (file.size/1024/1024).toFixed(1) + ' MB)';
  nombre.style.display = 'block';
}

function borrarVideo(id) {
  Swal.fire({
    title:'¿Eliminar este video?', text:'Esta acción no se puede deshacer.',
    icon:'warning', showCancelButton:true,
    confirmButtonText:'Eliminar', cancelButtonText:'Cancelar',
    confirmButtonColor:'#ef4444', cancelButtonColor:'#6b7280',
  }).then(r=>{
    if(!r.isConfirmed) return;
    fetch('ajax/borrar-video.php',{method:'POST',body:new URLSearchParams({id}),headers:{'Content-Type':'application/x-www-form-urlencoded'}})
    .then(r=>r.json())
    .then(res=>{
      if(res.success){ document.getElementById('video-'+id)?.remove(); Swal.fire({icon:'success',title:'Eliminado',timer:1200,showConfirmButton:false}); }
      else Swal.fire({icon:'error',title:'Error',text:res.error});
    });
  });
}

document.getElementById('vidModal').addEventListener('click',e=>{ if(e.target===document.getElementById('vidModal')) cerrarVidModal(); });
document.getElementById('vidEditOverlay').addEventListener('click',e=>{ if(e.target===document.getElementById('vidEditOverlay')) cerrarVidEditar(); });
document.addEventListener('keydown',e=>{ if(e.key==='Escape'){ cerrarVidModal(); cerrarVidEditar(); } });
</script>