<?php
/**
 * Page d'une réalisation.
 * Les images de la galerie sont placées à droite des textes : la première face au besoin,
 * les suivantes face à notre réponse. Sans image, le texte occupe toute la largeur.
 *
 * @package Digitrail
 */

get_header();

while ( have_posts() ) :
  the_post();
  $dgt_type    = get_field( 'dgt_type_mission' );
  $dgt_date    = get_field( 'dgt_date_realisation' );
  $dgt_galerie = array_values( array_filter( (array) get_field( 'dgt_galerie' ) ) );
  $dgt_avis    = get_field( 'dgt_avis' );
  $dgt_site    = get_field( 'dgt_site_client' );

  // Blocs de texte présents, dans l'ordre.
  $dgt_blocs = array();
  if ( get_field( 'dgt_problematique' ) ) {
    $dgt_blocs[] = array(
      'titre' => __( 'Le besoin', 'digitrail' ),
      'texte' => get_field( 'dgt_problematique' ),
    );
  }
  if ( get_field( 'dgt_reponse' ) ) {
    $dgt_blocs[] = array(
      'titre' => __( 'Notre réponse', 'digitrail' ),
      'texte' => get_field( 'dgt_reponse' ),
    );
  }

  // Répartition des images : la première avec le premier bloc, les autres avec le dernier.
  $dgt_images = array_fill( 0, max( 1, count( $dgt_blocs ) ), array() );
  if ( count( $dgt_blocs ) > 1 && count( $dgt_galerie ) > 1 ) {
    $dgt_images[0] = array( $dgt_galerie[0] );
    $dgt_images[ count( $dgt_blocs ) - 1 ] = array_slice( $dgt_galerie, 1 );
  } else {
    $dgt_images[0] = $dgt_galerie;
  }
  ?>
  <article <?php post_class( 'page-simple realisation-page' ); ?>>
    <div class="conteneur">
      <header class="page-simple__entete">
        <?php if ( $dgt_type ) : ?>
          <p class="surtitre"><?php echo esc_html( $dgt_type ); ?></p>
        <?php endif; ?>
        <h1><?php the_title(); ?></h1>
        <?php if ( $dgt_date ) : ?>
          <p class="page-simple__meta">
            <time datetime="<?php echo esc_attr( wp_date( 'Y-m', strtotime( $dgt_date ) ) ); ?>"><?php echo esc_html( wp_date( 'F Y', strtotime( $dgt_date ) ) ); ?></time>
          </p>
        <?php endif; ?>
      </header>

      <?php foreach ( $dgt_blocs as $dgt_rang => $dgt_bloc ) : ?>
        <?php $dgt_visuels = $dgt_images[ $dgt_rang ] ?? array(); ?>
        <section class="realisation-page__bloc<?php echo $dgt_visuels ? ' realisation-page__bloc--illustre' : ''; ?>">
          <div class="contenu-texte">
            <h2><?php echo esc_html( $dgt_bloc['titre'] ); ?></h2>
            <?php echo wp_kses_post( $dgt_bloc['texte'] ); ?>
          </div>
          <?php if ( $dgt_visuels ) : ?>
            <div class="realisation-page__visuels">
              <?php
              foreach ( $dgt_visuels as $dgt_image_id ) {
                echo wp_get_attachment_image(
                  $dgt_image_id,
                  'large',
                  false,
                  array(
                    'loading' => 'lazy',
                    'sizes'   => '(max-width: 980px) 100vw, 560px',
                  )
                );
              }
              ?>
            </div>
          <?php endif; ?>
        </section>
      <?php endforeach; ?>

      <?php if ( ! $dgt_blocs && $dgt_galerie ) : ?>
        <div class="realisation-page__visuels realisation-page__visuels--seuls">
          <?php
          foreach ( $dgt_galerie as $dgt_image_id ) {
            echo wp_get_attachment_image( $dgt_image_id, 'large', false, array( 'loading' => 'lazy' ) );
          }
          ?>
        </div>
      <?php endif; ?>

      <?php if ( ! empty( $dgt_avis['texte'] ) ) : ?>
        <figure class="realisation-page__avis">
          <blockquote><p><?php echo esc_html( $dgt_avis['texte'] ); ?></p></blockquote>
          <?php if ( $dgt_avis['nom'] ) : ?>
            <figcaption>
              <b><?php echo esc_html( $dgt_avis['nom'] ); ?></b><?php echo $dgt_avis['fonction'] ? ', ' . esc_html( $dgt_avis['fonction'] ) : ''; ?>
            </figcaption>
          <?php endif; ?>
        </figure>
      <?php endif; ?>

      <?php if ( $dgt_site ) : ?>
        <p class="realisation-page__site">
          <a class="bouton bouton--contour" href="<?php echo esc_url( $dgt_site ); ?>" target="_blank" rel="noopener">
            <?php esc_html_e( 'Voir le site', 'digitrail' ); ?> <?php dgt_icone_fleche(); ?>
            <span class="screen-reader-text"><?php esc_html_e( '(nouvel onglet)', 'digitrail' ); ?></span>
          </a>
        </p>
      <?php endif; ?>
    </div>
  </article>
  <?php
endwhile;

get_template_part( 'parts/bandeau-contact' );

get_footer();
