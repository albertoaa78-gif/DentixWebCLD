<!-- ═══ FOOTER ═══════════════════════════════════════════════════ -->
<footer>
  <div class="fgrid">

    <!-- Columna marca -->
    <div class="fbrand">
      <h3>dentix</h3>
      <p><?php echo esc_html(get_theme_mod('dentix_footer_desc',
        'Distribuidores de instrumental y material odontológico profesional. Más de 10.000 referencias para clínicas dentales y profesionales del sector.'
      )); ?></p>
    </div>

    <!-- Columna Tienda -->
    <div class="fcol">
      <h4><?php esc_html_e('Tienda', 'dentix'); ?></h4>
      <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>">
        <?php esc_html_e('Catálogo completo', 'dentix'); ?>
      </a>
      <?php
      $nav_footer = [
        ['slug' => 'novedades', 'label' => 'Novedades'],
        ['slug' => 'ofertas',   'label' => 'Ofertas'],
        ['slug' => 'marcas',    'label' => 'Marcas'],
      ];
      foreach ($nav_footer as $item) :
        $term = get_term_by('slug', $item['slug'], 'product_cat');
        $url  = $term ? get_term_link($term) : '#';
        printf('<a href="%s">%s</a>', esc_url($url), esc_html($item['label']));
      endforeach;
      ?>
      <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>">
        <?php esc_html_e('Acceso profesional', 'dentix'); ?>
      </a>
    </div>

    <!-- Columna Ayuda -->
    <div class="fcol">
      <h4><?php esc_html_e('Ayuda', 'dentix'); ?></h4>
      <?php
      $help_pages = [
        'page-contacto'            => 'Cómo realizar un pedido',
        'page-envios'              => 'Envíos y plazos',
        'page-politica-devoluciones' => 'Devoluciones',
        'page-garantias'           => 'Garantías',
      ];
      foreach ($help_pages as $slug => $label) {
        $page = get_page_by_path($slug);
        $url  = $page ? get_permalink($page) : '#';
        printf('<a href="%s">%s</a>', esc_url($url), esc_html($label));
      }
      ?>
      <a href="<?php echo esc_url(get_page_link(get_page_by_path('contacto'))); ?>">
        <?php esc_html_e('Contacto', 'dentix'); ?>
      </a>
    </div>

    <!-- Columna Empresa -->
    <div class="fcol">
      <h4><?php esc_html_e('Empresa', 'dentix'); ?></h4>
      <?php
      $legal_pages = [
        'aviso-legal'           => 'Aviso legal',
        'politica-privacidad'   => 'Privacidad',
        'politica-cookies'      => 'Política de cookies',
        'condiciones-venta'     => 'Condiciones generales',
        'politica-devoluciones' => 'Devoluciones (14 días)',
      ];
      foreach ($legal_pages as $slug => $label) {
        $page = get_page_by_path($slug);
        $url  = $page ? get_permalink($page) : '#';
        printf('<a href="%s">%s</a>', esc_url($url), esc_html($label));
      }
      // Enlace ODR requerido por ley UE
      echo '<a href="https://ec.europa.eu/consumers/odr" target="_blank" rel="noopener noreferrer">Resolución litigios UE (ODR)</a>';
      ?>
    </div>

  </div>

  <!-- Pie legal y métodos de pago -->
  <div class="fbot">
    <span>
      © <?php echo date('Y'); ?>
      <?php bloginfo('name'); ?> · CIF B-85937787 ·
      Registro Mercantil de Madrid, Tomo 27.796, Folio 125
    </span>
    <div class="fpay">
      <span class="fpay-card">GETNET</span>
      <span class="fpay-card">VISA</span>
      <span class="fpay-card">MC</span>
      <span class="fpay-card">STRIPE</span>
      <span class="fpay-card">BIZUM</span>
      <span class="fpay-card">KLARNA</span>
      <span class="fpay-card">PAYPAL</span>
    </div>
  </div>

</footer>

<?php wp_footer(); ?>
</body>
</html>
