<?php
/**
 * Dentix Theme v1.5 — functions.php
 */
defined('ABSPATH') || exit;

// Cargar archivos de soporte
require get_template_directory() . '/inc/categories.php';
require get_template_directory() . '/inc/settings.php';
require get_template_directory() . '/inc/woocommerce-hooks.php';

// ══ 1. SETUP ════════════════════════════════════════════════════
add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form','comment-form','comment-list','gallery','caption','script','style']);
    add_theme_support('custom-logo', ['height'=>60,'width'=>200,'flex-height'=>true,'flex-width'=>true]);
    add_theme_support('woocommerce', [
        'thumbnail_image_width'         => 600,
        'gallery_thumbnail_image_width' => 120,
        'single_image_width'            => 800,
    ]);
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    register_nav_menus([
        'primary' => 'Menú principal',
        'footer'  => 'Menú footer',
    ]);
    add_image_size('dentix-product-thumb',   600,  600,  true);
    add_image_size('dentix-product-gallery', 800,  800,  true);
    add_image_size('dentix-cat-banner',      800,  400,  true);
    load_theme_textdomain('dentix', get_template_directory() . '/languages');
});

// ══ 2. WIDGETS ══════════════════════════════════════════════════
add_action('widgets_init', function () {
    register_sidebar([
        'name'          => 'Sidebar Tienda',
        'id'            => 'shop-sidebar',
        'before_widget' => '<div class="filter-box" id="%1$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4>',
        'after_title'   => '</h4>',
    ]);
});

// ══ 3. ENQUEUE ══════════════════════════════════════════════════
add_action('wp_enqueue_scripts', function () {
    $ver = '1.5.0';
    $uri = get_template_directory_uri();

    wp_enqueue_style('dentix-fonts',
        'https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap',
        [], null
    );
    wp_enqueue_style('dentix-main', $uri . '/assets/css/dentix.css', ['dentix-fonts'], $ver);

    if (function_exists('is_woocommerce') && (is_woocommerce() || is_cart() || is_checkout() || is_account_page())) {
        wp_enqueue_style('dentix-woo', $uri . '/assets/css/woocommerce.css', ['dentix-main'], $ver);
    }

    wp_enqueue_script('dentix-main', $uri . '/assets/js/dentix.js', [], $ver, true);

    if (is_front_page()) {
        wp_enqueue_script('dentix-carousel', $uri . '/assets/js/carousel.js', ['dentix-main'], $ver, true);
    }

    wp_localize_script('dentix-main', 'dentixVars', [
        'ajaxUrl'   => admin_url('admin-ajax.php'),
        'nonce'     => wp_create_nonce('dentix_nonce'),
        'cartUrl'   => function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cesta/'),
        'cartCount' => function_exists('WC') && WC()->cart ? WC()->cart->get_cart_contents_count() : 0,
    ]);
});

// Fix imágenes carrusel
add_action('wp_head', function () {
    if (!is_front_page()) return;
    $img = get_template_directory_uri() . '/assets/images/';
    echo '<style id="dentix-carousel-images">';
    for ($i = 1; $i <= 5; $i++) {
        echo ".hs{$i}{background-image:url('" . esc_url($img . "image{$i}.jpeg") . "')!important}";
    }
    echo '</style>' . "\n";
}, 99);

// ══ 4. BODY CLASSES ═════════════════════════════════════════════
add_filter('body_class', function ($classes) {
    if (!function_exists('is_woocommerce')) return $classes;
    if (is_woocommerce() || is_cart() || is_checkout() || is_account_page()) $classes[] = 'dentix-wc-page';
    if (is_shop() || is_product_category())  $classes[] = 'dentix-shop-page';
    if (is_product())   $classes[] = 'dentix-product-page';
    if (is_cart())      $classes[] = 'dentix-cart-page';
    if (is_checkout())  $classes[] = 'dentix-checkout-page';
    if (is_account_page()) $classes[] = 'dentix-account-page';
    return $classes;
});

