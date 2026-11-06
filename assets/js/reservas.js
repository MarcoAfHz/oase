// ===== HELPER I18N =====
function i18n(key) {
  var lang = window.OASE_LANG || 'de';
  var dict = window.OASE_I18N || {};
  return dict[key] ? (dict[key][lang] || dict[key]['de'] || key) : key;
}

// ===== ESTADO =====
var currentStep = 1;
var horaSeleccionada = null;
var servicioSeleccionado = null;
var sobrepasaHorario = false;
var cierreHorario = '';

// ===== DOTS =====
function updateDots() {
  for (var i = 1; i <= 4; i++) {
    var dot = document.getElementById('dot' + i);
    if (dot) dot.classList.toggle('active', i <= currentStep);
  }
}

// ===== SHOW STEP =====
function showStep(step) {
  for (var i = 1; i <= 4; i++) {
    var el = document.getElementById('step' + i);
    if (el) el.style.display = i === step ? 'block' : 'none';
  }
  var btnBack    = document.getElementById('btnBack');
  var btnConfirm = document.getElementById('btnConfirm');
  var resumen    = document.getElementById('bookingResumen');
  var avisoWa    = document.getElementById('avisoWhatsApp');

  if (btnBack)    { btnBack.style.display = step > 1 ? 'inline-flex' : 'none'; btnBack.innerHTML = '&#8592; ' + i18n('back'); }
  if (btnConfirm) { btnConfirm.style.display = step === 4 ? 'block' : 'none'; btnConfirm.textContent = i18n('confirm'); }
  if (resumen)    resumen.style.display = step === 4 ? 'block' : 'none';
  if (avisoWa)    avisoWa.style.display = step === 4 ? 'block' : 'none';

  currentStep = step;
  updateDots();
}

// ===== NEXT STEP =====
function nextStep(fromStep) {
  if (fromStep === 1) {
    var sel = document.getElementById('selectServicio');
    if (!sel || !sel.value) return;
    servicioSeleccionado = {
      id:     sel.value,
      nombre: sel.options[sel.selectedIndex].text.split('—')[0].trim()
    };
    var fechaEl = document.getElementById('selectFecha');
    if (fechaEl && fechaEl.value) {
      cargarHoras();
    } else {
      showStep(2);
    }
  }
}

function prevStep() {
  if (currentStep > 1) showStep(currentStep - 1);
}

// ===== CARGAR HORAS =====
function cargarHoras() {
  var fecha  = document.getElementById('selectFecha').value;
  var servId = document.getElementById('selectServicio').value;
  var grid   = document.getElementById('horasGrid');
  var avisoLimite = document.getElementById('avisoLimite');
  if (!fecha || !servId || !grid) return;

  horaSeleccionada  = null;
  sobrepasaHorario  = false;
  grid.innerHTML = '<p style="font-family:-apple-system,sans-serif;font-size:13px;color:#5a5246;">' + i18n('loading') + '</p>';
  if (avisoLimite) avisoLimite.style.display = 'none';

  fetch('/api/horarios-disponibles.php?fecha=' + fecha + '&servicio_id=' + servId)
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (!data.horas || data.horas.length === 0) {
        grid.innerHTML = '<p style="font-family:-apple-system,sans-serif;font-size:13px;color:#c9a84c;">' + i18n('no_slots') + '</p>';
        return;
      }
      grid.innerHTML = '';
      data.horas.forEach(function(hora) {
        var btn = document.createElement('button');
        btn.className = 'hora-btn';
        btn.textContent = hora;
        btn.onclick = function() {
          document.querySelectorAll('.hora-btn').forEach(function(b) { b.classList.remove('active'); });
          btn.classList.add('active');
          horaSeleccionada = hora;
          setTimeout(function() { showStep(4); actualizarResumen(); }, 300);
        };
        grid.appendChild(btn);
      });

      // Slots que sobrepasan el cierre — Edwin decide
      if (data.horas_limite && data.horas_limite.length > 0) {
        // Separador
        if (data.horas.length > 0) {
          var sep = document.createElement('div');
          sep.style.cssText = 'grid-column:1/-1;padding:6px 0 2px;font-family:-apple-system,sans-serif;font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:#f97316;display:flex;align-items:center;gap:8px;';
          sep.innerHTML = '<i class="ti ti-alert-triangle"></i> ' + (i18n('aviso_limite') || 'Pendiente confirmación de Edwin');
          grid.appendChild(sep);
        }
        data.horas_limite.forEach(function(hora) {
          var btn = document.createElement('button');
          btn.className = 'hora-btn hora-btn-limite';
          btn.textContent = hora + ' ⚠️';
          btn.style.borderColor = '#f97316';
          btn.style.color = '#f97316';
          btn.onclick = function() {
            document.querySelectorAll('.hora-btn').forEach(function(b) { b.classList.remove('active'); });
            btn.classList.add('active');
            horaSeleccionada = hora;
            setTimeout(function() { showStep(4); actualizarResumen(); }, 300);
          };
          grid.appendChild(btn);
        });
        cierreHorario = data.cierre || '';
      }
      if (avisoLimite) avisoLimite.style.display = 'none';

      showStep(3);
    })
    .catch(function() {
      grid.innerHTML = '<p style="font-family:-apple-system,sans-serif;font-size:13px;color:#c44;">' + i18n('error_conn') + '</p>';
    });
}

