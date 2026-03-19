<?php
/**
 * front-page.php — Homepage de Dentix
 * Se muestra cuando "Página de inicio" está configurada en
 * Ajustes → Lectura → Tu página de inicio muestra → Una página estática
 */
get_header();
?>

<!-- ═══ HERO con carrusel ════════════════════════════════════ -->
<section class="hero">

  <!-- Carrusel de fondo — 5 escenas CSS del sector dental -->
  <div class="hero-carousel">
    <div class="hero-slide hs1 active"></div>
    <div class="hero-slide hs2"></div>
    <div class="hero-slide hs3"></div>
    <div class="hero-slide hs4"></div>
    <div class="hero-slide hs5"></div>
    <div class="hero-carousel-overlay"></div>
  </div>

  <!-- Círculos decorativos del diseño validado index1 -->
  <div class="hero-circle-red"></div>
  <div class="hero-circle-white"></div>

  <!-- Dots de navegación -->
  <div class="carousel-dots" id="carouselDots">
    <div class="cdot active" data-slide="0"></div>
    <div class="cdot" data-slide="1"></div>
    <div class="cdot" data-slide="2"></div>
    <div class="cdot" data-slide="3"></div>
    <div class="cdot" data-slide="4"></div>
  </div>

  <!-- Izquierda — titular dinámico por slide -->
  <div class="hero-left">
    <div class="hero-eyebrow">
      <div class="hero-eline"></div>
      <span class="hero-etag" id="slideTag">Distribuidor oficial · España</span>
    </div>

    <h1 class="hero-h1" id="slideTitle">El instrumental<br>que tu clínica<br><em>merece.</em></h1>
    <p class="hero-h1-line2" id="slideSubtitle">Precisión. Calidad. Garantía Profesional.</p>

    <p class="hero-desc" id="slideDesc">
      Más de <strong>10.000 referencias</strong> de instrumental odontológico profesional.
      Marcas líderes mundiales, precios B2B exclusivos y entrega garantizada
      en <strong>24–48h</strong> para toda España.
    </p>

    <div class="hero-ctas">
      <button class="btn-red" id="slideCta1">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h7"/></svg>
        <span id="slideCta1Text">Explorar catálogo</span>
      </button>
      <button class="btn-outline-d">Ver novedades</button>
    </div>

    <div class="hero-stats">
      <div class="stat">
        <div class="stat-n">10<sup>K+</sup></div>
        <div class="stat-l">Referencias en catálogo</div>
      </div>
      <div class="stat">
        <div class="stat-n">48<sup>h</sup></div>
        <div class="stat-l">Entrega máx. España</div>
      </div>
      <div class="stat">
        <div class="stat-n">20<sup>+</sup></div>
        <div class="stat-l">Años distribuyendo</div>
      </div>
    </div>
  </div>

  <!-- Derecha — productos showcase -->
  <div class="hero-right">
    <div class="hero-showcase">

      <!-- Card featured -->
      <div class="hsc hsc-featured">
        <div class="hsc-img" style="background:rgba(192,57,43,0.25)">
          <!-- SVG implante dental -->
          <svg width="40" height="40" viewBox="0 0 40 40" fill="none">
            <rect x="16" y="2" width="8" height="22" rx="4" fill="white" opacity=".8"/>
            <rect x="12" y="22" width="16" height="4" rx="2" fill="white" opacity=".6"/>
            <path d="M10 26 Q20 38 30 26" fill="white" opacity=".4"/>
            <circle cx="20" cy="12" r="3" fill="#C0392B" opacity=".9"/>
          </svg>
        </div>
        <div class="hsc-info">
          <span class="hsc-badge badge-best">★ Más vendido</span>
          <div class="hsc-name">Kit Implantología Premium<br>Bone Level Straumann RC</div>
          <div class="hsc-ref">REF. STR-BL-RC-335</div>
          <div class="hsc-price">
            <s>340,00 €</s>
            265,00 €
          </div>
        </div>
      </div>

      <!-- Card 2 -->
      <div class="hsc">
        <div class="hsc-img" style="background:rgba(52,152,219,0.18);margin-bottom:14px">
          <!-- SVG turbina -->
          <svg width="38" height="38" viewBox="0 0 38 38" fill="none">
            <circle cx="19" cy="19" r="9" fill="none" stroke="white" stroke-width="2" opacity=".6"/>
            <circle cx="19" cy="19" r="4" fill="white" opacity=".8"/>
            <line x1="19" y1="4" x2="19" y2="10" stroke="white" stroke-width="2.5" stroke-linecap="round" opacity=".5"/>
            <line x1="19" y1="28" x2="19" y2="34" stroke="white" stroke-width="2.5" stroke-linecap="round" opacity=".5"/>
            <line x1="4" y1="19" x2="10" y2="19" stroke="white" stroke-width="2.5" stroke-linecap="round" opacity=".5"/>
            <line x1="28" y1="19" x2="34" y2="19" stroke="white" stroke-width="2.5" stroke-linecap="round" opacity=".5"/>
          </svg>
        </div>
        <span class="hsc-badge badge-new">Novedad</span>
        <div class="hsc-name">Turbina Alta Velocidad LED · NSK P900</div>
        <div class="hsc-ref">REF. NSK-P900L</div>
        <div class="hsc-price">189,00 €</div>
        <div class="hsc-cta">Ver →</div>
      </div>

      <!-- Card 3 -->
      <div class="hsc">
        <div class="hsc-img" style="background:rgba(76,175,80,0.18);margin-bottom:14px">
          <!-- SVG pinza/instrumental -->
          <svg width="38" height="38" viewBox="0 0 38 38" fill="none">
            <rect x="17" y="4" width="4" height="24" rx="2" fill="white" opacity=".8"/>
            <ellipse cx="19" cy="6" rx="6" ry="4" fill="white" opacity=".5"/>
            <rect x="14" y="22" width="10" height="3" rx="1.5" fill="white" opacity=".4"/>
            <rect x="15" y="28" width="8" height="3" rx="1.5" fill="white" opacity=".3"/>
            <rect x="16" y="33" width="6" height="2" rx="1" fill="white" opacity=".25"/>
          </svg>
        </div>
        <span class="hsc-badge badge-sale">-15%</span>
        <div class="hsc-name">Set Espejos Hu-Friedy Explorer 6 uds</div>
        <div class="hsc-ref">REF. HF-EM2-S6</div>
        <div class="hsc-price">
          <s>29,90 €</s>
          24,90 €
        </div>
        <div class="hsc-cta">Ver →</div>
      </div>

    </div>

    <!-- Búsqueda rápida por referencia -->
    <div class="hero-quickref">
      <svg width="14" height="14" fill="none" stroke="#9A9898" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
      <span class="hqr-label">Pedido por referencia:</span>
      <input class="hqr-input" placeholder="Escribe el código SKU o referencia exacta…">
      <button class="hqr-btn">Añadir</button>
    </div>
  </div>
