<?php
/**
 * Page simple (mentions légales, confidentialité…), contenu saisi dans l'éditeur de blocs.
 *
 * @package Digitrail
 */

get_header();

while ( have_posts() ) :
  the_post();
  ?>
  <article <?php post_class( 'page-simple' ); ?>>
    <div class="conteneur">
      <header class="page-simple__entete">
        <h1><?php the_title(); ?></h1>
      </header>
      <div class="contenu-texte">
        <?php the_content(); ?>
      </div>
    </div>
  </article>
  <?php
endwhile;

get_footer();