// ===== RESUMEN =====
function actualizarResumen() {
  var fecha = document.getElementById('selectFecha') ? document.getElementById('selectFecha').value : '';
  var fecha_fmt = '—';
  if (fecha) {
    var d = new Date(fecha + 'T00:00:00');
    fecha_fmt = d.toLocaleDateString(window.OASE_LANG || 'de', {day:'2-digit',month:'2-digit',year:'numeric'});
  }
  var rS = document.getElementById('rServicio');
  var rF = document.getElementById('rFecha');
  var rH = document.getElementById('rHora');
  var rN = document.getElementById('rNombre');
  var inputNombre = document.getElementById('inputNombre');
  if (rS) rS.textContent = servicioSeleccionado ? servicioSeleccionado.nombre : '—';
  if (rF) rF.textContent = fecha_fmt;
  if (rH) rH.textContent = horaSeleccionada || '—';
  if (rN) rN.textContent = (inputNombre ? inputNombre.value : '') || '—';
}

// ===== CONFIRMAR =====
function confirmarReserva() {
  var nombre     = document.getElementById('inputNombre') ? document.getElementById('inputNombre').value.trim() : '';
  var email      = document.getElementById('inputEmail') ? document.getElementById('inputEmail').value.trim() : '';
  var emailConf  = document.getElementById('inputEmailConf') ? document.getElementById('inputEmailConf').value.trim() : '';
  var telefono   = document.getElementById('inputTelefono') ? document.getElementById('inputTelefono').value.trim() : '';
  var fecha      = document.getElementById('selectFecha') ? document.getElementById('selectFecha').value : '';
  var servId     = document.getElementById('selectServicio') ? document.getElementById('selectServicio').value : '';

  // Validaciones
  if (!nombre) { mostrarMsg(i18n('fill'), 'error'); return; }
  if (!email || !email.includes('@')) { mostrarMsg(i18n('email_invalid'), 'error'); return; }
  if (email !== emailConf) { mostrarMsg(i18n('email_mismatch'), 'error'); return; }
  if (!horaSeleccionada || !fecha || !servId) { mostrarMsg(i18n('fill'), 'error'); return; }

  var btnConfirm = document.getElementById('btnConfirm');
  if (btnConfirm) { btnConfirm.disabled = true; btnConfirm.textContent = '...'; }

  var data = new FormData();
  data.append('nombre',               nombre);
  data.append('email',                email);
  data.append('email_confirmacion',   emailConf);
  data.append('telefono',             telefono);
  data.append('servicio_id',          servId);
  data.append('fecha',                fecha);
  data.append('hora',                 horaSeleccionada + ':00');
  data.append('lang',                 window.OASE_LANG || 'de');

  fetch('/api/reservar.php', { method: 'POST', body: data })
    .then(function(r) { return r.json(); })
    .then(function(res) {
      if (res.success) {
        // Mostrar código de reserva
        var codigoEl = document.getElementById('codigoReserva');
        if (codigoEl) {
          codigoEl.textContent = res.codigo;
          codigoEl.parentElement.style.display = 'block';
        }

        if (res.sobrepasa) {
          mostrarMsg(i18n('pendiente_msg'), 'warning');
        } else {
          mostrarMsg(i18n('success'), 'success');
        }

        if (btnConfirm) btnConfirm.style.display = 'none';
        var btnBack = document.getElementById('btnBack');
        if (btnBack) btnBack.style.display = 'none';
        var avisoWa = document.getElementById('avisoWhatsApp');
        if (avisoWa) avisoWa.style.display = 'none';

        // Abrir WhatsApp
        if (res.whatsapp_url) {
          setTimeout(function() { window.open(res.whatsapp_url, '_blank'); }, 800);
        }
      } else {
        // Manejar errores específicos
        var errMsg = i18n('error');
        if (res.error === 'slot_ocupado')     errMsg = i18n('slot_ocupado');
        if (res.error === 'email_duplicado')  errMsg = i18n('email_duplicado') + (res.codigo ? ' (' + res.codigo + ')' : '');
        if (res.error === 'email_no_coincide') errMsg = i18n('email_mismatch');
        if (res.error === 'email_invalido')    errMsg = i18n('email_invalid');
        mostrarMsg(errMsg, 'error');
        if (btnConfirm) { btnConfirm.disabled = false; btnConfirm.textContent = i18n('confirm'); }
      }
    })
    .catch(function() {
      mostrarMsg(i18n('error_conn'), 'error');
      if (btnConfirm) { btnConfirm.disabled = false; btnConfirm.textContent = i18n('confirm'); }
    });
}

