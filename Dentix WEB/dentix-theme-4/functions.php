<?php
/**
 * Dentix Theme — functions.php
 * Núcleo del tema: setup, enqueues, hooks de WooCommerce y personalización B2B
 */

defined('ABSPATH') || exit;

// ══════════════════════════════════════════════════════════════════
// 1. SETUP DEL TEMA
// ══════════════════════════════════════════════════════════════════
function dentix_theme_setup() {
    // Soporte básico WordPress
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('automatic-feed-links');
    add_theme_support('html5', ['search-form','comment-form','comment-list','gallery','caption','script','style']);
    add_theme_support('custom-logo', [
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ]);

    // Soporte WooCommerce
    add_theme_support('woocommerce', [
        'thumbnail_image_width' => 600,
        'gallery_thumbnail_image_width' => 120,
        'single_image_width'    => 800,
        'product_grid'          => ['default_rows' => 4, 'min_rows' => 2, 'default_columns' => 4, 'min_columns' => 2, 'max_columns' => 6],
    ]);
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');

    // Menús de navegación
    register_nav_menus([
        'primary'  => __('Menú principal (Nav)', 'dentix'),
        'topbar'   => __('Menú superior (Topbar)', 'dentix'),
        'footer-1' => __('Footer — Tienda', 'dentix'),
        'footer-2' => __('Footer — Ayuda', 'dentix'),
        'footer-3' => __('Footer — Empresa', 'dentix'),
    ]);

    // Tamaños de imagen para WooCommerce
    add_image_size('dentix-product-thumb', 600, 600, true);
    add_image_size('dentix-product-gallery', 800, 800, true);
    add_image_size('dentix-category-banner', 1200, 400, true);

    // Idioma
    load_theme_textdomain('dentix', get_template_directory() . '/languages');
}
add_action('after_setup_theme', 'dentix_theme_setup');

// ══════════════════════════════════════════════════════════════════
// 2. SIDEBARS / WIDGET AREAS
// ══════════════════════════════════════════════════════════════════
function dentix_register_sidebars() {
    register_sidebar([
        'name'          => 'Sidebar Tienda (Filtros)',
        'id'            => 'shop-sidebar',
        'description'   => 'Filtros del catálogo de productos',
        'before_widget' => '<div class="filter-box" id="%1$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4>',
        'after_title'   => '</h4>',
    ]);
    register_sidebar([
        'name'          => 'Footer — Columna 1',
        'id'            => 'footer-1',
        'before_widget' => '<div id="%1$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4>',
        'after_title'   => '</h4>',
    ]);
}
add_action('widgets_init', 'dentix_register_sidebars');

// ══════════════════════════════════════════════════════════════════
// 3. ENQUEUE — CSS Y JS
// ══════════════════════════════════════════════════════════════════
function dentix_enqueue_assets() {
    $ver = wp_get_theme()->get('Version');
    $uri = get_template_directory_uri();

    // Google Fonts — Playfair Display + DM Sans
    wp_enqueue_style('dentix-fonts',
        'https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap',
        [], null
    );

    // CSS principal del tema
    wp_enqueue_style('dentix-main', $uri . '/assets/css/dentix.css', ['dentix-fonts'], $ver);

    // JS compartido
    wp_enqueue_script('dentix-main', $uri . '/assets/js/dentix.js', [], $ver, true);

    // JS del carrusel — solo en la homepage
    if (is_front_page()) {
        wp_enqueue_script('dentix-carousel', $uri . '/assets/js/carousel.js', ['dentix-main'], $ver, true);
    }

    // Pasar variables PHP → JS
    wp_localize_script('dentix-main', 'dentixVars', [
        'ajaxUrl'   => admin_url('admin-ajax.php'),
        'nonce'     => wp_create_nonce('dentix_nonce'),
        'cartUrl'   => wc_get_cart_url(),
        'shopUrl'   => get_permalink(wc_get_page_id('shop')),
        'currency'  => get_woocommerce_currency_symbol(),
        'isLoggedIn'=> is_user_logged_in() ? 'yes' : 'no',
        'cartCount' => WC()->cart ? WC()->cart->get_cart_contents_count() : 0,
    ]);
}
add_action('wp_enqueue_scripts', 'dentix_enqueue_assets');

// ══════════════════════════════════════════════════════════════════
// 4. CAMPO NIF/CIF OBLIGATORIO EN EL CHECKOUT (B2B)
// ══════════════════════════════════════════════════════════════════