// ══ 5. WOOCOMMERCE WRAPPERS ══════════════════════════════════════
remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action('woocommerce_after_main_content',  'woocommerce_output_content_wrapper_end', 10);
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
add_action('woocommerce_before_main_content', function () { echo '<div class="dentix-wc-wrap">'; }, 10);
add_action('woocommerce_after_main_content',  function () { echo '</div>'; }, 10);
add_filter('loop_shop_per_page', fn() => 24, 20);
add_filter('loop_shop_columns',  fn() => 3);

// ══ 6. HPOS COMPATIBILITY ════════════════════════════════════════
add_action('before_woocommerce_init', function () {
    if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
    }
});

// ══ 7. CHECKOUT — CAMPO NIF/CIF ══════════════════════════════════
add_filter('woocommerce_checkout_fields', function ($fields) {
    $fields['billing']['billing_nif'] = [
        'label'       => 'NIF / CIF',
        'placeholder' => 'B-12345678 / 12345678A',
        'required'    => true,
        'class'       => ['form-row-wide'],
        'priority'    => 35,
    ];
    if (isset($fields['billing']['billing_company'])) {
        $fields['billing']['billing_company']['required'] = true;
    }
    return $fields;
});

add_action('woocommerce_checkout_process', function () {
    if (empty($_POST['billing_nif'])) {
        wc_add_notice('<strong>NIF/CIF</strong> es obligatorio para acceso profesional.', 'error');
    }
});

add_action('woocommerce_checkout_update_order_meta', function ($order_id) {
    if (!empty($_POST['billing_nif'])) {
        update_post_meta($order_id, '_billing_nif', sanitize_text_field(strtoupper($_POST['billing_nif'])));
    }
});

add_action('woocommerce_admin_order_data_after_billing_address', function ($order) {
    $nif = get_post_meta($order->get_id(), '_billing_nif', true);
    if ($nif) echo '<p><strong>NIF/CIF:</strong> ' . esc_html($nif) . '</p>';
});

// ══ 8. AJAX CARRITO ══════════════════════════════════════════════
function dentix_ajax_add_to_cart() {
    check_ajax_referer('dentix_nonce', 'nonce');
    $product_id = absint($_POST['product_id'] ?? 0);
    $quantity   = absint($_POST['quantity']   ?? 1);
    if (!$product_id) wp_send_json_error(['message' => 'Producto no válido']);
    $result = WC()->cart->add_to_cart($product_id, $quantity);
    if ($result) {
        wp_send_json_success([
            'message'    => sprintf('<strong>%s</strong> añadido.', get_the_title($product_id)),
            'cart_count' => WC()->cart->get_cart_contents_count(),
        ]);
    } else {
        wp_send_json_error(['message' => 'No se pudo añadir.']);
    }
}
add_action('wp_ajax_dentix_add_to_cart',        'dentix_ajax_add_to_cart');
add_action('wp_ajax_nopriv_dentix_add_to_cart', 'dentix_ajax_add_to_cart');

// ══ 9. BUSCADOR — forzar post_type=product ═══════════════════════
add_action('pre_get_posts', function ($query) {
    if (is_admin() || !$query->is_main_query()) return;
    if ($query->is_search() && isset($_GET['post_type']) && $_GET['post_type'] === 'product') {
        $query->set('post_type', 'product');
    }
});
add_action('template_redirect', function () {
    if (is_search() && !get_query_var('post_type')) {
        $s = get_search_query();
        if ($s) { wp_redirect(home_url('/?s=' . urlencode($s) . '&post_type=product')); exit; }
    }
});

// ══ 10. FRAGMENTOS AJAX CARRITO (badge header) ═══════════════════
add_filter('woocommerce_add_to_cart_fragments', function ($fragments) {
    ob_start();
    echo '<span class="badge" id="cartBadge">' . WC()->cart->get_cart_contents_count() . '</span>';
    $fragments['#cartBadge'] = ob_get_clean();
    return $fragments;
});

