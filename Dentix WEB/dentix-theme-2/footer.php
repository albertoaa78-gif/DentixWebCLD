<!-- ═══ FOOTER ═══════════════════════════════════════════════════ -->
<footer>
  <div class="fgrid">
    <div class="fbrand">
      <h3>dentix</h3>
      <p><?php echo esc_html(dentix_opt('footer_desc', 'Distribuidores de instrumental y material odontológico profesional. Más de 2.500 referencias para clínicas dentales y profesionales del sector.')); ?></p>
    </div>
    <div class="fcol">
      <h4>Especialidades</h4>
      <?php foreach (dentix_get_categories() as $cat) :
        $term = get_term_by('slug', $cat['slug'], 'product_cat');
        $url  = $term ? get_term_link($term) : '#';
      ?>
        <a href="<?php echo esc_url($url); ?>"><?php echo $cat['icon']; ?> <?php echo esc_html($cat['label']); ?></a>
      <?php endforeach; ?>
    </div>
    <div class="fcol">
      <h4>Ayuda</h4>
      <?php
      $help = ['contacto'=>'Contacto','politica-devoluciones'=>'Devoluciones (14 días)','page-envios'=>'Envíos y plazos'];
      foreach ($help as $slug => $label) {
          $p = get_page_by_path($slug);
          echo '<a href="' . esc_url($p ? get_permalink($p) : '#') . '">' . esc_html($label) . '</a>';
      }
      ?>
    </div>
    <div class="fcol">
      <h4>Empresa</h4>
      <?php
      $legal = ['aviso-legal'=>'Aviso legal','politica-privacidad'=>'Privacidad','politica-cookies'=>'Cookies','condiciones-venta'=>'Condiciones','politica-devoluciones'=>'Devoluciones'];
      foreach ($legal as $slug => $label) {
          $p = get_page_by_path($slug);
          echo '<a href="' . esc_url($p ? get_permalink($p) : '#') . '">' . esc_html($label) . '</a>';
      }
      echo '<a href="https://ec.europa.eu/consumers/odr" target="_blank" rel="noopener">Resolución litigios UE</a>';
      ?>
    </div>
  </div>
  <div class="fbot">
    <span>© <?php echo date('Y'); ?> Dentix Productos Dentales, S.L. · CIF B-85937787</span>
    <div class="fpay">
      <?php foreach (['GETNET','VISA','MC','STRIPE','BIZUM','KLARNA','PAYPAL'] as $m) : ?>
        <span class="fpay-card"><?php echo $m; ?></span>
      <?php endforeach; ?>
    </div>
  </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
