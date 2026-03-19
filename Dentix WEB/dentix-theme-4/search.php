<?php
/**
 * search.php — Resultados de búsqueda (busca en productos WooCommerce por nombre y SKU)
 */
get_header();
?>

<div class="breadcrumb">
  <a href="<?php echo home_url(); ?>">Inicio</a>
  <span>›</span>
  <strong>Resultados para: <?php echo esc_html(get_search_query()); ?></strong>
</div>

<div style="padding:48px 60px 80px;max-width:1440px;margin:0 auto">
  <div style="margin-bottom:32px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px">
    <div>
      <h1 style="font-family:'Playfair Display',serif;font-size:28px;font-weight:700;color:var(--dark-main)">
        Resultados para: <em><?php echo esc_html(get_search_query()); ?></em>
      </h1>
      <?php if (have_posts()) : ?>
        <p style="font-size:13px;color:var(--gray-mid);margin-top:6px">
          <?php echo sprintf(_n('%s resultado encontrado', '%s resultados encontrados', $wp_query->found_posts, 'dentix'), number_format_i18n($wp_query->found_posts)); ?>
        </p>
      <?php endif; ?>
    </div>
    <!-- Nueva búsqueda -->
    <form role="search" method="get" action="<?php echo home_url('/'); ?>" style="display:flex;gap:8px">
      <div class="search-outer" style="max-width:320px">
        <input type="search" name="s" placeholder="Nueva búsqueda…" value="<?php echo esc_attr(get_search_query()); ?>">
        <input type="hidden" name="post_type" value="product">
        <button type="submit" class="search-btn">Buscar</button>
      </div>
    </form>
  </div>

  <?php if (have_posts()) : ?>
    <div class="prod-grid">
      <?php while (have_posts()) : the_post();
        $product = wc_get_product(get_the_ID());
        if (!$product) continue;
        $img = get_the_post_thumbnail_url(get_the_ID(), 'dentix-product-thumb');
        $sku = $product->get_sku();
        $brand_terms = wp_get_post_terms(get_the_ID(), 'pa_marca');
        $brand = !empty($brand_terms) && !is_wp_error($brand_terms) ? $brand_terms[0]->name : '';
      ?>
        <a href="<?php the_permalink(); ?>" class="prod-card">
          <div class="prod-img">
            <?php if ($img) : ?>
              <img src="<?php echo esc_url($img); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
            <?php else : ?>
              <div class="prod-circle">
                <svg class="prod-svg" viewBox="0 0 64 64" fill="none" stroke="var(--gray-mid)" stroke-width="1.5">
                  <rect x="16" y="20" width="32" height="24" rx="3"/>
                </svg>
              </div>
            <?php endif; ?>
            <button class="p-wish">♡</button>
          </div>
          <div class="prod-body">
            <?php if ($brand) echo '<div class="p-brand">' . esc_html($brand) . '</div>'; ?>
            <div class="p-name"><?php the_title(); ?></div>
            <?php if ($sku) echo '<div class="p-ref">REF: ' . esc_html($sku) . '</div>'; ?>
            <div class="p-footer">
              <div class="p-price"><span class="p-price-main"><?php echo $product->get_price_html(); ?></span></div>
              <button class="p-add" data-product-id="<?php echo get_the_ID(); ?>">+</button>
            </div>
          </div>
        </a>
      <?php endwhile; ?>
    </div>
    <!-- Paginación -->
    <div style="margin-top:48px;display:flex;justify-content:center">
      <?php echo paginate_links(['type' => 'list', 'prev_text' => '‹', 'next_text' => '›']); ?>
    </div>
  <?php else : ?>
    <div style="text-align:center;padding:80px 0">
      <div style="font-size:48px;margin-bottom:16px">🔍</div>
      <h2 style="font-family:'Playfair Display',serif;font-size:24px;color:var(--dark-main);margin-bottom:12px">
        No encontramos resultados para "<?php echo esc_html(get_search_query()); ?>"
      </h2>
      <p style="color:var(--gray-mid);font-size:14px;margin-bottom:32px">
        Intenta buscar por referencia de producto, SKU o nombre de marca.<br>
        También puedes buscar con menos palabras o revisar la ortografía.
      </p>
      <a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>" class="btn-red" style="display:inline-flex;align-items:center;gap:8px;padding:14px 28px;background:var(--red);color:white;border-radius:8px;font-weight:600">
        Ver catálogo completo
      </a>
    </div>
  <?php endif; ?>
</div>

<?php get_footer(); ?>
