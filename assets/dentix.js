/* ══════════════════════════════════════════════════════════════
   dentix.js — JavaScript compartido para todas las páginas
   ══════════════════════════════════════════════════════════════ */

(function(){

  /* ── Buscador dropdown ──────────────────────────────────────── */
  var searchInput = document.querySelector('.search-outer input');
  var searchHint  = document.getElementById('searchHint');
  if(searchInput && searchHint){
    searchInput.addEventListener('focus', function(){ searchHint.classList.add('open'); });
    document.addEventListener('click', function(e){
      if(!e.target.closest('.search-block')) searchHint.classList.remove('open');
    });
    searchHint.querySelectorAll('.search-tag').forEach(function(tag){
      tag.addEventListener('click', function(){
        searchInput.value = tag.textContent;
        searchHint.classList.remove('open');
        searchInput.focus();
      });
    });
    /* Enter → ir a búsqueda */
    searchInput.addEventListener('keydown', function(e){
      if(e.key === 'Enter' && searchInput.value.trim()){
        window.location.href = 'busqueda.html?q=' + encodeURIComponent(searchInput.value.trim());
      }
    });
  }

  /* ── Nav: marcar ítem activo según URL ──────────────────────── */
  var navItems = document.querySelectorAll('.nav-item');
  var currentPage = window.location.pathname.split('/').pop() || 'index.html';
  navItems.forEach(function(item){
    var link = item.getAttribute('data-page') || '';
    if(link && currentPage.includes(link)){
      item.classList.add('on');
    }
  });

  /* ── Pills filtro de productos ──────────────────────────────── */
  document.querySelectorAll('.pill').forEach(function(p){
    p.addEventListener('click', function(){
      var group = p.closest('.filter-pills');
      if(group){
        group.querySelectorAll('.pill').forEach(function(x){ x.classList.remove('on'); });
      }
      p.classList.add('on');
    });
  });

  /* ── Wishlist toggle ─────────────────────────────────────────── */
  document.querySelectorAll('.p-wish').forEach(function(w){
    w.addEventListener('click', function(e){
      e.stopPropagation();
      var isLiked = w.innerHTML.includes('♥') && !w.innerHTML.includes('♡');
      w.innerHTML = isLiked ? '♡' : '♥';
      w.style.color = isLiked ? '' : '#C0392B';
    });
  });

  /* ── Tabs (fichas de producto) ──────────────────────────────── */
  document.querySelectorAll('.tab-btn').forEach(function(btn){
    btn.addEventListener('click', function(){
      var group = btn.closest('.prod-tabs');
      if(!group) return;
      group.querySelectorAll('.tab-btn').forEach(function(b){ b.classList.remove('active'); });
      group.querySelectorAll('.tab-content').forEach(function(c){ c.classList.remove('active'); });
      btn.classList.add('active');
      var target = btn.getAttribute('data-tab');
      var content = group.querySelector('#' + target);
      if(content) content.classList.add('active');
    });
  });

  /* ── Qty controls ───────────────────────────────────────────── */
  document.querySelectorAll('.qty-ctrl').forEach(function(ctrl){
    var minusBtn = ctrl.querySelector('.qty-minus');
    var plusBtn  = ctrl.querySelector('.qty-plus');
    var valEl    = ctrl.querySelector('.qty-val');
    if(!valEl) return;
    if(minusBtn) minusBtn.addEventListener('click', function(){
      var v = parseInt(valEl.value||valEl.textContent||1);
      if(v > 1){ valEl.tagName==='INPUT' ? valEl.value=v-1 : valEl.textContent=v-1; }
    });
    if(plusBtn) plusBtn.addEventListener('click', function(){
      var v = parseInt(valEl.value||valEl.textContent||1);
      valEl.tagName==='INPUT' ? valEl.value=v+1 : valEl.textContent=v+1;
    });
  });

  /* ── Auth tabs (login/registro) ─────────────────────────────── */
  document.querySelectorAll('.auth-tab').forEach(function(tab){
    tab.addEventListener('click', function(){
      document.querySelectorAll('.auth-tab').forEach(function(t){ t.classList.remove('active'); });
      document.querySelectorAll('.auth-panel').forEach(function(p){ p.style.display='none'; });
      tab.classList.add('active');
      var panel = document.getElementById(tab.getAttribute('data-panel'));
      if(panel) panel.style.display='block';
    });
  });

  /* ── Checkout: método de pago ───────────────────────────────── */
  document.querySelectorAll('.pay-opt').forEach(function(opt){
    opt.addEventListener('click', function(){
      document.querySelectorAll('.pay-opt').forEach(function(o){ o.classList.remove('active'); });
      opt.classList.add('active');
    });
  });

  /* ── Cookie banner ──────────────────────────────────────────── */
  var cookieBanner = document.getElementById('cookieBanner');
  if(cookieBanner && !localStorage.getItem('dentix_cookies_ok')){
    cookieBanner.style.display = 'flex';
  }
  var cookieAccept = document.getElementById('cookieAccept');
  if(cookieAccept){
    cookieAccept.addEventListener('click', function(){
      localStorage.setItem('dentix_cookies_ok','1');
      if(cookieBanner) cookieBanner.style.display = 'none';
    });
  }

  /* ── Mobile menu toggle ─────────────────────────────────────── */
  var mobileMenuBtn = document.getElementById('mobileMenuBtn');
  var mobileMenu    = document.getElementById('mobileMenu');
  if(mobileMenuBtn && mobileMenu){
    mobileMenuBtn.addEventListener('click', function(){
      var open = mobileMenu.style.display === 'block';
      mobileMenu.style.display = open ? 'none' : 'block';
    });
  }

})();
