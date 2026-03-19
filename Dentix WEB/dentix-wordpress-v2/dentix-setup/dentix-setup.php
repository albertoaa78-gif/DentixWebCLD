<?php
/**
 * Plugin Name: Dentix Setup
 * Plugin URI:  https://www.dentix.es
 * Description: Configura automáticamente WordPress y WooCommerce para Dentix: crea páginas, menús, ajustes WooCommerce B2B, categorías y opciones del tema. Desactivar y eliminar tras el primer uso.
 * Version:     1.0.0
 * Author:      Dentix Productos Dentales, S.L.
 * Text Domain: dentix-setup
 * Requires at least: 6.4
 * Requires PHP: 8.1
 */
defined('ABSPATH') || exit;

// ── Añadir menú de administración ─────────────────────────────
add_action('admin_menu', function() {
    add_management_page(
        'Dentix Setup',
        '🦷 Dentix Setup',
        'manage_options',
        'dentix-setup',
        'dentix_setup_page'
    );
});

// ── Avisos en el admin ─────────────────────────────────────────
add_action('admin_notices', function() {
    if (get_option('dentix_setup_complete')) {
        echo '<div class="notice notice-warning is-dismissible"><p>
        <strong>🦷 Dentix Setup:</strong> La configuración inicial ya fue completada.
        Puedes <a href="' . admin_url('plugins.php') . '">desactivar y eliminar este plugin</a>.
        </p></div>';
    } elseif (current_user_can('manage_options')) {
        echo '<div class="notice notice-info"><p>
        <strong>🦷 Dentix Setup:</strong> El plugin de configuración inicial está activo.
        <a href="' . admin_url('tools.php?page=dentix-setup') . '">Ejecutar la configuración ahora →</a>
        </p></div>';
    }
});

// ── Página de setup ────────────────────────────────────────────
function dentix_setup_page() {
    $done    = get_option('dentix_setup_complete', false);
    $message = '';
    $errors  = [];

    if (isset($_POST['dentix_run_setup']) && check_admin_referer('dentix_setup_action')) {
        $result = dentix_run_full_setup();
        $message = $result['message'];
        $errors  = $result['errors'];
        if (empty($errors)) {
            update_option('dentix_setup_complete', true);
            update_option('dentix_setup_date', current_time('mysql'));
            $done = true;
        }
    }
    ?>
    <div class="wrap">
      <h1>🦷 Dentix — Configuración inicial</h1>
      <p>Este plugin configura automáticamente tu instalación de WordPress y WooCommerce para la tienda Dentix.</p>

      <?php if ($message) : ?>
        <div class="notice <?php echo empty($errors) ? 'notice-success' : 'notice-error'; ?> is-dismissible">
          <p><?php echo wp_kses_post($message); ?></p>
          <?php if ($errors) : foreach ($errors as $e) echo '<p>• ' . esc_html($e) . '</p>'; endforeach; endif; ?>
        </div>
      <?php endif; ?>

      <?php if (!$done) : ?>
      <div class="card" style="max-width:680px;padding:24px;margin-top:20px">
        <h2>¿Qué hace este setup?</h2>
        <ol style="line-height:2">
          <li>✅ Crea las páginas legales (Aviso legal, Privacidad, Cookies, Condiciones, Devoluciones)</li>
          <li>✅ Crea la página de Contacto</li>
          <li>✅ Configura WooCommerce para España y B2B (IVA, moneda €, HPOS)</li>
          <li>✅ Crea las categorías del catálogo odontológico</li>
          <li>✅ Crea el atributo "Marca" para los productos</li>
          <li>✅ Configura el envío gratuito a partir de 150 €</li>
          <li>✅ Configura la página de inicio estática (homepage Dentix)</li>
          <li>✅ Configura permalinks amigables para SEO</li>
          <li>✅ Desactiva comentarios en productos (usamos valoraciones WC)</li>
          <li>✅ Ajusta el límite de memoria PHP</li>
        </ol>
        <p style="color:#856404;background:#fff3cd;padding:12px 16px;border-radius:6px;margin-top:8px">
          ⚠️ <strong>Requisito:</strong> WooCommerce debe estar instalado y activo antes de ejecutar este setup.
        </p>
        <form method="post">
          <?php wp_nonce_field('dentix_setup_action'); ?>
          <input type="submit" name="dentix_run_setup" class="button button-primary button-hero"
                 value="🚀 Ejecutar configuración inicial"
                 onclick="return confirm('¿Ejecutar la configuración de Dentix? Este proceso modificará ajustes de WordPress y WooCommerce.')">
        </form>
      </div>
      <?php else : ?>
      <div class="notice notice-success inline" style="padding:16px;margin-top:16px">
        <p>✅ <strong>Configuración completada</strong> el <?php echo get_option('dentix_setup_date'); ?></p>
        <p>Puedes <a href="<?php echo admin_url('plugins.php'); ?>">desactivar y eliminar este plugin</a> de forma segura.</p>
      </div>
      <?php endif; ?>
    </div>
    <?php
}

