<footer id="kontakt">
  <div class="footer-top">
    <div class="footer-brand">
      <div class="footer-logo-main">Oase</div>
      <div class="footer-logo-sub">Friseur &amp; Ästhetik</div>
      <p class="body-text" style="margin-top:1.5rem;font-size:12px;line-height:2;">
        Musterstrasse 12<br>8001 Zürich<br>+41 44 000 00 00
      </p>
    </div>
    <div>
      <div class="footer-col-title"><?= $t['footer_nav'] ?? 'Navigation' ?></div>
      <div class="footer-links">
        <a href="/servicios.php"><?= $t['nav_services'] ?? 'Leistungen' ?></a>
        <a href="/sobre-edwin.php"><?= $t['nav_about'] ?? 'Über uns' ?></a>
        <a href="/galeria.php"><?= $t['gallery_eyebrow'] ?? 'Galerie' ?></a>
        <a href="/contacto.php"><?= $t['nav_contact'] ?? 'Kontakt' ?></a>
        <a href="/booking.php"><?= $t['nav_book'] ?? 'Termin buchen' ?></a>
      </div>
    </div>
    <div>
      <div class="footer-col-title"><?= $t['footer_hours'] ?? 'Öffnungszeiten' ?></div>
      <div class="footer-links">
        <a href="#">Mo – Fr: 9:00 – 19:00</a>
        <a href="#">Sa: 9:00 – 17:00</a>
        <a href="#">So: <?= $t['closed'] ?? 'geschlossen' ?></a>
      </div>
    </div>
    <div>
      <div class="footer-col-title"><?= $t['footer_follow'] ?? 'Folge uns' ?></div>
      <div class="footer-social">
        <a href="#" class="soc-link"><i class="ti ti-brand-instagram"></i> Instagram</a>
        <a href="https://wa.me/41767133341" class="soc-link"><i class="ti ti-brand-whatsapp"></i> WhatsApp</a>
        <a href="#" class="soc-link"><i class="ti ti-map-pin"></i> Google Maps</a>
      </div>
      <div style="margin-top:1.5rem;">
        <a href="/mi-reserva.php" style="font-family:-apple-system,sans-serif;font-size:12px;color:#4a4035;text-decoration:none;display:flex;align-items:center;gap:6px;transition:color 0.2s;" onmouseover="this.style.color='#c9a84c'" onmouseout="this.style.color='#4a4035'">
          <i class="ti ti-calendar-search"></i>
          <?= $t['mi_reserva'] ?? 'Mi reserva' ?>
        </a>
        <a href="/politica-cancelacion.php" style="font-family:-apple-system,sans-serif;font-size:12px;color:#4a4035;text-decoration:none;display:flex;align-items:center;gap:6px;margin-top:8px;transition:color 0.2s;" onmouseover="this.style.color='#c9a84c'" onmouseout="this.style.color='#4a4035'">
          <i class="ti ti-info-circle"></i>
          <?= $t['politica_link'] ?? 'Política de cancelación' ?>
        </a>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    <span class="footer-copy">© <?= date('Y') ?> Oase Friseur &amp; Ästhetik · Zürich</span>
    <div class="lang-switch">
      <a href="?lang=de" class="<?= $lang==='de'?'active':'' ?>">DE</a>
      <span>|</span>
      <a href="?lang=es" class="<?= $lang==='es'?'active':'' ?>">ES</a>
      <span>|</span>
      <a href="?lang=fr" class="<?= $lang==='fr'?'active':'' ?>">FR</a>
      <span>|</span>
      <a href="?lang=en" class="<?= $lang==='en'?'active':'' ?>">EN</a>
    </div>
  </div>
</footer>

