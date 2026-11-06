<?php
$page_title = 'Edwin — Oase Friseur & Ästhetik';
require_once __DIR__ . '/config.php';
include 'includes/header.php';

$bio = [
    'de' => 'Mit über 20 Jahren Erfahrung hat sich Edwin als Spezialist für krauses, lockiges und Afro-Haar einen Namen gemacht. Er ist zertifizierter Kolorist der L\'Oréal Akademie in Paris und hat in Panama, Chile und Spanien gearbeitet — eine Erfahrung, die ihm ermöglicht, mit jedem Haartyp umzugehen: europäisches, amerikanisches und Afro-Haar. Heute bringt er sein Wissen und seine Leidenschaft nach Zürich.',
    'es' => 'Con más de 20 años de experiencia, Edwin se ha especializado en cabello crespo, rizado y afro. Es colorimetrista graduado de la Academia L\'Oréal en París y ha trabajado en Panamá, Chile y España, lo que le permite trabajar con cualquier tipo de cabello: europeo, americano y afro. Hoy trae su conocimiento y pasión a Zúrich.',
    'fr' => 'Avec plus de 20 ans d\'expérience, Edwin s\'est spécialisé dans les cheveux crépus, bouclés et afro. Coloriste diplômé de l\'Académie L\'Oréal à Paris, il a travaillé au Panama, au Chili et en Espagne, ce qui lui permet de travailler avec tous les types de cheveux : européens, américains et afro. Aujourd\'hui, il apporte son savoir-faire et sa passion à Zurich.',
    'en' => 'With over 20 years of experience, Edwin has specialised in curly, wavy and afro hair. He is a certified colourist from the L\'Oréal Academy in Paris and has worked in Panama, Chile and Spain, giving him the ability to work with any hair type: European, American and afro. Today he brings his knowledge and passion to Zurich.',
];
$bio_text = $bio[$lang] ?? $bio['de'];
?>

<!-- HERO EDWIN -->
<section class="edwin-page-hero">
  <div class="edwin-page-hero-inner">
    <div class="edwin-page-img reveal-left">
      <div class="edwin-img-wrap">
        <img src="assets/img/perfilEdwin.png" alt="Edwin">
        <div class="edwin-img-badge">
          <span class="badge-num" data-count="20">0</span>
          <span class="badge-label"><?= $t['stat_years'] ?? 'Jahre' ?></span>
        </div>
      </div>
    </div>
    <div class="edwin-page-text reveal-right">
      <div class="section-eyebrow"><span class="eyebrow-line"></span><?= $t['edwin_eyebrow'] ?? 'Der Meister' ?></div>
      <h1 class="section-h2">Edwin<br><em><?= $t['edwin_title'] ?? 'Friseurmeister & Ästhetiker' ?></em></h1>
      <div class="gold-line"></div>
      <p class="body-text"><?= $bio_text ?></p>

      <!-- Credenciales -->
      <div class="edwin-credenciales">
        <div class="credencial-item">
          <i class="ti ti-certificate"></i>
          <span><?= ['de'=>'Zertifizierter Kolorist — L\'Oréal Akademie Paris','es'=>'Colorimetrista graduado — Academia L\'Oréal París','fr'=>'Coloriste certifié — Académie L\'Oréal Paris','en'=>'Certified Colourist — L\'Oréal Academy Paris'][$lang] ?? 'Colorimetrista — L\'Oréal París' ?></span>
        </div>
        <div class="credencial-item">
          <i class="ti ti-world"></i>
          <span><?= ['de'=>'International tätig: Panama · Chile · Spanien','es'=>'Experiencia internacional: Panamá · Chile · España','fr'=>'Expérience internationale : Panama · Chili · Espagne','en'=>'International experience: Panama · Chile · Spain'][$lang] ?? 'Panamá · Chile · España' ?></span>
        </div>
        <div class="credencial-item">
          <i class="ti ti-clock"></i>
          <span><?= ['de'=>'Über 20 Jahre Berufserfahrung','es'=>'Más de 20 años de experiencia','fr'=>'Plus de 20 ans d\'expérience','en'=>'Over 20 years of experience'][$lang] ?? '+20 años' ?></span>
        </div>
      </div>

      <a href="/booking.php" class="btn-primary" style="display:inline-block; text-decoration:none; margin-top:1.5rem;">
        <?= $t['btn_book'] ?? 'Termin buchen' ?>
      </a>
    </div>
  </div>
</section>

