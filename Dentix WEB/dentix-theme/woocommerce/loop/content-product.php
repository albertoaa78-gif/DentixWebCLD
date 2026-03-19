<?php
/**
 * content-product.php — Tarjeta de producto en el loop de la tienda
 */
defined('ABSPATH') || exit;

global $product;
if (empty($product) || !$product->is_visible()) return;

$img         = get_the_post_thumbnail_url(get_the_ID(), 'dentix-product-thumb');
$sku         = $product->get_sku();
$on_sale     = $product->is_on_sale();
$featured    = $product->is_featured();
$in_stock    = $product->is_in_stock();
$qty         = $product->get_stock_quantity();
$brand_terms = wp_get_post_terms(get_the_ID(), 'pa_marca');
$brand       = !is_wp_error($brand_terms) && !empty($brand_terms) ? $brand_terms[0]->name : '';
$reg_price   = $product->get_regular_price();
$sale_price  = $product->get_sale_price();
?>

<a href="<?php the_permalink(); ?>" class="prod-card<?php echo !$in_stock ? ' out-of-stock' : ''; ?>">

  <!-- Imagen / Ilustración -->
  <div class="prod-img">
    <?php if ($img) : ?>
      <img src="<?php echo esc_url($img); ?>"
           alt="<?php echo esc_attr($product->get_name()); ?>"
           loading="lazy"
           style="width:100%;height:100%;object-fit:contain;padding:16px">
    <?php else : ?>
      <div class="prod-circle">
        <svg class="prod-svg" viewBox="0 0 64 64" fill="none" stroke="var(--gray-mid)" stroke-width="1.5">
          <rect x="16" y="20" width="32" height="24" rx="3"/>
          <line x1="24" y1="28" x2="40" y2="28"/>
          <line x1="24" y1="34" x2="32" y2="34"/>
        </svg>
      </div>
    <?php endif; ?>

    <!-- Wishlist -->
    <button class="p-wish"
            aria-label="Añadir a favoritos"
            data-product-id="<?php echo get_the_ID(); ?>"
            onclick="event.preventDefault()">♡</button>

    <!-- Badges -->
    <?php if ($on_sale && $sale_price && $reg_price) :
      $discount = round((($reg_price - $sale_price) / $reg_price) * 100);
    ?>
      <span class="p-badge sale">−<?php echo $discount; ?>%</span>
    <?php elseif ($featured) : ?>
      <span class="p-badge new">Destacado</span>
    <?php elseif ($qty && $qty <= 5) : ?>
      <span class="p-badge new" style="background:var(--dark-main)">Stock bajo</span>
    <?php endif; ?>

    <!-- Sin stock overlay -->
    <?php if (!$in_stock) : ?>
      <div style="position:absolute;inset:0;background:rgba(255,255,255,.7);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:600;color:var(--gray-mid)">Sin stock</div>
    <?php endif; ?>
  </div>

  <!-- Info -->
  <div class="prod-body">
    <?php if ($brand) : ?>
      <div class="p-brand"><?php echo esc_html($brand); ?></div>
    <?php endif; ?>

    <div class="p-name"><?php the_title(); ?></div>

    <?php if ($sku) : ?>
      <div class="p-ref">REF: <?php echo esc_html($sku); ?></div>
    <?php endif; ?>

    <div class="p-footer">
      <div class="p-price">
        <?php if ($on_sale && $sale_price) : ?>
          <span class="p-price-main"><?php echo wc_price($sale_price); ?></span>
          <span class="p-price-old"><?php echo wc_price($reg_price); ?></span>
        <?php else : ?>
          <span class="p-price-main"><?php echo $product->get_price_html(); ?></span>
          <span class="p-price-sub">IVA excl.</span>
        <?php endif; ?>
      </div>

      <?php if ($in_stock) : ?>
        <button class="p-add"
                data-product-id="<?php echo get_the_ID(); ?>"
                onclick="event.preventDefault(); dentixAddToCart(<?php echo get_the_ID(); ?>, this)"
                aria-label="Añadir al carrito">+</button>
      <?php else : ?>
        <button class="p-add" style="opacity:.4;cursor:not-allowed" disabled>−</button>
      <?php endif; ?>
    </div>
  </div>

</a>
