<?php
/**
 * single-product.php — Ficha completa de producto
 */
defined('ABSPATH') || exit;
get_header();

while (have_posts()) : the_post();
  global $product;
  $product = wc_get_product(get_the_ID());
  if (!$product) continue;

  $images      = $product->get_gallery_image_ids();
  $main_img_id = $product->get_image_id();
  $main_img    = $main_img_id ? wp_get_attachment_image_url($main_img_id, 'dentix-product-gallery') : '';
  $sku         = $product->get_sku();
  $ean         = get_post_meta(get_the_ID(), '_ean', true);
  $sage_ref    = get_post_meta(get_the_ID(), '_sage_ref', true) ?: $sku;
  $in_stock    = $product->is_in_stock();
  $qty         = $product->get_stock_quantity();
  $reg_price   = wc_price($product->get_regular_price());
  $sale_price  = $product->get_sale_price() ? wc_price($product->get_sale_price()) : '';
  $on_sale     = $product->is_on_sale();
  $brand_terms = wp_get_post_terms(get_the_ID(), 'pa_marca');
  $brand       = !is_wp_error($brand_terms) && !empty($brand_terms) ? $brand_terms[0]->name : '';
  $rating      = $product->get_average_rating();
  $review_count= $product->get_review_count();
?>

<?php dentix_breadcrumb(); ?>