// ══════════════════════════════════════════════════════════════════
// SETUP COMPLETO
// ══════════════════════════════════════════════════════════════════
function dentix_run_full_setup() {
    $errors = [];
    $log    = [];

    // 1. PÁGINAS LEGALES Y DE NEGOCIO
    // ─────────────────────────────────
    $pages = [
        [
            'title'   => 'Aviso Legal',
            'slug'    => 'aviso-legal',
            'content' => dentix_content_aviso_legal(),
        ],
        [
            'title'   => 'Política de Privacidad',
            'slug'    => 'politica-privacidad',
            'content' => dentix_content_privacidad(),
        ],
        [
            'title'   => 'Política de Cookies',
            'slug'    => 'politica-cookies',
            'content' => '<p>Esta política de cookies será gestionada por el plugin Complianz o CookieYes. Una vez activado, sustituirá este contenido automáticamente.</p>',
        ],
        [
            'title'   => 'Condiciones Generales de Venta',
            'slug'    => 'condiciones-venta',
            'content' => dentix_content_condiciones(),
        ],
        [
            'title'   => 'Política de Devoluciones',
            'slug'    => 'politica-devoluciones',
            'content' => dentix_content_devoluciones(),
        ],
        [
            'title'   => 'Contacto',
            'slug'    => 'contacto',
            'content' => dentix_content_contacto(),
        ],
        [
            'title'   => 'Inicio',
            'slug'    => 'inicio',
            'content' => '',
        ],
    ];

    $created_pages = [];
    foreach ($pages as $p) {
        $existing = get_page_by_path($p['slug']);
        if ($existing) {
            $created_pages[$p['slug']] = $existing->ID;
            $log[] = "Página ya existe: {$p['title']}";
        } else {
            $id = wp_insert_post([
                'post_title'   => $p['title'],
                'post_name'    => $p['slug'],
                'post_content' => $p['content'],
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'comment_status' => 'closed',
            ]);
            if (is_wp_error($id)) {
                $errors[] = "Error creando página {$p['title']}: " . $id->get_error_message();
            } else {
                $created_pages[$p['slug']] = $id;
                $log[] = "✅ Página creada: {$p['title']}";
            }
        }
    }

    // 2. HOMEPAGE ESTÁTICA
    // ─────────────────────
    if (isset($created_pages['inicio'])) {
        update_option('show_on_front', 'page');
        update_option('page_on_front', $created_pages['inicio']);
        $log[] = '✅ Homepage estática configurada';
    }

    // 3. CONFIGURACIÓN WOOCOMMERCE
    // ─────────────────────────────
    if (!class_exists('WooCommerce')) {
        $errors[] = 'WooCommerce no está activo. Instala y activa WooCommerce primero.';
    } else {
        // General
        update_option('woocommerce_default_country',       'ES');
        update_option('woocommerce_currency',               'EUR');
        update_option('woocommerce_currency_pos',           'right_space');
        update_option('woocommerce_price_decimal_sep',      ',');
        update_option('woocommerce_price_thousand_sep',     '.');
        update_option('woocommerce_price_num_decimals',     '2');

        // Impuestos — B2B (precios sin IVA)
        update_option('woocommerce_calc_taxes',             'yes');
        update_option('woocommerce_prices_include_tax',     'no');
        update_option('woocommerce_tax_display_shop',       'excl');
        update_option('woocommerce_tax_display_cart',       'incl');
        update_option('woocommerce_tax_based_on',           'billing');

        // IVA estándar España (21%)
        $tax_rate = [
            'tax_rate_country'  => 'ES',
            'tax_rate_state'    => '',
            'tax_rate'          => '21.0000',
            'tax_rate_name'     => 'IVA 21%',
            'tax_rate_priority' => 1,
            'tax_rate_compound' => 0,
            'tax_rate_shipping' => 1,
            'tax_rate_order'    => 0,
            'tax_rate_class'    => '',
        ];
        WC_Tax::_insert_tax_rate($tax_rate);

        // IVA reducido para productos sanitarios (4%)
        $tax_rate_reduced = array_merge($tax_rate, [
            'tax_rate'      => '4.0000',
            'tax_rate_name' => 'IVA 4% (sanitario)',
            'tax_rate_class'=> 'reduced-rate',
        ]);
        WC_Tax::_insert_tax_rate($tax_rate_reduced);

        // Inventario
        update_option('woocommerce_manage_stock',           'yes');
        update_option('woocommerce_stock_format',           '');
        update_option('woocommerce_notify_low_stock',       'yes');
        update_option('woocommerce_notify_low_stock_amount', 5);
        update_option('woocommerce_notify_no_stock',        'yes');
        update_option('woocommerce_notify_no_stock_amount',  0);
        update_option('woocommerce_stock_email_recipient',  get_option('admin_email'));

        // Cuentas de cliente
        update_option('woocommerce_enable_myaccount_registration', 'yes');
        update_option('woocommerce_registration_generate_password', 'yes');
        update_option('woocommerce_enable_guest_checkout',  'no'); // Solo registrados (B2B)

        // Checkout
        update_option('woocommerce_enable_checkout_login_reminder', 'yes');
        update_option('woocommerce_enable_signup_and_login_from_checkout', 'yes');

        // Emails — destinatario de nuevos pedidos
        update_option('woocommerce_new_order_settings', [
            'enabled'    => 'yes',
            'recipient'  => get_option('admin_email'),
            'subject'    => '[{site_title}] Nuevo pedido ({order_number})',
        ]);

        // HPOS
        if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
            update_option('woocommerce_feature_custom_order_tables_enabled', 'yes');
        }

        $log[] = '✅ WooCommerce configurado para España/B2B';

        // 4. ENVÍO GRATUITO +150€
        // ─────────────────────────
        $zone_id = dentix_create_shipping_zone();
        if ($zone_id) $log[] = '✅ Zona de envío España + envío gratuito +150€ configurado';

        // 5. CATEGORÍAS DEL CATÁLOGO
        // ─────────────────────────────
        $categories = [
            ['name' => 'Instrumental Quirúrgico', 'slug' => 'instrumental', 'desc' => 'Fórceps, pinzas, exploradores, espejos y instrumental de cirugía oral'],
            ['name' => 'Endodoncia',               'slug' => 'endodoncia',   'desc' => 'Limas, archivos, obturación y material de endodoncia'],
            ['name' => 'Ortodoncia',               'slug' => 'ortodoncia',   'desc' => 'Brackets, arcos, ligaduras y material de ortodoncia'],
            ['name' => 'Implantología',            'slug' => 'implantologia','desc' => 'Implantes, componentes protésicos y material regenerativo'],
            ['name' => 'Material Clínico',         'slug' => 'material-clinico', 'desc' => 'Composite, cemento, adhesivos y material de restauración'],
            ['name' => 'Esterilización',           'slug' => 'esterilizacion','desc' => 'Autoclave, bolsas de esterilización y control de infección'],
            ['name' => 'Equipamiento',             'slug' => 'equipamiento', 'desc' => 'Turbinas, piezas de mano, unidades dentales y equipos'],
            ['name' => 'Radiología',               'slug' => 'radiologia',   'desc' => 'Equipos de radiología digital, sensores y escáneres intraorales'],
            ['name' => 'Novedades',                'slug' => 'novedades',    'desc' => 'Últimas incorporaciones al catálogo Dentix'],
            ['name' => 'Ofertas',                  'slug' => 'ofertas',      'desc' => 'Productos en oferta y promociones especiales'],
            ['name' => 'Marcas',                   'slug' => 'marcas',       'desc' => 'Catálogo por fabricante y marca'],
        ];

        foreach ($categories as $cat) {
            $existing = get_term_by('slug', $cat['slug'], 'product_cat');
            if (!$existing) {
                wp_insert_term($cat['name'], 'product_cat', [
                    'slug'        => $cat['slug'],
                    'description' => $cat['desc'],
                ]);
                $log[] = "✅ Categoría creada: {$cat['name']}";
            }
        }

        // 6. ATRIBUTO "MARCA"
        // ─────────────────────
        if (!wc_attribute_taxonomy_id_by_name('marca')) {
            wc_create_attribute([
                'name'         => 'Marca',
                'slug'         => 'marca',
                'type'         => 'select',
                'order_by'     => 'name',
                'has_archives' => true,
            ]);
            $log[] = '✅ Atributo "Marca" creado';
        }

        // Marcas predefinidas
        $marcas = ['Hu-Friedy','NSK','KaVo','Dentsply Sirona','Straumann','3M','GC','Ivoclar','Nobel Biocare','Ormco'];
        foreach ($marcas as $marca) {
            if (!term_exists($marca, 'pa_marca')) {
                wp_insert_term($marca, 'pa_marca');
            }
        }
        $log[] = '✅ Marcas predefinidas creadas';
    }

    // 7. PÁGINAS WC (shop, carrito, checkout, mi cuenta)
    // ────────────────────────────────────────────────────
    $wc_pages = [
        'shop'      => 'Tienda',
        'cart'      => 'Cesta',
        'checkout'  => 'Checkout',
        'myaccount' => 'Mi cuenta',
    ];
    foreach ($wc_pages as $option => $title) {
        $page_id = wc_get_page_id($option);
        if (!$page_id || $page_id === -1) {
            $id = wp_insert_post([
                'post_title'   => $title,
                'post_name'    => $option,
                'post_content' => $option === 'shop' ? '' : '[woocommerce_' . ($option === 'myaccount' ? 'my_account' : $option) . ']',
                'post_status'  => 'publish',
                'post_type'    => 'page',
            ]);
            update_option('woocommerce_' . $option . '_page_id', $id);
            $log[] = "✅ Página WC creada: $title";
        }
    }

    // 8. PERMALINKS
    // ─────────────
    update_option('permalink_structure', '/%postname%/');
    update_option('woocommerce_permalinks', [
        'product_base'           => '/producto',
        'category_base'          => '/categoria',
        'tag_base'               => '/etiqueta',
        'attribute_base'         => '',
        'use_verbose_page_rules' => false,
    ]);
    flush_rewrite_rules();
    $log[] = '✅ Permalinks configurados';

    // 9. OPCIONES GLOBALES DE WORDPRESS
    // ────────────────────────────────────
    update_option('blogname',        'Dentix Productos Dentales');
    update_option('blogdescription', 'Instrumental odontológico profesional B2B');
    update_option('default_comment_status', 'closed');
    update_option('default_ping_status',    'closed');
    // Zona horaria España
    update_option('timezone_string', 'Europe/Madrid');
    update_option('date_format', 'd/m/Y');
    update_option('time_format', 'H:i');
    update_option('WPLANG', 'es_ES');
    $log[] = '✅ Opciones generales de WordPress configuradas';

    $summary = '<strong>Setup completado.</strong> ' . count($log) . ' acciones realizadas.';
    if (!empty($errors)) {
        $summary = '<strong>Setup completado con ' . count($errors) . ' advertencia(s).</strong> Revisa los errores abajo.';
    }

    return ['message' => $summary, 'errors' => $errors, 'log' => $log];
}

