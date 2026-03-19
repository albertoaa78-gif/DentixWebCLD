<?php
/**
 * Dentix — Página de configuración del tema
 * wp-admin → Apariencia → Configuración Dentix
 */
defined('ABSPATH') || exit;

// ── Registrar submenú bajo Apariencia ─────────────────────────
add_action('admin_menu', function () {
    add_submenu_page(
        'themes.php',                    // padre: Apariencia
        'Configuracion Dentix',          // título página (sin emoji — más compatible)
        'Configuracion Dentix',          // texto en el menú
        'manage_options',
        'dentix-settings',
        'dentix_settings_page'
    );
}, 99);

// ── Registrar y guardar opciones ───────────────────────────────
add_action('admin_init', function () {
    register_setting('dentix_settings_group', 'dentix_options', [
        'sanitize_callback' => 'dentix_sanitize_options',
    ]);
});

function dentix_sanitize_options($input): array {
    $clean = [];
    $clean['phone']         = sanitize_text_field($input['phone']         ?? '900 123 456');
    $clean['email']         = sanitize_email($input['email']              ?? 'info@dentix.es');
    $clean['free_shipping'] = absint($input['free_shipping']              ?? 150);
    $clean['footer_desc']   = sanitize_textarea_field($input['footer_desc'] ?? '');
    $clean['show_hero']     = !empty($input['show_hero'])     ? 1 : 0;
    $clean['show_cats']     = !empty($input['show_cats'])     ? 1 : 0;
    $clean['show_featured'] = !empty($input['show_featured']) ? 1 : 0;
    $clean['cats_in_nav']   = array_map('sanitize_text_field', (array)($input['cats_in_nav'] ?? []));
    return $clean;
}

function dentix_opt(string $key, $default = '') {
    static $opts = null;
    if ($opts === null) $opts = get_option('dentix_options', []);
    return $opts[$key] ?? $default;
}

