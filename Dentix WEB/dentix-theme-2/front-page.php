<?php
/**
 * front-page.php — Homepage Dentix v5.5
 * Carrusel original din8 + especialidades dinámicas de WooCommerce
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

<?php if (dentix_opt('show_cats', 1)) :
  $wc_cats = dentix_get_wc_categories();
  if (!empty($wc_cats)) : ?>

<!-- ═══ ESPECIALIDADES ══════════════════════════════════════════ -->
<section class="sec-cats">
  <div class="sec-header">
    <div class="sec-pre">Catálogo organizado por especialidad</div>
    <h2 class="sec-title">Elige tu especialidad</h2>
    <p class="sec-desc">
      <?php echo esc_html(get_option('woocommerce_catalog_count', '2.500')); ?> referencias
      distribuidas en <?php echo count($wc_cats); ?> especialidades odontológicas.
    </p>
  </div>
  <div class="cats-grid">
    <?php foreach ($wc_cats as $cat) : ?>
      <a href="<?php echo esc_url($cat['url']); ?>" class="cat-card">
        <div class="cat-icon-wrap" style="background:<?php echo esc_attr($cat['color']); ?>">
          <?php if (!empty($cat['image'])) : ?>
            <img src="<?php echo esc_url($cat['image']); ?>"
                 alt="<?php echo esc_attr($cat['label']); ?>"
                 class="cat-img">
          <?php else : ?>
            <span class="cat-icon"><?php echo $cat['icon']; ?></span>
          <?php endif; ?>
        </div>
        <div class="cat-info">
          <div class="cat-name"><?php echo esc_html($cat['label']); ?></div>
          <div class="cat-desc"><?php echo esc_html($cat['desc']); ?></div>
          <?php if ($cat['count'] > 0) : ?>
            <div class="cat-count"><?php echo $cat['count']; ?> referencias</div>
          <?php endif; ?>
        </div>
        <div class="cat-arrow">→</div>
      </a>
    <?php endforeach; ?>
  </div>
  <div style="text-align:center;margin-top:40px">
    <?php
    $shop_url = function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('shop')) : home_url('/tienda/');
    ?>
    <a href="<?php echo esc_url($shop_url); ?>" class="btn-catalog">
      Ver catálogo completo — todas las referencias
    </a>
  </div>
</section>

<?php endif; endif; ?>

<?php if (dentix_opt('show_featured', 1)) :
  $featured = function_exists('wc_get_products') ? wc_get_products([
    'limit'   => 8,
    'status'  => 'publish',
    'orderby' => 'date',
    'order'   => 'DESC',
  ]) : [];
  if (!empty($featured)) : ?>

<!-- ═══ PRODUCTOS RECIENTES ════════════════════════════════════ -->
<section class="sec-featured">
  <div class="sec-header">
    <div class="sec-pre">Selección Dentix</div>
    <h2 class="sec-title">Últimas incorporaciones</h2>
  </div>
  <div class="prod-grid">
    <?php foreach ($featured as $product) :
      $img        = get_the_post_thumbnail_url($product->get_id(), 'dentix-product-thumb');
      $sku        = $product->get_sku();
      $on_sale    = $product->is_on_sale();
      $reg_price  = $product->get_regular_price();
      $sale_price = $product->get_sale_price();
      $brand_terms = wp_get_post_terms($product->get_id(), 'pa_marca');
      $brand      = (!is_wp_error($brand_terms) && !empty($brand_terms)) ? $brand_terms[0]->name : '';
    ?>
      <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>" class="prod-card">
        <div class="prod-img">
          <?php if ($img) : ?>
            <img src="<?php echo esc_url($img); ?>"
                 alt="<?php echo esc_attr($product->get_name()); ?>" loading="lazy">
          <?php else : ?>
            <div class="prod-circle">
              <svg class="prod-svg" viewBox="0 0 64 64" fill="none" stroke="var(--gray-mid)" stroke-width="1.5">
                <rect x="16" y="20" width="32" height="24" rx="3"/>
                <line x1="24" y1="28" x2="40" y2="28"/><line x1="24" y1="34" x2="32" y2="34"/>
              </svg>
            </div>
          <?php endif; ?>
          <button class="p-wish" onclick="event.preventDefault()">♡</button>
          <?php if ($on_sale && $reg_price) :
            $disc = round((($reg_price - $sale_price) / $reg_price) * 100);
            echo '<span class="p-badge sale">−' . $disc . '%</span>';
          endif; ?>
        </div>
        <div class="prod-body">
          <?php if ($brand) : ?>
            <div class="p-brand"><?php echo esc_html($brand); ?></div>
          <?php endif; ?>
          <div class="p-name"><?php echo esc_html($product->get_name()); ?></div>
          <?php if ($sku) : ?>
            <div class="p-ref">REF: <?php echo esc_html($sku); ?></div>
          <?php endif; ?>
          <div class="p-footer">
            <div class="p-price">
              <span class="p-price-main"><?php echo $product->get_price_html(); ?></span>
              <span class="p-price-sub">IVA excl.</span>
            </div>
            <?php if ($product->is_in_stock()) : ?>
              <button class="p-add"
                data-product-id="<?php echo $product->get_id(); ?>"
                onclick="event.preventDefault();dentixAddToCart(<?php echo $product->get_id(); ?>,this)">
                +
              </button>
            <?php endif; ?>
          </div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
  <div style="text-align:center;margin-top:32px">
    <a href="<?php echo esc_url(function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('shop')) : home_url('/tienda/')); ?>"
       class="btn-catalog">
      Ver todo el catálogo →
    </a>
  </div>
</section>

<?php endif; endif; ?>

<?php get_footer(); ?>
