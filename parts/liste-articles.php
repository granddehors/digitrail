<?php
/**
 * Liste d'articles avec titre de page : blog, archives, résultats de recherche.
 *
 * Arguments : 'surtitre', 'titre', 'description' (HTML autorisé : wp_kses_post).
 *
 * @package Digitrail
 */

$dgt_liste = wp_parse_args(
  $args ?? array(),
  array(
    'surtitre'    => '',
    'titre'       => '',
    'description' => '',
  )
);
?>
<div class="page-simple">
  <div class="conteneur">
    <header class="page-simple__entete">
      <?php if ( $dgt_liste['surtitre'] ) : ?>
        <p class="surtitre"><?php echo esc_html( $dgt_liste['surtitre'] ); ?></p>
      <?php endif; ?>
      <h1><?php echo esc_html( $dgt_liste['titre'] ); ?></h1>
      <?php if ( $dgt_liste['description'] ) : ?>
        <div class="chapo"><?php echo wp_kses_post( $dgt_liste['description'] ); ?></div>
      <?php endif; ?>
    </header>

    <?php if ( have_posts() ) : ?>
      <div class="liste-articles">
        <?php
        $dgt_premier = true;
        while ( have_posts() ) :
          the_post();
          get_template_part( 'parts/carte-article', null, array( 'premier' => $dgt_premier ) );
          $dgt_premier = false;
        endwhile;
        ?>
      </div>
      <?php
      the_posts_pagination(
        array(
          'prev_text'          => __( 'Précédent', 'digitrail' ),
          'next_text'          => __( 'Suivant', 'digitrail' ),
          'screen_reader_text' => __( 'Pages', 'digitrail' ),
        )
      );
      ?>
    <?php else : ?>
      <div class="contenu-texte">
        <p><?php esc_html_e( "Aucun contenu ne correspond. Essayez une autre recherche.", 'digitrail' ); ?></p>
        <?php get_search_form(); ?>
      </div>
    <?php endif; ?>
  </div>
</div>
