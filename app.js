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
    if(!getChoice()){
      setTimeout(function(){ cookieBar.classList.add('show'); }, 900);
    }
    var acc = document.getElementById('cookieAccept');
    var rej = document.getElementById('cookieReject');
    if(acc) acc.addEventListener('click', function(){ setChoice('accepted'); cookieBar.classList.remove('show'); });
    if(rej) rej.addEventListener('click', function(){ setChoice('rejected'); cookieBar.classList.remove('show'); });
  }

  /* ---------- Google Ads conversion tracking (lead intent) ---------- */
  if(typeof gtag === 'function'){
    document.querySelectorAll('a[href^="mailto:info@soulsilvermarketing.com"]').forEach(function(a){
      a.addEventListener('click', function(){
        gtag('event', 'conversion', {
          'send_to': 'AW-17312625266/hpAtCOOHyeocEPLkpr9A',
          'value': 1.0,
          'currency': 'HUF'
        });
      });
    });
  }
})();