</section>

<!-- ═══ TRUST BAR ═════════════════════════════════════════════ -->
<div class="trust">
  <div class="trust-item">
    <span class="t-icon">🔒</span>
    <div class="t-txt">
      <strong>Solo profesionales</strong>
      <span>Acceso acreditado B2B</span>
    </div>
  </div>
  <div class="trust-item">
    <span class="t-icon">🚚</span>
    <div class="t-txt">
      <strong>Envío gratis +150€</strong>
      <span>Toda España</span>
    </div>
  </div>
  <div class="trust-item">
    <span class="t-icon">⏱</span>
    <div class="t-txt">
      <strong>Entrega 24–48h</strong>
      <span>95% mismo día</span>
    </div>
  </div>
  <div class="trust-item">
    <span class="t-icon">🔄</span>
    <div class="t-txt">
      <strong>Stock en tiempo real</strong>
      <span>Sincronizado con ERP</span>
    </div>
  </div>
  <div class="trust-item">
    <span class="t-icon">↩️</span>
    <div class="t-txt">
      <strong>Devolución 30 días</strong>
      <span>Sin preguntas</span>
    </div>
  </div>
</div>

<!-- ═══ CATEGORÍAS BENTO ══════════════════════════════════════ -->
<section class="sec-cats">
  <div class="sec-head">
    <div>
      <div class="sec-kicker">Catálogo completo</div>
      <div class="sec-title">Navega por <em>especialidad</em></div>
    </div>
    <a class="sec-link" href="#">Ver todas las categorías →</a>
  </div>

  <div class="bento">

    <!-- 1. Instrumental Quirúrgico -->
    <div class="cat cat-1 cbg-1" href="#">
      <div class="cat-bg cbg-1"></div>
      <div class="cat-overlay"></div>
      <!-- Ilustración SVG instrumental -->
      <svg class="cat-illo" width="120" height="160" viewBox="0 0 120 160" fill="none">
        <rect x="55" y="8" width="10" height="140" rx="5" fill="white"/>
        <ellipse cx="60" cy="14" rx="14" ry="9" fill="white" opacity=".6"/>
        <rect x="44" y="80" width="32" height="5" rx="2.5" fill="white" opacity=".4"/>
        <rect x="48" y="100" width="24" height="4" rx="2" fill="white" opacity=".3"/>
        <rect x="50" y="55" width="20" height="4" rx="2" fill="white" opacity=".3"/>
        <rect x="52" y="125" width="16" height="3" rx="1.5" fill="white" opacity=".25"/>
      </svg>
      <div class="cat-content">
        <span class="cat-icon">🔩</span>
        <div class="cat-name">Instrumental Quirúrgico</div>
        <div class="cat-n">3.420 referencias</div>
      </div>
      <div class="cat-arrow">→</div>
    </div>

    <!-- 2. Implantología -->
    <div class="cat cat-2 cbg-2">
      <div class="cat-bg cbg-2"></div>
      <div class="cat-overlay"></div>
      <svg class="cat-illo" width="100" height="130" viewBox="0 0 100 130" fill="none">
        <rect x="42" y="4" width="16" height="70" rx="8" fill="white" opacity=".5"/>
        <rect x="36" y="18" width="28" height="6" rx="3" fill="white" opacity=".3"/>
        <rect x="36" y="30" width="28" height="6" rx="3" fill="white" opacity=".3"/>
        <rect x="36" y="42" width="28" height="6" rx="3" fill="white" opacity=".3"/>
        <path d="M30 74 Q50 110 70 74" fill="white" opacity=".2"/>
        <circle cx="50" cy="30" r="7" fill="#C0392B" opacity=".7"/>
      </svg>
      <div class="cat-content">
        <span class="cat-icon">🔬</span>
        <div class="cat-name">Implantología</div>
        <div class="cat-n">1.240 referencias</div>
      </div>
      <div class="cat-arrow">→</div>
    </div>

    <!-- 3. Endodoncia -->
    <div class="cat cat-3 cbg-3">
      <div class="cat-bg cbg-3"></div>
      <div class="cat-overlay"></div>
      <svg class="cat-illo" width="90" height="130" viewBox="0 0 90 130" fill="none">
        <rect x="41" y="5" width="8" height="100" rx="4" fill="white" opacity=".6"/>
        <path d="M25 60 Q45 120 65 60" fill="white" opacity=".2"/>
        <circle cx="45" cy="30" r="5" fill="white" opacity=".5"/>
        <circle cx="45" cy="50" r="3" fill="white" opacity=".3"/>
        <ellipse cx="45" cy="10" rx="10" ry="6" fill="white" opacity=".4"/>
      </svg>
      <div class="cat-content">
        <span class="cat-icon">🦷</span>
        <div class="cat-name">Endodoncia</div>
        <div class="cat-n">2.110 referencias</div>
      </div>
      <div class="cat-arrow">→</div>
    </div>

    <!-- 4. Ortodoncia -->
    <div class="cat cat-4 cbg-4">
      <div class="cat-bg cbg-4"></div>
      <div class="cat-overlay"></div>
      <svg class="cat-illo" width="100" height="100" viewBox="0 0 100 100" fill="none">
        <rect x="10" y="40" width="80" height="20" rx="10" fill="white" opacity=".25"/>
        <rect x="20" y="45" width="12" height="10" rx="3" fill="white" opacity=".4"/>
        <rect x="38" y="45" width="12" height="10" rx="3" fill="white" opacity=".4"/>
        <rect x="56" y="45" width="12" height="10" rx="3" fill="white" opacity=".4"/>
        <path d="M10 50 Q50 20 90 50" fill="none" stroke="white" stroke-width="1.5" opacity=".3"/>
      </svg>
      <div class="cat-content">
        <span class="cat-icon">📐</span>
        <div class="cat-name">Ortodoncia</div>
        <div class="cat-n">980 referencias</div>
      </div>
      <div class="cat-arrow">→</div>
    </div>

    <!-- 5. Esterilización — ancho 5col -->
    <div class="cat cat-5 cbg-5">
      <div class="cat-bg cbg-5"></div>
      <div class="cat-overlay"></div>
      <svg class="cat-illo" width="200" height="90" viewBox="0 0 200 90" fill="none">
        <rect x="10" y="18" width="180" height="54" rx="12" fill="none" stroke="white" stroke-width="1" opacity=".2"/>
        <rect x="20" y="28" width="70" height="34" rx="6" fill="white" opacity=".07"/>
        <rect x="105" y="28" width="70" height="34" rx="6" fill="white" opacity=".07"/>
        <circle cx="55" cy="45" r="10" fill="white" opacity=".15"/>
        <circle cx="140" cy="45" r="10" fill="white" opacity=".15"/>
        <circle cx="55" cy="45" r="4" fill="white" opacity=".3"/>
        <circle cx="140" cy="45" r="4" fill="white" opacity=".3"/>
        <line x1="90" y1="30" x2="90" y2="60" stroke="white" stroke-width="1" opacity=".15"/>
        <line x1="100" y1="30" x2="100" y2="60" stroke="white" stroke-width="1" opacity=".15"/>
      </svg>
      <div class="cat-content">
        <span class="cat-icon">💧</span>
        <div class="cat-name">Esterilización y Desinfección</div>
        <div class="cat-n">1.580 referencias</div>
      </div>
      <div class="cat-arrow">→</div>
    </div>

    <!-- 6. Radiología -->
    <div class="cat cat-6 cbg-6">
      <div class="cat-bg cbg-6"></div>
      <div class="cat-overlay"></div>
      <svg class="cat-illo" width="90" height="90" viewBox="0 0 90 90" fill="none">
        <circle cx="45" cy="45" r="35" fill="none" stroke="white" stroke-width="1" opacity=".2"/>
        <circle cx="45" cy="45" r="22" fill="none" stroke="white" stroke-width="1" opacity=".2"/>
        <circle cx="45" cy="45" r="8" fill="white" opacity=".3"/>
        <line x1="45" y1="10" x2="45" y2="20" stroke="white" stroke-width="2" opacity=".4"/>
        <line x1="45" y1="70" x2="45" y2="80" stroke="white" stroke-width="2" opacity=".4"/>
        <line x1="10" y1="45" x2="20" y2="45" stroke="white" stroke-width="2" opacity=".4"/>
        <line x1="70" y1="45" x2="80" y2="45" stroke="white" stroke-width="2" opacity=".4"/>
      </svg>
      <div class="cat-content">
        <span class="cat-icon">📡</span>
        <div class="cat-name">Radiología Digital</div>
        <div class="cat-n">420 referencias</div>
      </div>
      <div class="cat-arrow">→</div>
    </div>

    <!-- 7. Equipamiento -->
    <div class="cat cat-7 cbg-7">
      <div class="cat-bg cbg-7"></div>
      <div class="cat-overlay"></div>
      <svg class="cat-illo" width="120" height="90" viewBox="0 0 120 90" fill="none">
        <rect x="15" y="20" width="90" height="50" rx="8" fill="none" stroke="white" stroke-width="1" opacity=".25"/>
        <rect x="25" y="30" width="30" height="30" rx="4" fill="white" opacity=".08"/>
        <rect x="65" y="30" width="30" height="12" rx="3" fill="white" opacity=".08"/>
        <rect x="65" y="48" width="30" height="12" rx="3" fill="white" opacity=".08"/>
        <circle cx="40" cy="45" r="8" fill="none" stroke="white" stroke-width="1.5" opacity=".3"/>
        <circle cx="40" cy="45" r="3" fill="white" opacity=".4"/>
      </svg>
      <div class="cat-content">
        <span class="cat-icon">⚙️</span>
        <div class="cat-name">Equipamiento Clínico</div>
        <div class="cat-n">760 referencias</div>
      </div>
      <div class="cat-arrow">→</div>
    </div>

  </div>
