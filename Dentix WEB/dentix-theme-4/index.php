<?php
/**
 * index.php — Plantilla fallback de WordPress
 * Redirige a la homepage si no hay otra plantilla aplicable
 */
get_header();
?>

<div style="padding:80px 60px;max-width:900px;margin:0 auto">
  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <article>
      <h1 style="font-family:'Playfair Display',serif;font-size:36px;margin-bottom:16px"><?php the_title(); ?></h1>
      <div style="font-size:15px;line-height:1.8;color:var(--gray-mid)"><?php the_content(); ?></div>
    </article>
  <?php endwhile; endif; ?>
</div>

<?php get_footer(); ?>