// ── Renderizar página ──────────────────────────────────────────
function dentix_settings_page() {
    if (!current_user_can('manage_options')) return;

    // Guardar si viene de formulario
    if (isset($_POST['dentix_save']) && check_admin_referer('dentix_settings_save')) {
        $sanitized = dentix_sanitize_options($_POST['dentix_options'] ?? []);
        update_option('dentix_options', $sanitized);
        echo '<div class="notice notice-success is-dismissible"><p><strong>Configuracion guardada correctamente.</strong></p></div>';
    }

    $cats_in_nav = dentix_opt('cats_in_nav', array_column(dentix_get_categories(), 'slug'));
    ?>
    <div class="wrap">
      <h1>Dentix &mdash; Configuracion del tema</h1>
      <p style="color:#666;margin-bottom:20px">
        Las <strong>especialidades y categorias</strong> se gestionan en
        <a href="<?php echo admin_url('edit-tags.php?taxonomy=product_cat&post_type=product'); ?>">
          Productos &rarr; Categorias
        </a>
      </p>

      <form method="post">
        <?php wp_nonce_field('dentix_settings_save'); ?>
        <input type="hidden" name="dentix_save" value="1">

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;max-width:960px">

          <!-- Izquierda: datos y homepage -->
          <div>
            <div class="postbox" style="padding:20px">
              <h2 class="hndle" style="font-size:14px;padding:0 0 12px;border-bottom:1px solid #eee">
                Datos de contacto
              </h2>
              <table class="form-table" style="margin:0">
                <tr>
                  <th style="width:150px"><label for="dp_phone">Telefono</label></th>
                  <td><input type="text" id="dp_phone" name="dentix_options[phone]"
                       value="<?php echo esc_attr(dentix_opt('phone','900 123 456')); ?>"
                       class="regular-text"></td>
                </tr>
                <tr>
                  <th><label for="dp_email">Email contacto</label></th>
                  <td><input type="email" id="dp_email" name="dentix_options[email]"
                       value="<?php echo esc_attr(dentix_opt('email','info@dentix.es')); ?>"
                       class="regular-text"></td>
                </tr>
                <tr>
                  <th><label for="dp_ship">Envio gratis desde (&euro;)</label></th>
                  <td>
                    <input type="number" id="dp_ship" name="dentix_options[free_shipping]"
                       value="<?php echo esc_attr(dentix_opt('free_shipping',150)); ?>"
                       class="small-text" min="0" step="5">
                    <p class="description">Pedidos iguales o superiores a este importe tendran envio gratuito</p>
                  </td>
                </tr>
              </table>
            </div>

            <div class="postbox" style="padding:20px;margin-top:16px">
              <h2 class="hndle" style="font-size:14px;padding:0 0 12px;border-bottom:1px solid #eee">
                Secciones de la homepage
              </h2>
              <?php
              $sections = [
                  'show_hero'     => 'Mostrar carrusel de imagenes',
                  'show_cats'     => 'Mostrar bloque de especialidades',
                  'show_featured' => 'Mostrar productos destacados',
              ];
              foreach ($sections as $key => $label) : ?>
                <label style="display:flex;align-items:center;gap:8px;margin-bottom:12px;font-size:13px">
                  <input type="checkbox" name="dentix_options[<?php echo $key; ?>]" value="1"
                    <?php checked(dentix_opt($key, 1)); ?>>
                  <?php echo esc_html($label); ?>
                </label>
              <?php endforeach; ?>
            </div>

            <div class="postbox" style="padding:20px;margin-top:16px">
              <h2 class="hndle" style="font-size:14px;padding:0 0 12px;border-bottom:1px solid #eee">
                Texto descriptivo del footer
              </h2>
              <textarea name="dentix_options[footer_desc]" rows="3" class="large-text"><?php
                echo esc_textarea(dentix_opt('footer_desc',
                  'Distribuidores de instrumental y material odontologico profesional. Mas de 2.500 referencias para clinicas dentales en toda Espana.'
                ));
              ?></textarea>
            </div>
          </div>

          <!-- Derecha: especialidades en el nav -->
          <div>
            <div class="postbox" style="padding:20px">
              <h2 class="hndle" style="font-size:14px;padding:0 0 12px;border-bottom:1px solid #eee">
                Especialidades visibles en el menu de navegacion
              </h2>
              <p class="description" style="margin-bottom:16px">
                Marca las especialidades que apareceran en la barra de navegacion superior de la tienda.
              </p>
              <?php foreach (dentix_get_categories() as $cat) :
                $checked  = in_array($cat['slug'], (array)$cats_in_nav);
                $term     = get_term_by('slug', $cat['slug'], 'product_cat');
                $count    = $term ? $term->count : 0;
              ?>
                <label style="display:flex;align-items:center;gap:10px;margin-bottom:12px;padding:8px 12px;background:<?php echo $checked ? '#f0f7ff' : '#fafafa'; ?>;border:1px solid <?php echo $checked ? '#2271b1' : '#ddd'; ?>;border-radius:6px;cursor:pointer">
                  <input type="checkbox"
                    name="dentix_options[cats_in_nav][]"
                    value="<?php echo esc_attr($cat['slug']); ?>"
                    <?php checked($checked); ?>>
                  <span style="font-size:20px"><?php echo $cat['icon']; ?></span>
                  <span>
                    <strong style="font-size:13px"><?php echo esc_html($cat['label']); ?></strong>
                    <span style="color:#999;font-size:11px;margin-left:6px">
                      /<?php echo $cat['slug']; ?> &middot; <?php echo $count; ?> productos
                    </span>
                  </span>
                </label>
              <?php endforeach; ?>
            </div>

            <!-- Accesos directos -->
            <div class="postbox" style="padding:20px;margin-top:16px">
              <h2 class="hndle" style="font-size:14px;padding:0 0 12px;border-bottom:1px solid #eee">
                Accesos directos
              </h2>
              <div style="display:flex;flex-direction:column;gap:8px">
                <a href="<?php echo admin_url('edit-tags.php?taxonomy=product_cat&post_type=product'); ?>"
                   class="button button-secondary" style="justify-content:flex-start">
                  Gestionar especialidades y categorias
                </a>
                <a href="<?php echo admin_url('nav-menus.php'); ?>"
                   class="button button-secondary">
                  Menus de navegacion
                </a>
                <a href="<?php echo admin_url('edit.php?post_type=product'); ?>"
                   class="button button-secondary">
                  Todos los productos
                </a>
                <a href="<?php echo admin_url('admin.php?page=wc-settings'); ?>"
                   class="button button-secondary">
                  Ajustes WooCommerce
                </a>
              </div>
            </div>
          </div>
        </div>

        <p style="margin-top:20px">
          <input type="submit" class="button button-primary button-large" value="Guardar configuracion">
        </p>
      </form>
    </div>
    <?php
}
