// ══════════════════════════════════════════════════════════════════
// CARRUSEL HERO
// ──────────────────────────────────────────────────────────────────
// Lógica de crossfade entre 5 slides con texto dinámico por slide.
//
// Flujo:
//  1. startAuto() lanza setInterval cada 5.5s llamando a goTo(current+1)
//  2. goTo(n) cambia classes .active en slides[] y dots[]
//  3. updateText(n) hace fade del heroLeft y actualiza los textos
//  4. mouseenter/mouseleave pausan/reanudan — solo tras el primer
//     mousemove real (evita que el evento fantasma al cargar pare el auto)
//  5. Clic en dot → stopAuto → goTo → startAuto
// ══════════════════════════════════════════════════════════════════
(function(){
  const slides   = document.querySelectorAll('.hero-slide');
  const dots     = document.querySelectorAll('.cdot');
  const heroLeft = document.querySelector('.hero-left');
  let current    = 0;
  let timer      = null;

  const slideData = [
    {
      tag:   'Instrumental Quirúrgico · Distribución oficial',
      title: 'El instrumental<br>que tu clínica<br><em>merece.</em>',
      sub:   'Precisión. Calidad. Garantía Profesional.',
      desc:  'Más de <strong>10.000 referencias</strong> de instrumental odontológico profesional. Marcas líderes mundiales y entrega garantizada en <strong>24–48h</strong> para toda España.',
      cta:   'Explorar catálogo'
    },
    {
      tag:   'Equipamiento Clínico · NSK · KaVo · Dentsply',
      title: 'Turbinas, piezas<br>de mano y<br><em>material fungible.</em>',
      sub:   'Tecnología al servicio del profesional.',
      desc:  'Toda la gama de <strong>equipamiento clínico</strong> de las marcas más exigentes. Stock permanente, recambios originales y soporte técnico especializado.',
      cta:   'Ver equipamiento'
    },
    {
      tag:   'Implantología · Straumann · Nobel Biocare',
      title: 'Sistemas de<br>implantes para<br><em>cada caso clínico.</em>',
      sub:   'Bone Level, Tissue Level, soluciones CAD/CAM.',
      desc:  'Distribuidor oficial de los principales fabricantes de <strong>sistemas de implantes</strong>. Kits completos, componentes protésicos y material regenerativo.',
      cta:   'Ver implantología'
    },
    {
      tag:   'Radiología Digital · Diagnóstico · Tecnología',
      title: 'Diagnóstico<br>digital de<br><em>última generación.</em>',
      sub:   'Sensores, escáneres y software de imagen.',
      desc:  'Equipos de <strong>radiología digital</strong> intraoral y panorámica, escáneres intraorales y soluciones CAD/CAM para el laboratorio moderno.',
      cta:   'Ver radiología'
    },
    {
      tag:   'Ortodoncia · Endodoncia · Material Clínico',
      title: 'Todo lo que<br>necesita tu<br><em>clínica dental.</em>',
      sub:   'Un solo proveedor. 10.000 referencias.',
      desc:  'Desde <strong>brackets y arcos de ortodoncia</strong> hasta limas de endodoncia, composites y material de impresión. Todo sincronizado con tu pedido en tiempo real.',
      cta:   'Ver catálogo completo'
    }
  ];

  // Obtener referencias de texto
  const elTag   = document.getElementById('slideTag');
  const elTitle = document.getElementById('slideTitle');
  const elSub   = document.getElementById('slideSubtitle');
  const elDesc  = document.getElementById('slideDesc');
  const elCta   = document.getElementById('slideCta1Text');

  // updateText: hace fade del bloque de texto izquierdo y actualiza
  // tag, título, subtítulo, descripción y CTA con el contenido del slide n
  function updateText(n){
    if(!heroLeft) return;
    heroLeft.style.opacity = '0';
    setTimeout(function(){
      var d = slideData[n];
      if(elTag)   elTag.textContent = d.tag;
      if(elTitle) elTitle.innerHTML = d.title;
      if(elSub)   elSub.textContent = d.sub;
      if(elDesc)  elDesc.innerHTML  = d.desc;
      if(elCta)   elCta.textContent = d.cta;
      heroLeft.style.opacity = '1';
    }, 450);
  }

  // goTo(n): activa el slide n desactivando el actual.
  // Guard: si n === current no hace nada (evita flash innecesario)
  function goTo(n){
    var next = ((n % slides.length) + slides.length) % slides.length;
    if(next === current) return;
    slides[current].classList.remove('active');
    dots[current].classList.remove('active');
    current = next;
    slides[current].classList.add('active');
    dots[current].classList.add('active');
    updateText(current);
  }

  // startAuto: limpia cualquier intervalo previo y lanza uno nuevo.
  // Se llama también al salir del hover y al soltar un dot.
  function startAuto(){
    if(timer) clearInterval(timer);
    timer = setInterval(function(){
      goTo(current + 1);
    }, 5500);
  }
  function stopAuto(){
    if(timer){ clearInterval(timer); timer = null; }
  }

  // Clic en dots — para el auto, cambia slide, reanuda
  dots.forEach(function(dot){
    dot.addEventListener('click', function(){
      stopAuto();
      goTo(parseInt(dot.dataset.slide, 10));
      startAuto();
    });
  });

  // El carrusel corre siempre. Solo se interrumpe mientras el usuario
  // mantiene pulsado el botón del ratón dentro del hero (mousedown),
  // y reanuda en cuanto lo suelta (mouseup) o el cursor sale (mouseleave
  // como seguridad por si suelta el botón fuera del hero).
  var heroEl = document.querySelector('.hero');
  if(heroEl){
    heroEl.addEventListener('mousedown',  stopAuto);
    heroEl.addEventListener('mouseup',    startAuto);
    heroEl.addEventListener('mouseleave', startAuto); // seguridad: suelta fuera
  }

  // Arrancar directamente.
  // El slide 0 ya tiene class "active" en el HTML, no lo tocamos.
  startAuto();

})();

// ── BUSCADOR DROPDOWN ─────────────────────────────────────────
const searchInput = document.querySelector('.search-outer input');
const searchHint  = document.getElementById('searchHint');
if(searchInput && searchHint){
  searchInput.addEventListener('focus', () => searchHint.classList.add('open'));
  document.addEventListener('click', (e) => {
    if(!e.target.closest('.search-block')) searchHint.classList.remove('open');
  });
  // Click en un tag → rellena el input y cierra
  searchHint.querySelectorAll('.search-tag').forEach(tag => {
    tag.addEventListener('click', () => {
      searchInput.value = tag.textContent;
      searchHint.classList.remove('open');
      searchInput.focus();
    });
  });
}

// Pills de filtro de productos
document.querySelectorAll('.pill').forEach(p => {
  p.addEventListener('click', () => {
    document.querySelectorAll('.pill').forEach(x => x.classList.remove('on'));
    p.classList.add('on');
  });
});

// Botón wishlist toggle
document.querySelectorAll('.p-wish').forEach(w => {
  w.addEventListener('click', (e) => {
    e.stopPropagation();
    const isLiked = w.textContent === '♥';
    w.textContent = isLiked ? '♡' : '♥';
    w.style.color  = isLiked ? '' : '#C0392B';
  });
});