</section>

<!-- ═══ MARCAS ═════════════════════════════════════════════════ -->
<div class="sec-brands">
  <div class="brands-head">Marcas oficiales disponibles en catálogo</div>
  <div class="brands-row">
    <div class="brand"><div class="brand-name">Hu-Friedy<small>Instrumental</small></div></div>
    <div class="brand"><div class="brand-name">NSK<small>Equipamiento</small></div></div>
    <div class="brand"><div class="brand-name">Straumann<small>Implantología</small></div></div>
    <div class="brand"><div class="brand-name">Dentsply Sirona<small>Endodoncia</small></div></div>
    <div class="brand"><div class="brand-name">KaVo<small>Equipamiento</small></div></div>
    <div class="brand"><div class="brand-name">3M Oral Care<small>Restauración</small></div></div>
    <div class="brand"><div class="brand-name">GC America<small>Materiales</small></div></div>
    <div class="brand"><div class="brand-name">Ivoclar<small>Prótesis</small></div></div>
  </div>
</div>

<!-- ═══ PRODUCTOS DESTACADOS ═══════════════════════════════════ -->
<section class="sec-featured">
  <div class="sec-head">
    <div>
      <div class="sec-kicker">Selección del mes</div>
      <div class="sec-title">Más <em>vendidos</em></div>
    </div>
    <a class="sec-link" href="#">Ver catálogo →</a>
  </div>

  <div class="filter-pills">
    <div class="pill on">Más vendidos</div>
    <div class="pill">Novedades</div>
    <div class="pill">Ofertas activas</div>
    <div class="pill">Instrumental</div>
    <div class="pill">Implantología</div>
    <div class="pill">Endodoncia</div>
    <div class="pill">Equipamiento</div>
  </div>

  <div class="pgrid">

    <!-- Producto 1 -->
    <div class="pcard">
      <div class="pcard-img">
        <div class="pcard-img-inner pi-red">
          <svg class="prod-svg" viewBox="0 0 62 62" fill="none">
            <rect x="28" y="6" width="6" height="46" rx="3" fill="#C0392B" opacity=".18"/>
            <rect x="28" y="6" width="6" height="46" rx="3" fill="none" stroke="#2D2D2D" stroke-width="1.5"/>
            <ellipse cx="31" cy="9" rx="9" ry="5" fill="#2D2D2D" opacity=".15"/>
            <ellipse cx="31" cy="9" rx="9" ry="5" fill="none" stroke="#2D2D2D" stroke-width="1.5"/>
            <rect x="22" y="30" width="18" height="3" rx="1.5" fill="#2D2D2D" opacity=".4"/>
            <rect x="24" y="38" width="14" height="3" rx="1.5" fill="#2D2D2D" opacity=".3"/>
            <rect x="26" y="46" width="10" height="2.5" rx="1.25" fill="#2D2D2D" opacity=".25"/>
          </svg>
        </div>
        <span class="pb pb-top">Más vendido</span>
        <span class="p-wish">♡</span>
      </div>
      <div class="pcard-body">
        <div class="p-sku">REF. HF-4240</div>
        <div class="p-name">Pinza Hemostática Allis 18cm Acero Inoxidable</div>
        <div class="p-brand">Hu-Friedy · Instrumental Quirúrgico</div>
        <div class="pcard-foot">
          <div class="p-price">
            <s>38,50 €</s>
            29,90 € <span class="p-unit">/ ud</span>
          </div>
          <button class="add-btn">+</button>
        </div>
      </div>
    </div>

    <!-- Producto 2 -->
    <div class="pcard">
      <div class="pcard-img">
        <div class="pcard-img-inner pi-blue">
          <svg class="prod-svg" viewBox="0 0 62 62" fill="none">
            <circle cx="31" cy="31" r="14" fill="none" stroke="#1A1A2E" stroke-width="2" opacity=".15"/>
            <circle cx="31" cy="31" r="7" fill="#1A1A2E" opacity=".12"/>
            <circle cx="31" cy="31" r="3" fill="#2D2D2D" opacity=".5"/>
            <line x1="31" y1="10" x2="31" y2="18" stroke="#2D2D2D" stroke-width="2.5" stroke-linecap="round" opacity=".4"/>
            <line x1="31" y1="44" x2="31" y2="52" stroke="#2D2D2D" stroke-width="2.5" stroke-linecap="round" opacity=".4"/>
            <line x1="10" y1="31" x2="18" y2="31" stroke="#2D2D2D" stroke-width="2.5" stroke-linecap="round" opacity=".4"/>
            <line x1="44" y1="31" x2="52" y2="31" stroke="#2D2D2D" stroke-width="2.5" stroke-linecap="round" opacity=".4"/>
          </svg>
        </div>
        <span class="pb pb-new">Nuevo</span>
        <span class="p-wish">♡</span>
      </div>
      <div class="pcard-body">
        <div class="p-sku">REF. NSK-P900L</div>
        <div class="p-name">Turbina Alta Velocidad con Luz LED P900L</div>
        <div class="p-brand">NSK · Equipamiento Clínico</div>
        <div class="pcard-foot">
          <div class="p-price">189,00 € <span class="p-unit">/ ud</span></div>
          <button class="add-btn">+</button>
        </div>
      </div>
    </div>

    <!-- Producto 3 -->
    <div class="pcard">
      <div class="pcard-img">
        <div class="pcard-img-inner pi-green">
          <svg class="prod-svg" viewBox="0 0 62 62" fill="none">
            <rect x="27" y="4" width="8" height="32" rx="4" fill="#1E3020" opacity=".15"/>
            <rect x="27" y="4" width="8" height="32" rx="4" fill="none" stroke="#2D2D2D" stroke-width="1.5"/>
            <rect x="20" y="10" width="22" height="5" rx="2.5" fill="#2D2D2D" opacity=".2"/>
            <rect x="20" y="20" width="22" height="5" rx="2.5" fill="#2D2D2D" opacity=".2"/>
            <rect x="20" y="30" width="22" height="5" rx="2.5" fill="#2D2D2D" opacity=".2"/>
            <path d="M18 36 Q31 58 44 36" fill="#27AE60" opacity=".15"/>
            <path d="M18 36 Q31 58 44 36" fill="none" stroke="#2D2D2D" stroke-width="1.5" opacity=".4"/>
          </svg>
        </div>
        <span class="pb pb-off">-22%</span>
        <span class="p-wish">♡</span>
      </div>
      <div class="pcard-body">
        <div class="p-sku">REF. STR-BL-RC-335</div>
        <div class="p-name">Implante Bone Level RC Ø3.3mm · Conexión Cónica</div>
        <div class="p-brand">Straumann · Implantología</div>
        <div class="pcard-foot">
          <div class="p-price">
            <s>340,00 €</s>
            265,00 € <span class="p-unit">/ ud</span>
          </div>
          <button class="add-btn">+</button>
        </div>
      </div>
    </div>

    <!-- Producto 4 -->
    <div class="pcard">
      <div class="pcard-img">
        <div class="pcard-img-inner pi-amber">
          <svg class="prod-svg" viewBox="0 0 62 62" fill="none">
            <rect x="29" y="5" width="4" height="48" rx="2" fill="#2A1A1A" opacity=".2"/>
            <rect x="29" y="5" width="4" height="48" rx="2" fill="none" stroke="#2D2D2D" stroke-width="1.5"/>
            <path d="M24 12 L38 12" stroke="#2D2D2D" stroke-width="1.5" stroke-linecap="round" opacity=".4"/>
            <path d="M22 20 L40 20" stroke="#2D2D2D" stroke-width="1.5" stroke-linecap="round" opacity=".35"/>
            <path d="M24 28 L38 28" stroke="#2D2D2D" stroke-width="1.5" stroke-linecap="round" opacity=".3"/>
            <path d="M24 36 L38 36" stroke="#2D2D2D" stroke-width="1.5" stroke-linecap="round" opacity=".25"/>
            <ellipse cx="31" cy="8" rx="7" ry="4" fill="#E67E22" opacity=".2"/>
            <ellipse cx="31" cy="8" rx="7" ry="4" fill="none" stroke="#2D2D2D" stroke-width="1.2"/>
          </svg>
        </div>
        <span class="p-wish">♡</span>
      </div>
      <div class="pcard-body">
        <div class="p-sku">REF. DS-PROTAPER-G</div>
        <div class="p-name">Limas ProTaper Gold Rotatorias F1-F3 Endodoncia</div>
        <div class="p-brand">Dentsply Sirona · Endodoncia</div>
        <div class="pcard-foot">
          <div class="p-price">48,50 € <span class="p-unit">/ caja 6 ud</span></div>
          <button class="add-btn">+</button>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- ═══ BANNER OFERTA ══════════════════════════════════════════ -->