// Añadir el campo NIF/CIF en el checkout después de la empresa
function dentix_add_nif_checkout_field($fields) {
    $fields['billing']['billing_nif'] = [
        'label'       => __('NIF / CIF', 'dentix'),
        'placeholder' => __('B-12345678 / 12345678A', 'dentix'),
        'required'    => true,
        'class'       => ['form-row-wide'],
        'priority'    => 35, // justo después del campo empresa (prioridad 30)
        'validate'    => ['nif'],
    ];
    // Hacer el campo empresa obligatorio también
    $fields['billing']['billing_company']['required'] = true;
    return $fields;
}
add_filter('woocommerce_checkout_fields', 'dentix_add_nif_checkout_field');

// Validar el campo NIF/CIF
function dentix_validate_nif_checkout($data, $errors) {
    if (empty($data['billing_nif'])) {
        $errors->add('billing_nif_required', __('<strong>NIF/CIF</strong> es un campo obligatorio para acceso profesional.', 'dentix'));
    } else {
        $nif = strtoupper(trim($data['billing_nif']));
        // Validación básica formato español: NIF (8 dígitos + letra), NIE (X/Y/Z + 7 dígitos + letra), CIF (letra + 7 dígitos + letra/número)
        if (!preg_match('/^([0-9]{8}[A-Z]|[XYZ][0-9]{7}[A-Z]|[ABCDEFGHJKLMNPQRSUVW][0-9]{7}[0-9A-J])$/', $nif)) {
            $errors->add('billing_nif_invalid', __('El <strong>NIF/CIF</strong> introducido no tiene un formato válido.', 'dentix'));
        }
    }
}
add_action('woocommerce_checkout_process', 'dentix_validate_nif_checkout');

// Guardar el campo NIF/CIF en el pedido
function dentix_save_nif_checkout_field($order_id) {
    if (!empty($_POST['billing_nif'])) {
        update_post_meta($order_id, '_billing_nif', sanitize_text_field(strtoupper($_POST['billing_nif'])));
    }
}
add_action('woocommerce_checkout_update_order_meta', 'dentix_save_nif_checkout_field');

// Mostrar NIF/CIF en el panel de administración del pedido
function dentix_display_nif_in_admin($order) {
    $nif = get_post_meta($order->get_id(), '_billing_nif', true);
    if ($nif) {
        echo '<p><strong>' . __('NIF/CIF:', 'dentix') . '</strong> ' . esc_html($nif) . '</p>';
    }
}
add_action('woocommerce_admin_order_data_after_billing_address', 'dentix_display_nif_in_admin');

// ══════════════════════════════════════════════════════════════════
// 5. PERSONALIZACIÓN WOOCOMMERCE — LAYOUT Y COMPORTAMIENTO
// ══════════════════════════════════════════════════════════════════

// Quitar el sidebar de WooCommerce (usamos nuestro layout propio)
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);

// Número de productos por página
add_filter('loop_shop_per_page', fn() => 24, 20);

// Número de columnas en el grid (lo gestionamos nosotros con CSS)
add_filter('loop_shop_columns', fn() => 4);

// Quitar el breadcrumb nativo de WooCommerce (usamos el nuestro)
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);

// Quitar wrappers nativos de WooCommerce y usar los nuestros
remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action('woocommerce_after_main_content',  'woocommerce_output_content_wrapper_end', 10);

function dentix_wc_wrapper_start() { echo '<div class="dentix-wc-wrap">'; }
function dentix_wc_wrapper_end()   { echo '</div>'; }
add_action('woocommerce_before_main_content', 'dentix_wc_wrapper_start', 10);
add_action('woocommerce_after_main_content',  'dentix_wc_wrapper_end',   10);

// Mover el resumen del producto (precio, botón añadir) antes de los tabs
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 15);

// Añadir clase CSS al body para páginas WooCommerce
function dentix_body_classes($classes) {
    if (is_woocommerce() || is_cart() || is_checkout() || is_account_page()) {
        $classes[] = 'dentix-wc-page';
    }
    if (is_shop() || is_product_category() || is_product_tag()) {
        $classes[] = 'dentix-shop-page';
    }
    if (is_product()) {
        $classes[] = 'dentix-product-page';
    }
    if (is_cart()) {
        $classes[] = 'dentix-cart-page';
    }
    if (is_checkout()) {
        $classes[] = 'dentix-checkout-page';
    }
    if (is_account_page()) {
        $classes[] = 'dentix-account-page';
    }
    return $classes;
}
add_filter('body_class', 'dentix_body_classes');

