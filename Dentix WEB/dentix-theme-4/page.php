<?php
/**
 * page.php — Plantilla genérica para páginas de WordPress
 * Usada para: aviso legal, privacidad, condiciones, devoluciones, cookies, contacto
 */
get_header();
?>

<?php dentix_breadcrumb(); ?>

<div class="page-content-wrap" style="max-width:860px;margin:0 auto;padding:56px 60px 80px">
  <?php while (have_posts()) : the_post(); ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
      <header style="margin-bottom:40px;padding-bottom:24px;border-bottom:1px solid var(--border)">
        <h1 style="font-family:'Playfair Display',serif;font-size:36px;font-weight:700;color:var(--dark-main)">
          <?php the_title(); ?>
        </h1>
        <?php if (get_the_modified_date()) : ?>
          <p style="font-size:12px;color:var(--gray-mid);margin-top:8px">
            Última actualización: <?php echo get_the_modified_date('d \d\e F \d\e Y'); ?>
          </p>
        <?php endif; ?>
      </header>
      <div class="entry-content" style="font-size:14.5px;line-height:1.9;color:var(--gray-mid)">
        <?php the_content(); ?>
      </div>
    </article>
  <?php endwhile; ?>
</div>

<?php get_footer(); ?>