<div class="banner">
  <div class="banner-left">
    <div class="banner-kicker">Oferta especial de temporada</div>
    <div class="banner-h">Kits de <em>implantología</em><br>Straumann con descuento</div>
    <div class="banner-desc">
      Tarifas negociadas directamente con fabricante.
      Disponible para clínicas con acuerdo de volumen.
      Stock limitado — aprovecha ahora.
    </div>
  </div>
  <div class="banner-right">
    <div class="banner-disc">
      <div class="banner-disc-from">Hasta un</div>
      <div class="banner-disc-n">-22<span>%</span></div>
      <div class="banner-disc-sub">en kits seleccionados</div>
    </div>
    <button class="btn-red" style="justify-content:center">Ver oferta completa →</button>
    <button class="btn-outline-d" style="text-align:center">Solicitar tarifa especial</button>
  </div>
</div>

<!-- ═══ NOVEDADES ══════════════════════════════════════════════ -->
<section class="sec-new">
  <div class="sec-head">
    <div>
      <div class="sec-kicker">Recién llegados al catálogo</div>
      <div class="sec-title">Últimas <em>incorporaciones</em></div>
    </div>
    <a class="sec-link" href="#">Ver novedades →</a>
  </div>

  <div class="pgrid-3">

    <div class="pcard">
      <div class="pcard-img">
        <div class="pcard-img-inner pi-blue">
          <svg class="prod-svg" viewBox="0 0 62 62" fill="none">
            <rect x="24" y="8" width="14" height="38" rx="4" fill="#1A1A2E" opacity=".1"/>
            <rect x="24" y="8" width="14" height="38" rx="4" fill="none" stroke="#2D2D2D" stroke-width="1.5"/>
            <circle cx="31" cy="22" r="7" fill="none" stroke="#2D2D2D" stroke-width="1.2" opacity=".4"/>
            <circle cx="31" cy="22" r="3" fill="#2D2D2D" opacity=".3"/>
            <rect x="18" y="44" width="26" height="10" rx="3" fill="#2D2D2D" opacity=".1"/>
            <rect x="18" y="44" width="26" height="10" rx="3" fill="none" stroke="#2D2D2D" stroke-width="1.2" opacity=".5"/>
          </svg>
        </div>
        <span class="pb pb-new">Nuevo</span>
        <span class="p-wish">♡</span>
      </div>
      <div class="pcard-body">
        <div class="p-sku">REF. KV-OPTN-20</div>
        <div class="p-name">Microscopio Dental Operación 20x con Cámara 4K integrada</div>
        <div class="p-brand">KaVo · Equipamiento</div>
        <div class="pcard-foot">
          <div class="p-price">4.200,00 € <span class="p-unit">/ ud</span></div>
          <button class="add-btn">+</button>
        </div>
      </div>
    </div>

    <div class="pcard">
      <div class="pcard-img">
        <div class="pcard-img-inner pi-green">
          <svg class="prod-svg" viewBox="0 0 62 62" fill="none">
            <rect x="20" y="18" width="22" height="28" rx="6" fill="#1E3020" opacity=".1"/>
            <rect x="20" y="18" width="22" height="28" rx="6" fill="none" stroke="#2D2D2D" stroke-width="1.5"/>
            <rect x="25" y="10" width="12" height="10" rx="3" fill="none" stroke="#2D2D2D" stroke-width="1.2" opacity=".5"/>
            <circle cx="31" cy="32" r="5" fill="#27AE60" opacity=".25"/>
            <circle cx="31" cy="32" r="5" fill="none" stroke="#2D2D2D" stroke-width="1.2" opacity=".5"/>
          </svg>
        </div>
        <span class="pb pb-new">Nuevo</span>
        <span class="p-wish">♡</span>
      </div>
      <div class="pcard-body">
        <div class="p-sku">REF. 3M-FIL-SUFLOW-A2</div>
        <div class="p-name">Composite Filtek Supreme Ultra Flow Jeringa 3M</div>
        <div class="p-brand">3M Oral Care · Restauración</div>
        <div class="pcard-foot">
          <div class="p-price">38,90 € <span class="p-unit">/ jeringa</span></div>
          <button class="add-btn">+</button>
        </div>
      </div>
    </div>

    <div class="pcard">
      <div class="pcard-img">
        <div class="pcard-img-inner pi-red">
          <svg class="prod-svg" viewBox="0 0 62 62" fill="none">
            <rect x="22" y="12" width="18" height="28" rx="5" fill="#1C1A1A" opacity=".1"/>
            <rect x="22" y="12" width="18" height="28" rx="5" fill="none" stroke="#2D2D2D" stroke-width="1.5"/>
            <rect x="27" y="8" width="8" height="6" rx="2" fill="none" stroke="#2D2D2D" stroke-width="1.2" opacity=".5"/>
            <rect x="26" y="40" width="10" height="10" rx="3" fill="#C0392B" opacity=".2"/>
            <rect x="26" y="40" width="10" height="10" rx="3" fill="none" stroke="#2D2D2D" stroke-width="1.2" opacity=".5"/>
            <line x1="22" y1="22" x2="40" y2="22" stroke="#2D2D2D" stroke-width="1" opacity=".25"/>
            <line x1="22" y1="28" x2="40" y2="28" stroke="#2D2D2D" stroke-width="1" opacity=".2"/>
            <line x1="22" y1="34" x2="40" y2="34" stroke="#2D2D2D" stroke-width="1" opacity=".15"/>
          </svg>
        </div>
        <span class="pb pb-new">Nuevo</span>
        <span class="p-wish">♡</span>
      </div>
      <div class="pcard-body">
        <div class="p-sku">REF. IVL-E2-A1-14</div>
        <div class="p-name">Cerámica IPS e.max CAD Bloque CAD/CAM Ivoclar A1</div>
        <div class="p-brand">Ivoclar · Prótesis Digital</div>
        <div class="pcard-foot">
          <div class="p-price">29,50 € <span class="p-unit">/ bloque</span></div>
          <button class="add-btn">+</button>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- ═══ COMPRA RÁPIDA POR REFERENCIA ══════════════════════════ -->