// ══════════════════════════════════════════════════════════════════
// 6. FLASH MESSAGES / NOTICES WOOCOMMERCE
// ══════════════════════════════════════════════════════════════════
// Personalizar los mensajes de WooCommerce con clases de Dentix
add_filter('woocommerce_notice_types', fn($types) => $types);

// ══════════════════════════════════════════════════════════════════
// 7. CARRITO MEDIANTE AJAX (actualizar badge del header)
// ══════════════════════════════════════════════════════════════════
function dentix_ajax_add_to_cart() {
    check_ajax_referer('dentix_nonce', 'nonce');
    $product_id = absint($_POST['product_id'] ?? 0);
    $quantity   = absint($_POST['quantity']   ?? 1);

    if (!$product_id) wp_send_json_error(['message' => 'Producto no válido']);

    $result = WC()->cart->add_to_cart($product_id, $quantity);
    if ($result) {
        wp_send_json_success([
            'message'    => sprintf(__('<strong>%s</strong> añadido a la cesta.', 'dentix'), get_the_title($product_id)),
            'cart_count' => WC()->cart->get_cart_contents_count(),
            'cart_url'   => wc_get_cart_url(),
        ]);
    } else {
        wp_send_json_error(['message' => 'No se pudo añadir el producto.']);
    }
}
add_action('wp_ajax_dentix_add_to_cart',        'dentix_ajax_add_to_cart');
add_action('wp_ajax_nopriv_dentix_add_to_cart', 'dentix_ajax_add_to_cart');

// ══════════════════════════════════════════════════════════════════
// 8. BÚSQUEDA POR SKU/REFERENCIA
// ══════════════════════════════════════════════════════════════════
function dentix_search_by_sku($query) {
    if (!$query->is_search || !$query->is_main_query() || is_admin()) return;

    $search_term = $query->query_vars['s'];

    // Si parece una referencia/SKU (sin espacios, <= 20 chars), buscar también en meta
    if (strlen($search_term) <= 20 && strpos($search_term, ' ') === false) {
        add_filter('posts_join',    'dentix_sku_search_join');
        add_filter('posts_where',   'dentix_sku_search_where');
        add_filter('posts_distinct','dentix_sku_search_distinct');
    }
}
add_action('pre_get_posts', 'dentix_search_by_sku');

function dentix_sku_search_join($join) {
    global $wpdb;
    $join .= " LEFT JOIN {$wpdb->postmeta} AS dentix_pm ON ({$wpdb->posts}.ID = dentix_pm.post_id AND dentix_pm.meta_key = '_sku')";
    return $join;
}
function dentix_sku_search_where($where) {
    global $wpdb;
    $search = esc_sql($GLOBALS['wp_query']->query_vars['s']);
    $where .= " OR (dentix_pm.meta_value LIKE '%{$search}%')";
    return $where;
}
function dentix_sku_search_distinct($distinct) {
    return 'DISTINCT';
}

// ══════════════════════════════════════════════════════════════════
// 9. FUNCIONES HELPER DEL TEMA
// ══════════════════════════════════════════════════════════════════

// Renderizar el logo SVG de Dentix
function dentix_logo_svg($width = 56, $height = 56) {
    $custom_logo_id = get_theme_mod('custom_logo');
    if ($custom_logo_id) {
        echo wp_get_attachment_image($custom_logo_id, 'full', false, ['class' => 'logo-svg', 'width' => $width, 'height' => $height]);
    } else {
        // Logo SVG embebido como fallback
        printf(
            '<svg class="logo-svg" width="%d" height="%d" viewBox="0 0 44 44" fill="none" aria-label="Dentix">
              <circle cx="20" cy="22" r="18" fill="#1A1A1A"/>
              <circle cx="26" cy="16" r="12" fill="#2D2D2D"/>
              <circle cx="9"  cy="32" r="7"  fill="#C0392B"/>
            </svg>',
            $width, $height
        );
    }
}

// Contar artículos del carrito (para el badge del header)
function dentix_cart_count() {
    if (function_exists('WC') && WC()->cart) {
        return WC()->cart->get_cart_contents_count();
    }
    return 0;
}

// URL del carrito
function dentix_cart_url() {
    return function_exists('wc_get_cart_url') ? wc_get_cart_url() : '#';
}

