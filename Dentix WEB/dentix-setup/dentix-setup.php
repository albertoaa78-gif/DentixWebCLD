<?php
/**
 * Plugin Name: Dentix Setup
 * Description: Configura Dentix: 8 especialidades, productos demo, WooCommerce B2B. Desactivar tras usar.
 * Version:     3.4.0
 * Author:      Dentix Productos Dentales, S.L.
 */
if (!defined('ABSPATH')) exit;

add_action('admin_menu', function () {
    add_management_page('Dentix Setup', '🦷 Dentix Setup', 'manage_options', 'dentix-setup', 'dentix_setup_render_page');
});

add_action('admin_notices', function () {
    if (!current_user_can('manage_options')) return;
    if (get_option('dentix_setup_complete')) {
        echo '<div class="notice notice-warning is-dismissible"><p><strong>🦷 Dentix Setup:</strong> Completado. <a href="' . admin_url('plugins.php') . '">Desactivar y eliminar →</a></p></div>';
    } else {
        echo '<div class="notice notice-info"><p><strong>🦷 Dentix Setup:</strong> <a href="' . admin_url('tools.php?page=dentix-setup') . '"><strong>Ejecutar configuración inicial →</strong></a></p></div>';
    }
});

function dentix_setup_render_page() {
    // Permitir re-ejecutar via GET param ?dentix_reset=1
    if (isset($_GET['dentix_reset']) && wp_verify_nonce($_GET['_wpnonce'], 'dentix_reset')) {
        delete_option('dentix_setup_complete');
        delete_option('dentix_setup_date');
        wp_redirect(admin_url('tools.php?page=dentix-setup&dentix_resetok=1'));
        exit;
    }

    $done = (bool)get_option('dentix_setup_complete', false);
    $msg = ''; $errors = [];
    if (isset($_POST['dentix_run_setup']) && check_admin_referer('dentix_setup_nonce')) {
        $result = dentix_v3_execute();
        $msg = $result['message']; $errors = $result['errors'];
        if (empty($errors)) { update_option('dentix_setup_complete', true); update_option('dentix_setup_date', current_time('mysql')); $done = true; }
    }
    ?>
    <div class="wrap">
      <h1>🦷 Dentix Setup v3.0</h1>
      <?php if (isset($_GET['dentix_resetok'])) : ?>
        <div class="notice notice-info is-dismissible"><p>Estado reiniciado. Ya puedes ejecutar la configuracion de nuevo.</p></div>
      <?php endif; ?>
      <?php if ($msg) : ?>
        <div class="notice <?php echo empty($errors)?'notice-success':'notice-error'; ?> inline">
          <p><?php echo wp_kses_post($msg); ?></p>
          <?php foreach ($errors as $e) echo '<p>• '.esc_html($e).'</p>'; ?>
        </div>
      <?php endif; ?>
      <?php if (!$done) : ?>
      <div class="card" style="max-width:640px;padding:24px;margin-top:16px">
        <h2 style="margin-top:0">¿Qué hace este setup?</h2>
        <?php if (!class_exists('WooCommerce')) : ?>
          <div class="notice notice-error inline"><p>⚠️ <strong>WooCommerce no está activo.</strong> <a href="<?php echo admin_url('plugin-install.php?s=woocommerce&tab=search'); ?>">Instálalo primero →</a></p></div>
        <?php endif; ?>
        <ol style="line-height:2.4">
          <li>🗂️ Crea las <strong>8 especialidades</strong>: Cirugía, Diagnóstico, Periodoncia, Restauradora, Implantología, Ortodoncia, Laboratorio, Accesorios</li>
          <li>🛍️ Crea <strong>16 productos de demo</strong> (2 por especialidad) para visualizar el diseño</li>
          <li>⚙️ Configura <strong>WooCommerce</strong> para España y B2B</li>
          <li>📄 Crea las <strong>páginas legales</strong> obligatorias</li>
          <li>🚚 Configura <strong>envío gratuito</strong> a partir de 150€</li>
          <li>🏠 Establece la <strong>homepage estática</strong></li>
        </ol>
        <p style="background:#fff3cd;border-left:3px solid #ffc107;padding:10px 14px;font-size:13px">
          ℹ️ Los productos de demo son ficticios. Serán <strong>reemplazados</strong> por los reales cuando se conecte SAGE 50.
        </p>
        <form method="post">
          <?php wp_nonce_field('dentix_setup_nonce'); ?>
          <input type="submit" name="dentix_run_setup" class="button button-primary button-hero"
            value="🚀 Ejecutar configuración"
            <?php echo !class_exists('WooCommerce') ? 'disabled' : ''; ?>
            onclick="return confirm('¿Ejecutar la configuración inicial de Dentix?')">
        </form>
      </div>
      <?php else : ?>
        <div class="notice notice-success inline" style="padding:16px;margin-top:16px">
          <p>✅ <strong>Completado</strong> el <?php echo esc_html(get_option('dentix_setup_date','—')); ?></p>
          <div style="margin-top:10px;display:flex;gap:10px;flex-wrap:wrap">
            <a href="<?php echo admin_url('edit.php?post_type=product'); ?>" class="button">Ver productos</a>
            <a href="<?php echo admin_url('edit-tags.php?taxonomy=product_cat&post_type=product'); ?>" class="button">Ver especialidades</a>
            <a href="<?php echo admin_url('plugins.php'); ?>" class="button button-secondary">Desactivar plugin</a>
          </div>
        </div>
        <div style="margin-top:16px;padding:16px;background:#fff8e1;border:1px solid #ffe082;border-radius:6px;max-width:640px">
          <p style="margin:0 0 12px;font-size:13px">
            <strong>Volver a ejecutar el setup</strong> — por ejemplo para regenerar la homepage o las categorias.
          </p>
          <a href="<?php echo esc_url(wp_nonce_url(admin_url('tools.php?page=dentix-setup&dentix_reset=1'), 'dentix_reset')); ?>"
             class="button button-secondary"
             onclick="return confirm('Esto volvera a ejecutar la configuracion inicial. Continuar?')">
            Volver a ejecutar
          </a>
        </div>
      <?php endif; ?>
    </div>
    <?php
}