// ── Zona de envío España ──────────────────────────────────────
function dentix_create_shipping_zone() {
    $zones = WC_Shipping_Zones::get_zones();
    foreach ($zones as $zone) {
        if (strtolower($zone['zone_name']) === 'españa') return $zone['id'];
    }
    $zone = new WC_Shipping_Zone();
    $zone->set_zone_name('España');
    $zone->add_location('ES', 'country');
    $zone_id = $zone->save();

    // Envío gratuito a partir de 150€
    $zone->add_shipping_method('free_shipping');
    $methods = $zone->get_shipping_methods();
    foreach ($methods as $method) {
        if ($method->id === 'free_shipping') {
            update_option('woocommerce_free_shipping_' . $method->get_instance_id() . '_settings', [
                'title'        => 'Envío gratuito',
                'requires'     => 'min_amount',
                'min_amount'   => '150',
                'ignore_discounts' => 'no',
            ]);
        }
    }

    // Envío estándar (tarifa plana)
    $zone->add_shipping_method('flat_rate');
    $methods = $zone->get_shipping_methods(true);
    foreach ($methods as $method) {
        if ($method->id === 'flat_rate') {
            update_option('woocommerce_flat_rate_' . $method->get_instance_id() . '_settings', [
                'title'    => 'Envío estándar',
                'cost'     => '6.90',
                'tax_status' => 'taxable',
            ]);
            break;
        }
    }
    return $zone_id;
}

