(function(){
  "use strict";
  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------- reveal on scroll ---------- */
  var revealEls = document.querySelectorAll('.reveal:not(.is-visible)');
  if('IntersectionObserver' in window){
    var io = new IntersectionObserver(function(entries){
      entries.forEach(function(entry){
        if(entry.isIntersecting){
          entry.target.classList.add('is-visible');
          io.unobserve(entry.target);
        }
      });
    }, {threshold:0.15});
    revealEls.forEach(function(el){ io.observe(el); });
  } else {
    revealEls.forEach(function(el){ el.classList.add('is-visible'); });
  }

  /* ---------- foreground parallax ---------- */
  var parallaxEls = Array.prototype.slice.call(document.querySelectorAll('[data-parallax]'));
  function onScrollParallax(){
    var y = window.scrollY;
    parallaxEls.forEach(function(el){
      var speed = parseFloat(el.getAttribute('data-parallax')) || 0.05;
      el.style.transform = 'translateY(' + (y*speed*-1) + 'px)';
    });
  }
  if(!reduceMotion && parallaxEls.length){
    window.addEventListener('scroll', function(){ requestAnimationFrame(onScrollParallax); }, {passive:true});
  }

  /* ---------- sticky process tracker ---------- */
  var stage = document.querySelector('.process-stage');
  var shell = document.querySelector('.process-scroller');
  var railItems = document.querySelectorAll('.rail-item');
  var stepCards = document.querySelectorAll('.step-card');
  var railFill = document.getElementById('railFill');
  var STEP_COUNT = stepCards.length;

  function updateProcess(){
    if(!shell) return;
    var rect = shell.getBoundingClientRect();
    var total = rect.height - window.innerHeight;
    if(total <= 0) return;
    var progress = Math.min(1, Math.max(0, -rect.top / total));
    var idx = Math.min(STEP_COUNT-1, Math.floor(progress * STEP_COUNT));
    railItems.forEach(function(el){ el.classList.toggle('active', Number(el.dataset.step) === idx); });
    stepCards.forEach(function(el){ el.classList.toggle('active', Number(el.dataset.card) === idx); });
    railFill.style.height = (((idx+1)/STEP_COUNT)*100) + '%';
  }
  window.addEventListener('scroll', function(){ requestAnimationFrame(updateProcess); }, {passive:true});
  updateProcess();

  /* ---------- mobile nav ---------- */
  var navToggle = document.getElementById('navToggle');
  var mobilePanel = document.getElementById('mobilePanel');
  if(navToggle && mobilePanel){
    navToggle.addEventListener('click', function(){
      var open = mobilePanel.classList.toggle('open');
      navToggle.setAttribute('aria-expanded', open ? 'true':'false');
    });
    mobilePanel.querySelectorAll('a').forEach(function(a){
      a.addEventListener('click', function(){ mobilePanel.classList.remove('open'); });
    });
  }

  /* ---------- newsletter (visual only) ---------- */
  var newsForm = document.getElementById('newsForm');
  var newsStatus = document.getElementById('newsStatus');
  if(newsForm && newsStatus){
    newsForm.addEventListener('submit', function(e){
      e.preventDefault();
      var email = document.getElementById('newsEmail').value;
      newsStatus.textContent = 'Feliratkozva: ' + email + ' — köszönjük!';
      newsForm.querySelector('input').value = '';
    });
  }

  /* ---------- cookie bar ---------- */
  var cookieBar = document.getElementById('cookieBar');
  var COOKIE_KEY = 'soulsilver_cookie_choice';
  function getChoice(){
    try{ return localStorage.getItem(COOKIE_KEY); }catch(e){ return null; }
  }
  function setChoice(v){
    try{ localStorage.setItem(COOKIE_KEY, v); }catch(e){}
  }
  if(cookieBar){
    var hideBar = function(v){ setChoice(v); cookieBar.classList.remove('show'); document.body.classList.remove('has-cookiebar'); };
    if(!getChoice()){
      setTimeout(function(){ cookieBar.classList.add('show'); document.body.classList.add('has-cookiebar'); }, 900);
    }
    var acc = document.getElementById('cookieAccept');
    var rej = document.getElementById('cookieReject');
    if(acc) acc.addEventListener('click', function(){ hideBar('accepted'); });
    if(rej) rej.addEventListener('click', function(){ hideBar('rejected'); });
  }

  /* ---------- lebegő WhatsApp gomb (minden oldalon) ----------
     A [href^="https://wa.me/"] miatt a lentebbi konverziókövetés
     automatikusan rácsatlakozik — ezért fut ez a tracking blokk ELŐTT. */
  if(document.body && !document.querySelector('.wa-float')){
    var waMsg = 'Szia! Ingyenes konzultációt kérnék a SOULSILVER csapatától.\n\nNevem: \nAmiben segítséget szeretnék: ';
    var wa = document.createElement('a');
    wa.className = 'wa-float';
    wa.href = 'https://wa.me/36202964933?text=' + encodeURIComponent(waMsg);
    wa.target = '_blank';
    wa.rel = 'noopener';
    wa.setAttribute('aria-label', 'Írj nekünk WhatsApp-on');
    wa.innerHTML = '<span class="wa-float-pulse" aria-hidden="true"></span>' +
      '<img src="img/logos/whatsapp.svg" alt="" width="30" height="30" aria-hidden="true">';
    document.body.appendChild(wa);
  }


  /* ============================================================
     REFERENCIÁK OLDAL
     Minden modul null-guardolt: a többi oldalon egyszerűen kilép.
     ============================================================ */

  /* ---------- statisztika count-up ---------- */
  var counters = document.querySelectorAll('[data-count]');
  if(counters.length){
    var fmt = function(v, dec){
      return dec ? v.toFixed(dec).replace('.', ',') : String(Math.round(v));
    };
    var runCount = function(el){
      var target = parseFloat(el.getAttribute('data-count')) || 0;
      var dec = parseInt(el.getAttribute('data-decimals'), 10) || 0;
      var suffix = el.getAttribute('data-suffix') || '';
      if(reduceMotion){ el.textContent = fmt(target, dec) + suffix; return; }
      var start = null, dur = 1400;
      var tick = function(ts){
        if(start === null) start = ts;
        var p = Math.min(1, (ts - start) / dur);
        var eased = 1 - Math.pow(1 - p, 3);
        el.textContent = fmt(target * eased, dec) + suffix;
        if(p < 1) requestAnimationFrame(tick);
      };
      requestAnimationFrame(tick);
    };
    if('IntersectionObserver' in window){
      var co = new IntersectionObserver(function(entries){
        entries.forEach(function(e){
          if(e.isIntersecting){ runCount(e.target); co.unobserve(e.target); }
        });
      }, {threshold:0.4});
      counters.forEach(function(el){ co.observe(el); });
    } else {
      counters.forEach(runCount);
    }
  }

  /* ---------- bento szűrő ---------- */
  var bento = document.getElementById('bento');
  var chips = document.querySelectorAll('.chip[data-filter]');
  var emptyNote = document.getElementById('bentoEmpty');
  var bentoItems = bento ? Array.prototype.slice.call(bento.querySelectorAll('.bento-item')) : [];

  function visibleItems(){
    return bentoItems.filter(function(el){ return !el.classList.contains('is-hidden'); });
  }

  function applyFilter(cat, updateHash){
    if(!bento) return;
    var shown = 0;
    bentoItems.forEach(function(el){
      var cats = (el.getAttribute('data-cat') || '').split(/\s+/);
      var match = (cat === '*') || cats.indexOf(cat) !== -1;
      if(match){
        el.classList.remove('is-hidden');
        void el.offsetWidth;               /* reflow, hogy az átmenet elinduljon */
        el.classList.remove('is-fading');
        shown++;
      } else {
        el.classList.add('is-fading');
        if(reduceMotion){
          el.classList.add('is-hidden');
        } else {
          window.setTimeout(function(){
            if(el.classList.contains('is-fading')) el.classList.add('is-hidden');
          }, 300);
        }
      }
    });
    chips.forEach(function(c){ c.classList.toggle('active', c.getAttribute('data-filter') === cat); });
    if(emptyNote) emptyNote.style.display = shown ? 'none' : '';
    if(updateHash && window.history && window.history.replaceState){
      window.history.replaceState(null, '', cat === '*' ? window.location.pathname : '#' + cat);
    }
  }

  if(chips.length && bento){
    chips.forEach(function(c){
      c.addEventListener('click', function(){ applyFilter(c.getAttribute('data-filter'), true); });
    });
    var initial = (window.location.hash || '').replace('#', '');
    var known = Array.prototype.map.call(chips, function(c){ return c.getAttribute('data-filter'); });
    if(initial && known.indexOf(initial) !== -1) applyFilter(initial, false);
  }

  /* ---------- lightbox ---------- */
  var lb = document.getElementById('lightbox');
  if(lb && bentoItems.length){
    var lbMedia = document.getElementById('lbMedia');
    var lbTag = document.getElementById('lbTag');
    var lbTitle = document.getElementById('lbTitle');
    var lbDesc = document.getElementById('lbDesc');
    var lbCounter = document.getElementById('lbCounter');
    var lbClose = document.getElementById('lbClose');
    var lbPrev = document.getElementById('lbPrev');
    var lbNext = document.getElementById('lbNext');
    var lastTrigger = null;
    var current = [];
    var index = 0;

    function mediaFallback(){
      lbMedia.innerHTML = '';
      var p = document.createElement('p');
      p.className = 'lb-empty';
      p.textContent = 'A látványanyag hamarosan felkerül.';
      lbMedia.appendChild(p);
    }

    function renderMedia(item){
      var type = item.getAttribute('data-lb-type') || 'image';
      var src = item.getAttribute('data-lb-src') || '';
      lbMedia.innerHTML = '';
      if(!src){ mediaFallback(); return; }
      if(type === 'youtube'){
        var frame = document.createElement('iframe');
        frame.src = src;
        frame.title = item.getAttribute('data-lb-title') || 'Videó';
        frame.allow = 'accelerometer; autoplay; encrypted-media; picture-in-picture';
        frame.setAttribute('allowfullscreen', '');
        lbMedia.appendChild(frame);
      } else if(type === 'video'){
        var vid = document.createElement('video');
        vid.src = src;
        vid.controls = true;
        vid.autoplay = true;
        vid.loop = true;
        vid.muted = true;
        vid.playsInline = true;
        vid.addEventListener('error', mediaFallback);
        lbMedia.appendChild(vid);
      } else {
        var img = document.createElement('img');
        img.src = src;
        img.alt = item.getAttribute('data-lb-title') || '';
        img.addEventListener('error', mediaFallback);
        lbMedia.appendChild(img);
      }
    }

    function show(i){
      if(!current.length) return;
      index = (i + current.length) % current.length;
      var item = current[index];
      renderMedia(item);
      lbTag.textContent = item.getAttribute('data-lb-tag') || '';
      lbTitle.textContent = item.getAttribute('data-lb-title') || '';
      lbDesc.textContent = item.getAttribute('data-lb-desc') || '';
      lbCounter.textContent = (index + 1) + ' / ' + current.length;
      var multi = current.length > 1;
      lbPrev.style.display = multi ? '' : 'none';
      lbNext.style.display = multi ? '' : 'none';
    }

    function openLb(item){
      current = visibleItems();
      var i = current.indexOf(item);
      lastTrigger = item;
      lb.classList.add('open');
      document.body.style.overflow = 'hidden';
      show(i < 0 ? 0 : i);
      void lb.offsetWidth;                 /* reflow a display:none -> flex után,
                                              hogy az opacity-átmenet lefusson */
      lb.classList.add('shown');
      lbClose.focus();
    }

    function closeLb(){
      lb.classList.remove('shown');
      document.body.style.overflow = '';
      var done = function(){
        lb.classList.remove('open');
        lbMedia.innerHTML = '';
      };
      if(reduceMotion) done(); else window.setTimeout(done, 300);
      if(lastTrigger) lastTrigger.focus();
    }

    bentoItems.forEach(function(item){
      item.addEventListener('click', function(){ openLb(item); });
    });
    lbClose.addEventListener('click', closeLb);
    lbPrev.addEventListener('click', function(){ show(index - 1); });
    lbNext.addEventListener('click', function(){ show(index + 1); });
    lb.addEventListener('click', function(e){ if(e.target === lb) closeLb(); });

    document.addEventListener('keydown', function(e){
      if(!lb.classList.contains('open')) return;
      if(e.key === 'Escape'){ closeLb(); }
      else if(e.key === 'ArrowLeft'){ show(index - 1); }
      else if(e.key === 'ArrowRight'){ show(index + 1); }
      else if(e.key === 'Tab'){
        /* fókuszcsapda a lightboxon belül */
        var focusables = lb.querySelectorAll('button, [href], video, iframe');
        if(!focusables.length) return;
        var first = focusables[0], last = focusables[focusables.length - 1];
        if(e.shiftKey && document.activeElement === first){ e.preventDefault(); last.focus(); }
        else if(!e.shiftKey && document.activeElement === last){ e.preventDefault(); first.focus(); }
      }
    });
  }

  /* ---------- full-bleed videó-sáv: parallax + lejátszás-vezérlés ----------
     Nem a globális [data-parallax] motort használja: az a lap tetejéhez képest
     számol, ami lap közepén lévő sávnál elcsúszna. Itt elem-relatív a haladás. */
  var vbands = Array.prototype.slice.call(document.querySelectorAll('[data-vband]'));
  if(vbands.length){
    var FRAME_SHIFT = 70;   /* px, a videó elmozdulása */
    var TEXT_SHIFT = 26;    /* px, a szöveg ellenirányú elmozdulása */

    function updateVbands(){
      var vh = window.innerHeight;
      vbands.forEach(function(band){
        var rect = band.getBoundingClientRect();
        if(rect.bottom < -200 || rect.top > vh + 200) return;
        var progress = (vh - rect.top) / (vh + rect.height); /* 0 → 1 */
        var offset = (progress - 0.5) * 2;
        var frame = band.querySelector('.vband-frame');
        var content = band.querySelector('.vband-content');
        if(frame) frame.style.transform = 'translate3d(0,' + (offset * FRAME_SHIFT).toFixed(1) + 'px,0)';
        if(content) content.style.transform = 'translate3d(0,' + (offset * -TEXT_SHIFT).toFixed(1) + 'px,0)';
      });
    }

    if(!reduceMotion){
      window.addEventListener('scroll', function(){ requestAnimationFrame(updateVbands); }, {passive:true});
      window.addEventListener('resize', function(){ requestAnimationFrame(updateVbands); }, {passive:true});
      updateVbands();
    }

    /* csak akkor játsszon, ha látszik — akku és sávszélesség */
    var videos = document.querySelectorAll('.vband-video');
    if(videos.length && 'IntersectionObserver' in window){
      var vo = new IntersectionObserver(function(entries){
        entries.forEach(function(e){
          var v = e.target;
          if(e.isIntersecting){
            var pr = v.play();
            if(pr && pr.catch) pr.catch(function(){ /* nincs forrás vagy blokkolt autoplay */ });
          } else if(!v.paused){
            v.pause();
          }
        });
      }, {threshold:0.25});
      videos.forEach(function(v){ vo.observe(v); });
    }
  }

  /* ---------- bento hover-tilt ---------- */
  var canHover = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
  if(canHover && !reduceMotion && bentoItems.length){
    var MAX_TILT = 4;
    bentoItems.forEach(function(el){
      el.addEventListener('mousemove', function(e){
        var r = el.getBoundingClientRect();
        var px = (e.clientX - r.left) / r.width - 0.5;
        var py = (e.clientY - r.top) / r.height - 0.5;
        el.style.setProperty('--tx', (px * MAX_TILT).toFixed(2) + 'deg');
        el.style.setProperty('--ty', (-py * MAX_TILT).toFixed(2) + 'deg');
      });
      el.addEventListener('mouseleave', function(){
        el.style.setProperty('--tx', '0deg');
        el.style.setProperty('--ty', '0deg');
      });
    });
  }


  /* ============================================================
     WORKFLOW AUTOMATIZÁLÁS OLDAL
     Mindkét modul null-guardolt: a többi oldalon kilép.
     ============================================================ */

  /* ---------- workflow katalógus akkordeon ---------- */
  var wfTriggers = document.querySelectorAll('.wf-trigger[aria-controls]');
  if(wfTriggers.length){
    wfTriggers.forEach(function(btn){
      btn.addEventListener('click', function(){
        var panel = document.getElementById(btn.getAttribute('aria-controls'));
        if(!panel) return;
        var open = btn.getAttribute('aria-expanded') === 'true';
        btn.setAttribute('aria-expanded', open ? 'false' : 'true');
        panel.hidden = open;
      });
    });
  }

  /* ---------- ROI kalkulátor ---------- */
  var calcPeople = document.getElementById('wfPeople');
  var calcHours = document.getElementById('wfHours');
  var calcCost = document.getElementById('wfCost');

  if(calcPeople && calcHours && calcCost){
    var MUNKANAP = 21;          /* átlagos munkanap egy hónapban */
    var AUTOMATIZALHATO = 0.7;  /* feltételezett arány — a lapon jelölve */
    var HAVI_MUNKAORA = 168;    /* 1 fő teljes munkaideje / hó */

    /* A hu-HU alapbol NEM csoportositja a negyjegyu szamokat ("6000"),
       ezert useGrouping:'always' — igy egyezik a HTML-ben levo kezdoertekkel
       ("4 500 Ft"). Regi bongeszo ezt egyszeruen figyelmen kivul hagyja. */
    var nf0 = new Intl.NumberFormat('hu-HU', {maximumFractionDigits:0, useGrouping:'always'});
    var nf1 = new Intl.NumberFormat('hu-HU', {minimumFractionDigits:1, maximumFractionDigits:1});
    var nf2 = new Intl.NumberFormat('hu-HU', {minimumFractionDigits:2, maximumFractionDigits:2});
    /* egesz ora eseten ne irjunk ki tizedest ("2 ora", nem "2,0 ora") */
    function oraFmt(v){ return (v % 1 === 0) ? nf0.format(v) : nf1.format(v); }
    /* 1 fo alatt egy tizedes "0,0"-t adna, ami ertelmetlen — ott ket tizedes */
    function fteFmt(v){ return v < 1 ? nf2.format(v) : nf1.format(v); }

    var out = {
      peopleVal: document.getElementById('wfPeopleVal'),
      hoursVal: document.getElementById('wfHoursVal'),
      costVal: document.getElementById('wfCostVal'),
      hoursMonth: document.getElementById('wfHoursMonth'),
      hoursYear: document.getElementById('wfHoursYear'),
      costMonth: document.getElementById('wfCostMonth'),
      costYear: document.getElementById('wfCostYear'),
      fte: document.getElementById('wfFte')
    };

    function recalc(){
      var people = parseFloat(calcPeople.value);
      var hours = parseFloat(calcHours.value);
      var cost = parseFloat(calcCost.value);

      var hoursMonth = people * hours * MUNKANAP * AUTOMATIZALHATO;
      var hoursYear = hoursMonth * 12;
      var costMonth = hoursMonth * cost;
      var fte = hoursMonth / HAVI_MUNKAORA;

      if(out.peopleVal) out.peopleVal.textContent = nf0.format(people) + ' fő';
      if(out.hoursVal) out.hoursVal.textContent = oraFmt(hours) + ' óra';
      if(out.costVal) out.costVal.textContent = nf0.format(cost) + ' Ft';

      if(out.hoursMonth) out.hoursMonth.textContent = nf0.format(hoursMonth);
      if(out.hoursYear) out.hoursYear.textContent = nf0.format(hoursYear) + ' óra';
      if(out.costMonth) out.costMonth.textContent = nf0.format(costMonth) + ' Ft';
      if(out.costYear) out.costYear.textContent = nf0.format(costMonth * 12) + ' Ft';
      if(out.fte) out.fte.textContent = fteFmt(fte);
    }

    [calcPeople, calcHours, calcCost].forEach(function(el){
      el.addEventListener('input', recalc);
      el.addEventListener('change', recalc);
    });
    recalc();   /* induláskor is, hogy ne 0 legyen a kimenet */
  }

  /* ---------- Google Ads + GA4 conversion tracking ----------
     Két külön konverziót mérünk, mert nem egyenértékűek:
       - Űrlapbeküldés (koszonjuk.html) = befejezett lead → "Potenciális ügyfél
         űrlapjának beküldése" (elsődleges, erre licitál a Smart Bidding).
       - Email-/WhatsApp-kattintás = kapcsolatfelvételi szándék, de nem
         garantáltan elküldött üzenet → külön "Kapcsolatfelvétel" konverzió.
     Ha a kattintások is a lead-címkére mennének, felhígítanák az elsődleges
     célt, és a Smart Bidding az olcsó kattintásokra optimalizálna. */
  if(typeof gtag === 'function'){
    var CONTACT_INTENT = 'AW-17312625266/2Gt3CI3gguscEPLkpr9A';
    var WHATSAPP_INTENT = 'AW-17312625266/HlTICL_g_OscEPLkpr9A';

    function trackContactIntent(selector, gaEvent, conversionLabel){
      document.querySelectorAll(selector).forEach(function(a){
        a.addEventListener('click', function(){
          gtag('event', gaEvent, { 'value': 1.0, 'currency': 'HUF' });
          gtag('event', 'conversion', {
            'send_to': conversionLabel,
            'value': 1.0,
            'currency': 'HUF'
          });
        });
      });
    }

    trackContactIntent('a[href^="mailto:info@soulsilvermarketing.com"]', 'contact_email', CONTACT_INTENT);
    trackContactIntent('a[href^="https://wa.me/"]', 'contact_whatsapp', WHATSAPP_INTENT);
  }
})();
