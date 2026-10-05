<?php
/**
 * Toutes les réalisations (/realisations/), cible du bouton « Toutes nos réalisations ».
 *
 * @package Digitrail
 */

get_header();
?>
<div class="page-simple">
  <div class="conteneur">
    <header class="page-simple__entete">
      <p class="surtitre"><?php esc_html_e( 'Réalisations', 'digitrail' ); ?></p>
      <h1><?php esc_html_e( 'Ils ne laissent rien au hasard. Nous non plus.', 'digitrail' ); ?></h1>
    </header>
    <?php if ( have_posts() ) : ?>
      <div class="realisations-grille">
        <?php
        $dgt_rang = 0;
        while ( have_posts() ) :
          the_post();
          get_template_part( 'parts/carte-realisation', null, array( 'delai' => $dgt_rang++ % 4 ) );
        endwhile;
        ?>
      </div>
      <?php the_posts_pagination(); ?>
    <?php else : ?>
      <p><?php esc_html_e( 'Aucune réalisation publiée pour le moment.', 'digitrail' ); ?></p>
    <?php endif; ?>
  </div>
</div>
<?php
get_template_part( 'parts/bandeau-contact' );

get_footer();
