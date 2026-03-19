/* ══════════════════════════════════════════════════════════════
   dentix.js — JavaScript compartido todas las páginas
   ══════════════════════════════════════════════════════════════ */

// ── Buscador dropdown ─────────────────────────────────────────
(function(){
  const searchInput = document.querySelector('.search-outer input');
  const searchHint  = document.getElementById('searchHint');
  if(!searchInput || !searchHint) return;
  searchInput.addEventListener('focus', () => searchHint.classList.add('open'));
  document.addEventListener('click', (e) => {
    if(!e.target.closest('.search-block')) searchHint.classList.remove('open');
  });
  searchHint.querySelectorAll('.search-tag').forEach(tag => {
    tag.addEventListener('click', () => {
      searchInput.value = tag.textContent;
      searchHint.classList.remove('open');
      searchInput.focus();
    });
  });
})();

// ── Pills de filtro ───────────────────────────────────────────
document.querySelectorAll('.pill').forEach(p => {
  p.addEventListener('click', () => {
    const group = p.closest('.filter-pills');
    (group || document).querySelectorAll('.pill').forEach(x => x.classList.remove('on'));
    p.classList.add('on');
  });
});

// ── Wishlist toggle ───────────────────────────────────────────
document.querySelectorAll('.p-wish').forEach(w => {
  w.addEventListener('click', (e) => {
    e.stopPropagation();
    const liked = w.textContent.trim() === '♥';
    w.textContent = liked ? '♡' : '♥';
    w.style.color = liked ? '' : '#C0392B';
  });
});

// ── Nav: marca active según página actual ─────────────────────
(function(){
  const path = window.location.pathname.split('/').pop();
  document.querySelectorAll('.nav-item').forEach(el => {
    el.classList.remove('on');
    if(el.dataset.page && path.includes(el.dataset.page)) el.classList.add('on');
  });
})();