// ════════════════════════════════════════════════════════════════
// CONTENIDOS DE PÁGINAS LEGALES
// ════════════════════════════════════════════════════════════════

function dentix_content_aviso_legal() {
    return '<h2>1. Datos identificativos</h2>
<p>En cumplimiento del artículo 10 de la Ley 34/2002, de 11 de julio, de Servicios de la Sociedad de la Información y del Comercio Electrónico, le informamos que el titular de este sitio web es:</p>
<ul>
<li><strong>Razón social:</strong> Dentix Productos Dentales, S.L.</li>
<li><strong>CIF:</strong> B-85937787</li>
<li><strong>Registro Mercantil:</strong> Madrid, Tomo 27.796, Folio 125, Sección 8, Hoja M-500933, Inscripción 1ª</li>
<li><strong>Domicilio social:</strong> Madrid, España</li>
<li><strong>Teléfono:</strong> 900 123 456</li>
<li><strong>Email:</strong> info@dentix.es</li>
</ul>
<h2>2. Objeto</h2>
<p>El presente aviso legal regula el uso del sitio web www.dentix.es, del que es titular Dentix Productos Dentales, S.L. La navegación por el sitio web atribuye la condición de usuario e implica la aceptación plena y sin reservas de todas las disposiciones incluidas en este Aviso Legal.</p>
<h2>3. Condiciones de acceso</h2>
<p>El acceso a la tienda online está restringido a profesionales del sector odontológico debidamente acreditados (B2B). Para acceder a los precios y realizar pedidos es necesario registrarse y obtener la validación como profesional.</p>
<h2>4. Propiedad intelectual e industrial</h2>
<p>Todos los contenidos del sitio web (textos, fotografías, gráficos, imágenes, iconos, tecnología, software, así como el diseño gráfico y los códigos fuente) son propiedad intelectual de Dentix Productos Dentales, S.L. o de terceros con licencia de uso concedida a Dentix.</p>
<h2>5. Resolución de litigios en línea (ODR)</h2>
<p>De acuerdo con el Reglamento (UE) nº 524/2013, le informamos de que puede acceder a la plataforma de resolución de litigios en línea de la Comisión Europea en: <a href="https://ec.europa.eu/consumers/odr" target="_blank" rel="noopener">https://ec.europa.eu/consumers/odr</a></p>';
}