<div class="pdp-layout">

  <!-- ── Galería ──────────────────────────────────────────────── -->
  <div class="pdp-gallery">
    <div class="pdp-main-img" id="pdpMainImg">
      <div class="pdp-badge-wrap">
        <?php if ($in_stock) : ?>
          <span class="pdp-badge avail" style="background:var(--dark-main);color:white;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;letter-spacing:.5px;text-transform:uppercase">
            <?php echo ($qty && $qty <= 5) ? "Stock bajo · {$qty} uds" : 'En stock'; ?>
          </span>
        <?php else : ?>
          <span class="pdp-badge" style="background:var(--gray-mid);color:white;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;letter-spacing:.5px;text-transform:uppercase">Sin stock</span>
        <?php endif; ?>
        <?php if ($on_sale) : ?>
          <span class="pdp-badge" style="background:var(--red);color:white;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700">Oferta</span>
        <?php endif; ?>
      </div>
      <?php if ($main_img) : ?>
        <img id="pdpMainImgTag" src="<?php echo esc_url($main_img); ?>"
             alt="<?php echo esc_attr($product->get_name()); ?>"
             style="max-width:80%;max-height:80%;object-fit:contain">
      <?php else : ?>
        <svg viewBox="0 0 64 64" fill="none" stroke="var(--gray-mid)" stroke-width="1.2" style="width:160px;height:160px">
          <rect x="16" y="20" width="32" height="24" rx="3"/>
          <line x1="24" y1="28" x2="40" y2="28"/>
          <line x1="24" y1="34" x2="32" y2="34"/>
          <line x1="24" y1="40" x2="36" y2="40"/>
        </svg>
      <?php endif; ?>
    </div>

    <!-- Miniaturas -->
    <?php if (!empty($images)) : ?>
      <div class="pdp-thumbs">
        <?php if ($main_img_id) : $thumb = wp_get_attachment_image_url($main_img_id, 'thumbnail'); ?>
          <div class="pdp-thumb on" data-full="<?php echo esc_url($main_img); ?>">
            <img src="<?php echo esc_url($thumb); ?>" alt="" style="width:100%;height:100%;object-fit:contain">
          </div>
        <?php endif; ?>
        <?php foreach ($images as $img_id) :
          $thumb = wp_get_attachment_image_url($img_id, 'thumbnail');
          $full  = wp_get_attachment_image_url($img_id, 'dentix-product-gallery');
        ?>
          <div class="pdp-thumb" data-full="<?php echo esc_url($full); ?>">
            <img src="<?php echo esc_url($thumb); ?>" alt="" style="width:100%;height:100%;object-fit:contain">
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- ── Info ─────────────────────────────────────────────────── -->
  <div class="pdp-info">

    <?php if ($brand) : ?>
      <div class="pdp-brand"><?php echo esc_html($brand); ?></div>
    <?php endif; ?>

    <h1 class="pdp-name"><?php the_title(); ?></h1>

    <!-- Referencias -->
    <div class="pdp-refs">
      <?php if ($sku)      echo '<div>Ref: <strong>' . esc_html($sku) . '</strong></div>'; ?>
      <?php if ($ean)      echo '<div>EAN: <strong>' . esc_html($ean) . '</strong></div>'; ?>
      <?php if ($sage_ref && $sage_ref !== $sku) echo '<div>SKU: <strong>' . esc_html($sage_ref) . '</strong></div>'; ?>
    </div>

    <!-- Rating -->
    <?php if ($rating > 0) : ?>
      <div class="pdp-rating">
        <span class="stars"><?php echo str_repeat('★', round($rating)) . str_repeat('☆', 5 - round($rating)); ?></span>
        <span style="font-size:13px;font-weight:600;color:var(--dark-main)"><?php echo number_format($rating, 1); ?></span>
        <span class="rating-count">(<?php echo $review_count; ?> valoraciones)</span>
      </div>
    <?php endif; ?>

    <!-- Stock badge -->
    <span class="stock-badge <?php echo $in_stock ? 'stock-ok' : 'stock-low'; ?>">
      <?php echo $in_stock
        ? ($qty ? "En stock · {$qty} unidades disponibles" : 'En stock')
        : 'Sin stock — consultar disponibilidad'; ?>
    </span>

    <!-- Precio -->
    <div class="pdp-price">
      <?php if ($on_sale && $sale_price) : ?>
        <?php echo $sale_price; ?>
        <span class="pdp-price-old"><?php echo $reg_price; ?></span>
      <?php else : ?>
        <?php echo $product->get_price_html(); ?>
      <?php endif; ?>
    </div>
    <div class="pdp-price-note">Precio sin IVA · IVA aplicable en checkout según tipo de producto</div>

    <!-- Formulario añadir al carrito (WooCommerce lo gestiona: variantes, cantidad, etc.) -->
    <?php woocommerce_template_single_add_to_cart(); ?>

    <!-- Acciones secundarias -->
    <div class="pdp-sub-actions">
      <button class="pdp-sub-btn">📋 Añadir a pedido recurrente</button>
      <button class="pdp-sub-btn" onclick="window.print()">📄 Imprimir ficha</button>
    </div>

    <!-- Info de entrega -->
    <div class="pdp-delivery">
      <div class="pdp-delivery-row">
        <span>🚚</span>
        <span>Entrega <strong>mañana</strong> si realizas el pedido antes de las 14h</span>
      </div>
      <div class="pdp-delivery-row">
        <span>🆓</span>
        <span>Envío gratuito en pedidos superiores a <strong>150 €</strong></span>
      </div>
      <div class="pdp-delivery-row">
        <span>↩️</span>
        <span>Devolución gratuita en <strong>14 días</strong> — ley española</span>
      </div>
    </div>

    <!-- Tabs: descripción, especificaciones, documentos, valoraciones -->
    <div class="tab-nav">
      <button class="tab-btn on" data-tab="desc">Descripción</button>
      <button class="tab-btn" data-tab="spec">Especificaciones</button>
      <button class="tab-btn" data-tab="docs">Documentos</button>
      <?php if (comments_open()) : ?>
        <button class="tab-btn" data-tab="reviews">
          Valoraciones (<?php echo $review_count; ?>)
        </button>
      <?php endif; ?>
    </div>

    <div class="tab-content on" id="tab-desc">
      <?php if ($product->get_description()) : ?>
        <div style="font-size:14px;line-height:1.8;color:var(--gray-mid)">
          <?php echo wp_kses_post($product->get_description()); ?>
        </div>
      <?php else : ?>
        <p style="color:var(--gray-mid);font-size:14px">Descripción no disponible.</p>
      <?php endif; ?>
    </div>

    <div class="tab-content" id="tab-spec">
      <?php
      // Atributos del producto como tabla de especificaciones
      $attributes = $product->get_attributes();
      if (!empty($attributes)) :
      ?>
        <table class="spec-table">
          <?php foreach ($attributes as $attribute) :
            if (!$attribute->get_visible()) continue;
            $name  = wc_attribute_label($attribute->get_name());
            $value = implode(', ', $attribute->get_terms()
              ? array_map(fn($t) => $t->name, $attribute->get_terms())
              : (array) $attribute->get_options());
          ?>
            <tr>
              <td><?php echo esc_html($name); ?></td>
              <td><?php echo esc_html($value); ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if ($sku) echo "<tr><td>Referencia / SKU</td><td>" . esc_html($sku) . "</td></tr>"; ?>
          <?php if ($ean) echo "<tr><td>EAN / Código de barras</td><td>" . esc_html($ean) . "</td></tr>"; ?>
          <?php
          $weight = $product->get_weight();
          if ($weight) echo "<tr><td>Peso</td><td>" . esc_html($weight) . " kg</td></tr>";
          ?>
        </table>
      <?php else : ?>
        <p style="color:var(--gray-mid);font-size:14px">Especificaciones técnicas no disponibles.</p>
      <?php endif; ?>
    </div>

    <div class="tab-content" id="tab-docs">
      <?php
      // Documentos adjuntos al producto (adjuntos de WordPress)
      $attachments = get_posts([
        'post_type'      => 'attachment',
        'post_parent'    => get_the_ID(),
        'post_mime_type' => 'application/pdf',
        'numberposts'    => 10,
      ]);
      if (!empty($attachments)) :
        foreach ($attachments as $att) :
          $url = wp_get_attachment_url($att->ID);
      ?>
          <a href="<?php echo esc_url($url); ?>" target="_blank"
             style="display:flex;align-items:center;gap:10px;padding:12px 16px;border:1px solid var(--border);border-radius:8px;margin-bottom:8px;font-size:13px;color:var(--dark-main)">
            📄 <?php echo esc_html($att->post_title); ?>
          </a>
      <?php endforeach;
      else : ?>
        <p style="color:var(--gray-mid);font-size:14px">
          Documentación disponible bajo petición.
          <a href="<?php echo get_page_link(get_page_by_path('contacto')); ?>"
             style="color:var(--red)">Contactar</a>
        </p>
      <?php endif; ?>
    </div>

    <?php if (comments_open()) : ?>
      <div class="tab-content" id="tab-reviews">
        <?php comments_template(); ?>
      </div>
    <?php endif; ?>

  </div><!-- /pdp-info -->

