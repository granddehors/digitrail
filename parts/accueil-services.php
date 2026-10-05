<?php
/**
 * Accueil : liste numérotée des services.
 *
 * @package Digitrail
 */

$dgt_services = get_field( 'services' );
?>
<section class="section" id="services">
  <div class="conteneur">
    <div class="section__tete apparition">
      <div>
        <p class="surtitre"><?php echo esc_html( get_field( 'services_surtitre' ) ); ?></p>
        <h2><?php echo esc_html( get_field( 'services_titre' ) ); ?></h2>
        <?php if ( get_field( 'services_chapo' ) ) : ?>
          <p class="chapo"><?php echo esc_html( get_field( 'services_chapo' ) ); ?></p>
        <?php endif; ?>
      </div>
      <?php dgt_bouton_acf( get_field( 'services_bouton' ) ); ?>
    </div>

    <?php if ( $dgt_services ) : ?>
      <div class="services">
        <?php
        foreach ( $dgt_services as $dgt_rang => $dgt_service ) :
          $dgt_balise = $dgt_service['lien'] ? 'a' : 'div';
          ?>
          <<?php echo esc_attr( $dgt_balise ); ?> class="service apparition"<?php echo $dgt_service['lien'] ? ' href="' . esc_url( $dgt_service['lien'] ) . '"' : ''; ?>>
            <span class="service__numero"><?php echo esc_html( sprintf( '%02d', $dgt_rang + 1 ) ); ?></span>
            <h3><?php echo esc_html( $dgt_service['titre'] ); ?></h3>
            <p><?php echo esc_html( $dgt_service['texte'] ); ?></p>
            <?php if ( $dgt_service['lien'] ) : ?>
              <span class="service__lien" aria-hidden="true"><?php esc_html_e( 'En savoir plus', 'digitrail' ); ?></span>
            <?php endif; ?>
          </<?php echo esc_attr( $dgt_balise ); ?>>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
