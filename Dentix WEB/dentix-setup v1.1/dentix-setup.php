<?php
/**
 * Plugin Name: Dentix Setup
 * Plugin URI:  https://www.dentix.es
 * Description: Configura automáticamente WordPress y WooCommerce para Dentix. Ejecutar una sola vez y desactivar.
 * Version:     1.1.0
 * Author:      Dentix Productos Dentales, S.L.
 * Text Domain: dentix-setup
 * Requires at least: 6.4
 * Requires PHP: 8.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ── Menú en el admin ───────────────────────────────────────────
add_action( 'admin_menu', function () {
    add_management_page(
        'Dentix Setup', '🦷 Dentix Setup',
        'manage_options', 'dentix-setup', 'dentix_setup_render_page'
    );
} );

// ── Aviso en el admin ──────────────────────────────────────────
add_action( 'admin_notices', function () {
    if ( ! current_user_can( 'manage_options' ) ) return;
    if ( get_option( 'dentix_setup_complete' ) ) {
        echo '<div class="notice notice-warning is-dismissible"><p><strong>🦷 Dentix Setup:</strong> '
           . 'Configuración completada. Puedes <a href="' . admin_url( 'plugins.php' ) . '">desactivar y eliminar este plugin</a>.</p></div>';
    } else {
        echo '<div class="notice notice-info"><p><strong>🦷 Dentix Setup:</strong> Pendiente. '
           . '<a href="' . admin_url( 'tools.php?page=dentix-setup' ) . '"><strong>Ejecutar configuración →</strong></a></p></div>';
    }
} );

// ── Página ─────────────────────────────────────────────────────
function dentix_setup_render_page() {
    $done = (bool) get_option( 'dentix_setup_complete', false );
    $message = ''; $errors = [];

    if ( isset( $_POST['dentix_run_setup'] ) && check_admin_referer( 'dentix_setup_nonce' ) ) {
        $result  = dentix_execute_setup();
        $message = $result['message'];
        $errors  = $result['errors'];
        if ( empty( $errors ) ) {
            update_option( 'dentix_setup_complete', true );
            update_option( 'dentix_setup_date', current_time( 'mysql' ) );
            $done = true;
        }
    }
    ?>
    <div class="wrap">
        <h1>🦷 Dentix — Configuración inicial</h1>
        <?php if ( $message ) : ?>
            <div class="notice <?php echo empty( $errors ) ? 'notice-success' : 'notice-error'; ?> inline">
                <p><?php echo wp_kses_post( $message ); ?></p>
                <?php foreach ( $errors as $e ) echo '<p>• ' . esc_html( $e ) . '</p>'; ?>
            </div>
        <?php endif; ?>
        <?php if ( ! $done ) : ?>
        <div class="card" style="max-width:640px;padding:24px;margin-top:20px">
            <h2 style="margin-top:0">¿Qué hace este setup?</h2>
            <?php if ( ! dentix_wc_ok() ) : ?>
                <div class="notice notice-error inline"><p>
                    ⚠️ <strong>WooCommerce no está activo.</strong>
                    <a href="<?php echo admin_url('plugin-install.php?s=woocommerce&tab=search&type=term'); ?>">Instálalo primero →</a>
                </p></div>
            <?php endif; ?>
            <ol style="line-height:2.2">
                <li>Páginas legales (Aviso legal, Privacidad, Cookies, Condiciones, Devoluciones)</li>
                <li>WooCommerce para España y B2B (€, IVA excluido, solo registrados)</li>
                <li>Categorías del catálogo odontológico</li>
                <li>Atributo "Marca" con marcas del sector dental</li>
                <li>Envío gratuito a partir de 150 € (España)</li>
                <li>Páginas de WooCommerce (Tienda, Cesta, Checkout, Mi cuenta)</li>
                <li>Permalinks SEO y zona horaria España</li>
            </ol>
            <form method="post">
                <?php wp_nonce_field( 'dentix_setup_nonce' ); ?>
                <input type="submit" name="dentix_run_setup" class="button button-primary button-hero"
                       value="🚀 Ejecutar configuración"
                       <?php echo ! dentix_wc_ok() ? 'disabled' : ''; ?>
                       onclick="return confirm('¿Ejecutar la configuración inicial de Dentix?')">
            </form>
        </div>
        <?php else : ?>
        <div class="notice notice-success inline" style="padding:16px;margin-top:16px">
            <p>✅ <strong>Completado</strong> el <?php echo esc_html( get_option( 'dentix_setup_date', '—' ) ); ?></p>
            <p><a href="<?php echo admin_url('plugins.php'); ?>" class="button">Desactivar este plugin</a></p>
        </div>
        <?php endif; ?>
    </div>
    <?php
}

function dentix_wc_ok(): bool {
    return class_exists( 'WooCommerce' ) && function_exists( 'wc_get_page_id' );
}

// ══════════════════════════════════════════════════════════════
// SETUP PRINCIPAL
// ══════════════════════════════════════════════════════════════
function dentix_execute_setup(): array {
    $errors = []; $log = [];

    // 1. PÁGINAS
    $pages = [
        [ 'title' => 'Inicio',                   'slug' => 'inicio',               'content' => '' ],
        [ 'title' => 'Aviso Legal',              'slug' => 'aviso-legal',          'content' => dentix_page_aviso_legal() ],
        [ 'title' => 'Política de Privacidad',   'slug' => 'politica-privacidad',  'content' => dentix_page_privacidad() ],
        [ 'title' => 'Política de Cookies',      'slug' => 'politica-cookies',     'content' => '<p>Política de cookies gestionada por Complianz.</p>' ],
        [ 'title' => 'Condiciones Generales',    'slug' => 'condiciones-venta',    'content' => dentix_page_condiciones() ],
        [ 'title' => 'Política de Devoluciones', 'slug' => 'politica-devoluciones','content' => dentix_page_devoluciones() ],
        [ 'title' => 'Contacto',                 'slug' => 'contacto',             'content' => '<p>Contacto: <strong>info@dentix.es</strong> · <strong>900 123 456</strong> (Lunes–Viernes 9–18h)</p>' ],
    ];

    $page_ids = [];
    foreach ( $pages as $p ) {
        $existing = get_page_by_path( $p['slug'] );
        if ( $existing ) {
            $page_ids[ $p['slug'] ] = $existing->ID;
        } else {
            $id = wp_insert_post( [
                'post_title'   => $p['title'],
                'post_name'    => $p['slug'],
                'post_content' => $p['content'],
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'comment_status' => 'closed',
            ], true );
            if ( is_wp_error( $id ) ) {
                $errors[] = 'Error creando "' . $p['title'] . '": ' . $id->get_error_message();
            } else {
                $page_ids[ $p['slug'] ] = $id;
                $log[] = 'Página creada: ' . $p['title'];
            }
        }
    }

    // Homepage estática
    if ( isset( $page_ids['inicio'] ) ) {
        update_option( 'show_on_front', 'page' );
        update_option( 'page_on_front', $page_ids['inicio'] );
        $log[] = 'Homepage estática configurada';
    }

    // 2. WOOCOMMERCE
    if ( ! dentix_wc_ok() ) {
        $errors[] = 'WooCommerce no estaba activo — configuración de WooCommerce omitida.';
    } else {
        // Moneda y formato España
        update_option( 'woocommerce_default_country',     'ES' );
        update_option( 'woocommerce_currency',             'EUR' );
        update_option( 'woocommerce_currency_pos',         'right_space' );
        update_option( 'woocommerce_price_decimal_sep',    ',' );
        update_option( 'woocommerce_price_thousand_sep',   '.' );
        update_option( 'woocommerce_price_num_decimals',   '2' );
        // IVA — B2B (sin IVA en precios de catálogo)
        update_option( 'woocommerce_calc_taxes',           'yes' );
        update_option( 'woocommerce_prices_include_tax',   'no' );
        update_option( 'woocommerce_tax_display_shop',     'excl' );
        update_option( 'woocommerce_tax_display_cart',     'incl' );
        update_option( 'woocommerce_tax_based_on',         'billing' );
        // Stock
        update_option( 'woocommerce_manage_stock',         'yes' );
        update_option( 'woocommerce_notify_low_stock',     'yes' );
        update_option( 'woocommerce_notify_low_stock_amount', 5 );
        update_option( 'woocommerce_notify_no_stock',      'yes' );
        update_option( 'woocommerce_stock_email_recipient', get_option( 'admin_email' ) );
        // Solo registrados (B2B)
        update_option( 'woocommerce_enable_guest_checkout',         'no' );
        update_option( 'woocommerce_enable_myaccount_registration', 'yes' );
        update_option( 'woocommerce_registration_generate_password','yes' );
        // HPOS
        update_option( 'woocommerce_feature_custom_order_tables_enabled', 'yes' );
        $log[] = 'WooCommerce configurado para España/B2B';

        // IVA (escritura directa en tabla — no usa métodos internos de WC)
        dentix_insert_tax_rates();
        $log[] = 'Tipos de IVA configurados';

        // Zona de envío España
        dentix_create_shipping();
        $log[] = 'Zona de envío España configurada';

        // Páginas de WooCommerce
        $wc_pages = [
            'shop'      => [ 'Tienda',    '' ],
            'cart'      => [ 'Cesta',     '[woocommerce_cart]' ],
            'checkout'  => [ 'Checkout',  '[woocommerce_checkout]' ],
            'myaccount' => [ 'Mi cuenta', '[woocommerce_my_account]' ],
        ];
        foreach ( $wc_pages as $key => [ $title, $content ] ) {
            $pid = wc_get_page_id( $key );
            if ( ! $pid || $pid < 1 || ! get_post( $pid ) ) {
                $id = wp_insert_post( [
                    'post_title'   => $title,
                    'post_name'    => $key,
                    'post_content' => $content,
                    'post_status'  => 'publish',
                    'post_type'    => 'page',
                ] );
                if ( ! is_wp_error( $id ) ) {
                    update_option( 'woocommerce_' . $key . '_page_id', $id );
                    $log[] = 'Página WC creada: ' . $title;
                }
            }
        }

        // Categorías
        $cats = [
            [ 'Instrumental Quirúrgico', 'instrumental'   ],
            [ 'Endodoncia',              'endodoncia'      ],
            [ 'Ortodoncia',              'ortodoncia'      ],
            [ 'Implantología',           'implantologia'   ],
            [ 'Material Clínico',        'material-clinico'],
            [ 'Esterilización',          'esterilizacion'  ],
            [ 'Equipamiento',            'equipamiento'    ],
            [ 'Radiología',              'radiologia'      ],
            [ 'Novedades',               'novedades'       ],
            [ 'Ofertas',                 'ofertas'         ],
        ];
        foreach ( $cats as [ $name, $slug ] ) {
            if ( ! get_term_by( 'slug', $slug, 'product_cat' ) ) {
                wp_insert_term( $name, 'product_cat', [ 'slug' => $slug ] );
            }
        }
        $log[] = 'Categorías del catálogo creadas';

        // Atributo Marca
        if ( function_exists( 'wc_create_attribute' ) && ! wc_attribute_taxonomy_id_by_name( 'marca' ) ) {
            wc_create_attribute( [
                'name' => 'Marca', 'slug' => 'marca',
                'type' => 'select', 'order_by' => 'name', 'has_archives' => false,
            ] );
            $log[] = 'Atributo "Marca" creado';
        }
        // Insertar marcas del sector dental
        if ( ! taxonomy_exists( 'pa_marca' ) ) {
            register_taxonomy( 'pa_marca', 'product' );
        }
        foreach ( [ 'Hu-Friedy','NSK','KaVo','Dentsply Sirona','Straumann','3M','GC','Ivoclar','Nobel Biocare','Ormco','Coltene','Kerr' ] as $m ) {
            if ( ! term_exists( $m, 'pa_marca' ) ) wp_insert_term( $m, 'pa_marca' );
        }
        $log[] = 'Marcas del sector dental creadas';
    }

    // 3. WORDPRESS GENERAL
    update_option( 'blogname',             'Dentix Productos Dentales' );
    update_option( 'blogdescription',      'Instrumental odontológico profesional B2B' );
    update_option( 'timezone_string',      'Europe/Madrid' );
    update_option( 'date_format',          'd/m/Y' );
    update_option( 'time_format',          'H:i' );
    update_option( 'default_comment_status', 'closed' );
    update_option( 'default_ping_status',    'closed' );
    update_option( 'permalink_structure',  '/%postname%/' );
    flush_rewrite_rules();
    $log[] = 'Ajustes WordPress configurados';

    $msg = empty( $errors )
        ? '<strong>✅ Configuración completada.</strong> ' . count( $log ) . ' acciones realizadas.'
        : '<strong>⚠️ Completado con advertencias.</strong> ' . count( $errors ) . ' errores (ver abajo).';

    return [ 'message' => $msg, 'errors' => $errors, 'log' => $log ];
}

// ── IVA: escritura directa en tabla de WooCommerce ────────────
function dentix_insert_tax_rates(): void {
    global $wpdb;
    $table = $wpdb->prefix . 'woocommerce_tax_rates';
    if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) !== $table ) return;
    $existing = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE tax_rate_country=%s", 'ES' ) );
    if ( $existing > 0 ) return;
    $wpdb->insert( $table, [ 'tax_rate_country'=>'ES','tax_rate_state'=>'','tax_rate'=>'21.0000','tax_rate_name'=>'IVA 21%','tax_rate_priority'=>1,'tax_rate_compound'=>0,'tax_rate_shipping'=>1,'tax_rate_order'=>0,'tax_rate_class'=>'' ] );
    $wpdb->insert( $table, [ 'tax_rate_country'=>'ES','tax_rate_state'=>'','tax_rate'=>'4.0000','tax_rate_name'=>'IVA 4% Sanitario','tax_rate_priority'=>1,'tax_rate_compound'=>0,'tax_rate_shipping'=>0,'tax_rate_order'=>1,'tax_rate_class'=>'reduced-rate' ] );
}

// ── Zona de envío España ──────────────────────────────────────
function dentix_create_shipping(): void {
    if ( ! class_exists( 'WC_Shipping_Zones' ) ) return;
    foreach ( WC_Shipping_Zones::get_zones() as $z ) {
        if ( stripos( $z['zone_name'], 'espa' ) !== false ) return;
    }
    try {
        $zone = new WC_Shipping_Zone();
        $zone->set_zone_name( 'España' );
        $zone->add_location( 'ES', 'country' );
        $zid = $zone->save();
        if ( ! $zid ) return;
        $fid = $zone->add_shipping_method( 'flat_rate' );
        if ( $fid ) update_option( 'woocommerce_flat_rate_' . $fid . '_settings', [ 'enabled'=>'yes','title'=>'Envío estándar','cost'=>'6.90','tax_status'=>'taxable' ] );
        $gid = $zone->add_shipping_method( 'free_shipping' );
        if ( $gid ) update_option( 'woocommerce_free_shipping_' . $gid . '_settings', [ 'enabled'=>'yes','title'=>'Envío gratuito','requires'=>'min_amount','min_amount'=>'150','ignore_discounts'=>'no' ] );
    } catch ( \Throwable $e ) { /* fallo no crítico */ }
}

