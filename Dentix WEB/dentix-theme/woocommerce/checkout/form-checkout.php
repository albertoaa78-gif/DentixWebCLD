<?php
/**
 * checkout/form-checkout.php — Formulario de checkout
 */
defined('ABSPATH') || exit;
if (!is_user_logged_in() && 'no' === get_option('woocommerce_enable_guest_checkout')) {
    echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message',
        __('Debes iniciar sesión para realizar un pedido.', 'dentix')));
    return;
}
get_header();
?>

<?php dentix_breadcrumb(); ?>

<div style="padding:48px 60px 80px;max-width:1300px;margin:0 auto">
  <h1 style="font-family:'Playfair Display',serif;font-size:32px;font-weight:700;color:var(--dark-main);margin-bottom:40px">
    Finalizar pedido
  </h1>

  <?php wc_print_notices(); ?>
  <?php do_action('woocommerce_before_checkout_form', $checkout); ?>

  <form name="checkout" method="post" class="checkout woocommerce-checkout"
        action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">

    <div style="display:grid;grid-template-columns:1fr 400px;gap:40px;align-items:start">

      <!-- Columna izquierda: datos facturación + envío + método de pago -->
      <div>
        <?php if ($checkout->get_checkout_fields()) : ?>

          <?php do_action('woocommerce_checkout_before_customer_details'); ?>

          <!-- Datos de facturación (con NIF/CIF) -->
          <div style="background:white;border:1px solid var(--border);border-radius:16px;padding:28px;margin-bottom:24px">
            <h3 style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:var(--dark-main);margin-bottom:20px">
              Datos profesionales y facturación
            </h3>
            <?php do_action('woocommerce_checkout_billing'); ?>
          </div>

          <!-- Dirección de envío -->
          <?php if (WC()->cart->needs_shipping()) : ?>
            <div style="background:white;border:1px solid var(--border);border-radius:16px;padding:28px;margin-bottom:24px">
              <h3 style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:var(--dark-main);margin-bottom:20px">
                Dirección de entrega
              </h3>
              <?php do_action('woocommerce_checkout_shipping'); ?>
            </div>
          <?php endif; ?>

          <?php do_action('woocommerce_checkout_after_customer_details'); ?>

        <?php endif; ?>

        <!-- Notas adicionales del pedido -->
        <div style="background:white;border:1px solid var(--border);border-radius:16px;padding:28px;margin-bottom:24px">
          <h3 style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:var(--dark-main);margin-bottom:20px">
            Notas del pedido
          </h3>
          <?php foreach ($checkout->get_checkout_fields('order') as $key => $field) :
            woocommerce_form_field($key, $field, $checkout->get_value($key));
          endforeach; ?>
        </div>

        <!-- Métodos de pago -->
        <div style="background:white;border:1px solid var(--border);border-radius:16px;padding:28px">
          <h3 style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:var(--dark-main);margin-bottom:20px">
            Método de pago
          </h3>
          <?php do_action('woocommerce_checkout_payment'); ?>
        </div>
      </div>

      <!-- Columna derecha: resumen del pedido -->
      <div style="position:sticky;top:116px">
        <div style="background:var(--panel);border:1px solid var(--border);border-radius:16px;padding:28px">
          <h3 style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:var(--dark-main);margin-bottom:20px">
            Tu pedido
          </h3>
          <?php do_action('woocommerce_checkout_before_order_review'); ?>
          <div id="order_review">
            <?php do_action('woocommerce_checkout_order_review'); ?>
          </div>
          <?php do_action('woocommerce_checkout_after_order_review'); ?>
        </div>

        <!-- Métodos de pago aceptados -->
        <div style="margin-top:16px;text-align:center">
          <div style="font-size:11px;color:var(--gray-mid);margin-bottom:8px;letter-spacing:1px;text-transform:uppercase">Pago seguro con</div>
          <div class="fpay" style="justify-content:center">
            <span class="fpay-card">GETNET</span>
            <span class="fpay-card">VISA</span>
            <span class="fpay-card">MC</span>
            <span class="fpay-card">STRIPE</span>
            <span class="fpay-card">BIZUM</span>
            <span class="fpay-card">KLARNA</span>
          </div>
        </div>
      </div>

    </div>
  </form>

  <?php do_action('woocommerce_after_checkout_form', $checkout); ?>
</div>

<?php get_footer(); ?>