<div class="sec-qref">
  <div class="qref-left">
    <h3>Pedido rápido por referencia</h3>
    <p>¿Sabes el código SKU del producto? Añádelo directamente sin buscar por el catálogo.</p>
  </div>
  <div class="qref-form">
    <input class="qref-input" placeholder="Escribe el código de referencia o SKU exacto…">
    <input class="qref-input qref-input-qty" placeholder="Cant.">
    <button class="qref-btn">Añadir al carrito →</button>
  </div>
</div>

<!-- ═══ POR QUÉ DENTIX ════════════════════════════════════════ -->
<section class="sec-why">
  <div class="sec-head" style="margin-bottom:0;position:relative;z-index:1">
    <div>
      <div class="sec-kicker">Nuestra propuesta de valor</div>
      <div class="sec-title">Por qué eligen <em style="color:var(--red)">Dentix</em></div>
    </div>
  </div>
  <div class="why-grid">
    <div class="why-card">
      <div class="why-icon-wrap">🏭</div>
      <div class="why-h">Distribuidor oficial</div>
      <div class="why-p">Producto 100% original con garantía de fabricante. Trabajamos directamente con los líderes mundiales del instrumental odontológico.</div>
    </div>
    <div class="why-card">
      <div class="why-icon-wrap">🚀</div>
      <div class="why-h">Entrega 24–48h</div>
      <div class="why-p">Almacén propio en España. Más del 95% de pedidos salen el mismo día antes de las 16h. Envío gratuito a partir de 150€.</div>
    </div>
    <div class="why-card">
      <div class="why-icon-wrap">🔄</div>
      <div class="why-h">Stock en tiempo real</div>
      <div class="why-p">Catálogo sincronizado con nuestro ERP SAGE 50 Cloud. Lo que ves disponible, está disponible de verdad.</div>
    </div>
    <div class="why-card">
      <div class="why-icon-wrap">🎯</div>
      <div class="why-h">Soporte especializado</div>
      <div class="why-p">Nuestro equipo conoce el sector. Asesoramiento técnico para clínicas, hospitales y profesionales autónomos del sector dental.</div>
    </div>
  </div>