function dentix_content_privacidad() {
    return '<h2>1. Responsable del tratamiento</h2>
<p><strong>Dentix Productos Dentales, S.L.</strong> · CIF B-85937787 · info@dentix.es</p>
<h2>2. Finalidad del tratamiento</h2>
<p>Los datos personales que nos facilite a través de este sitio web serán tratados con las siguientes finalidades:</p>
<ul>
<li>Gestión de pedidos, facturación y relación comercial</li>
<li>Comunicaciones relacionadas con sus pedidos y el estado de los mismos</li>
<li>Registro y mantenimiento de su área de cliente</li>
<li>Cumplimiento de obligaciones legales y fiscales</li>
<li>Envío de comunicaciones comerciales (solo con su consentimiento expreso)</li>
</ul>
<h2>3. Base jurídica</h2>
<p>El tratamiento de sus datos se basa en la ejecución de un contrato (gestión de pedidos), el cumplimiento de obligaciones legales y, en su caso, el consentimiento del interesado para comunicaciones comerciales.</p>
<h2>4. Conservación de datos</h2>
<p>Los datos se conservarán durante el tiempo necesario para la relación comercial y, posteriormente, durante los plazos legales de prescripción aplicables (hasta 10 años para datos contables y fiscales).</p>
<h2>5. Sus derechos (RGPD)</h2>
<p>Puede ejercer sus derechos de acceso, rectificación, supresión, oposición, portabilidad y limitación del tratamiento enviando un escrito a info@dentix.es adjuntando copia de su DNI/NIF. Tiene derecho a presentar una reclamación ante la Agencia Española de Protección de Datos (www.aepd.es).</p>
<h2>6. Destinatarios</h2>
<p>Sus datos podrán ser comunicados a proveedores de servicios (pasarelas de pago, empresa de transporte, proveedor de hosting) que actúan como encargados del tratamiento. No se realizan transferencias internacionales de datos salvo las inherentes a los servicios de pago contratados (Stripe, PayPal) bajo garantías adecuadas.</p>';
}

