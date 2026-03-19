<?php
/**
 * archive-product.php — Página de tienda y categorías
 */
defined('ABSPATH') || exit;
get_header();
dentix_breadcrumb();
?>

<div class="shop-layout">

  <!-- ── Sidebar filtros ──────────────────────────────────────── -->
  <aside class="sidebar" id="dentixSidebar">

    <!-- Especialidades (categorías dinámicas) -->
    <div class="filter-box">
      <h4>Especialidad</h4>
      <?php
      $current_cat_slug = is_product_category() ? get_queried_object()->slug : '';
      $shop_url = get_permalink(wc_get_page_id('shop'));

      // Enlace "Todas" con recuento total
      $total_count = array_sum(array_column(dentix_get_wc_categories(), 'count'));
      $all_active  = !$current_cat_slug ? ' checked' : '';
      ?>
      <label class="filter-check">
        <input type="radio" name="cat_filter"<?php echo $all_active; ?>
          onclick="window.location='<?php echo esc_url($shop_url); ?>'">
        Todas las especialidades
        <span class="filter-count"><?php echo $total_count ?: '—'; ?></span>
      </label>
      <?php
      foreach (dentix_get_wc_categories() as $cat) :
        $term = get_term_by('slug', $cat['slug'], 'product_cat');
        if (!$term) continue;
        $url     = get_term_link($term);
        $checked = ($current_cat_slug === $cat['slug']) ? ' checked' : '';
      ?>
        <label class="filter-check">
          <input type="radio" name="cat_filter"<?php echo $checked; ?>
            onclick="window.location='<?php echo esc_url($url); ?>'">
          <?php echo $cat['icon']; ?> <?php echo esc_html($cat['label']); ?>
          <span class="filter-count"><?php echo $cat['count']; ?></span>
        </label>
      <?php endforeach; ?>
    </div>

    <!-- Marcas -->
    <?php
    $brands = get_terms(['taxonomy'=>'pa_marca','hide_empty'=>true,'number'=>15,'orderby'=>'name']);
    if (!is_wp_error($brands) && !empty($brands)) :
    ?>
    <div class="filter-box">
      <h4>Marca</h4>
      <?php
      $current_brand = $_GET['filter_marca'] ?? '';
      foreach ($brands as $brand) :
        $url   = add_query_arg('filter_marca', $brand->slug, get_pagenum_link());
        $clear = remove_query_arg('filter_marca');
        $active = ($current_brand === $brand->slug);
      ?>
        <label class="filter-check">
          <input type="checkbox" <?php checked($active); ?>
            onclick="window.location='<?php echo esc_url($active ? $clear : $url); ?>'">
          <?php echo esc_html($brand->name); ?>
          <span class="filter-count"><?php echo $brand->count; ?></span>
        </label>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Precio rápido -->
    <div class="filter-box">
      <h4>Precio (€)</h4>
      <?php
      $ranges = ['0-25'=>'Hasta 25€','25-75'=>'25€ – 75€','75-200'=>'75€ – 200€','200-500'=>'200€ – 500€','500-'=>'+500€'];
      $current_range = $_GET['price_range'] ?? '';
      foreach ($ranges as $range => $label) :
        $url   = add_query_arg('price_range', $range, get_pagenum_link());
        $clear = remove_query_arg('price_range');
      ?>
        <label class="filter-check">
          <input type="radio" name="price_range" <?php checked($current_range, $range); ?>
            onclick="window.location='<?php echo esc_url($current_range===$range ? $clear : $url); ?>'">
          <?php echo esc_html($label); ?>
        </label>
      <?php endforeach; ?>
    </div>

    <!-- Disponibilidad -->
    <div class="filter-box">
      <h4>Disponibilidad</h4>
      <?php $stock = $_GET['stock'] ?? ''; ?>
      <label class="filter-check">
        <input type="checkbox" <?php checked($stock,'instock'); ?>
          onclick="window.location='<?php echo esc_url(add_query_arg('stock', $stock==='instock' ? '' : 'instock')); ?>'">
        En stock
      </label>
    </div>

    <!-- Enlace a wp-admin para el admin -->
    <?php if (current_user_can('manage_options')) : ?>
    <div class="filter-box" style="border:1px dashed var(--border);background:var(--gray-light)">
      <h4 style="color:var(--red)">⚙️ Admin</h4>
      <a href="<?php echo admin_url('edit-tags.php?taxonomy=product_cat&post_type=product'); ?>"
         style="font-size:12px;color:var(--red)">Gestionar especialidades →</a>
    </div>
    <?php endif; ?>

  </aside>

  <!-- ── Main ─────────────────────────────────────────────────── -->
  <main class="shop-main">

    <!-- Botón filtros móvil -->
    <button class="btn-mobile-filters" id="btnMobileFilters">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="10" y1="18" x2="14" y2="18"/>
      </svg>
      Filtrar
    </button>

    <!-- Cabecera de categoría -->
    <?php if (is_product_category()) :
      $cat_obj  = get_queried_object();
      $cat_cfg  = dentix_get_category($cat_obj->slug);
      $cat_desc = category_description();
    ?>
      <div class="shop-cat-header">
        <?php if ($cat_cfg) : ?>
          <div class="shop-cat-icon"><?php echo $cat_cfg['icon']; ?></div>
        <?php endif; ?>
        <div>
          <h1 class="shop-cat-title"><?php single_cat_title(); ?></h1>
          <?php if ($cat_desc) : ?>
            <p class="shop-cat-desc"><?php echo wp_kses_post($cat_desc); ?></p>
          <?php elseif ($cat_cfg) : ?>
            <p class="shop-cat-desc"><?php echo esc_html($cat_cfg['desc']); ?></p>
          <?php endif; ?>
        </div>
      </div>
    <?php elseif (is_shop()) : ?>
      <div class="shop-cat-header">
        <div>
          <h1 class="shop-cat-title">Catálogo completo</h1>
          <p class="shop-cat-desc">2.500 referencias de instrumental y material odontológico profesional</p>
        </div>
      </div>
    <?php endif; ?>

    <!-- Top bar: resultados + ordenación -->
    <div class="shop-topbar">
      <span class="shop-count"><?php woocommerce_result_count(); ?></span>
      <div class="shop-sort"><?php woocommerce_catalog_ordering(); ?></div>
    </div>

    <!-- Grid de productos -->
    <?php if (woocommerce_product_loop()) : ?>
      <div class="shop-grid">
        <?php woocommerce_product_loop_start(); ?>
        <?php while (have_posts()) : the_post(); wc_get_template_part('content', 'product'); endwhile; ?>
        <?php woocommerce_product_loop_end(); ?>
      </div>
      <div style="margin-top:40px">
        <?php woocommerce_pagination(); ?>
      </div>
    <?php else : ?>
      <div class="shop-empty">
        <div class="shop-empty-icon">🔍</div>
        <h2>No hay productos en esta especialidad</h2>
        <?php if (is_product_category() && !current_user_can('manage_options')) : ?>
          <p>El catálogo de esta especialidad estará disponible próximamente.<br>
          Los productos se sincronizan automáticamente desde SAGE 50 Cloud.</p>
        <?php elseif (current_user_can('manage_options')) : ?>
          <p>No hay productos publicados. Puedes añadirlos manualmente o configurar la sincronización con SAGE 50.</p>
          <a href="<?php echo admin_url('post-new.php?post_type=product'); ?>" class="btn-catalog" style="margin:16px auto;display:inline-flex">
            + Añadir producto de prueba
          </a>
        <?php endif; ?>
        <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>"
           class="btn-catalog" style="margin-top:16px;display:inline-flex">
          Ver todo el catálogo
        </a>
      </div>
    <?php endif; ?>

  </main>
</div>

<style>
.shop-cat-header{display:flex;align-items:flex-start;gap:16px;margin-bottom:24px;padding-bottom:20px;border-bottom:1px solid var(--border)}
.shop-cat-icon{font-size:36px;flex-shrink:0;line-height:1;margin-top:4px}
.shop-cat-title{font-family:'Playfair Display',serif;font-size:26px;font-weight:700;color:var(--dark-main);margin:0 0 6px}
.shop-cat-desc{font-size:13px;color:var(--gray-mid);margin:0;line-height:1.6}
.shop-empty{text-align:center;padding:80px 40px}
.shop-empty-icon{font-size:48px;margin-bottom:16px}
.shop-empty h2{font-family:'Playfair Display',serif;font-size:22px;color:var(--dark-main);margin-bottom:12px}
.shop-empty p{color:var(--gray-mid);font-size:14px;line-height:1.7;max-width:440px;margin:0 auto 20px}
</style>

<script>
const btnF = document.getElementById('btnMobileFilters');
const sb   = document.getElementById('dentixSidebar');
if (btnF && sb) btnF.addEventListener('click', () => sb.classList.toggle('open'));
</script>

<?php get_footer(); ?>