</section>

<!-- ═══ TESTIMONIOS ════════════════════════════════════════════ -->
<section class="sec-testi">
  <div class="sec-head">
    <div>
      <div class="sec-kicker">Opiniones verificadas</div>
      <div class="sec-title">Profesionales que <em>confían en Dentix</em></div>
    </div>
  </div>

  <div class="tgrid">
    <div class="tcard">
      <div class="tcard-stars">★★★★★</div>
      <div class="tcard-q">Llevamos 8 años comprando instrumental Hu-Friedy a Dentix. La calidad es impecable y el servicio de entrega nunca falla. Totalmente recomendable.</div>
      <div class="tcard-author">
        <div class="tcard-av">MR</div>
        <div>
          <div class="tcard-name">Dra. María Rodríguez</div>
          <div class="tcard-role">Clínica Sonrisa · Madrid</div>
        </div>
        <div class="tcard-spec">Implantología</div>
      </div>
    </div>
    <div class="tcard">
      <div class="tcard-stars">★★★★★</div>
      <div class="tcard-q">Lo mejor es que el stock siempre es real. Con otros proveedores teníamos problemas de rotura. Desde que trabajamos con Dentix ese problema desapareció.</div>
      <div class="tcard-author">
        <div class="tcard-av">JL</div>
        <div>
          <div class="tcard-name">Dr. Javier López</div>
          <div class="tcard-role">Dental Plus · Barcelona</div>
        </div>
        <div class="tcard-spec">Endodoncia</div>
      </div>
    </div>
    <div class="tcard">
      <div class="tcard-stars">★★★★★</div>
      <div class="tcard-q">El equipo me asesoró perfectamente para elegir el sistema de implantes adecuado para mi caso clínico. Un servicio que va mucho más allá de vender.</div>
      <div class="tcard-author">
        <div class="tcard-av">AC</div>
        <div>
          <div class="tcard-name">Dra. Ana Campos</div>
          <div class="tcard-role">Oral Healthy · Sevilla</div>
        </div>
        <div class="tcard-spec">Cirugía oral</div>
      </div>
    </div>
  </div>
