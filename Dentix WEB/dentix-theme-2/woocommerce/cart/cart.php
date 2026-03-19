<?php
/**
 * cart/cart.php — Página de carrito
 */
defined('ABSPATH') || exit;
get_header();
?>

<?php dentix_breadcrumb(); ?>

<div style="padding:48px 60px 80px;max-width:1300px;margin:0 auto">
  <h1 style="font-family:'Playfair Display',serif;font-size:32px;font-weight:700;color:var(--dark-main);margin-bottom:40px">
    Mi cesta
  </h1>

  <?php wc_print_notices(); ?>

  <?php do_action('woocommerce_before_cart'); ?>

  <form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">

    <?php do_action('woocommerce_before_cart_table'); ?>

    <div style="display:grid;grid-template-columns:1fr 360px;gap:32px;align-items:start">

      <!-- Líneas del carrito -->
      <div>
        <table style="width:100%;border-collapse:collapse" class="shop_table cart">
          <thead>
            <tr style="border-bottom:2px solid var(--border)">
              <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gray-mid)">Producto</th>
              <th style="padding:12px 16px;text-align:center;font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gray-mid)">Precio unit.</th>
              <th style="padding:12px 16px;text-align:center;font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gray-mid)">Cantidad</th>
              <th style="padding:12px 16px;text-align:right;font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gray-mid)">Subtotal</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php do_action('woocommerce_before_cart_contents'); ?>
            <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) :
              $_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
              $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
              if (!$_product || !$_product->exists() || $cart_item['quantity'] == 0) continue;
              $product_name  = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
              $thumbnail     = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('thumbnail'), $cart_item, $cart_item_key);
              $product_price = apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key);
              $product_sub   = apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key);
              $sku = $_product->get_sku();
            ?>
              <tr style="border-bottom:1px solid var(--border)">
                <td style="padding:20px 16px">
                  <div style="display:flex;align-items:center;gap:16px">
                    <div style="width:72px;height:72px;background:var(--gray-light);border-radius:8px;flex-shrink:0;overflow:hidden;display:flex;align-items:center;justify-content:center">
                      <?php echo $thumbnail; ?>
                    </div>
                    <div>
                      <div style="font-weight:600;font-size:14px;color:var(--dark-main)">
                        <a href="<?php echo esc_url(get_permalink($product_id)); ?>" style="color:inherit">
                          <?php echo wp_kses_post($product_name); ?>
                        </a>
                      </div>
                      <?php if ($sku) echo '<div style="font-size:12px;color:var(--gray-mid);margin-top:2px">REF: ' . esc_html($sku) . '</div>'; ?>
                      <?php echo wc_get_formatted_cart_item_data($cart_item); ?>
                    </div>
                  </div>
                </td>
                <td style="padding:20px 16px;text-align:center;font-size:14px;color:var(--dark-main)">
                  <?php echo $product_price; ?>
                </td>
                <td style="padding:20px 16px;text-align:center">
                  <?php
                  $min_val  = $_product->get_min_purchase_quantity();
                  $max_val  = $_product->get_max_purchase_quantity();
                  woocommerce_quantity_input([
                    'input_name'  => "cart[{$cart_item_key}][qty]",
                    'input_value' => $cart_item['quantity'],
                    'max_value'   => $max_val,
                    'min_value'   => $min_val,
                    'product_name'=> $_product->get_name(),
                  ], $_product);
                  ?>
                </td>
                <td style="padding:20px 16px;text-align:right;font-weight:600;font-size:14px;color:var(--dark-main)">
                  <?php echo $product_sub; ?>
                </td>
                <td style="padding:20px 16px;text-align:center">
                  <?php
                  echo apply_filters('woocommerce_cart_item_remove_link',
                    sprintf('<a href="%s" class="remove" aria-label="%s" style="font-size:18px;color:var(--gray-mid);text-decoration:none" data-product_id="%d" data-product_sku="%s">×</a>',
                      esc_url(wc_get_cart_remove_url($cart_item_key)),
                      esc_attr__('Eliminar este artículo', 'dentix'),
                      absint($product_id),
                      esc_attr($_product->get_sku())
                    ), $cart_item_key);
                  ?>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php do_action('woocommerce_cart_contents'); ?>
          </tbody>
        </table>

        <!-- Botones actualizar / seguir comprando -->
        <div style="display:flex;gap:12px;margin-top:20px;flex-wrap:wrap">
          <button type="submit" name="update_cart" value="1"
                  style="padding:12px 24px;border:1.5px solid var(--border);background:white;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;color:var(--dark-main)">
            Actualizar cesta
          </button>
          <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>"
             style="padding:12px 24px;border:1.5px solid var(--border);background:white;border-radius:8px;font-size:13px;font-weight:600;color:var(--dark-main);text-decoration:none">
            ← Seguir comprando
          </a>
          <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
          <?php do_action('woocommerce_cart_actions'); ?>
        </div>

        <!-- Cupón -->
        <?php if (wc_coupons_enabled()) : ?>
          <div style="margin-top:24px;display:flex;gap:10px;flex-wrap:wrap">
            <input type="text" name="coupon_code" id="coupon_code" value=""
                   placeholder="Código de cupón"
                   style="padding:11px 16px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;min-width:200px">
            <button type="submit" name="apply_coupon"
                    style="padding:11px 20px;background:var(--dark-main);color:white;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer">
              Aplicar cupón
            </button>
          </div>
        <?php endif; ?>
      </div>

      <!-- Resumen del pedido -->
      <div style="background:var(--panel);border:1px solid var(--border);border-radius:16px;padding:28px">
        <h2 style="font-family:'Playfair Display',serif;font-size:20px;font-weight:700;color:var(--dark-main);margin-bottom:24px">
          Resumen del pedido
        </h2>
        <?php do_action('woocommerce_before_cart_collaterals'); ?>
        <?php woocommerce_cart_totals(); ?>
      </div>

    </div>
    <?php do_action('woocommerce_after_cart_table'); ?>
  </form>

  <?php do_action('woocommerce_after_cart'); ?>
</div>

<?php get_footer(); ?>
