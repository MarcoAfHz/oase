<?php
$stmt_g = $pdo->query('
    SELECT g.*, 
           (SELECT archivo FROM galeria_fotos WHERE grupo_id = g.id ORDER BY orden ASC, id ASC LIMIT 1) as portada,
           (SELECT COUNT(*) FROM galeria_fotos WHERE grupo_id = g.id) as num_fotos
    FROM galeria_grupos g 
    WHERE g.activo = 1 
    ORDER BY g.orden ASC, g.id DESC
');
$grupos = $stmt_g->fetchAll();

$fotos_por_grupo = [];
if (!empty($grupos)) {
    $ids = implode(',', array_column($grupos, 'id'));
    $stmt_f = $pdo->query("SELECT * FROM galeria_fotos WHERE grupo_id IN ($ids) ORDER BY orden ASC, id ASC");
    foreach ($stmt_f->fetchAll() as $f) {
        $fotos_por_grupo[$f['grupo_id']][] = $f;
    }
}

$categorias = ['corte'=>'Corte','color'=>'Color','tratamiento'=>'Tratamiento','estetica'=>'Estética','general'=>'General'];
?>

<style>
.gal-tipos { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 2rem; }
.gal-tipo-btn { background: #fff; border: 1.5px solid #e5e7eb; border-radius: 16px; padding: 1.5rem 1rem; text-align: center; cursor: pointer; transition: all 0.2s; display: flex; flex-direction: column; align-items: center; gap: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
.gal-tipo-btn:hover { border-color: #c9a84c; background: rgba(201,168,76,0.04); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
.gal-tipo-icon { width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 26px; }
.gal-tipo-icon.foto { background: rgba(59,130,246,0.1); color: #3b82f6; }
.gal-tipo-icon.ad { background: rgba(201,168,76,0.12); color: #c9a84c; }
.gal-tipo-icon.carrusel { background: rgba(34,197,94,0.1); color: #22c55e; }
.gal-tipo-nombre { font-size: 13px; font-weight: 700; color: #111827; }
.gal-tipo-desc { font-size: 11px; color: #9ca3af; line-height: 1.4; }

.gal-form { display: none; }
.gal-form.active { display: block; }

.gal-upload-area { border: 2px dashed #e5e7eb; border-radius: 10px; padding: 2rem; text-align: center; cursor: pointer; transition: all 0.2s; background: #fafafa; position: relative; }
.gal-upload-area:hover { border-color: #c9a84c; background: rgba(201,168,76,0.04); }
.gal-upload-area input[type=file] { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; }
.gal-upload-icon { font-size: 32px; color: #d1d5db; margin-bottom: 0.5rem; }
.gal-upload-text { font-size: 13px; color: #6b7280; }
.gal-upload-sub { font-size: 11px; color: #9ca3af; margin-top: 4px; }

.gal-ad-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.gal-ad-zone { border: 2px dashed #e5e7eb; border-radius: 10px; aspect-ratio: 3/4; display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; position: relative; overflow: hidden; background: #fafafa; }
.gal-ad-zone:hover { border-color: #c9a84c; background: rgba(201,168,76,0.04); }
.gal-ad-zone input[type=file] { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; z-index: 5; }
.gal-ad-zone-label { font-size: 11px; letter-spacing: 2px; text-transform: uppercase; font-weight: 700; margin-bottom: 0.5rem; }
.gal-ad-zone.antes .gal-ad-zone-label { color: #6b7280; }
.gal-ad-zone.despues .gal-ad-zone-label { color: #c9a84c; }
.gal-ad-zone-icon { font-size: 28px; color: #d1d5db; }
.gal-ad-zone-hint { font-size: 10px; color: #9ca3af; margin-top: 0.5rem; text-align: center; padding: 0 0.5rem; }
.gal-ad-preview { position: absolute; inset: 0; object-fit: cover; width: 100%; height: 100%; display: none; }
.gal-ad-preview.show { display: block; }
.gal-ad-remove { position: absolute; top: 6px; right: 6px; z-index: 10; background: rgba(0,0,0,0.6); color: #fff; border: none; border-radius: 50%; width: 24px; height: 24px; font-size: 12px; cursor: pointer; display: none; align-items: center; justify-content: center; }
.gal-ad-remove.show { display: flex; }

.gal-carrusel-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 0.75rem; }
.gal-carrusel-add { aspect-ratio: 1; border: 2px dashed #e5e7eb; border-radius: 8px; display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; position: relative; background: #fafafa; min-height: 100px; }
.gal-carrusel-add:hover { border-color: #c9a84c; background: rgba(201,168,76,0.04); }
.gal-carrusel-add input { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; }
.gal-carrusel-thumb { aspect-ratio: 1; border-radius: 8px; overflow: hidden; position: relative; background: #f3f4f6; }
.gal-carrusel-thumb img { width: 100%; height: 100%; object-fit: cover; }
.gal-carrusel-thumb-remove { position: absolute; top: 3px; right: 3px; background: rgba(0,0,0,0.6); color: #fff; border: none; border-radius: 50%; width: 20px; height: 20px; font-size: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; }

.gal-info-form { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 1.25rem; margin-top: 1.25rem; }
.gal-field { margin-bottom: 1rem; }
.gal-field label { display: block; font-size: 10px; letter-spacing: 1.5px; text-transform: uppercase; color: #9ca3af; margin-bottom: 5px; }
.gal-field input, .gal-field select, .gal-field textarea { width: 100%; padding: 9px 12px; font-size: 13px; border: 1px solid #e5e7eb; border-radius: 6px; outline: none; background: #fff; color: #111827; font-family: inherit; }
.gal-field input:focus, .gal-field select:focus, .gal-field textarea:focus { border-color: #c9a84c; }
.gal-field textarea { resize: vertical; min-height: 70px; }
.gal-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }

.tags-wrap { display: flex; flex-wrap: wrap; gap: 6px; padding: 8px; border: 1px solid #e5e7eb; border-radius: 6px; background: #fff; min-height: 40px; cursor: text; }
.tag-item { background: rgba(201,168,76,0.12); color: #92681a; padding: 2px 10px; border-radius: 20px; font-size: 12px; display: flex; align-items: center; gap: 5px; }
.tag-item button { background: none; border: none; color: #92681a; cursor: pointer; font-size: 12px; padding: 0; line-height: 1; }
.tag-input { border: none; outline: none; font-size: 13px; color: #111827; flex: 1; min-width: 80px; padding: 2px 4px; }

.gal-form-btns { display: flex; gap: 0.75rem; margin-top: 1.25rem; }
.btn-gal-cancel { padding: 10px 20px; background: #f3f4f6; color: #6b7280; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 600; transition: all 0.2s; }
.btn-gal-cancel:hover { background: #e5e7eb; }
.btn-gal-publish { padding: 10px 24px; background: #c9a84c; color: #0f0f0f; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 600; transition: all 0.2s; display: flex; align-items: center; gap: 6px; }
.btn-gal-publish:hover { background: #e0c06a; }

.gal-entries-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
.gal-entry-card { border-radius: 10px; overflow: hidden; border: 1px solid #e5e7eb; background: #fff; transition: all 0.2s; }
.gal-entry-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
.gal-entry-thumb { aspect-ratio: 4/3; overflow: hidden; background: #f3f4f6; position: relative; }
.gal-entry-thumb img { width: 100%; height: 100%; object-fit: cover; }
.gal-entry-badge { position: absolute; top: 8px; left: 8px; padding: 3px 8px; border-radius: 20px; font-size: 10px; font-weight: 700; }
.badge-foto { background: rgba(59,130,246,0.85); color: #fff; }
.badge-antes_despues { background: rgba(201,168,76,0.9); color: #0f0f0f; }
.badge-carrusel { background: rgba(34,197,94,0.85); color: #fff; }
.gal-entry-info { padding: 0.75rem; }
.gal-entry-titulo { font-size: 13px; font-weight: 600; color: #111827; margin-bottom: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.gal-entry-meta { font-size: 11px; color: #9ca3af; display: flex; align-items: center; justify-content: space-between; }
.gal-entry-actions { display: flex; gap: 5px; }
.btn-gal-del { background: none; border: none; color: #9ca3af; cursor: pointer; font-size: 15px; padding: 2px; transition: color 0.2s; }
.btn-gal-del:hover { color: #ef4444; }
.btn-gal-edit { background: none; border: none; color: #9ca3af; cursor: pointer; font-size: 15px; padding: 2px; transition: color 0.2s; }
.btn-gal-edit:hover { color: #c9a84c; }

/* Modal editar */
.gal-edit-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 9000; align-items: center; justify-content: center; }
.gal-edit-overlay.open { display: flex; }
.gal-edit-box { background: #fff; border-radius: 12px; width: 100%; max-width: 640px; max-height: 90vh; overflow-y: auto; margin: 1rem; }
.gal-edit-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; justify-content: space-between; }
.gal-edit-header h3 { font-size: 15px; font-weight: 600; color: #111827; margin: 0; }
.gal-edit-close { background: none; border: none; font-size: 20px; color: #9ca3af; cursor: pointer; }
.gal-edit-body { padding: 1.5rem; }

.edit-fotos-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(90px, 1fr)); gap: 0.5rem; margin-bottom: 0.5rem; }
.edit-foto-item { position: relative; aspect-ratio: 1; border-radius: 6px; overflow: hidden; background: #f3f4f6; }
.edit-foto-item img { width: 100%; height: 100%; object-fit: cover; }
.edit-foto-del { position: absolute; top: 3px; right: 3px; background: rgba(239,68,68,0.9); color: #fff; border: none; border-radius: 50%; width: 20px; height: 20px; font-size: 11px; cursor: pointer; display: flex; align-items: center; justify-content: center; }
.edit-foto-label { position: absolute; bottom: 3px; left: 3px; background: rgba(0,0,0,0.6); color: #fff; font-size: 9px; padding: 1px 5px; border-radius: 2px; letter-spacing: 0.5px; text-transform: uppercase; }

@media (max-width: 900px) {
  .gal-tipos { grid-template-columns: 1fr 1fr; gap: 0.75rem; }
  .gal-tipo-btn { flex-direction: column; text-align: center; padding: 1.25rem 0.5rem; border-radius: 20px; }
  .gal-tipo-icon { width: 60px; height: 60px; border-radius: 18px; font-size: 28px; }
  .gal-tipo-nombre { font-size: 11px; font-weight: 700; }
  .gal-tipo-desc { display: none; }
  .gal-ad-grid { grid-template-columns: 1fr 1fr; }
  .gal-grid-2 { grid-template-columns: 1fr; }
  .gal-entries-grid { grid-template-columns: repeat(2, 1fr); }
  .gal-form-btns { flex-direction: column; }
}
@media (max-width: 480px) {
  .gal-entries-grid { grid-template-columns: 1fr; }
}
</style>

<!-- MODAL EDITAR -->
<div class="gal-edit-overlay" id="galEditOverlay">
  <div class="gal-edit-box">
    <div class="gal-edit-header">
      <h3><i class="ti ti-pencil" style="color:#c9a84c"></i> Editar entrada</h3>
      <button class="gal-edit-close" onclick="cerrarEditar()">✕</button>
    </div>
    <div class="gal-edit-body">
      <input type="hidden" id="edit-id">
      <input type="hidden" id="edit-tipo">

      <!-- FOTO ÚNICA -->
      <div id="edit-zona-foto" style="display:none;">
        <div class="gal-field">
          <label>Foto actual</label>
          <div id="edit-foto-actual" class="edit-fotos-grid" style="margin-bottom:0.75rem;"></div>
          <label>Reemplazar foto</label>
          <div class="gal-upload-area" style="padding:1rem;">
            <input type="file" id="edit-file-foto" accept="image/*" onchange="previewEditFoto(this,'edit-preview-foto')">
            <div style="font-size:22px;color:#d1d5db;"><i class="ti ti-cloud-upload"></i></div>
            <div style="font-size:12px;color:#6b7280;margin-top:4px;">Clic para reemplazar</div>
          </div>
          <img id="edit-preview-foto" src="" style="display:none;max-height:150px;margin-top:8px;border-radius:6px;">
        </div>
      </div>

      <!-- ANTES & DESPUÉS -->
<div id="edit-zona-ad" style="display:none;">
  <div class="gal-field">
    <label>Reemplazar fotos (deja en blanco para mantener)</label>
    <div class="gal-ad-grid">
      <div class="gal-ad-zone antes" style="min-height:160px;position:relative;cursor:default;">
  <img id="edit-bg-antes" src="" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;display:none;border-radius:8px;">
  <div class="gal-ad-zone-label" id="edit-label-antes">ANTES</div>
  <div class="gal-ad-zone-icon" id="edit-icon-antes"><i class="ti ti-camera"></i></div>
  <div class="gal-ad-zone-hint" id="edit-hint-antes">Toca para subir</div>
  <!-- Botón reemplazar visible encima de la foto -->
  <label id="edit-btn-antes" style="position:absolute;bottom:8px;left:50%;transform:translateX(-50%);background:rgba(201,168,76,0.9);color:#0f0f0f;padding:4px 12px;border-radius:4px;font-size:11px;font-weight:700;cursor:pointer;z-index:10;display:none;">
    <i class="ti ti-refresh"></i> Reemplazar
    <input type="file" accept="image/*" id="edit-file-antes" onchange="previewEditAD(this,'antes')" style="display:none;">
  </label>
</div>

<div class="gal-ad-zone despues" style="min-height:160px;position:relative;cursor:default;">
  <img id="edit-bg-despues" src="" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;display:none;border-radius:8px;">
  <div class="gal-ad-zone-label" style="color:#c9a84c;" id="edit-label-despues">DESPUÉS</div>
  <div class="gal-ad-zone-icon" id="edit-icon-despues"><i class="ti ti-sparkles"></i></div>
  <div class="gal-ad-zone-hint" id="edit-hint-despues">Toca para subir</div>
  <label id="edit-btn-despues" style="position:absolute;bottom:8px;left:50%;transform:translateX(-50%);background:rgba(201,168,76,0.9);color:#0f0f0f;padding:4px 12px;border-radius:4px;font-size:11px;font-weight:700;cursor:pointer;z-index:10;display:none;">
    <i class="ti ti-refresh"></i> Reemplazar
    <input type="file" accept="image/*" id="edit-file-despues" onchange="previewEditAD(this,'despues')" style="display:none;">
  </label>
</div>
    </div>
    <div style="font-size:11px;color:#9ca3af;margin-top:6px;">Deja en blanco si no quieres cambiar esa foto.</div>
  </div>
</div>

      <!-- CARRUSEL -->
      <div id="edit-zona-carrusel" style="display:none;">
        <div class="gal-field">
          <label>Fotos actuales</label>
          <div class="edit-fotos-grid" id="edit-fotos-grid"></div>
        </div>
        <div class="gal-field">
          <label>Añadir fotos nuevas</label>
          <div class="gal-upload-area" style="padding:1rem;">
            <input type="file" id="edit-nuevas-fotos" accept="image/*" multiple onchange="previewNuevas(this)">
            <div style="font-size:22px;color:#d1d5db;"><i class="ti ti-cloud-upload"></i></div>
            <div style="font-size:12px;color:#6b7280;margin-top:4px;">Arrastra o haz clic</div>
          </div>
          <div class="edit-fotos-grid" id="edit-nuevas-preview" style="margin-top:0.5rem;"></div>
        </div>
      </div>

      <!-- INFO COMÚN -->
      <div class="gal-grid-2">
        <div class="gal-field">
          <label>Título (español)</label>
          <input type="text" id="edit-titulo" placeholder="Título del trabajo">
        </div>
        <div class="gal-field">
          <label>Categoría</label>
          <select id="edit-categoria">
            <?php foreach ($categorias as $k=>$v): ?>
            <option value="<?= $k ?>"><?= $v ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="gal-field">
        <label>Descripción (opcional)</label>
        <textarea id="edit-desc" placeholder="Describe el trabajo..."></textarea>
      </div>
      <div class="gal-field">
        <label>Tags (pulsa Enter para añadir)</label>
        <div class="tags-wrap" id="tags-edit-wrap" onclick="document.getElementById('tag-edit-input').focus()">
          <input type="text" class="tag-input" id="tag-edit-input" placeholder="#balayage" onkeydown="addTag(event,'edit')">
        </div>
        <input type="hidden" id="edit-tags">
      </div>
      <div class="gal-form-btns">
        <button class="btn-gal-cancel" onclick="cerrarEditar()">Cancelar</button>
        <button class="btn-gal-publish" onclick="guardarEdicion()"><i class="ti ti-check"></i> Guardar cambios</button>
      </div>
    </div>
  </div>
</div>

<div class="panel-card">
  <div class="panel-card-header">
    <h5><i class="ti ti-photo"></i> Galería</h5>
  </div>
  <div class="p-3">

    <div id="gal-step1">
      <div style="font-size:13px;font-weight:600;color:#111827;margin-bottom:1rem;">¿Qué quieres añadir?</div>
      <div class="gal-tipos">
        <div class="gal-tipo-btn" onclick="elegirTipo('foto')">
          <div class="gal-tipo-icon foto"><i class="ti ti-photo"></i></div>
          <div class="gal-tipo-nombre">Foto única</div>
          <div class="gal-tipo-desc">Sube una sola foto de tu trabajo</div>
        </div>
        <div class="gal-tipo-btn" onclick="elegirTipo('antes_despues')">
          <div class="gal-tipo-icon ad"><i class="ti ti-arrows-left-right"></i></div>
          <div class="gal-tipo-nombre">Antes & Después</div>
          <div class="gal-tipo-desc">Compara el resultado con 2 fotos</div>
        </div>
        <div class="gal-tipo-btn" onclick="elegirTipo('carrusel')">
          <div class="gal-tipo-icon carrusel"><i class="ti ti-layout-grid"></i></div>
          <div class="gal-tipo-nombre">Carrusel</div>
          <div class="gal-tipo-desc">Sube varias fotos del mismo trabajo</div>
        </div>
      </div>

      <?php if (!empty($grupos)): ?>
      <div style="font-size:13px;font-weight:600;color:#111827;margin-bottom:0.75rem;margin-top:0.5rem;">
        Entradas publicadas (<?= count($grupos) ?>)
      </div>
      <div class="gal-entries-grid">
        <?php foreach ($grupos as $g): ?>
        <div class="gal-entry-card" id="gal-entry-<?= $g['id'] ?>">
          <div class="gal-entry-thumb">
            <?php if ($g['portada']): ?>
            <img src="/uploads/<?= htmlspecialchars($g['portada']) ?>" alt="">
            <?php else: ?>
            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#d1d5db;font-size:28px;"><i class="ti ti-photo-off"></i></div>
            <?php endif; ?>
            <span class="gal-entry-badge badge-<?= $g['tipo'] ?>">
              <?= $g['tipo']==='foto'?'📷 Foto':($g['tipo']==='antes_despues'?'↔️ A&D':'📸 '.($g['num_fotos']).' fotos') ?>
            </span>
          </div>
          <div class="gal-entry-info">
            <div class="gal-entry-titulo"><?= htmlspecialchars($g['titulo_es'] ?: ($g['titulo_de'] ?: ($g['titulo_fr'] ?: ($g['titulo_en'] ?: 'Sin título')))) ?></div>
            <div class="gal-entry-meta">
              <span><?= $categorias[$g['categoria']] ?? $g['categoria'] ?></span>
              <div class="gal-entry-actions">
                <button class="btn-gal-edit" onclick="abrirEditar(<?= $g['id'] ?>)"><i class="ti ti-pencil"></i></button>
                <button class="btn-gal-del" onclick="borrarEntrada(<?= $g['id'] ?>)"><i class="ti ti-trash"></i></button>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
      <div style="text-align:center;padding:3rem 1rem;color:#9ca3af;font-size:13px;">
        <i class="ti ti-photo-off" style="font-size:40px;display:block;margin-bottom:0.75rem;"></i>
        No hay entradas aún.
      </div>
      <?php endif; ?>
    </div>

    <!-- FORM FOTO ÚNICA -->
    <div id="gal-form-foto" class="gal-form">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:1.25rem;">
        <button onclick="volverTipos()" style="background:none;border:none;color:#9ca3af;cursor:pointer;font-size:20px;padding:0;"><i class="ti ti-arrow-left"></i></button>
        <div style="font-size:15px;font-weight:600;color:#111827;">Foto única</div>
      </div>
      <div class="gal-upload-area">
        <input type="file" id="file-foto" accept="image/*" onchange="previewFoto(this)">
        <div id="preview-foto-wrap">
          <div class="gal-upload-icon"><i class="ti ti-cloud-upload"></i></div>
          <div class="gal-upload-text">Arrastra la foto aquí o haz clic</div>
          <div class="gal-upload-sub">JPG, PNG, WEBP — máx. 10MB</div>
        </div>
        <img id="preview-foto-img" style="display:none;max-height:300px;border-radius:8px;margin:0 auto;">
      </div>
      <div class="gal-info-form">
        <div class="gal-grid-2">
          <div class="gal-field"><label>Título</label><input type="text" id="foto-titulo" placeholder="Ej: Balayage + corte"></div>
          <div class="gal-field"><label>Categoría</label><select id="foto-categoria"><?php foreach ($categorias as $k=>$v): ?><option value="<?= $k ?>"><?= $v ?></option><?php endforeach; ?></select></div>
        </div>
        <div class="gal-field"><label>Descripción</label><textarea id="foto-desc" placeholder="Describe el trabajo..."></textarea></div>
        <div class="gal-field">
          <label>Tags</label>
          <div class="tags-wrap" id="tags-foto-wrap" onclick="document.getElementById('tag-foto-input').focus()">
            <input type="text" class="tag-input" id="tag-foto-input" placeholder="#balayage" onkeydown="addTag(event,'foto')">
          </div>
          <input type="hidden" id="foto-tags">
        </div>
      </div>
      <div class="gal-form-btns">
        <button class="btn-gal-cancel" onclick="volverTipos()">Cancelar</button>
        <button class="btn-gal-publish" onclick="publicarFoto()"><i class="ti ti-upload"></i> Publicar</button>
      </div>
    </div>

    <!-- FORM ANTES & DESPUÉS -->
    <div id="gal-form-antes_despues" class="gal-form">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:1.25rem;">
        <button onclick="volverTipos()" style="background:none;border:none;color:#9ca3af;cursor:pointer;font-size:20px;padding:0;"><i class="ti ti-arrow-left"></i></button>
        <div style="font-size:15px;font-weight:600;color:#111827;">Antes & Después</div>
      </div>
      <div style="background:rgba(201,168,76,0.06);border:0.5px solid rgba(201,168,76,0.3);border-radius:8px;padding:10px 14px;font-size:12px;color:#92681a;margin-bottom:1rem;">
        <i class="ti ti-info-circle"></i> Para mejor resultado usa fotos verticales del mismo ángulo.
      </div>
      <div class="gal-ad-grid">
        <div class="gal-ad-zone antes" id="zone-antes">
          <input type="file" accept="image/*" onchange="previewAD(this,'antes')">
          <img class="gal-ad-preview" id="preview-antes" src="">
          <button class="gal-ad-remove" id="remove-antes" onclick="removeAD('antes')">✕</button>
          <div class="gal-ad-zone-label">ANTES</div>
          <div class="gal-ad-zone-icon"><i class="ti ti-camera"></i></div>
          <div class="gal-ad-zone-hint">Foto antes</div>
        </div>
        <div class="gal-ad-zone despues" id="zone-despues">
          <input type="file" accept="image/*" onchange="previewAD(this,'despues')">
          <img class="gal-ad-preview" id="preview-despues" src="">
          <button class="gal-ad-remove" id="remove-despues" onclick="removeAD('despues')">✕</button>
          <div class="gal-ad-zone-label" style="color:#c9a84c;">DESPUÉS</div>
          <div class="gal-ad-zone-icon"><i class="ti ti-sparkles"></i></div>
          <div class="gal-ad-zone-hint">Foto después</div>
        </div>
      </div>
      <div class="gal-info-form">
        <div class="gal-grid-2">
          <div class="gal-field"><label>Título</label><input type="text" id="ad-titulo" placeholder="Ej: Transformación balayage"></div>
          <div class="gal-field"><label>Categoría</label><select id="ad-categoria"><?php foreach ($categorias as $k=>$v): ?><option value="<?= $k ?>"><?= $v ?></option><?php endforeach; ?></select></div>
        </div>
        <div class="gal-field"><label>Descripción</label><textarea id="ad-desc" placeholder="Describe el trabajo..."></textarea></div>
        <div class="gal-field">
          <label>Tags</label>
          <div class="tags-wrap" id="tags-ad-wrap" onclick="document.getElementById('tag-ad-input').focus()">
            <input type="text" class="tag-input" id="tag-ad-input" placeholder="#balayage" onkeydown="addTag(event,'ad')">
          </div>
          <input type="hidden" id="ad-tags">
        </div>
      </div>
      <div class="gal-form-btns">
        <button class="btn-gal-cancel" onclick="volverTipos()">Cancelar</button>
        <button class="btn-gal-publish" onclick="publicarAD()"><i class="ti ti-upload"></i> Publicar</button>
      </div>
    </div>

    <!-- FORM CARRUSEL -->
    <div id="gal-form-carrusel" class="gal-form">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:1.25rem;">
        <button onclick="volverTipos()" style="background:none;border:none;color:#9ca3af;cursor:pointer;font-size:20px;padding:0;"><i class="ti ti-arrow-left"></i></button>
        <div style="font-size:15px;font-weight:600;color:#111827;">Carrusel de fotos</div>
      </div>
      <div class="gal-carrusel-grid" id="carrusel-grid">
        <div class="gal-carrusel-add">
          <input type="file" accept="image/*" multiple onchange="addCarruselFotos(this)">
          <i class="ti ti-plus" style="font-size:24px;color:#9ca3af;"></i>
          <div style="font-size:11px;color:#9ca3af;margin-top:4px;">Añadir fotos</div>
        </div>
      </div>
      <div style="font-size:11px;color:#9ca3af;margin-top:6px;">Hasta 10 fotos.</div>
      <div class="gal-info-form">
        <div class="gal-grid-2">
          <div class="gal-field"><label>Título</label><input type="text" id="car-titulo" placeholder="Ej: Look de boda"></div>
          <div class="gal-field"><label>Categoría</label><select id="car-categoria"><?php foreach ($categorias as $k=>$v): ?><option value="<?= $k ?>"><?= $v ?></option><?php endforeach; ?></select></div>
        </div>
        <div class="gal-field"><label>Descripción</label><textarea id="car-desc" placeholder="Describe el trabajo..."></textarea></div>
        <div class="gal-field">
          <label>Tags</label>
          <div class="tags-wrap" id="tags-car-wrap" onclick="document.getElementById('tag-car-input').focus()">
            <input type="text" class="tag-input" id="tag-car-input" placeholder="#boda" onkeydown="addTag(event,'car')">
          </div>
          <input type="hidden" id="car-tags">
        </div>
      </div>
      <div class="gal-form-btns">
        <button class="btn-gal-cancel" onclick="volverTipos()">Cancelar</button>
        <button class="btn-gal-publish" onclick="publicarCarrusel()"><i class="ti ti-upload"></i> Publicar</button>
      </div>
    </div>

  </div>
</div>

<script>
var carruselFiles = [];
var adFiles = { antes: null, despues: null };
var tags = { foto: [], ad: [], car: [], edit: [] };
var editNuevasFiles = [];

var gruposData = <?= json_encode(array_map(function($g) use ($fotos_por_grupo) {
    return [
        'id'             => $g['id'],
        'tipo'           => $g['tipo'],
        'titulo_es'      => $g['titulo_es'] ?? '',
        'descripcion_es' => $g['descripcion_es'] ?? '',
        'categoria'      => $g['categoria'],
        'tags'           => $g['tags'] ?? '',
        'fotos'          => array_map(fn($f) => ['id'=>$f['id'],'archivo'=>$f['archivo'],'rol'=>$f['rol']], $fotos_por_grupo[$g['id']] ?? []),
    ];
}, $grupos)) ?>;

function elegirTipo(tipo) {
  document.getElementById('gal-step1').style.display = 'none';
  document.querySelectorAll('.gal-form').forEach(f => f.classList.remove('active'));
  document.getElementById('gal-form-' + tipo).classList.add('active');
}
function volverTipos() {
  document.getElementById('gal-step1').style.display = 'block';
  document.querySelectorAll('.gal-form').forEach(f => f.classList.remove('active'));
  carruselFiles = []; adFiles = { antes: null, despues: null };
  resetCarruselGrid();
}

function previewFoto(input) {
  if (!input.files[0]) return;
  const reader = new FileReader();
  reader.onload = e => {
    document.getElementById('preview-foto-wrap').style.display = 'none';
    const img = document.getElementById('preview-foto-img');
    img.src = e.target.result; img.style.display = 'block';
  };
  reader.readAsDataURL(input.files[0]);
}

function previewAD(input, rol) {
  if (!input.files[0]) return;
  adFiles[rol] = input.files[0];
  const reader = new FileReader();
  reader.onload = e => {
    const preview = document.getElementById('preview-' + rol);
    const remove  = document.getElementById('remove-' + rol);
    preview.src = e.target.result; preview.classList.add('show'); remove.classList.add('show');
    const zone = document.getElementById('zone-' + rol);
    zone.querySelector('.gal-ad-zone-label').style.display = 'none';
    zone.querySelector('.gal-ad-zone-icon').style.display = 'none';
    zone.querySelector('.gal-ad-zone-hint').style.display = 'none';
  };
  reader.readAsDataURL(input.files[0]);
}
function removeAD(rol) {
  adFiles[rol] = null;
  document.getElementById('preview-' + rol).classList.remove('show');
  document.getElementById('remove-' + rol).classList.remove('show');
  const zone = document.getElementById('zone-' + rol);
  zone.querySelector('.gal-ad-zone-label').style.display = '';
  zone.querySelector('.gal-ad-zone-icon').style.display = '';
  zone.querySelector('.gal-ad-zone-hint').style.display = '';
}

function addCarruselFotos(input) {
  Array.from(input.files).forEach(f => { if (carruselFiles.length < 10) carruselFiles.push(f); });
  renderCarruselGrid();
}
function renderCarruselGrid() {
  const grid = document.getElementById('carrusel-grid');
  grid.innerHTML = '';
  carruselFiles.forEach((file, i) => {
    const url = URL.createObjectURL(file);
    const div = document.createElement('div');
    div.className = 'gal-carrusel-thumb';
    div.innerHTML = `<img src="${url}"><button class="gal-carrusel-thumb-remove" onclick="removeCarrusel(${i})">✕</button>`;
    grid.appendChild(div);
  });
  if (carruselFiles.length < 10) {
    const add = document.createElement('div');
    add.className = 'gal-carrusel-add';
    add.innerHTML = `<input type="file" accept="image/*" multiple onchange="addCarruselFotos(this)"><i class="ti ti-plus" style="font-size:24px;color:#9ca3af;"></i><div style="font-size:11px;color:#9ca3af;margin-top:4px;">Añadir</div>`;
    grid.appendChild(add);
  }
}
function removeCarrusel(i) { carruselFiles.splice(i, 1); renderCarruselGrid(); }
function resetCarruselGrid() {
  document.getElementById('carrusel-grid').innerHTML = `<div class="gal-carrusel-add"><input type="file" accept="image/*" multiple onchange="addCarruselFotos(this)"><i class="ti ti-plus" style="font-size:24px;color:#9ca3af;"></i><div style="font-size:11px;color:#9ca3af;margin-top:4px;">Añadir fotos</div></div>`;
}

function addTag(event, tipo) {
  if (event.key !== 'Enter') return;
  event.preventDefault();
  const input = event.target;
  let val = input.value.trim().replace(/\s+/g,'');
  if (!val) return;
  if (!val.startsWith('#')) val = '#' + val;
  if (tags[tipo].includes(val)) { input.value=''; return; }
  tags[tipo].push(val);
  renderTags(tipo);
  input.value = '';
}
function renderTags(tipo) {
  const wrapId = 'tags-' + tipo + '-wrap';
  const inputId = 'tag-' + tipo + '-input';
  const hiddenMap = {foto:'foto-tags',ad:'ad-tags',car:'car-tags',edit:'edit-tags'};
  const wrap = document.getElementById(wrapId);
  const inputEl = document.getElementById(inputId);
  wrap.innerHTML = '';
  tags[tipo].forEach((tag, i) => {
    const span = document.createElement('span');
    span.className = 'tag-item';
    span.innerHTML = `${tag} <button onclick="removeTag('${tipo}',${i})">✕</button>`;
    wrap.appendChild(span);
  });
  wrap.appendChild(inputEl);
  if (document.getElementById(hiddenMap[tipo])) document.getElementById(hiddenMap[tipo]).value = tags[tipo].join(',');
}
function removeTag(tipo, i) { tags[tipo].splice(i, 1); renderTags(tipo); }

function publicarFoto() {
  const file = document.getElementById('file-foto').files[0];
  if (!file) return Swal.fire({icon:'warning',title:'Selecciona una foto',timer:2000,showConfirmButton:false});
  const data = new FormData();
  data.append('tipo','foto'); data.append('titulo', document.getElementById('foto-titulo').value);
  data.append('descripcion', document.getElementById('foto-desc').value);
  data.append('categoria', document.getElementById('foto-categoria').value);
  data.append('tags', document.getElementById('foto-tags').value);
  data.append('foto', file);
  subirEntrada(data);
}
function publicarAD() {
  if (!adFiles.antes || !adFiles.despues) return Swal.fire({icon:'warning',title:'Sube las dos fotos',timer:2000,showConfirmButton:false});
  const data = new FormData();
  data.append('tipo','antes_despues'); data.append('titulo', document.getElementById('ad-titulo').value);
  data.append('descripcion', document.getElementById('ad-desc').value);
  data.append('categoria', document.getElementById('ad-categoria').value);
  data.append('tags', document.getElementById('ad-tags').value);
  data.append('foto_antes', adFiles.antes); data.append('foto_despues', adFiles.despues);
  subirEntrada(data);
}
function publicarCarrusel() {
  if (carruselFiles.length < 2) return Swal.fire({icon:'warning',title:'Sube al menos 2 fotos',timer:2000,showConfirmButton:false});
  const data = new FormData();
  data.append('tipo','carrusel'); data.append('titulo', document.getElementById('car-titulo').value);
  data.append('descripcion', document.getElementById('car-desc').value);
  data.append('categoria', document.getElementById('car-categoria').value);
  data.append('tags', document.getElementById('car-tags').value);
  carruselFiles.forEach(f => data.append('fotos[]', f));
  subirEntrada(data);
}
function subirEntrada(data) {
  Swal.fire({title:'Subiendo...',allowOutsideClick:false,didOpen:()=>Swal.showLoading()});
  fetch('/admin/ajax/crear-entrada-galeria.php',{method:'POST',body:data})
    .then(r=>r.json())
    .then(res=>{
      if(res.success) Swal.fire({icon:'success',title:'Publicado',timer:1500,showConfirmButton:false}).then(()=>location.reload());
      else Swal.fire({icon:'error',title:'Error',text:res.error||'No se pudo publicar'});
    })
    .catch(()=>Swal.fire({icon:'error',title:'Error de conexión'}));
}

// ===== EDITAR =====
function abrirEditar(id) {
  const g = gruposData.find(x => x.id == id);
  if (!g) return;
  document.getElementById('edit-id').value = id;
  document.getElementById('edit-tipo').value = g.tipo;
  document.getElementById('edit-titulo').value = g.titulo_es || '';
  document.getElementById('edit-desc').value = g.descripcion_es || '';
  document.getElementById('edit-categoria').value = g.categoria;

  tags['edit'] = g.tags ? g.tags.split(',').map(t=>t.trim()).filter(Boolean) : [];
  document.getElementById('edit-tags').value = g.tags || '';
  renderTags('edit');

  // Ocultar todas las zonas
  ['edit-zona-foto','edit-zona-ad','edit-zona-carrusel'].forEach(z => document.getElementById(z).style.display = 'none');

  if (g.tipo === 'foto') {
    document.getElementById('edit-zona-foto').style.display = 'block';
    const actual = document.getElementById('edit-foto-actual');
    actual.innerHTML = g.fotos[0] ? `<div class="edit-foto-item"><img src="/uploads/${g.fotos[0].archivo}"></div>` : '';
    document.getElementById('edit-preview-foto').style.display = 'none';
    document.getElementById('edit-file-foto').value = '';

  } else if (g.tipo === 'antes_despues') {
    document.getElementById('edit-zona-ad').style.display = 'block';
    // Mostrar fotos actuales como fondo
    ['antes','despues'].forEach(rol => {
  const foto = g.fotos.find(f => f.rol === rol);
  const bg  = document.getElementById('edit-bg-' + rol);
  const btn = document.getElementById('edit-btn-' + rol);
  if (foto && bg) {
    bg.src = '/uploads/' + foto.archivo;
    bg.style.display = 'block';
    if (btn) btn.style.display = 'block'; // mostrar botón reemplazar
    document.getElementById('edit-label-' + rol).style.display = 'none';
    document.getElementById('edit-icon-' + rol).style.display = 'none';
    document.getElementById('edit-hint-' + rol).style.display = 'none';
  }
  document.getElementById('edit-file-' + rol).value = '';
});

  } else if (g.tipo === 'carrusel') {
    document.getElementById('edit-zona-carrusel').style.display = 'block';
    const grid = document.getElementById('edit-fotos-grid');
    grid.innerHTML = '';
    g.fotos.forEach(f => {
      const div = document.createElement('div');
      div.className = 'edit-foto-item';
      div.id = 'edit-foto-' + f.id;
      div.innerHTML = `<img src="/uploads/${f.archivo}"><button class="edit-foto-del" onclick="borrarFotoEdit(${f.id})">✕</button>`;
      grid.appendChild(div);
    });
    editNuevasFiles = [];
    document.getElementById('edit-nuevas-preview').innerHTML = '';
    document.getElementById('edit-nuevas-fotos').value = '';
  }

  document.getElementById('galEditOverlay').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function cerrarEditar() {
  document.getElementById('galEditOverlay').classList.remove('open');
  document.body.style.overflow = '';
}

function previewEditAD(input, rol) {
  if (!input.files[0]) return;
  const reader = new FileReader();
  reader.onload = e => {
    const bg = document.getElementById('edit-bg-' + rol);
    if (bg) { bg.src = e.target.result; bg.style.display = 'block'; }
    // Ocultar placeholder
    document.getElementById('edit-label-' + rol).style.display = 'none';
    document.getElementById('edit-icon-' + rol).style.display = 'none';
    document.getElementById('edit-hint-' + rol).style.display = 'none';
  };
  reader.readAsDataURL(input.files[0]);
}

function previewNuevas(input) {
  editNuevasFiles = Array.from(input.files);
  const preview = document.getElementById('edit-nuevas-preview');
  preview.innerHTML = '';
  editNuevasFiles.forEach((f, i) => {
    const url = URL.createObjectURL(f);
    const div = document.createElement('div');
    div.className = 'edit-foto-item';
    div.innerHTML = `<img src="${url}"><button class="edit-foto-del" onclick="quitarNueva(${i})">✕</button>`;
    preview.appendChild(div);
  });
}
function quitarNueva(i) {
  editNuevasFiles.splice(i, 1);
  const preview = document.getElementById('edit-nuevas-preview');
  preview.innerHTML = '';
  editNuevasFiles.forEach((f, j) => {
    const url = URL.createObjectURL(f);
    const div = document.createElement('div');
    div.className = 'edit-foto-item';
    div.innerHTML = `<img src="${url}"><button class="edit-foto-del" onclick="quitarNueva(${j})">✕</button>`;
    preview.appendChild(div);
  });
}

function borrarFotoEdit(fotoId) {
  fetch('ajax/borrar-foto-galeria.php', {
    method:'POST', body:new URLSearchParams({id:fotoId}),
    headers:{'Content-Type':'application/x-www-form-urlencoded'}
  })
  .then(r=>r.json())
  .then(res=>{
    if(res.success) document.getElementById('edit-foto-'+fotoId)?.remove();
    else Swal.fire({icon:'error',title:'Error',text:res.error});
  });
}

function guardarEdicion() {
  const id   = document.getElementById('edit-id').value;
  const tipo = document.getElementById('edit-tipo').value;
  const data = new FormData();
  data.append('id', id);
  data.append('titulo',      document.getElementById('edit-titulo').value);
  data.append('descripcion', document.getElementById('edit-desc').value);
  data.append('categoria',   document.getElementById('edit-categoria').value);
  data.append('tags',        document.getElementById('edit-tags').value);

  if (tipo === 'foto') {
    const file = document.getElementById('edit-file-foto').files[0];
    if (file) data.append('foto', file);
  } else if (tipo === 'antes_despues') {
    const antes   = document.getElementById('edit-file-antes').files[0];
    const despues = document.getElementById('edit-file-despues').files[0];
    if (antes)   data.append('antes',   antes);
    if (despues) data.append('despues', despues);
  } else if (tipo === 'carrusel') {
    editNuevasFiles.forEach(f => data.append('fotos[]', f));
  }

  Swal.fire({title:'Guardando...',allowOutsideClick:false,didOpen:()=>Swal.showLoading()});
  fetch('ajax/editar-entrada-galeria.php',{method:'POST',body:data})
    .then(r=>r.json())
    .then(res=>{
      if(res.success){
        cerrarEditar();
        Swal.fire({icon:'success',title:'Guardado',timer:1500,showConfirmButton:false}).then(()=>location.reload());
      } else {
        Swal.fire({icon:'error',title:'Error',text:res.error||'No se pudo guardar'});
      }
    })
    .catch(()=>Swal.fire({icon:'error',title:'Error de conexión'}));
}

function borrarEntrada(id) {
  Swal.fire({
    title:'¿Eliminar esta entrada?', text:'Se eliminarán todas las fotos.',
    icon:'warning', showCancelButton:true,
    confirmButtonText:'Eliminar', cancelButtonText:'Cancelar',
    confirmButtonColor:'#ef4444', cancelButtonColor:'#6b7280',
  }).then(r=>{
    if(!r.isConfirmed) return;
    fetch('ajax/borrar-entrada-galeria.php',{method:'POST',body:new URLSearchParams({id}),headers:{'Content-Type':'application/x-www-form-urlencoded'}})
    .then(r=>r.json())
    .then(res=>{
      if(res.success){ document.getElementById('gal-entry-'+id)?.remove(); Swal.fire({icon:'success',title:'Eliminado',timer:1200,showConfirmButton:false}); }
      else Swal.fire({icon:'error',title:'Error',text:res.error});
    });
  });
}

document.getElementById('galEditOverlay').addEventListener('click', e => {
  if(e.target === document.getElementById('galEditOverlay')) cerrarEditar();
});
</script>