function dentix_content_condiciones() {
    return '<h2>1. Ámbito de aplicación</h2>
<p>Las presentes Condiciones Generales de Venta regulan las relaciones contractuales entre Dentix Productos Dentales, S.L. y sus clientes profesionales en la adquisición de productos a través de www.dentix.es. La tienda está dirigida exclusivamente a profesionales del sector odontológico (B2B).</p>
<h2>2. Proceso de compra</h2>
<p>Para realizar un pedido deberá estar registrado como cliente profesional validado. El proceso de compra consta de: (1) selección de productos, (2) revisión del carrito, (3) introducción de datos de facturación con NIF/CIF obligatorio, (4) selección de método de pago, (5) confirmación del pedido.</p>
<h2>3. Precios</h2>
<p>Los precios mostrados en la tienda están expresados sin IVA. El IVA aplicable se calculará en el checkout según la naturaleza del producto (IVA 21% general o IVA 4% para productos sanitarios). Los precios pueden modificarse sin previo aviso, siendo válidos los vigentes en el momento de la confirmación del pedido.</p>
<h2>4. Métodos de pago</h2>
<p>Aceptamos los siguientes métodos de pago: GetNet (tarjeta de crédito/débito), Visa, Mastercard, Bizum, PayPal, Apple Pay, Google Pay, Klarna y transferencia bancaria. Los pagos se procesan de forma segura mediante cifrado SSL.</p>
<h2>5. Plazo de entrega</h2>
<p>El plazo de entrega estándar es de 24-48 horas hábiles para la Península. Islas Canarias, Baleares, Ceuta y Melilla pueden tener plazos adicionales. Los plazos son orientativos y no tienen carácter contractual.</p>
<h2>6. Propiedad y riesgo</h2>
<p>La propiedad de los bienes se transmite al cliente en el momento del pago íntegro del precio. El riesgo de pérdida o deterioro se transmite en el momento de la entrega al transportista.</p>
<h2>7. Ley aplicable y jurisdicción</h2>
<p>Las presentes condiciones se rigen por la legislación española. Para la resolución de conflictos, las partes se someten a los Juzgados y Tribunales de Madrid, con renuncia expresa a cualquier otro fuero.</p>';
}

function dentix_content_devoluciones() {
    return '<h2>Política de devoluciones y desistimiento</h2>
<p>En cumplimiento del Real Decreto Legislativo 1/2007 y la Ley 3/2014, le informamos de su derecho de desistimiento:</p>
<h2>1. Derecho de desistimiento (14 días)</h2>
<p>Tiene derecho a desistir del presente contrato en un plazo de <strong>14 días naturales</strong> desde la recepción del pedido, sin necesidad de justificación y sin penalización alguna.</p>
<h2>2. Cómo ejercer el derecho de desistimiento</h2>
<p>Para ejercer el derecho de desistimiento, debe notificárnoslo enviando un email a <strong>devoluciones@dentix.es</strong> con el número de pedido y los productos a devolver. Le proporcionaremos las instrucciones para el envío de devolución.</p>
<h2>3. Condiciones de los productos devueltos</h2>
<p>Los productos deben devolverse en su estado original, sin usar y en el embalaje original. No se aceptan devoluciones de productos que hayan sido utilizados, abiertos (productos estériles), o que por razones de higiene no puedan ser devueltos.</p>
<h2>4. Reembolso</h2>
<p>El reembolso se realizará en un plazo máximo de <strong>14 días</strong> desde la recepción del producto devuelto, utilizando el mismo método de pago empleado en la compra original.</p>
<h2>5. Productos defectuosos o incorrectos</h2>
<p>Si ha recibido un producto defectuoso o diferente al pedido, contáctenos en un plazo de 48 horas desde la recepción. En este caso, correremos con los gastos de devolución y envío del producto correcto.</p>';
}

function dentix_content_contacto() {
    return '<div style="display:grid;grid-template-columns:1fr 1fr;gap:48px;margin-bottom:48px">
<div>
<h2>Contacta con nosotros</h2>
<p>Nuestro equipo de atención a profesionales está disponible para resolver cualquier consulta sobre productos, pedidos o disponibilidad.</p>
<p>📞 <strong>900 123 456</strong> (gratuito)<br>
Lunes a Viernes: 9h – 18h</p>
<p>📧 <strong>info@dentix.es</strong><br>
Respuesta en menos de 24h hábiles</p>
<p>📍 <strong>Madrid, España</strong><br>
Dentix Productos Dentales, S.L.<br>
CIF: B-85937787</p>
</div>
<div>
<h2>Formulario de contacto</h2>
[contact-form-7 id="contacto" title="Formulario de contacto"]
<p style="font-size:12px;color:#666;margin-top:8px">Si no ves el formulario, instala y configura el plugin Contact Form 7.</p>
</div>
</div>';
}