// Breadcrumb personalizado
function dentix_breadcrumb() {
    $items = [];
    $sep   = '<span aria-hidden="true">›</span>';
    $items[] = '<a href="' . esc_url(home_url('/')) . '">' . __('Inicio', 'dentix') . '</a>';

    if ( function_exists('is_woocommerce') && is_woocommerce() ) {
        $shop_id  = function_exists('wc_get_page_id') ? wc_get_page_id('shop') : 0;
        $shop_url = $shop_id ? get_permalink($shop_id) : home_url('/tienda/');

        if ( ! is_shop() ) {
            $items[] = '<a href="' . esc_url($shop_url) . '">' . __('Tienda', 'dentix') . '</a>';
        }

        if ( is_product_category() ) {
            $cat = get_queried_object();
            if ( $cat && $cat->parent ) {
                $parent  = get_term($cat->parent, 'product_cat');
                if ( $parent && ! is_wp_error($parent) ) {
                    $items[] = '<a href="' . esc_url(get_term_link($parent)) . '">' . esc_html($parent->name) . '</a>';
                }
            }
            $items[] = '<strong>' . esc_html($cat->name ?? '') . '</strong>';

        } elseif ( is_product() ) {
            // get_the_title() con ID explícito — funciona fuera del loop
            $items[] = '<strong>' . esc_html(get_the_title(get_the_ID())) . '</strong>';

        } elseif ( is_shop() ) {
            $items[] = '<strong>' . __('Tienda', 'dentix') . '</strong>';

        } elseif ( is_cart() ) {
            $items[] = '<strong>' . __('Cesta', 'dentix') . '</strong>';

        } elseif ( is_checkout() ) {
            $items[] = '<strong>' . __('Checkout', 'dentix') . '</strong>';

        } elseif ( is_account_page() ) {
            $items[] = '<strong>' . __('Mi cuenta', 'dentix') . '</strong>';
        }

    } elseif ( is_page() ) {
        // Usar el ID de la página directamente (no depende del loop)
        $queried = get_queried_object();
        $title   = $queried ? $queried->post_title : get_the_title();
        if ( $title ) {
            $items[] = '<strong>' . esc_html($title) . '</strong>';
        }

    } elseif ( is_search() ) {
        $items[] = '<strong>' . sprintf(
            __('Resultados: %s', 'dentix'),
            esc_html(get_search_query())
        ) . '</strong>';

    } elseif ( is_404() ) {
        $items[] = '<strong>Página no encontrada</strong>';
    }

    if ( count($items) > 1 ) {
        echo '<nav aria-label="Ruta de navegación" class="breadcrumb">';
        echo implode($sep, $items);
        echo '</nav>';
    }
}

// ══════════════════════════════════════════════════════════════════
// 10. SAGE 50 — METADATOS EXTRAS EN PRODUCTO
// ══════════════════════════════════════════════════════════════════
// Cuando Conecta HUB sincroniza productos desde SAGE 50, almacena
// datos en post_meta. Estas funciones los recuperan para mostrarlos.

function dentix_get_sage_sku($product_id) {
    // El SKU nativo de WooCommerce es el código de artículo de SAGE 50
    return get_post_meta($product_id, '_sku', true);
}

function dentix_get_sage_ref($product_id) {
    // Referencia adicional (si Conecta HUB la mapea a este campo)
    return get_post_meta($product_id, '_sage_ref', true)
        ?: get_post_meta($product_id, '_sku', true);
}

function dentix_get_sage_ean($product_id) {
    return get_post_meta($product_id, '_ean', true)
        ?: get_post_meta($product_id, '_global_unique_id', true);
}

// ══════════════════════════════════════════════════════════════════
// 11. RENDIMIENTO — OPTIMIZACIONES
// ══════════════════════════════════════════════════════════════════

// Quitar emojis (no se usan en la tienda)
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

// Quitar versión de WP de las URLs de scripts/styles (seguridad)
add_filter('style_loader_src',  fn($src) => remove_query_arg('ver', $src));
add_filter('script_loader_src', fn($src) => remove_query_arg('ver', $src));

// Quitar el bloque CSS de Gutenberg (no usamos editor de bloques en tienda)
add_action('wp_enqueue_scripts', function() {
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('global-styles');
}, 100);

// ══════════════════════════════════════════════════════════════════
// 12. SEGURIDAD BÁSICA
// ══════════════════════════════════════════════════════════════════

// Quitar la versión de WordPress del código fuente
remove_action('wp_head', 'wp_generator');

// Quitar RSD link
remove_action('wp_head', 'rsd_link');

// Quitar wlwmanifest
remove_action('wp_head', 'wlwmanifest_link');

