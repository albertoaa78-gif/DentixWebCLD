<?php
/**
 * myaccount/my-account.php — Área privada del cliente Dentix
 */
defined('ABSPATH') || exit;
get_header();
dentix_breadcrumb();

$current   = WC()->query->get_current_endpoint();
$endpoints = wc_get_account_menu_items();
$user      = wp_get_current_user();
?>

<div class="account-wrap">

  <aside class="account-sidebar">
    <nav class="account-nav">
      <?php foreach ( $endpoints as $endpoint => $label ) :
        $url    = wc_get_account_endpoint_url( $endpoint );
        $active = ( $current === $endpoint || ( $endpoint === 'dashboard' && ! $current ) );
      ?>
        <a href="<?php echo esc_url( $url ); ?>"
           class="account-nav-item<?php echo $active ? ' active' : ''; ?>">
          <?php echo esc_html( $label ); ?>
        </a>
      <?php endforeach; ?>
    </nav>
    <?php if ( $user->ID ) : $nif = get_user_meta( $user->ID, 'billing_nif', true ); ?>
      <div class="account-user-card">
        <div class="account-user-name"><?php echo esc_html( $user->display_name ); ?></div>
        <div class="account-user-email"><?php echo esc_html( $user->user_email ); ?></div>
        <?php if ( $nif ) : ?>
          <div class="account-user-nif">NIF/CIF: <strong><?php echo esc_html( $nif ); ?></strong></div>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </aside>

  <main class="account-content">
    <?php wc_print_notices(); ?>
    <?php do_action( 'woocommerce_account_content' ); ?>
  </main>

</div>

<?php get_footer(); ?>
