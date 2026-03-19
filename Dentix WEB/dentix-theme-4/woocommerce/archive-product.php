<?php
/**
 * archive-product.php
 * Página de tienda, categorías y etiquetas de WooCommerce
 */
defined('ABSPATH') || exit;
get_header();
?>

<?php dentix_breadcrumb(); ?>

<div class="shop-layout">

  <!-- ── Sidebar filtros ──────────────────────────────────────── -->
  <aside class="sidebar" id="dentixSidebar">

    <!-- Categorías -->
    <div class="filter-box">
      <h4>Categoría</h4>
      <?php
      $current_cat_id = is_product_category() ? get_queried_object_id() : 0;
      $top_cats = get_terms(['taxonomy'=>'product_cat','parent'=>0,'hide_empty'=>true,'number'=>20]);
      foreach ($top_cats as $cat) :
        $checked = ($cat->term_id === $current_cat_id) ? ' checked' : '';
        $url = get_term_link($cat);
      ?>
        <label class="filter-check">
          <input type="radio" name="cat_filter"<?php echo $checked; ?>
            onclick="window.location='<?php echo esc_url($url); ?>'">
          <?php echo esc_html($cat->name); ?>
          <span class="filter-count"><?php echo $cat->count; ?></span>
        </label>
      <?php endforeach; ?>
    </div>

    <!-- Marcas -->
    <?php
    $brands = get_terms(['taxonomy'=>'pa_marca','hide_empty'=>true,'number'=>15]);
    if (!is_wp_error($brands) && !empty($brands)) :
    ?>
    <div class="filter-box">
      <h4>Marca</h4>
      <?php foreach ($brands as $brand) :
        $url = add_query_arg('filter_marca', $brand->slug, get_pagenum_link());
        $active = (isset($_GET['filter_marca']) && $_GET['filter_marca'] === $brand->slug);
      ?>
        <label class="filter-check">
          <input type="checkbox" <?php checked($active); ?>
            onclick="window.location='<?php echo esc_url($url); ?>'">
          <?php echo esc_html($brand->name); ?>
          <span class="filter-count"><?php echo $brand->count; ?></span>
        </label>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Precio -->
    <div class="filter-box">
      <h4>Precio (€)</h4>
      <?php
      // Rangos de precio rápidos
      $price_ranges = [
        '0-25'    => 'Hasta 25 €',
        '25-75'   => '25 € – 75 €',
        '75-200'  => '75 € – 200 €',
        '200-500' => '200 € – 500 €',
        '500-'    => 'Más de 500 €',
      ];
      $current_range = $_GET['price_range'] ?? '';
      foreach ($price_ranges as $range => $label) :
        $url = add_query_arg('price_range', $range, get_pagenum_link());
      ?>
        <label class="filter-check">
          <input type="radio" name="price_range"
            <?php checked($current_range, $range); ?>
            onclick="window.location='<?php echo esc_url($url); ?>'">
          <?php echo esc_html($label); ?>
        </label>
      <?php endforeach; ?>
      <?php if ($current_range) : ?>
        <a href="<?php echo esc_url(remove_query_arg('price_range')); ?>"
           class="btn-filter-clear">Quitar filtro precio</a>
      <?php endif; ?>
    </div>

    <!-- Disponibilidad -->
    <div class="filter-box">
      <h4>Disponibilidad</h4>
      <?php
      $stock_filter = $_GET['stock'] ?? '';
      $stock_url_si  = add_query_arg('stock', 'instock',  get_pagenum_link());
      $stock_url_no  = add_query_arg('stock', 'outofstock', get_pagenum_link());
      ?>
      <label class="filter-check">
        <input type="checkbox" <?php checked($stock_filter,'instock'); ?>
          onclick="window.location='<?php echo esc_url($stock_url_si); ?>'">
        En stock
      </label>
      <label class="filter-check">
        <input type="checkbox" <?php checked($stock_filter,'outofstock'); ?>
          onclick="window.location='<?php echo esc_url($stock_url_no); ?>'">
        Solicitar disponibilidad
      </label>
      <?php if ($stock_filter) : ?>
        <a href="<?php echo esc_url(remove_query_arg('stock')); ?>"
           class="btn-filter-clear">Quitar filtro</a>
      <?php endif; ?>
    </div>

  </aside><!-- /sidebar -->

  <!-- ── Main content ─────────────────────────────────────────── -->
  <main class="shop-main">

    <!-- Botón filtros móvil -->
    <button class="btn-mobile-filters" id="btnMobileFilters">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="10" y1="18" x2="14" y2="18"/>
      </svg>
      Filtrar resultados
    </button>

    <!-- Cabecera de categoría -->
    <?php if (is_product_category()) :
      $cat_desc = category_description();
    ?>
      <div style="margin-bottom:24px">
        <h1 style="font-family:'Playfair Display',serif;font-size:28px;font-weight:700;color:var(--dark-main)">
          <?php single_cat_title(); ?>
        </h1>
        <?php if ($cat_desc) echo '<p style="color:var(--gray-mid);font-size:14px;margin-top:8px">' . wp_kses_post($cat_desc) . '</p>'; ?>
      </div>
    <?php elseif (is_shop()) : ?>
      <div style="margin-bottom:24px">
        <h1 style="font-family:'Playfair Display',serif;font-size:28px;font-weight:700;color:var(--dark-main)">
          Catálogo completo
        </h1>
      </div>
    <?php endif; ?>

    <!-- Top bar resultados + ordenación -->
    <div class="shop-topbar">
      <span class="shop-count">
        <strong><?php echo woocommerce_result_count(); ?></strong>
      </span>
      <div class="shop-sort">
        <?php woocommerce_catalog_ordering(); ?>
        <div class="view-toggle">
          <div class="view-btn on" title="Grid">
            <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
              <rect x="1" y="1" width="6" height="6" rx="1"/><rect x="9" y="1" width="6" height="6" rx="1"/>
              <rect x="1" y="9" width="6" height="6" rx="1"/><rect x="9" y="9" width="6" height="6" rx="1"/>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Grid de productos -->
    <?php if (woocommerce_product_loop()) : ?>
      <div class="shop-grid">
        <?php woocommerce_product_loop_start(); ?>
        <?php while (have_posts()) : the_post(); wc_get_template_part('content', 'product'); endwhile; ?>
        <?php woocommerce_product_loop_end(); ?>
      </div>
      <!-- Paginación -->
      <div class="pagination" style="margin-top:48px;justify-content:center;display:flex;gap:6px">
        <?php woocommerce_pagination(); ?>
      </div>
    <?php else : ?>
      <div style="text-align:center;padding:80px 0">
        <div style="font-size:40px;margin-bottom:16px">🔍</div>
        <p style="font-size:16px;color:var(--gray-mid)">
          <?php esc_html_e('No hay productos que coincidan con tu búsqueda.', 'dentix'); ?>
        </p>
        <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>"
           style="display:inline-block;margin-top:20px;padding:12px 28px;background:var(--red);color:white;border-radius:8px;font-weight:600">
          Ver todo el catálogo
        </a>
      </div>
    <?php endif; ?>

  </main><!-- /shop-main -->

</div><!-- /shop-layout -->

<script>
// Toggle sidebar en móvil
const btnF = document.getElementById('btnMobileFilters');
const sb   = document.getElementById('dentixSidebar');
if (btnF && sb) btnF.addEventListener('click', () => sb.classList.toggle('open'));
</script>

<?php get_footer(); ?>