// Limitar intentos de login (configurar también con plugin Limit Login Attempts)
function dentix_failed_login($username) {
    $ip = $_SERVER['REMOTE_ADDR'];
    // Log para monitorización — el plugin WP 2FA se encarga del bloqueo
    error_log("[Dentix Security] Login fallido para: $username desde IP: $ip");
}
add_action('wp_login_failed', 'dentix_failed_login');

// ══════════════════════════════════════════════════════════════════
// 13. COMPATIBILIDAD HPOS (High Performance Order Storage)
// ══════════════════════════════════════════════════════════════════
add_action('before_woocommerce_init', function() {
    if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
    }
});

// ══════════════════════════════════════════════════════════════════
// 14. CARGAR ARCHIVOS ADICIONALES DEL TEMA
// ══════════════════════════════════════════════════════════════════
require get_template_directory() . '/inc/woocommerce-hooks.php';

// ══════════════════════════════════════════════════════════════
// FIX IMÁGENES CARRUSEL — Sobreescribe las rutas relativas del CSS
// con URLs absolutas correctas usando get_template_directory_uri()
// ══════════════════════════════════════════════════════════════
add_action('wp_head', function() {
    $img = get_template_directory_uri() . '/assets/images/';
    // Solo añadir si estamos en la homepage
    if ( ! is_front_page() ) return;
    echo '<style id="dentix-carousel-images">
.hs1 { background-image: url("' . esc_url($img . 'image1.jpeg') . '") !important; }
.hs2 { background-image: url("' . esc_url($img . 'image2.jpeg') . '") !important; }
.hs3 { background-image: url("' . esc_url($img . 'image3.jpeg') . '") !important; }
.hs4 { background-image: url("' . esc_url($img . 'image4.jpeg') . '") !important; }
.hs5 { background-image: url("' . esc_url($img . 'image5.jpeg') . '") !important; }
</style>' . "\n";
}, 99);

// ══════════════════════════════════════════════════════════════
// FIX BUSCADOR — Redirigir búsquedas de productos a la tienda
// y asegurar que el tipo de post se respeta en la query
// ══════════════════════════════════════════════════════════════

// 1. Forzar post_type=product en búsquedas del frontend
add_action('pre_get_posts', function($query) {
    if ( is_admin() || ! $query->is_main_query() ) return;
    if ( $query->is_search() && isset($_GET['post_type']) && $_GET['post_type'] === 'product' ) {
        $query->set('post_type', 'product');
    }
});

// 2. Redirigir búsqueda de producto a la página de la tienda con ?s=
add_action('template_redirect', function() {
    if ( is_search() && get_query_var('post_type') === 'product' ) {
        // Ya estamos en una búsqueda de productos — dejar que search.php la gestione
        return;
    }
    // Si es una búsqueda genérica (sin post_type), redirigir a búsqueda de productos
    if ( is_search() && ! get_query_var('post_type') ) {
        $s = get_search_query();
        if ( $s ) {
            wp_redirect( home_url('/?s=' . urlencode($s) . '&post_type=product') );
            exit;
        }
    }
});

// ── Cargar woocommerce.css solo en páginas WC ──────────────
add_action('wp_enqueue_scripts', function() {
    if (is_woocommerce() || is_cart() || is_checkout() || is_account_page()) {
        wp_enqueue_style('dentix-woo',
            get_template_directory_uri() . '/assets/css/woocommerce.css',
            ['dentix-main'],
            wp_get_theme()->get('Version')
        );
    }
}, 20);

// ── JS añadir al carrito vía AJAX (frontend) ──────────────
add_action('wp_footer', function() { ?>
<script>
function dentixAddToCart(productId, btn) {
  if (!productId) return;
  const orig = btn.textContent;
  btn.textContent = '…';
  btn.disabled = true;
  fetch(dentixVars.ajaxUrl, {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: 'action=dentix_add_to_cart&product_id='+productId+'&quantity=1&nonce='+dentixVars.nonce
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      // Actualizar badge carrito
      const badge = document.getElementById('cartBadge');
      if (badge) badge.textContent = data.data.cart_count;
      // Flash confirmación
      btn.textContent = '✓';
      btn.style.background = '#2E7D32';
      setTimeout(() => {
        btn.textContent = orig;
        btn.style.background = '';
        btn.disabled = false;
      }, 1800);
    } else {
      btn.textContent = orig;
      btn.disabled = false;
    }
  })
  .catch(() => { btn.textContent = orig; btn.disabled = false; });
}
</script>
<?php });