// ===== MENSAJE =====
function mostrarMsg(msg, tipo) {
  var el = document.getElementById('bookingMsg');
  if (!el) return;
  el.style.display = 'block';
  el.textContent   = msg;
  if (tipo === 'success') { el.style.background='rgba(34,197,94,0.08)'; el.style.border='0.5px solid #22c55e'; el.style.color='#22c55e'; }
  else if (tipo === 'warning') { el.style.background='rgba(249,115,22,0.08)'; el.style.border='0.5px solid #f97316'; el.style.color='#f97316'; }
  else { el.style.background='rgba(180,60,60,0.1)'; el.style.border='0.5px solid #c44'; el.style.color='#c44'; }
}

// ===== COPIAR CÓDIGO =====
function copiarCodigoReserva() {
  var codigo = document.getElementById('codigoReserva');
  if (!codigo) return;
  navigator.clipboard.writeText(codigo.textContent).then(function() {
    var btn = document.getElementById('btnCopiarCodigo');
    if (btn) {
      var prev = btn.innerHTML;
      btn.innerHTML = '<i class="ti ti-check"></i>';
      btn.style.color = '#22c55e';
      setTimeout(function() { btn.innerHTML = prev; btn.style.color = ''; }, 2000);
    }
  });
}

// ===== INIT =====
document.addEventListener('DOMContentLoaded', function() {
  var fechaInput = document.getElementById('selectFecha');
  if (fechaInput) fechaInput.addEventListener('change', cargarHoras);
  var nombreInput = document.getElementById('inputNombre');
  if (nombreInput) nombreInput.addEventListener('input', actualizarResumen);

  // Texto aviso WhatsApp
  var avisoWaEl = document.getElementById('textoAvisoWa');
  if (avisoWaEl) avisoWaEl.textContent = i18n('aviso_wa');

  // Label código
  var codigoLabel = document.getElementById('codigoLabel');
  if (codigoLabel) codigoLabel.textContent = i18n('codigo_label');

  // Link mi reserva
  var linkMiReserva = document.getElementById('linkMiReserva');
  if (linkMiReserva) {
    var textos = {de:'Buchung ansehen & verwalten', es:'Ver y gestionar mi reserva', fr:'Voir et gérer mon RDV', en:'View & manage my booking'};
    linkMiReserva.textContent = textos[window.OASE_LANG || 'de'] || textos['de'];
  }
});