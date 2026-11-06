document.addEventListener('DOMContentLoaded', function () {

  // ===== NAV SCROLL =====
  const navbar = document.getElementById('navbar');
  let lastScroll = 0;
  window.addEventListener('scroll', () => {
    const scrollY = window.scrollY;
    navbar.classList.toggle('scrolled', scrollY > 80);
    lastScroll = scrollY;
  }, { passive: true });

  // ===== RADIAL MENU =====
  const overlay  = document.getElementById('radialOverlay');
  const burger   = document.getElementById('navBurger');
  const closeBtn = document.getElementById('radialClose');
  const items    = document.querySelectorAll('.radial-item');

  function positionItems() {
    const cx = window.innerWidth * 0.88;
    const cy = window.innerHeight / 2;
    const radius = window.innerHeight * 0.28;
    const startDeg = 240;
    const endDeg   = 120;
    const step     = (endDeg - startDeg) / (items.length - 1);
    items.forEach((item, i) => {
      const angle = (startDeg + step * i) * (Math.PI / 180);
      const x = cx + radius * Math.cos(angle);
      const y = cy + radius * Math.sin(angle);
      item.style.left = x + 'px';
      item.style.top  = y + 'px';
      item.style.opacity = '0';
      item.style.transform = 'translate(-50%, -50%) translateX(40px)';
    });
  }

  function animateItems() {
    items.forEach((item, i) => {
      setTimeout(() => {
        item.style.transition = 'opacity 0.45s ease, transform 0.45s ease';
        item.style.opacity    = '1';
        item.style.transform  = 'translate(-50%, -50%) translateX(0)';
      }, i * 90);
    });
  }

  function resetItems() {
    items.forEach(item => {
      item.style.transition = 'none';
      item.style.opacity    = '0';
      item.style.transform  = 'translate(-50%, -50%) translateX(40px)';
    });
  }

  function openMenu() {
    positionItems();
    overlay.classList.add('open');
    document.body.style.overflow = 'hidden';
    setTimeout(animateItems, 50);
    const spans = burger.querySelectorAll('span');
    spans[0].style.transform = 'translateY(6px) rotate(45deg)';
    spans[1].style.opacity   = '0';
    spans[2].style.transform = 'translateY(-6px) rotate(-45deg)';
  }

  function closeMenu() {
    resetItems();
    setTimeout(() => overlay.classList.remove('open'), 100);
    document.body.style.overflow = '';
    const spans = burger.querySelectorAll('span');
    spans[0].style.transform = '';
    spans[1].style.opacity   = '';
    spans[2].style.transform = '';
  }

  if (burger)   burger.addEventListener('click', () => overlay.classList.contains('open') ? closeMenu() : openMenu());
  if (closeBtn) closeBtn.addEventListener('click', closeMenu);
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeMenu(); });
  window.addEventListener('resize', () => { if (overlay && overlay.classList.contains('open')) positionItems(); });

  // ===== SCROLL REVEAL =====
  const revealEls = document.querySelectorAll('.reveal-up, .reveal-left, .reveal-right');
  const revealObs = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) { e.target.classList.add('revealed'); revealObs.unobserve(e.target); }
    });
  }, { threshold: 0.12 });
  revealEls.forEach(el => revealObs.observe(el));

  // ===== COUNT UP =====
  function animateCount(el, target, duration = 1800) {
    let start = 0;
    const step = target / (duration / 16);
    const timer = setInterval(() => {
      start += step;
      if (start >= target) { start = target; clearInterval(timer); }
      el.textContent = Math.floor(start).toLocaleString();
    }, 16);
  }
  const countEls = document.querySelectorAll('[data-count]');
  countEls.forEach(el => {
    new IntersectionObserver((entries) => {
      entries.forEach(e => { if (e.isIntersecting) { animateCount(e.target, parseInt(e.target.dataset.count)); } });
    }, { threshold: 0.5 }).observe(el);
  });

  // ===== SLIDER SERVICIOS =====
  const track    = document.getElementById('sliderTrack');
  const dotsWrap = document.getElementById('sliderDots');
  const prevBtn  = document.getElementById('sliderPrev');
  const nextBtn  = document.getElementById('sliderNext');

  if (track) {
    const slides = track.querySelectorAll('.slide');
    const total  = slides.length;
    let current  = 0;
    let autoplayTimer;

    function getSlidesVisible() {
      if (window.innerWidth <= 768) return 1;
      if (window.innerWidth <= 1024) return 2;
      return 3;
    }
    function getMaxIndex() { return Math.max(0, total - getSlidesVisible()); }

    function buildDots() {
      dotsWrap.innerHTML = '';
      for (let i = 0; i <= getMaxIndex(); i++) {
        const d = document.createElement('div');
        d.className = 'slider-dot' + (i === current ? ' active' : '');
        d.addEventListener('click', () => goTo(i));
        dotsWrap.appendChild(d);
      }
    }
    function updateDots() {
      dotsWrap.querySelectorAll('.slider-dot').forEach((d, i) => d.classList.toggle('active', i === current));
    }
    function getSlideWidth() { return slides[0] ? slides[0].getBoundingClientRect().width + 3 : 0; }
    function goTo(index) {
      current = Math.max(0, Math.min(index, getMaxIndex()));
      track.style.transform = `translateX(-${current * getSlideWidth()}px)`;
      updateDots();
    }
    function next() { goTo(current >= getMaxIndex() ? 0 : current + 1); }
    function prev() { goTo(current <= 0 ? getMaxIndex() : current - 1); }
    function startAutoplay() { autoplayTimer = setInterval(next, 4500); }
    function stopAutoplay()  { clearInterval(autoplayTimer); }

    prevBtn.addEventListener('click', () => { stopAutoplay(); prev(); startAutoplay(); });
    nextBtn.addEventListener('click', () => { stopAutoplay(); next(); startAutoplay(); });

    let startX = 0;
    track.addEventListener('touchstart', e => { startX = e.touches[0].clientX; stopAutoplay(); }, { passive: true });
    track.addEventListener('touchend',   e => {
      const diff = startX - e.changedTouches[0].clientX;
      if (Math.abs(diff) > 50) diff > 0 ? next() : prev();
      startAutoplay();
    });
    window.addEventListener('resize', () => { if (current > getMaxIndex()) current = getMaxIndex(); buildDots(); goTo(current); });

    buildDots(); goTo(0); startAutoplay();
  }

  // ===== SLIDER GALERÍA =====
  const gTrack   = document.getElementById('galeriaTrack');
  const gDots    = document.getElementById('galeriaDots');
  const gPrev    = document.getElementById('galeriaPrev');
  const gNext    = document.getElementById('galeriaNext');

  if (gTrack) {
    const gSlides = gTrack.querySelectorAll('.galeria-slide');
    const gTotal  = gSlides.length;
    let gCurrent  = 0;
    let gTimer;

    function getGVisible() {
      if (window.innerWidth <= 768)  return 1;
      if (window.innerWidth <= 1024) return 2;
      return 3;
    }
    function getGMax() { return Math.max(0, gTotal - getGVisible()); }
    function getGSlideW() { 
      if (!gSlides[0]) return 0;
      const rect = gSlides[0].getBoundingClientRect();
      const style = window.getComputedStyle(gTrack);
      const gap = parseFloat(style.gap) || 3;
      return rect.width + gap;
    }

    function buildGDots() {
      gDots.innerHTML = '';
      for (let i = 0; i <= getGMax(); i++) {
        const d = document.createElement('div');
        d.className = 'slider-dot' + (i === gCurrent ? ' active' : '');
        d.addEventListener('click', () => goToG(i));
        gDots.appendChild(d);
      }
    }
    function updateGDots() {
      gDots.querySelectorAll('.slider-dot').forEach((d, i) => d.classList.toggle('active', i === gCurrent));
    }
    function goToG(idx) {
      gCurrent = Math.max(0, Math.min(idx, getGMax()));
      const slideW = gSlides[0] ? gSlides[0].getBoundingClientRect().width + 3 : 0;
      gTrack.style.transform = `translateX(-${gCurrent * slideW}px)`;
      updateGDots();
    }
    function gNext2() { goToG(gCurrent >= getGMax() ? 0 : gCurrent + 1); }
    function gPrev2() { goToG(gCurrent <= 0 ? getGMax() : gCurrent - 1); }
    function startGAuto() { gTimer = setInterval(gNext2, 4000); }
    function stopGAuto()  { clearInterval(gTimer); }

    if (gPrev) gPrev.addEventListener('click', () => { stopGAuto(); gPrev2(); startGAuto(); });
    if (gNext) gNext.addEventListener('click', () => { stopGAuto(); gNext2(); startGAuto(); });

    let gStartX = 0;
    gTrack.addEventListener('touchstart', e => { gStartX = e.touches[0].clientX; stopGAuto(); }, { passive: true });
    gTrack.addEventListener('touchend',   e => {
      const diff = gStartX - e.changedTouches[0].clientX;
      if (Math.abs(diff) > 50) diff > 0 ? gNext2() : gPrev2();
      startGAuto();
    });
    window.addEventListener('resize', () => { if (gCurrent > getGMax()) gCurrent = getGMax(); buildGDots(); goToG(gCurrent); });

    setTimeout(function() {
      buildGDots(); goToG(0); startGAuto();
    }, 500);
  }

  // ===== HELPERS =====
  window.selectType   = el => { document.querySelectorAll('.type-option').forEach(o => o.classList.remove('active')); el.classList.add('active'); };
  window.selectDay    = el => { document.querySelectorAll('.day-btn').forEach(b => b.classList.remove('active')); el.classList.add('active'); };
  window.smoothScroll = id => { const el = document.getElementById(id); if (el) el.scrollIntoView({ behavior: 'smooth' }); };
  window.closeMobile  = () => {};

});