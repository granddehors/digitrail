<?php
/**
 * Section « Réalisations » : titre, bouton vers toutes les réalisations, grille de vignettes.
 * Utilisée sur l'accueil et la page athlètes.
 *
 * Arguments : surtitre, titre, ids (réalisations choisies, dans l'ordre), id_ancre.
 *
 * @package Digitrail
 */

$dgt_section = wp_parse_args(
  $args ?? array(),
  array(
    'surtitre' => __( 'Réalisations', 'digitrail' ),
    'titre'    => '',
    'ids'      => array(),
    'id_ancre' => 'realisations',
  )
);
if ( ! $dgt_section['ids'] ) {
  return;
}
$dgt_realisations = new WP_Query(
  array(
    'post_type'      => 'realisation',
    'post__in'       => array_map( 'intval', $dgt_section['ids'] ),
    'orderby'        => 'post__in',
    'posts_per_page' => 4,
    'no_found_rows'  => true,
  )
);
?>
<section class="section" id="<?php echo esc_attr( $dgt_section['id_ancre'] ); ?>">
  <div class="conteneur">
    <div class="section__tete apparition">
      <div>
        <p class="surtitre"><?php echo esc_html( $dgt_section['surtitre'] ); ?></p>
        <h2><?php echo esc_html( $dgt_section['titre'] ); ?></h2>
      </div>
      <a href="<?php echo esc_url( get_post_type_archive_link( 'realisation' ) ); ?>" class="bouton bouton--contour"><?php esc_html_e( 'Toutes nos réalisations', 'digitrail' ); ?></a>
    </div>
    <div class="realisations-grille">
      <?php
      $dgt_rang = 0;
      while ( $dgt_realisations->have_posts() ) :
        $dgt_realisations->the_post();
        get_template_part( 'parts/carte-realisation', null, array( 'delai' => $dgt_rang++ ) );
      endwhile;
      wp_reset_postdata();
      ?>
    </div>
  </div>
</section>
