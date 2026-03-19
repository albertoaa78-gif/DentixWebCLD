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
      <a href="tel:<?php echo esc_attr(preg_replace('/\s+/', '', dentix_phone())); ?>">
        <?php echo esc_html(dentix_phone()); ?>
      </a>
    </span>
    <span style="color:rgba(174,172,170,0.4)">|</span>
    <span>Lunes–Viernes · 9h–18h</span>
  </div>
  <div class="topbar-right">
    <div class="topbar-pill"><span class="dot"></span>Stock en tiempo real · SAGE 50</div>
    <span>🚚 Envío gratuito +<?php echo esc_html(dentix_free_ship()); ?>€ · Entrega 24/48h</span>
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
        <input type="search" name="s"
               placeholder="Buscar por nombre, referencia o SKU…"
               value="<?php echo esc_attr(get_search_query()); ?>"
               autocomplete="off">
        <input type="hidden" name="post_type" value="product">
        <button type="submit" class="search-btn" aria-label="Buscar">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
          </svg>
          Buscar
        </button>
      </form>
    </div>
    <!-- Sugerencias rápidas -->
    <div class="search-hint" id="searchHint">
      <span class="search-hint-label">Búsquedas frecuentes</span>
      <?php foreach (dentix_get_categories() as $cat) : ?>
        <a href="<?php echo esc_url(home_url('/?s=' . urlencode($cat['label']) . '&post_type=product')); ?>"
           class="search-tag"><?php echo esc_html($cat['label']); ?></a>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Iconos header -->
  <div class="hicons">
    <?php if (function_exists('wc_get_page_permalink')) : ?>
      <?php if (is_user_logged_in()) : ?>
        <a href="<?php echo esc_url(wc_get_account_endpoint_url('dashboard')); ?>" class="hicon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="20" height="20"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          Mi cuenta
        </a>
        <a href="<?php echo esc_url(wc_get_account_endpoint_url('orders')); ?>" class="hicon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="20" height="20"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
          Pedidos
        </a>
      <?php else : ?>
        <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="hicon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="20" height="20"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          Mi cuenta
        </a>
      <?php endif; ?>
      <a href="<?php echo esc_url(dentix_cart_url()); ?>" class="hicon">
        <div class="badge" id="cartBadge"><?php echo dentix_cart_count(); ?></div>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="20" height="20"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
        Cesta
      </a>
      <div class="header-divider"></div>
      <a href="<?php echo is_user_logged_in() ? esc_url(wc_get_account_endpoint_url('dashboard')) : esc_url(wc_get_page_permalink('myaccount')); ?>"
         class="btn-access">
        <?php echo is_user_logged_in() ? 'Área privada →' : 'Acceso profesional →'; ?>
      </a>
    <?php endif; ?>
  </div>
</header>

<!-- ═══ NAV ══════════════════════════════════════════════════════ -->
<nav>
  <?php
  // Categorías activas en el nav (configuradas en Apariencia → Dentix)
  $cats_in_nav = dentix_opt('cats_in_nav', array_column(dentix_get_categories(), 'slug'));
  $current_cat = (is_product_category()) ? get_queried_object()->slug : '';

  foreach (dentix_get_categories() as $cat) :
    if (!in_array($cat['slug'], (array)$cats_in_nav)) continue;
    $term = get_term_by('slug', $cat['slug'], 'product_cat');
    $url  = $term ? get_term_link($term) : home_url('/tienda/?cat=' . $cat['slug']);
    $active = ($current_cat === $cat['slug']) ? ' on' : '';
    printf('<a href="%s" class="nav-item%s">%s</a>', esc_url($url), $active, esc_html($cat['label']));
  endforeach;
  ?>
  <div class="nav-sep"></div>
  <div class="nav-end">
    <?php
    $ofertas_term = get_term_by('slug', 'ofertas', 'product_cat');
    // Mostrar ofertas si existe la categoría y tiene productos
    if ($ofertas_term && $ofertas_term->count > 0) :
    ?>
      <a href="<?php echo esc_url(get_term_link($ofertas_term)); ?>" class="nav-special">
        🔥 Ofertas del mes
      </a>
    <?php endif; ?>
  </div>
</nav>