</div><!-- /pdp-layout -->

<!-- Productos relacionados -->
<?php
$related_ids = wc_get_related_products(get_the_ID(), 4);
if (!empty($related_ids)) :
  $related_products = array_map('wc_get_product', $related_ids);
?>
  <section style="padding:56px 60px;background:var(--white)">
    <div class="sec-header" style="margin-bottom:32px">
      <h2 class="sec-title">También te puede interesar</h2>
    </div>
    <div class="prod-grid">
      <?php foreach ($related_products as $rel) :
        if (!$rel) continue;
        $rel_img   = get_the_post_thumbnail_url($rel->get_id(), 'dentix-product-thumb');
        $rel_brand = wp_get_post_terms($rel->get_id(), 'pa_marca');
        $rel_brand = !is_wp_error($rel_brand) && !empty($rel_brand) ? $rel_brand[0]->name : '';
      ?>
        <a href="<?php echo get_permalink($rel->get_id()); ?>" class="prod-card">
          <div class="prod-img">
            <?php if ($rel_img) : ?>
              <img src="<?php echo esc_url($rel_img); ?>" alt="<?php echo esc_attr($rel->get_name()); ?>"
                   loading="lazy" style="width:100%;height:100%;object-fit:contain;padding:16px">
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
            <?php if ($rel_brand) echo '<div class="p-brand">' . esc_html($rel_brand) . '</div>'; ?>
            <div class="p-name"><?php echo esc_html($rel->get_name()); ?></div>
            <div class="p-footer">
              <div class="p-price"><span class="p-price-main"><?php echo $rel->get_price_html(); ?></span></div>
              <button class="p-add" data-product-id="<?php echo $rel->get_id(); ?>"
                onclick="event.preventDefault();dentixAddToCart(<?php echo $rel->get_id(); ?>,this)">+</button>
            </div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </section>
<?php endif; ?>

<script>
// Galería de miniaturas
document.querySelectorAll('.pdp-thumb').forEach(t => {
  t.addEventListener('click', () => {
    document.querySelectorAll('.pdp-thumb').forEach(x => x.classList.remove('on'));
    t.classList.add('on');
    const full = t.dataset.full;
    const mainImg = document.getElementById('pdpMainImgTag');
    if (full && mainImg) mainImg.src = full;
  });
});
// Tabs
document.querySelectorAll('.tab-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('on'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('on'));
    btn.classList.add('on');
    const t = document.getElementById('tab-' + btn.dataset.tab);
    if (t) t.classList.add('on');
  });
});
</script>

<?php endwhile; ?>
<?php get_footer(); ?>