// ══ 11. BREADCRUMB ═══════════════════════════════════════════════
function dentix_breadcrumb() {
    $items = [];
    $sep   = '<span aria-hidden="true">›</span>';
    $items[] = '<a href="' . esc_url(home_url('/')) . '">Inicio</a>';

    if (function_exists('is_woocommerce') && is_woocommerce()) {
        $shop_id  = function_exists('wc_get_page_id') ? wc_get_page_id('shop') : 0;
        $shop_url = $shop_id > 0 ? get_permalink($shop_id) : home_url('/tienda/');

        if (!is_shop()) {
            $items[] = '<a href="' . esc_url($shop_url) . '">Tienda</a>';
        }
        if (is_product_category()) {
            $cat = get_queried_object();
            if ($cat && $cat->parent) {
                $parent = get_term($cat->parent, 'product_cat');
                if ($parent && !is_wp_error($parent)) {
                    $items[] = '<a href="' . esc_url(get_term_link($parent)) . '">' . esc_html($parent->name) . '</a>';
                }
            }
            $items[] = '<strong>' . esc_html($cat->name ?? '') . '</strong>';
        } elseif (is_product()) {
            $items[] = '<strong>' . esc_html(get_the_title(get_the_ID())) . '</strong>';
        } elseif (is_shop())      { $items[] = '<strong>Tienda</strong>'; }
        elseif (is_cart())        { $items[] = '<strong>Cesta</strong>'; }
        elseif (is_checkout())    { $items[] = '<strong>Checkout</strong>'; }
        elseif (is_account_page()){ $items[] = '<strong>Mi cuenta</strong>'; }
    } elseif (is_page()) {
        $q = get_queried_object();
        if ($q && $q->post_title) $items[] = '<strong>' . esc_html($q->post_title) . '</strong>';
    } elseif (is_search()) {
        $items[] = '<strong>Resultados: ' . esc_html(get_search_query()) . '</strong>';
    }

    if (count($items) > 1) {
        echo '<nav aria-label="Ruta de navegación" class="breadcrumb">';
        echo implode($sep, $items);
        echo '</nav>';
    }
}

// ══ 12. HELPERS ══════════════════════════════════════════════════
function dentix_logo_svg($w = 56, $h = 56) {
    $logo_id = get_theme_mod('custom_logo');
    if ($logo_id) {
        echo wp_get_attachment_image($logo_id, 'full', false, ['class'=>'logo-svg','width'=>$w,'height'=>$h]);
    } else {
        echo '<svg class="logo-svg" width="'.$w.'" height="'.$h.'" viewBox="0 0 44 44" fill="none" aria-hidden="true">
          <circle cx="20" cy="22" r="18" fill="#1A1A1A"/>
          <circle cx="26" cy="16" r="12" fill="#2D2D2D"/>
          <circle cx="9"  cy="32" r="7"  fill="#C0392B"/>
        </svg>';
    }
}
function dentix_cart_count()  { return (function_exists('WC') && WC()->cart) ? WC()->cart->get_cart_contents_count() : 0; }
function dentix_cart_url()    { return function_exists('wc_get_cart_url') ? wc_get_cart_url() : '#'; }
function dentix_phone()       { return dentix_opt('phone', '900 123 456'); }
function dentix_free_ship()   { return dentix_opt('free_shipping', 150); }

// ══ 13. RENDIMIENTO Y SEGURIDAD ══════════════════════════════════
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
add_action('wp_enqueue_scripts', function () {
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('global-styles');
}, 100);

// ══ 14. JS AÑADIR AL CARRITO (AJAX) ══════════════════════════════
add_action('wp_footer', function () { ?>
<script>
function dentixAddToCart(productId, btn) {
  if (!productId || !window.dentixVars) return;
  const orig = btn.innerHTML; btn.innerHTML='…'; btn.disabled=true;
  fetch(dentixVars.ajaxUrl, {
    method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:'action=dentix_add_to_cart&product_id='+productId+'&quantity=1&nonce='+dentixVars.nonce
  }).then(r=>r.json()).then(d=>{
    if(d.success){
      const b=document.getElementById('cartBadge');
      if(b) b.textContent=d.data.cart_count;
      btn.innerHTML='✓'; btn.style.background='#2E7D32';
      setTimeout(()=>{btn.innerHTML=orig;btn.style.background='';btn.disabled=false;},1800);
    } else { btn.innerHTML=orig; btn.disabled=false; }
  }).catch(()=>{ btn.innerHTML=orig; btn.disabled=false; });
}
</script>
<?php });
