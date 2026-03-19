<?php
/**
 * Dentix — Hooks específicos de WooCommerce
 * Separado de functions.php para mantener el código organizado
 */

defined('ABSPATH') || exit;

// ── Modificar los campos del checkout para B2B ────────────────
add_filter('woocommerce_checkout_fields', function($fields) {

    // Reordenar y etiquetar campos de facturación
    if (isset($fields['billing']['billing_first_name']))
        $fields['billing']['billing_first_name']['label'] = 'Nombre del responsable';
    if (isset($fields['billing']['billing_last_name']))
        $fields['billing']['billing_last_name']['label'] = 'Apellidos del responsable';
    if (isset($fields['billing']['billing_company'])) {
        $fields['billing']['billing_company']['label']    = 'Razón social / Nombre clínica';
        $fields['billing']['billing_company']['required'] = true;
        $fields['billing']['billing_company']['priority'] = 25;
    }
    if (isset($fields['billing']['billing_phone']))
        $fields['billing']['billing_phone']['required'] = true;

    return $fields;
});

// ── Mostrar stock en formato Dentix ──────────────────────────
add_filter('woocommerce_get_availability_text', function($availability, $product) {
    if ($product->is_in_stock()) {
        $qty = $product->get_stock_quantity();
        if ($qty && $qty <= 5) {
            return sprintf(__('Stock bajo · Solo %d unidades', 'dentix'), $qty);
        }
        return $qty ? sprintf(__('En stock · %d unidades disponibles', 'dentix'), $qty) : __('En stock', 'dentix');
    }
    return __('Sin stock — consultar disponibilidad', 'dentix');
}, 10, 2);

// ── Redirigir a checkout directamente al añadir al carrito ───
// (comportamiento B2B — menos fricción)
add_filter('woocommerce_add_to_cart_redirect', function($url) {
    // Comentar esta línea si se prefiere quedarse en la página del producto
    // return wc_get_checkout_url();
    return $url; // Por defecto: quedarse en la página
});

// ── Mostrar el SKU/referencia prominentemente en el producto ──
add_action('woocommerce_single_product_summary', function() {
    global $product;
    $sku = $product->get_sku();
    $ean = get_post_meta($product->get_id(), '_ean', true);
    if ($sku || $ean) {
        echo '<div class="pdp-refs">';
        if ($sku) echo '<div>Ref: <strong>' . esc_html($sku) . '</strong></div>';
        if ($ean) echo '<div>EAN: <strong>' . esc_html($ean) . '</strong></div>';
        echo '</div>';
    }
}, 6); // Justo después del título

// ── Etiquetas de marca (atributo) en la ficha de producto ────
add_action('woocommerce_single_product_summary', function() {
    global $product;
    $brand_terms = wp_get_post_terms($product->get_id(), 'pa_marca');
    if (!is_wp_error($brand_terms) && !empty($brand_terms)) {
        echo '<div class="pdp-brand">' . esc_html($brand_terms[0]->name) . '</div>';
    }
}, 3); // Antes del título

// ── Modificar el texto del botón "Añadir a la cesta" ─────────
add_filter('woocommerce_product_add_to_cart_text', function($text, $product) {
    return $product->is_in_stock() ? __('Añadir a la cesta', 'dentix') : __('Sin stock', 'dentix');
}, 10, 2);

add_filter('woocommerce_product_single_add_to_cart_text', fn() => __('Añadir a la cesta', 'dentix'));

// ── Email de pedido — añadir NIF/CIF ─────────────────────────
add_filter('woocommerce_email_order_meta_fields', function($fields, $sent_to_admin, $order) {
    $nif = get_post_meta($order->get_id(), '_billing_nif', true);
    if ($nif) {
        $fields['billing_nif'] = [
            'label' => __('NIF/CIF', 'dentix'),
            'value' => $nif,
        ];
    }
    return $fields;
}, 10, 3);

// ── Columna NIF/CIF en la lista de pedidos del admin ─────────
add_filter('manage_woocommerce_page_wc-orders_columns', function($columns) {
    $new = [];
    foreach ($columns as $key => $val) {
        $new[$key] = $val;
        if ($key === 'billing_address') {
            $new['billing_nif'] = 'NIF/CIF';
        }
    }
    return $new;
});
add_action('manage_woocommerce_page_wc-orders_custom_column', function($column, $order) {
    if ($column === 'billing_nif') {
        echo esc_html(get_post_meta($order->get_id(), '_billing_nif', true) ?: '—');
    }
}, 10, 2);

// ── Productos por página en el archivo de tienda ──────────────
add_filter('loop_shop_per_page', fn() => 24);

// ── Eliminar "Productos" del breadcrumb nativo de WC ─────────
add_filter('woocommerce_breadcrumb_defaults', function($defaults) {
    $defaults['delimiter'] = '&nbsp;›&nbsp;';
    $defaults['home']      = 'Inicio';
    return $defaults;
});

// ── Fragmentos del carrito para actualización AJAX ───────────
add_filter('woocommerce_add_to_cart_fragments', function($fragments) {
    // Actualizar el badge del carrito en el header
    ob_start();
    $count = WC()->cart->get_cart_contents_count();
    echo '<span class="badge" id="cartBadge">' . $count . '</span>';
    $fragments['#cartBadge'] = ob_get_clean();
    return $fragments;
});

// ── Shortcode: mostrar precio especial B2B por grupo ─────────
// Uso: [dentix_precio_grupo grupo="clinica"]
add_shortcode('dentix_precio_grupo', function($atts) {
    // Placeholder para integración con plugin de precios B2B
    // (WooCommerce B2B o similar según decisión de Dentix)
    return '';
});
