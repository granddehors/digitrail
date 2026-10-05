<?php
/**
 * Page d'un épisode : visuel, lecteur, description complète, plateformes.
 *
 * @package Digitrail
 */

get_header();

while ( have_posts() ) :
  the_post();
  $dgt_episode   = get_post();
  $dgt_interview = 'interview' === get_field( 'dgt_format' );
  $dgt_page      = get_posts(
    array(
      'post_type'      => 'page',
      'meta_key'       => '_wp_page_template',
      'meta_value'     => 'page-templates/podcast.php',
      'posts_per_page' => 1,
      'fields'         => 'ids',
    )
  );
  ?>
  <article <?php post_class( 'page-simple episode-page' ); ?>>
    <div class="conteneur episode-page__grille">
      <div>
        <?php the_post_thumbnail( 'full', array( 'class' => 'episode-page__image', 'alt' => '', 'sizes' => '(max-width: 860px) 100vw, 400px' ) ); ?>
      </div>
      <div>
        <header class="page-simple__entete">
          <p class="surtitre"><?php echo esc_html( $dgt_interview ? __( "L'interview digitale", 'digitrail' ) : __( 'La Skyline', 'digitrail' ) ); ?></p>
          <h1><?php the_title(); ?></h1>
          <?php dgt_meta_episode( $dgt_episode ); ?>
        </header>
        <div class="episode__lecteur"><?php dgt_bouton_ecoute( $dgt_episode ); ?></div>
        <div class="contenu-texte episode-page__texte">
          <?php the_content(); ?>
        </div>
        <?php dgt_plateformes_podcast(); ?>
        <?php if ( $dgt_page ) : ?>
          <p class="episode-page__retour">
            <a class="bouton bouton--contour" href="<?php echo esc_url( get_permalink( $dgt_page[0] ) . ( $dgt_interview ? '#interviews' : '#skyline' ) ); ?>"><?php esc_html_e( 'Tous les épisodes', 'digitrail' ); ?></a>
          </p>
        <?php endif; ?>
      </div>
    </div>
  </article>
  <?php
endwhile;

get_template_part( 'parts/bandeau-contact' );

get_footer();