</section>

<!-- ═══ CTA ACCESO PROFESIONAL ════════════════════════════════ -->
<div class="sec-cta">
  <div class="cta-left">
    <div class="cta-kicker">Acceso exclusivo B2B · Solo profesionales</div>
    <div class="cta-h">¿Eres profesional<br>del sector dental?</div>
    <div class="cta-p">Regístrate y accede a precios exclusivos para clínicas, tarifas por volumen, historial completo de pedidos y sincronización con tu sistema de gestión.</div>
    <div class="cta-checks">
      <span class="cta-check">Precios B2B exclusivos</span>
      <span class="cta-check">Tarifas por volumen</span>
      <span class="cta-check">Facturación automática</span>
      <span class="cta-check">Historial de pedidos</span>
    </div>
  </div>
  <div class="cta-right">
    <button class="btn-white">Solicitar acceso profesional →</button>
    <button class="btn-wghost">Ya tengo cuenta · Entrar</button>
  </div>
</div>

<!-- ═══ FOOTER ═════════════════════════════════════════════════ -->

<!-- ═══ PRODUCTOS DESTACADOS (WooCommerce dinámico) ══════════ -->
<?php
$featured_products = wc_get_products([
    'limit'    => 8,
    'featured' => true,
    'status'   => 'publish',
    'orderby'  => 'date',
    'order'    => 'DESC',
]);
if (!empty($featured_products)) :
?>
<section class="sec-featured">
  <div class="sec-header">
    <div class="sec-pre">Selección Dentix</div>
    <h2 class="sec-title">Productos destacados</h2>
  </div>
  <div class="filter-pills">
    <div class="pill on">Todos</div>
    <?php
    $shop_cats = get_terms(['taxonomy' => 'product_cat', 'number' => 6, 'hide_empty' => true]);
    foreach ($shop_cats as $cat) {
        printf('<div class="pill" data-cat="%s">%s</div>', esc_attr($cat->slug), esc_html($cat->name));
    }
    ?>
  </div>
  <div class="prod-grid">
    <?php foreach ($featured_products as $product) :
      $price = $product->get_price_html();
      $sku   = $product->get_sku();
      $brand_terms = wp_get_post_terms($product->get_id(), 'pa_marca');
      $brand = !empty($brand_terms) && !is_wp_error($brand_terms) ? $brand_terms[0]->name : get_bloginfo('name');
      $img   = get_the_post_thumbnail_url($product->get_id(), 'dentix-product-thumb');
    ?>
    <a href="<?php echo get_permalink($product->get_id()); ?>" class="prod-card">
      <div class="prod-img">
        <?php if ($img) : ?>
          <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($product->get_name()); ?>" loading="lazy">
        <?php else : ?>
          <div class="prod-circle">
            <svg class="prod-svg" viewBox="0 0 64 64" fill="none" stroke="var(--gray-mid)" stroke-width="1.5">
              <rect x="16" y="20" width="32" height="24" rx="3"/>
              <line x1="24" y1="28" x2="40" y2="28"/><line x1="24" y1="34" x2="32" y2="34"/>
            </svg>
          </div>
        <?php endif; ?>
        <button class="p-wish" aria-label="Favorito">♡</button>
        <?php if ($product->is_on_sale()) echo '<span class="p-badge sale">Oferta</span>'; ?>
        <?php if ($product->is_featured())  echo '<span class="p-badge new">Destacado</span>'; ?>
      </div>
      <div class="prod-body">
        <div class="p-brand"><?php echo esc_html($brand); ?></div>
        <div class="p-name"><?php echo esc_html($product->get_name()); ?></div>
        <?php if ($sku) echo '<div class="p-ref">REF: ' . esc_html($sku) . '</div>'; ?>
        <div class="p-footer">
          <div class="p-price">
            <span class="p-price-main"><?php echo $price; ?></span>
          </div>
          <button class="p-add"
            data-product-id="<?php echo $product->get_id(); ?>"
            aria-label="Añadir al carrito">+</button>
        </div>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<?php get_footer(); ?>