<!-- ESPECIALIDADES -->
<section class="edwin-especialidades">
  <div class="edwin-esp-inner">
    <div class="section-eyebrow" style="justify-content:center; margin-bottom:1rem;">
      <span class="eyebrow-line"></span>
      <?= $t['specialties_eyebrow'] ?? 'Spezialisierungen' ?>
    </div>
    <h2 class="section-h2" style="text-align:center; margin-bottom:3rem;">
      <?= $t['specialties_title'] ?? 'Was Edwin' ?> <em><?= $t['specialties_title2'] ?? 'am besten kann' ?></em>
    </h2>
    <div class="esp-grid">
      <div class="esp-card reveal-up" style="--delay:0s">
        <div class="esp-icon"><i class="ti ti-wave-sine"></i></div>
        <h3 class="esp-title"><?= ['de'=>'Krauses & Afro-Haar','es'=>'Cabello crespo & afro','fr'=>'Cheveux crépus & afro','en'=>'Curly & afro hair'][$lang] ?? 'Cabello crespo & afro' ?></h3>
        <p class="esp-desc"><?= ['de'=>'Spezialist für krauses, lockiges und voluminöses Haar jeder Textur','es'=>'Especialista en cabello crespo, rizado y voluminoso de cualquier textura','fr'=>'Spécialiste des cheveux crépus, bouclés et volumineux de toutes textures','en'=>'Specialist in curly, wavy and voluminous hair of any texture'][$lang] ?? '' ?></p>
        <div class="esp-tags">
          <span>Rizos</span><span>Afro</span><span>Crespo</span><span>Volumen</span>
        </div>
      </div>
      <div class="esp-card reveal-up" style="--delay:0.1s">
        <div class="esp-icon"><i class="ti ti-color-swatch"></i></div>
        <h3 class="esp-title"><?= $t['skill_1'] ?? 'Farbspezialist' ?></h3>
        <p class="esp-desc"><?= ['de'=>'Zertifizierter Kolorist — Balayage, Highlights & Farbkorrektur','es'=>'Colorimetrista certificado — Balayage, mechas & corrección de color','fr'=>'Coloriste certifié — Balayage, mèches & correction de couleur','en'=>'Certified colourist — Balayage, highlights & colour correction'][$lang] ?? '' ?></p>
        <div class="esp-tags">
          <span>Balayage</span><span>Highlights</span><span>Toner</span><span>L'Oréal</span>
        </div>
      </div>
      <div class="esp-card reveal-up" style="--delay:0.2s">
        <div class="esp-icon"><i class="ti ti-cut"></i></div>
        <h3 class="esp-title"><?= $t['skill_3'] ?? 'Präzisionsschnitt' ?></h3>
        <p class="esp-desc"><?= $t['skill_3_desc'] ?? 'Klassische & moderne Schnitttechniken' ?></p>
        <div class="esp-tags">
          <span>Capas</span><span>Bob</span><span>Degradado</span><span>Puntas</span>
        </div>
      </div>
      <div class="esp-card reveal-up" style="--delay:0.3s">
        <div class="esp-icon"><i class="ti ti-heart"></i></div>
        <h3 class="esp-title"><?= ['de'=>'Brautfrisuren & Extensions','es'=>'Peinados de novia & extensiones','fr'=>'Coiffures de mariée & extensions','en'=>'Bridal hairstyles & extensions'][$lang] ?? 'Peinados de novia & extensiones' ?></h3>
        <p class="esp-desc"><?= ['de'=>'Hochzeitsfrisuren, Hochsteckfrisuren und Haarverkauf','es'=>'Peinados de novia, moños, recogidos y venta de extensiones de pelo','fr'=>'Coiffures de mariée, chignons et vente d\'extensions capillaires','en'=>'Bridal hairstyles, updos and hair extensions for sale'][$lang] ?? '' ?></p>
        <div class="esp-tags">
          <span>Novia</span><span>Moño</span><span>Recogido</span><span>Extensiones</span>
        </div>
      </div>
      <div class="esp-card reveal-up" style="--delay:0.4s">
        <div class="esp-icon"><i class="ti ti-sparkles"></i></div>
        <h3 class="esp-title"><?= $t['skill_4'] ?? 'Ästhetik & Pflege' ?></h3>
        <p class="esp-desc"><?= $t['skill_4_desc'] ?? 'Gesichtsbehandlungen & Haarpflege' ?></p>
        <div class="esp-tags">
          <span>Olaplex</span><span>K18</span><span>Keratina</span><span>Hidratación</span>
        </div>
      </div>
      <div class="esp-card reveal-up" style="--delay:0.5s">
        <div class="esp-icon"><i class="ti ti-world"></i></div>
        <h3 class="esp-title"><?= ['de'=>'Alle Haartypen','es'=>'Todos los tipos de cabello','fr'=>'Tous les types de cheveux','en'=>'All hair types'][$lang] ?? 'Todos los tipos de cabello' ?></h3>
        <p class="esp-desc"><?= ['de'=>'Dank internationaler Erfahrung in Europa und Amerika mit allen Haartypen vertraut','es'=>'Gracias a su experiencia internacional en Europa y América, trabaja con todo tipo de cabello','fr'=>'Grâce à son expérience internationale en Europe et en Amérique, il maîtrise tous les types de cheveux','en'=>'Thanks to his international experience in Europe and America, he works with all hair types'][$lang] ?? '' ?></p>
        <div class="esp-tags">
          <span>Europeo</span><span>Americano</span><span>Afro</span><span>Internacional</span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- FILOSOFÍA -->
<section class="edwin-filosofia">
  <div class="edwin-filosofia-inner reveal-up">
    <div class="filosofia-quote">
      <div class="filosofia-icon"><i class="ti ti-quote"></i></div>
      <blockquote><?= $t['edwin_quote'] ?? '"Tu cabello, nuestra pasión — Ihr Haar, unsere Leidenschaft"' ?></blockquote>
      <cite>— Edwin, <?= $t['edwin_title'] ?? 'Friseurmeister' ?></cite>
    </div>
  </div>
</section>

<!-- CTA -->
<div class="servicios-cta reveal-up">
  <h2 class="section-h2"><?= $t['book_title_1'] ?? 'Reserviere deinen' ?> <em><?= $t['book_title_2'] ?? 'Termin' ?></em></h2>
  <p class="body-text"><?= $t['book_text'] ?? 'Schnell und einfach online buchen.' ?></p>
  <a href="/booking.php" class="btn-primary" style="display:inline-block; text-decoration:none; margin-top:1.5rem;">
    <?= $t['btn_book'] ?? 'Termin buchen' ?>
  </a>
</div>

<style>
.edwin-credenciales { margin-top: 1.5rem; display: flex; flex-direction: column; gap: 0.75rem; }
.credencial-item { display: flex; align-items: center; gap: 10px; font-family: -apple-system, sans-serif; font-size: 13px; color: #8a8070; }
.credencial-item i { color: #c9a84c; font-size: 16px; flex-shrink: 0; }
</style>

<?php include 'includes/footer.php'; ?>