// ══════════════════════════════════════════════════════════════
function dentix_v3_execute(): array {
    $errors = []; $log = [];

    // ── Limpiar categorias antiguas de versiones anteriores ──
    $old_slugs = [
        'instrumental','endodoncia','ortodoncia','implantologia',
        'material-clinico','esterilizacion','equipamiento','radiologia',
        'novedades','ofertas','marcas',
    ];
    foreach ($old_slugs as $slug) {
        $term = get_term_by('slug', $slug, 'product_cat');
        if ($term && !is_wp_error($term)) {
            // Solo borrar si no tiene productos reales de SAGE (SKU no empieza por 'D')
            $has_real = false;
            $products = get_posts(['post_type'=>'product','numberposts'=>-1,
                'tax_query'=>[['taxonomy'=>'product_cat','field'=>'term_id','terms'=>$term->term_id]]]);
            foreach ($products as $p) {
                $sku = get_post_meta($p->ID, '_sku', true);
                if ($sku && !preg_match('/^D[A-Z]{2}-/', $sku)) { $has_real = true; break; }
            }
            if (!$has_real) {
                wp_delete_term($term->term_id, 'product_cat');
                $log[] = 'Categoria antigua eliminada: ' . $slug;
            }
        }
    }

    // ── Especialidades (8 categorias nuevas) ─────────────────
    $especialidades = [
        ['name'=>'Cirugía',       'slug'=>'cirugia',       'desc'=>'Instrumental quirúrgico, bisturíes, fórceps y material de cirugía oral',                       'color'=>'#F0EEED'],
        ['name'=>'Diagnóstico',   'slug'=>'diagnostico',   'desc'=>'Exploradores, espejos, sondas y equipos de diagnóstico clínico',                               'color'=>'#EDF2F7'],
        ['name'=>'Periodoncia',   'slug'=>'periodoncia',   'desc'=>'Curetas, raspadores y material de tratamiento periodontal',                                     'color'=>'#F0F4F0'],
        ['name'=>'Restauradora',  'slug'=>'restauradora',  'desc'=>'Composite, cementos, adhesivos y material de restauración directa',                            'color'=>'#FDF8F0'],
        ['name'=>'Implantología', 'slug'=>'implantologia', 'desc'=>'Implantes, componentes protésicos y material de regeneración ósea',                            'color'=>'#F0F0F7'],
        ['name'=>'Ortodoncia',    'slug'=>'ortodoncia',    'desc'=>'Brackets, arcos, ligaduras y material de ortodoncia fija y removible',                         'color'=>'#F7F0F0'],
        ['name'=>'Laboratorio',   'slug'=>'laboratorio',   'desc'=>'Materiales de prótesis, escayolas, ceras y equipos de laboratorio dental',                     'color'=>'#F0F7F4'],
        ['name'=>'Accesorios',    'slug'=>'accesorios',    'desc'=>'Guantes, mascarillas, baberos y accesorios clínicos desechables',                              'color'=>'#F5F0EC'],
    ];

    $cat_ids = [];
    foreach ($especialidades as $esp) {
        $existing = get_term_by('slug', $esp['slug'], 'product_cat');
        if ($existing) {
            $cat_ids[$esp['slug']] = $existing->term_id;
        } else {
            $t = wp_insert_term($esp['name'], 'product_cat', ['slug'=>$esp['slug'],'description'=>$esp['desc']]);
            if (!is_wp_error($t)) {
                $cat_ids[$esp['slug']] = $t['term_id'];
                // Guardar color como meta para uso en el tema
                update_term_meta($t['term_id'], 'dentix_color', $esp['color']);
                $log[] = 'Especialidad creada: '.$esp['name'];
            } else {
                $errors[] = 'Error creando '.$esp['name'].': '.$t->get_error_message();
            }
        }
    }
    $log[] = '8 especialidades configuradas';

    // ── Atributo Marca ───────────────────────────────────────
    if (function_exists('wc_create_attribute') && !wc_attribute_taxonomy_id_by_name('marca')) {
        wc_create_attribute(['name'=>'Marca','slug'=>'marca','type'=>'select','order_by'=>'name','has_archives'=>false]);
    }
    if (!taxonomy_exists('pa_marca')) register_taxonomy('pa_marca', 'product');
    foreach (['Hu-Friedy','NSK','KaVo','Dentsply Sirona','Straumann','3M','GC','Ivoclar','Nobel Biocare','Ormco','Coltene','Kerr'] as $m) {
        if (!term_exists($m, 'pa_marca')) wp_insert_term($m, 'pa_marca');
    }
    $log[] = 'Atributo Marca y marcas creados';

    // ── Productos de demo (2 por especialidad) ───────────────
    $demo_products = [
        ['cirugia',      'Fórceps Extractores Pedo Superior',   'DEF-001', 85.00,  68.00,  'Hu-Friedy', 'Fórceps de extracción pediátrico para dientes superiores. Acero inoxidable de alta calidad. Autoclavable.'],
        ['cirugia',      'Bisturí Mango Metálico nº 3',         'DEF-002', 12.50,  null,   'Hu-Friedy', 'Mango de bisturí reutilizable en acero quirúrgico. Compatible con hojas estándar nº 10, 11, 12 y 15.'],
        ['diagnostico',  'Espejo Dental nº 5 con Mango',        'DDG-001', 8.90,   null,   'Hu-Friedy', 'Espejo intraoral plano nº 5. Mango de aluminio ligero. Superficie antivaho de alta definición.'],
        ['diagnostico',  'Sonda Periodontal OMS',               'DDG-002', 14.20,  11.00,  'Hu-Friedy', 'Sonda periodontal de referencia OMS. Marcas a 3.5, 5.5, 8.5 y 11.5mm. Punta esferoidal 0.5mm.'],
        ['periodoncia',  'Cureta Gracey 1/2 Universal',         'DPR-001', 45.00,  null,   'Hu-Friedy', 'Cureta Gracey 1/2 para raspado y alisado radicular en dientes anteriores. Doble extremo activo.'],
        ['periodoncia',  'Kit Períodos Básico 5 piezas',        'DPR-002', 189.00, 149.00, 'Hu-Friedy', 'Kit de instrumentos periodontales básicos: cureta Gracey, punta ultrasónica, sonda, espejo y sonda OMS.'],
        ['restauradora', 'Composite Filtek Z350 XT A2 Body',    'DRS-001', 38.50,  null,   '3M',        'Composite nanorelleno universal Filtek Z350 XT, jeringa 4g, tono A2 Body. Alta resistencia al desgaste.'],
        ['restauradora', 'Adhesivo Universal Scotchbond',       'DRS-002', 62.00,  52.00,  '3M',        'Adhesivo universal de 7ª generación. Compatible con técnicas autograbado, grabado selectivo y total.'],
        ['implantologia','Implante SLActive Standard Plus 4.1',  'DIM-001', 185.00, null,   'Straumann', 'Implante titanio SLActive diámetro 4.1mm, longitud 10mm. Conexión RC. Superficie SLActive hidrofílica.'],
        ['implantologia','Membrana Reabsorbible Bio-Gide 25x25', 'DIM-002', 145.00, 119.00, 'Geistlich', 'Membrana de colágeno bicapa reabsorbible para regeneración ósea guiada. 25x25mm.'],
        ['ortodoncia',   'Brackets Metálicos MBT Roth 0.022"',  'DOR-001', 95.00,  null,   'Ormco',     'Brackets metálicos prescripción MBT Roth slot 0.022". Pack 20 brackets. Base de malla estándar.'],
        ['ortodoncia',   'Arco Nitinol Termoactivado 0.014"',   'DOR-002', 18.50,  null,   'Ormco',     'Arco de NiTi termoactivado redondo 0.014". Compatible con brackets slot 0.022". Pack 10 uds.'],
        ['laboratorio',  'Yeso Tipo IV Fuji Rock EP 4kg',       'DLB-001', 42.00,  null,   'GC',        'Yeso de alta resistencia tipo IV para modelos de trabajo. Expansión controlada. 4kg polvo.'],
        ['laboratorio',  'Cera de Modelado Roja 500g',          'DLB-002', 15.00,  null,   'GC',        'Cera dental de modelado color rojo. Alta plasticidad. Punto de fusión controlado. 500g.'],
        ['accesorios',   'Guantes Nitrilo Talla M (100 uds)',   'DAC-001', 12.90,  null,   'Sempermed', 'Guantes de nitrilo sin polvo talla M. Color azul. Texturizados en dedos. Caja 100 unidades.'],
        ['accesorios',   'Mascarilla IIR 3 Capas (50 uds)',     'DAC-002', 9.50,   7.90,   'Foliodress', 'Mascarilla quirúrgica tipo IIR, 3 capas, eficacia 99%. BFE ≥98%. Caja 50 unidades.'],
    ];

    $created_products = 0;
    foreach ($demo_products as [$cat_slug, $name, $sku, $price, $sale, $brand, $desc]) {
        // Saltar si ya existe un producto con este SKU
        $existing = wc_get_product_id_by_sku($sku);
        if ($existing) continue;

        $product = new WC_Product_Simple();
        $product->set_name($name);
        $product->set_sku($sku);
        $product->set_regular_price((string)$price);
        if ($sale) $product->set_sale_price((string)$sale);
        $product->set_description($desc);
        $product->set_short_description($desc);
        $product->set_status('publish');
        $product->set_catalog_visibility('visible');
        $product->set_manage_stock(true);
        $product->set_stock_quantity(rand(5, 50));
        $product->set_stock_status('instock');
        $product->set_featured($created_products < 8); // Primeros 8 como destacados

        // Categoría
        if (isset($cat_ids[$cat_slug])) {
            $product->set_category_ids([$cat_ids[$cat_slug]]);
        }

        $product_id = $product->save();

        if ($product_id) {
            // Asignar marca
            if (taxonomy_exists('pa_marca')) {
                $brand_term = get_term_by('name', $brand, 'pa_marca');
                if ($brand_term) {
                    wp_set_post_terms($product_id, [$brand_term->term_id], 'pa_marca');
                }
            }
            $created_products++;
        }
    }
    $log[] = "{$created_products} productos de demo creados";

    // ── WooCommerce ──────────────────────────────────────────
    if (class_exists('WooCommerce')) {
        update_option('woocommerce_default_country',      'ES');
        update_option('woocommerce_currency',              'EUR');
        update_option('woocommerce_currency_pos',          'right_space');
        update_option('woocommerce_price_decimal_sep',     ',');
        update_option('woocommerce_price_thousand_sep',    '.');
        update_option('woocommerce_price_num_decimals',    '2');
        update_option('woocommerce_calc_taxes',            'yes');
        update_option('woocommerce_prices_include_tax',    'no');
        update_option('woocommerce_tax_display_shop',      'excl');
        update_option('woocommerce_tax_display_cart',      'incl');
        update_option('woocommerce_manage_stock',          'yes');
        update_option('woocommerce_enable_guest_checkout', 'no');
        update_option('woocommerce_enable_myaccount_registration', 'yes');
        update_option('woocommerce_feature_custom_order_tables_enabled', 'yes');

        // IVA España
        global $wpdb;
        $table = $wpdb->prefix . 'woocommerce_tax_rates';
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table}'")===$table) {
            $existing = (int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$table} WHERE tax_rate_country=%s",'ES'));
            if (!$existing) {
                $wpdb->insert($table,['tax_rate_country'=>'ES','tax_rate_state'=>'','tax_rate'=>'21.0000','tax_rate_name'=>'IVA 21%','tax_rate_priority'=>1,'tax_rate_compound'=>0,'tax_rate_shipping'=>1,'tax_rate_order'=>0,'tax_rate_class'=>'']);
                $wpdb->insert($table,['tax_rate_country'=>'ES','tax_rate_state'=>'','tax_rate'=>'4.0000','tax_rate_name'=>'IVA 4% Sanitario','tax_rate_priority'=>1,'tax_rate_compound'=>0,'tax_rate_shipping'=>0,'tax_rate_order'=>1,'tax_rate_class'=>'reduced-rate']);
            }
        }
        $log[] = 'WooCommerce configurado para España/B2B';

        // Zona de envío España
        if (class_exists('WC_Shipping_Zones')) {
            $zones = WC_Shipping_Zones::get_zones();
            $has_spain = false;
            foreach ($zones as $z) { if (stripos($z['zone_name'],'espa')!==false) { $has_spain=true; break; } }
            if (!$has_spain) {
                try {
                    $zone = new WC_Shipping_Zone();
                    $zone->set_zone_name('España');
                    $zone->add_location('ES','country');
                    $zid = $zone->save();
                    if ($zid) {
                        $fid = $zone->add_shipping_method('flat_rate');
                        if ($fid) update_option('woocommerce_flat_rate_'.$fid.'_settings',['enabled'=>'yes','title'=>'Envío estándar','cost'=>'6.90','tax_status'=>'taxable']);
                        $gid = $zone->add_shipping_method('free_shipping');
                        if ($gid) update_option('woocommerce_free_shipping_'.$gid.'_settings',['enabled'=>'yes','title'=>'Envío gratuito','requires'=>'min_amount','min_amount'=>'150']);
                    }
                } catch(\Throwable $e) {}
            }
            $log[] = 'Zona de envío España configurada';
        }

        // Páginas WooCommerce
        foreach (['shop'=>['Tienda',''],'cart'=>['Cesta','[woocommerce_cart]'],'checkout'=>['Checkout','[woocommerce_checkout]'],'myaccount'=>['Mi cuenta','[woocommerce_my_account]']] as $key=>[$title,$content]) {
            $pid = wc_get_page_id($key);
            if (!$pid||$pid<1||!get_post($pid)) {
                $id = wp_insert_post(['post_title'=>$title,'post_name'=>$key,'post_content'=>$content,'post_status'=>'publish','post_type'=>'page']);
                if (!is_wp_error($id)) { update_option('woocommerce_'.$key.'_page_id',$id); $log[]='Página WC creada: '.$title; }
            }
        }
    }

    // ── Páginas legales ──────────────────────────────────────
    $legal_pages = [
        ['Inicio','inicio',''],
        ['Aviso Legal','aviso-legal','<h2>Datos identificativos</h2><p><strong>Dentix Productos Dentales, S.L.</strong> · CIF B-85937787 · Registro Mercantil de Madrid, Tomo 27.796, Folio 125. Email: info@dentix.es</p>'],
        ['Política de Privacidad','politica-privacidad','<p>Responsable: Dentix Productos Dentales, S.L. · CIF B-85937787. Finalidad: gestión de pedidos y relación comercial. Derechos: acceso, rectificación y supresión enviando email a info@dentix.es.</p>'],
        ['Política de Cookies','politica-cookies','<p>Este sitio usa cookies técnicas y analíticas. Puedes gestionar tus preferencias en el banner de cookies.</p>'],
        ['Condiciones Generales','condiciones-venta','<p>Tienda B2B dirigida exclusivamente a profesionales del sector odontológico. Precios sin IVA. Pago: GetNet, Stripe, Bizum, PayPal, Klarna. Entrega 24-48h Península.</p>'],
        ['Política de Devoluciones','politica-devoluciones','<h2>Derecho de desistimiento — 14 días</h2><p>Notificar a devoluciones@dentix.es con número de pedido. Reembolso en 14 días desde recepción de la devolución.</p>'],
        ['Contacto','contacto','<p>📞 <strong>'.dentix_opt('phone','900 123 456').'</strong> · Lunes–Viernes 9–18h<br>📧 <strong>info@dentix.es</strong></p>'],
    ];

    $page_ids = [];
    foreach ($legal_pages as [$title,$slug,$content]) {
        $e = get_page_by_path($slug);
        $page_ids[$slug] = $e ? $e->ID : null;
        if (!$e) {
            $id = wp_insert_post(['post_title'=>$title,'post_name'=>$slug,'post_content'=>$content,'post_status'=>'publish','post_type'=>'page','comment_status'=>'closed'],true);
            if (!is_wp_error($id)) { $page_ids[$slug]=$id; $log[]='Página creada: '.$title; }
        }
    }

    // Homepage estática — siempre forzar aunque la página ya existiera
    $inicio_page = get_page_by_path('inicio');
    $inicio_id   = $inicio_page ? $inicio_page->ID : ($page_ids['inicio'] ?? 0);
    if ($inicio_id) {
        update_option('show_on_front', 'page');
        update_option('page_on_front',  $inicio_id);
        $log[] = 'Homepage estatica configurada (ID: ' . $inicio_id . ')';
    } else {
        $errors[] = 'No se encontro la pagina de inicio';
    }

    // WordPress general
    update_option('blogname','Dentix Productos Dentales');
    update_option('blogdescription','Instrumental odontológico profesional B2B');
    update_option('timezone_string','Europe/Madrid');
    update_option('date_format','d/m/Y');
    update_option('time_format','H:i');
    update_option('default_comment_status','closed');
    update_option('permalink_structure','/%postname%/');
    flush_rewrite_rules();
    $log[]='Ajustes WordPress configurados';

    $msg = empty($errors)
        ? '<strong>✅ Todo listo.</strong> '.count($log).' acciones realizadas. <a href="'.get_permalink(wc_get_page_id('shop')).'">Ver tienda →</a>'
        : '<strong>⚠️ Completado con '.count($errors).' advertencias.</strong>';
    return ['message'=>$msg,'errors'=>$errors,'log'=>$log];
}
