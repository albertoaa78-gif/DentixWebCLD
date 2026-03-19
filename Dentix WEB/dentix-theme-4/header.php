<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- ═══ TOPBAR ══════════════════════════════════════════════════ -->
<div class="topbar">
  <div style="display:flex;align-items:center;gap:20px">
    <span>📞 Atención profesional:
      <a href="tel:<?php echo esc_attr(get_theme_mod('dentix_phone', '900123456')); ?>">
        <?php echo esc_html(get_theme_mod('dentix_phone', '900 123 456')); ?>
      </a>
    </span>
    <span style="color:rgba(174,172,170,0.4)">|</span>
    <span>Lunes–Viernes · 9h–18h</span>
  </div>
  <div class="topbar-right">
    <div class="topbar-pill">
      <span class="dot"></span>Stock en tiempo real · SAGE 50
    </div>
    <span>🚚 Envío gratuito
      +<?php echo esc_html(get_option('woocommerce_free_shipping_min_amount', '150')); ?>€
      · Entrega 24/48h
    </span>
    <span>🔒 Acceso exclusivo B2B</span>
  </div>
</div>

<!-- ═══ HEADER ═══════════════════════════════════════════════════ -->
<header>

  <!-- Logo -->
  <a href="<?php echo esc_url(home_url('/')); ?>" class="logo" aria-label="<?php bloginfo('name'); ?>">
    <?php dentix_logo_svg(56, 56); ?>
    <div class="logo-text">
      <span class="logo-name">dentix</span>
      <div class="logo-rule"></div>
      <div class="logo-rule2"></div>
      <span class="logo-sub">Productos dentales, S.L.</span>
    </div>
  </a>

  <!-- Buscador -->
  <div class="search-block">
    <div class="search-outer">
      <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
        <input type="search"
               name="s"
               placeholder="<?php esc_attr_e('Buscar por nombre, referencia o SKU…', 'dentix'); ?>"
               value="<?php echo esc_attr(get_search_query()); ?>"
               autocomplete="off"
               id="dentixSearch">
        <input type="hidden" name="post_type" value="product">
        <button type="submit" class="search-btn" aria-label="Buscar">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
          </svg>
          <?php esc_html_e('Buscar', 'dentix'); ?>
        </button>
      </form>
    </div>
    <!-- Dropdown sugerencias -->
    <div class="search-hint" id="searchHint">
      <span class="search-hint-label">Búsquedas frecuentes</span>
      <span class="search-tag">Lima Endodoncia</span>
      <span class="search-tag">Implante Straumann</span>
      <span class="search-tag">Turbina NSK</span>
      <span class="search-tag">Composite 3M</span>
      <span class="search-tag">Pinza Hu-Friedy</span>
    </div>
  </div>

  <!-- Iconos de cuenta, pedidos y carrito -->
  <div class="hicons">

    <?php if (is_user_logged_in()) : ?>
      <a href="<?php echo esc_url(wc_get_account_endpoint_url('dashboard')); ?>" class="hicon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
          <circle cx="12" cy="7" r="4"/>
        </svg>
        <?php esc_html_e('Mi cuenta', 'dentix'); ?>
      </a>
      <a href="<?php echo esc_url(wc_get_account_endpoint_url('orders')); ?>" class="hicon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
          <rect x="9" y="3" width="6" height="4" rx="1"/>
        </svg>
        <?php esc_html_e('Pedidos', 'dentix'); ?>
      </a>
    <?php else : ?>
      <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="hicon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
          <circle cx="12" cy="7" r="4"/>
        </svg>
        <?php esc_html_e('Mi cuenta', 'dentix'); ?>
      </a>
    <?php endif; ?>

    <!-- Carrito -->
    <a href="<?php echo esc_url(dentix_cart_url()); ?>" class="hicon">
      <div class="badge" id="cartBadge"><?php echo dentix_cart_count(); ?></div>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
      </svg>
      <?php esc_html_e('Cesta', 'dentix'); ?>
    </a>

    <div class="header-divider"></div>

    <?php if (is_user_logged_in()) : ?>
      <a href="<?php echo esc_url(wc_get_account_endpoint_url('dashboard')); ?>" class="btn-access">
        <?php esc_html_e('Área privada →', 'dentix'); ?>
      </a>
    <?php else : ?>
      <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="btn-access">
        <?php esc_html_e('Acceso profesional →', 'dentix'); ?>
      </a>
    <?php endif; ?>

  </div>
</header>

<!-- ═══ NAV ══════════════════════════════════════════════════════ -->
<nav>
  <?php
  // Categorías principales de WooCommerce como nav items
  $nav_categories = [
    ['slug' => 'instrumental',    'label' => 'Instrumental'],
    ['slug' => 'endodoncia',      'label' => 'Endodoncia'],
    ['slug' => 'ortodoncia',      'label' => 'Ortodoncia'],
    ['slug' => 'implantologia',   'label' => 'Implantología'],
    ['slug' => 'material-clinico','label' => 'Material Clínico'],
  ];
  $nav_categories2 = [
    ['slug' => 'esterilizacion',  'label' => 'Esterilización'],
    ['slug' => 'equipamiento',    'label' => 'Equipamiento'],
    ['slug' => 'radiologia',      'label' => 'Radiología'],
    ['slug' => 'marcas',          'label' => 'Marcas'],
  ];
  $current_cat = is_product_category() ? get_queried_object()->slug : '';

  foreach ($nav_categories as $cat) :
    $term = get_term_by('slug', $cat['slug'], 'product_cat');
    $url  = $term ? get_term_link($term) : get_permalink(wc_get_page_id('shop')) . '?cat=' . $cat['slug'];
    $active = ($current_cat === $cat['slug']) ? ' on' : '';
    printf('<a href="%s" class="nav-item%s">%s</a>', esc_url($url), $active, esc_html($cat['label']));
  endforeach;
  ?>
  <div class="nav-sep"></div>
  <?php
  foreach ($nav_categories2 as $cat) :
    $term = get_term_by('slug', $cat['slug'], 'product_cat');
    $url  = $term ? get_term_link($term) : get_permalink(wc_get_page_id('shop')) . '?cat=' . $cat['slug'];
    $active = ($current_cat === $cat['slug']) ? ' on' : '';
    printf('<a href="%s" class="nav-item%s">%s</a>', esc_url($url), $active, esc_html($cat['label']));
  endforeach;
  ?>
  <div class="nav-end">
    <?php
    $ofertas = get_term_by('slug', 'ofertas', 'product_cat');
    $oferta_url = $ofertas ? get_term_link($ofertas) : get_permalink(wc_get_page_id('shop')) . '?cat=ofertas';
    ?>
    <a href="<?php echo esc_url($oferta_url); ?>" class="nav-special">
      🔥 <?php esc_html_e('Ofertas del mes', 'dentix'); ?>
    </a>
  </div>
</nav>