// ══════════════════════════════════════════════════════════════
// CONTENIDOS PÁGINAS LEGALES
// ══════════════════════════════════════════════════════════════
function dentix_page_aviso_legal(): string {
    return '<h2>1. Datos identificativos</h2><p>En cumplimiento del artículo 10 de la Ley 34/2002 (LSSI), el titular de este sitio es:</p><ul><li><strong>Razón social:</strong> Dentix Productos Dentales, S.L.</li><li><strong>CIF:</strong> B-85937787</li><li><strong>Registro Mercantil:</strong> Madrid, Tomo 27.796, Folio 125, Sección 8, Hoja M-500933</li><li><strong>Email:</strong> info@dentix.es</li></ul><h2>2. Condiciones de acceso</h2><p>Acceso restringido a profesionales del sector odontológico acreditados (B2B).</p><h2>3. Propiedad intelectual</h2><p>Todos los contenidos son propiedad de Dentix Productos Dentales, S.L. o de terceros con licencia.</p><h2>4. Resolución de litigios (ODR)</h2><p><a href="https://ec.europa.eu/consumers/odr" target="_blank" rel="noopener">https://ec.europa.eu/consumers/odr</a></p>';
}
function dentix_page_privacidad(): string {
    return '<h2>1. Responsable</h2><p>Dentix Productos Dentales, S.L. · CIF B-85937787 · info@dentix.es</p><h2>2. Finalidad</h2><p>Gestión de pedidos, facturación, relación comercial y cumplimiento legal.</p><h2>3. Base jurídica</h2><p>Ejecución de contrato y cumplimiento de obligaciones legales.</p><h2>4. Conservación</h2><p>Durante la relación comercial y plazos legales (hasta 10 años para datos fiscales).</p><h2>5. Sus derechos</h2><p>Acceso, rectificación, supresión, oposición y portabilidad enviando escrito a info@dentix.es. Puede reclamar ante la <a href="https://www.aepd.es" target="_blank" rel="noopener">AEPD</a>.</p>';
}
function dentix_page_condiciones(): string {
    return '<h2>1. Ámbito</h2><p>Condiciones entre Dentix Productos Dentales, S.L. y clientes profesionales (B2B) en www.dentix.es.</p><h2>2. Precios</h2><p>Sin IVA. IVA calculado en checkout (21% general o 4% sanitario).</p><h2>3. Pago</h2><p>GetNet, Visa, Mastercard, Bizum, PayPal, Apple Pay, Google Pay, Klarna y transferencia.</p><h2>4. Entrega</h2><p>24–48 horas hábiles en Península. Plazos orientativos.</p><h2>5. Ley aplicable</h2><p>Legislación española. Juzgados de Madrid.</p>';
}
function dentix_page_devoluciones(): string {
    return '<h2>Derecho de desistimiento — 14 días</h2><p>Tiene derecho a desistir en <strong>14 días naturales</strong> desde la recepción (RDL 1/2007 y Ley 3/2014).</p><h2>Cómo ejercerlo</h2><p>Email a <strong>devoluciones@dentix.es</strong> con número de pedido y productos a devolver.</p><h2>Condiciones</h2><p>Producto en estado original, sin usar y en embalaje original. No se admiten productos estériles abiertos.</p><h2>Reembolso</h2><p>Máximo <strong>14 días</strong> desde recepción de la devolución, con el mismo método de pago.</p>';
}
