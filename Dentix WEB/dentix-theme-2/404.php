<?php get_header(); ?>

<div style="text-align:center;padding:120px 60px;min-height:60vh;display:flex;flex-direction:column;align-items:center;justify-content:center">
  <div style="font-size:120px;font-family:'Playfair Display',serif;font-weight:700;color:var(--border);line-height:1">404</div>
  <h1 style="font-family:'Playfair Display',serif;font-size:28px;color:var(--dark-main);margin:16px 0 12px">
    Página no encontrada
  </h1>
  <p style="color:var(--gray-mid);font-size:15px;max-width:440px;margin-bottom:36px;line-height:1.7">
    La página que buscas no existe o ha sido movida.<br>
    Prueba a buscar el producto por referencia o navega al catálogo.
  </p>
  <div style="display:flex;gap:12px;flex-wrap:wrap;justify-content:center">
    <a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>"
       style="padding:13px 28px;background:var(--red);color:white;border-radius:8px;font-weight:600;font-size:14px">
      Ver catálogo
    </a>
    <a href="<?php echo home_url('/'); ?>"
       style="padding:13px 28px;border:1.5px solid var(--border);color:var(--dark-main);border-radius:8px;font-weight:500;font-size:14px">
      Ir al inicio
    </a>
  </div>
</div>

<?php get_footer(); ?>