<script>
window.OASE_LANG = '<?= $lang ?>';
window.OASE_I18N = {
  back:          {de:'Zurück',       es:'Atrás',                          fr:'Retour',                    en:'Back'},
  confirm:       {de:'Termin bestätigen', es:'Confirmar reserva',         fr:'Confirmer le RDV',          en:'Confirm booking'},
  loading:       {de:'Laden...',     es:'Cargando...',                    fr:'Chargement...',             en:'Loading...'},
  no_slots:      {de:'Keine verfügbaren Zeiten', es:'No hay horarios disponibles', fr:'Aucun créneau disponible', en:'No available slots'},
  success:       {de:'Termin bestätigt! Prüfen Sie Ihre E-Mail.', es:'¡Reserva confirmada! Revisa tu email.', fr:'RDV confirmé! Vérifiez votre email.', en:'Booking confirmed! Check your email.'},
  pendiente_msg: {de:'Ihre Anfrage wird geprüft. Sie erhalten eine Bestätigung per E-Mail.', es:'Tu solicitud está siendo revisada. Recibirás confirmación por email.', fr:'Votre demande est en cours d\'examen. Vous recevrez une confirmation par email.', en:'Your request is being reviewed. You will receive confirmation by email.'},
  error:         {de:'Fehler. Bitte erneut versuchen.', es:'Error. Inténtalo de nuevo.', fr:'Erreur. Réessayez.', en:'Error. Please try again.'},
  error_conn:    {de:'Verbindungsfehler.', es:'Error de conexión.', fr:'Erreur de connexion.', en:'Connection error.'},
  fill:          {de:'Bitte alle Felder ausfüllen.', es:'Completa todos los campos.', fr:'Remplissez tous les champs.', en:'Please fill all fields.'},
  email_invalid: {de:'Ungültige E-Mail-Adresse.', es:'Email no válido.', fr:'Adresse email invalide.', en:'Invalid email address.'},
  email_mismatch:{de:'Die E-Mail-Adressen stimmen nicht überein.', es:'Los emails no coinciden.', fr:'Les emails ne correspondent pas.', en:'Emails do not match.'},
  slot_ocupado:  {de:'Diese Zeit wurde soeben vergeben. Bitte wählen Sie eine andere.', es:'Alguien ha reservado antes que tú. Elige otra hora.', fr:'Ce créneau vient d\'être pris. Choisissez un autre.', en:'Someone just booked this slot. Please choose another time.'},
  email_duplicado:{de:'Sie haben bereits einen Termin an diesem Tag.', es:'Ya tienes una cita este día. Usa tu código para modificarla.', fr:'Vous avez déjà un RDV ce jour. Utilisez votre code pour le modifier.', en:'You already have an appointment on this day. Use your code to modify it.'},
  aviso_wa:      {de:'Wichtig: Nach der Bestätigung öffnet sich WhatsApp. Bitte senden Sie die Nachricht, damit Ihr Termin besser verwaltet werden kann.', es:'Importante: Al confirmar se abrirá WhatsApp. Debes enviar el mensaje para que tu cita pueda ser gestionada correctamente.', fr:'Important: Après confirmation, WhatsApp s\'ouvrira. Envoyez le message pour valider votre RDV.', en:'Important: After confirming, WhatsApp will open. Please send the message so your appointment can be properly managed.'},
  aviso_limite:  {de:'Einige Zeiten überschreiten die Öffnungszeiten. Edwin prüft, ob dies möglich ist.', es:'Algunos horarios sobrepasan el cierre. Edwin revisará si puede atenderte.', fr:'Certains créneaux dépassent les horaires. Edwin vérifiera si c\'est possible.', en:'Some slots exceed closing time. Edwin will check if he can accommodate you.'},
  select_hora:   {de:'Bitte eine Uhrzeit wählen.', es:'Por favor selecciona una hora.', fr:'Veuillez choisir un horaire.', en:'Please select a time.'},
  codigo_label:  {de:'Ihr Buchungscode', es:'Tu código de reserva', fr:'Votre code de réservation', en:'Your booking code'},
  copiar:        {de:'Kopieren', es:'Copiar', fr:'Copier', en:'Copy'},
};
</script>
<script src="/assets/js/reservas.js"></script>
<script src="/assets/js/main.js"></script>
<?= $extra_js ?? '' ?>
</body>
</html>