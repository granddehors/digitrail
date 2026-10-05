<?php
/**
 * Vignette d'une réalisation (grille de l'accueil, de la page athlètes, de l'archive).
 * Toute la carte est un lien : vers la page de la réalisation ou vers le site du client.
 * Sans image mise en avant, la carte passe en aplat anis (variante de la maquette).
 *
 * Arguments : 'delai' (0 à 3) pour l'apparition décalée.
 *
 * @package Digitrail
 */

$dgt_delai  = (int) ( $args['delai'] ?? 0 );
$dgt_lien   = dgt_lien_realisation();
$dgt_image  = has_post_thumbnail();
$dgt_type   = get_field( 'dgt_type_mission' );
$dgt_accroche = get_field( 'dgt_accroche' );
$dgt_classes  = 'realisation-carte apparition' . ( $dgt_image ? '' : ' realisation-carte--aplat' );
?>
<a
  class="<?php echo esc_attr( $dgt_classes ); ?>"
  href="<?php echo esc_url( $dgt_lien['url'] ); ?>"
  <?php if ( $dgt_delai ) : ?>data-d="<?php echo esc_attr( $dgt_delai ); ?>"<?php endif; ?>
  <?php if ( $dgt_lien['externe'] ) : ?>target="_blank" rel="noopener"<?php endif; ?>
>
  <?php
  if ( $dgt_image ) {
    the_post_thumbnail(
      'dgt-portrait',
      array(
        'alt'     => '',
        'loading' => 'lazy',
        'sizes'   => '(max-width: 560px) 100vw, (max-width: 1100px) 50vw, 25vw',
      )
    );
  }
  ?>
  <div class="realisation-carte__voile">
    <?php if ( $dgt_type ) : ?>
      <p class="realisation-carte__type"><?php echo esc_html( $dgt_type ); ?></p>
    <?php endif; ?>
    <h3><?php the_title(); ?></h3>
    <?php if ( $dgt_accroche ) : ?>
      <p><?php echo esc_html( $dgt_accroche ); ?></p>
    <?php endif; ?>
    <?php if ( $dgt_lien['externe'] ) : ?>
      <span class="screen-reader-text"><?php esc_html_e( '(site du client, nouvel onglet)', 'digitrail' ); ?></span>
    <?php else : ?>
      <span class="realisation-carte__suite">
        <span class="lien-carte"><?php esc_html_e( "Voir l'étude de cas", 'digitrail' ); ?> <?php dgt_icone_fleche(); ?></span>
      </span>
    <?php endif; ?>
  </div>
</a>
