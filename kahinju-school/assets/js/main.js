(function () {
  // Mobile menu
  var toggle = document.querySelector('.nav-toggle');
  var nav = document.querySelector('.main-nav');
  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      var open = nav.classList.toggle('open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }

  // Gallery filter
  var filters = document.querySelectorAll('.filter');
  var items = document.querySelectorAll('.g-item');
  filters.forEach(function (btn) {
    btn.addEventListener('click', function () {
      filters.forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');
      var f = btn.getAttribute('data-filter');
      items.forEach(function (it) {
        it.classList.toggle('hide', f !== 'all' && it.getAttribute('data-cat') !== f);
      });
    });
  });

  // Lightbox for photos and videos
  var box = document.getElementById('lightbox');
  if (box) {
    var img = box.querySelector('img');
    var vid = box.querySelector('video');
    var close = function () {
      box.hidden = true;
      img.removeAttribute('src');
      vid.pause();
      vid.removeAttribute('src');
    };
    document.querySelectorAll('.g-link').forEach(function (a) {
      a.addEventListener('click', function (e) {
        e.preventDefault();
        var url = a.getAttribute('href');
        if (a.getAttribute('data-type') === 'video') {
          img.style.display = 'none';
          vid.style.display = 'block';
          vid.src = url;
          vid.play();
        } else {
          vid.style.display = 'none';
          img.style.display = 'block';
          img.src = url;
        }
        box.hidden = false;
      });
    });
    box.addEventListener('click', function (e) { if (e.target === box || e.target.classList.contains('lb-close')) { close(); } });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { close(); } });
  }

  // Hero slider
  var hero = document.getElementById('hero');
  if (hero) {
    var slides = hero.querySelectorAll('.slide');
    var dotsBox = hero.querySelector('.hero-dots');
    var copies = hero.querySelectorAll('.hero-copy');
    if (slides.length > 1 && dotsBox) {
      var cur = 0, timer = null, dots = [];
      var show = function (n) {
        cur = (n + slides.length) % slides.length;
        slides.forEach(function (s, i) { s.classList.toggle('active', i === cur); });
        dots.forEach(function (d, i) { d.classList.toggle('active', i === cur); });
        copies.forEach(function (c, i) { c.classList.toggle('active', i === cur); });
      };
      slides.forEach(function (s, i) {
        var b = document.createElement('button');
        b.setAttribute('type', 'button');
        b.setAttribute('aria-label', 'Go to slide ' + (i + 1));
        b.addEventListener('click', function () { show(i); restart(); });
        dotsBox.appendChild(b);
        dots.push(b);
      });
      var play = function () {
        if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) { return; }
        timer = setInterval(function () { show(cur + 1); }, 6000);
      };
      var restart = function () { clearInterval(timer); if (!hero.matches(':hover')) { play(); } };
      var prev = hero.querySelector('.hero-arrow.prev');
      var next = hero.querySelector('.hero-arrow.next');
      if (prev) { prev.addEventListener('click', function () { show(cur - 1); restart(); }); }
      if (next) { next.addEventListener('click', function () { show(cur + 1); restart(); }); }
      hero.addEventListener('mouseenter', function () { clearInterval(timer); });
      hero.addEventListener('mouseleave', play);
      var x0 = null;
      hero.addEventListener('touchstart', function (e) { x0 = e.touches[0].clientX; }, { passive: true });
      hero.addEventListener('touchend', function (e) {
        if (x0 === null) { return; }
        var dx = e.changedTouches[0].clientX - x0;
        if (Math.abs(dx) > 50) { show(cur + (dx < 0 ? 1 : -1)); restart(); }
        x0 = null;
      });
      show(0);
      play();
    }
  }

  // Count-up numbers
  var counts = document.querySelectorAll('.count[data-target]');
  if (counts.length && 'IntersectionObserver' in window) {
    var run = function (el) {
      var target = parseInt(el.getAttribute('data-target'), 10) || 0;
      var t0 = null, dur = 1800;
      var step = function (t) {
        if (t0 === null) { t0 = t; }
        var k = Math.min((t - t0) / dur, 1);
        var e = 1 - Math.pow(1 - k, 3);
        el.textContent = Math.round(target * e).toLocaleString('en-US');
        if (k < 1) { requestAnimationFrame(step); }
      };
      requestAnimationFrame(step);
    };
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (en.isIntersecting) { run(en.target); io.unobserve(en.target); }
      });
    }, { threshold: 0.4 });
    counts.forEach(function (c) { c.textContent = '0'; io.observe(c); });
  }
})();
