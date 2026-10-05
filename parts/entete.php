<?php
/**
 * En-tête du site : logo, navigation principale, bouton du menu mobile.
 * Le comportement (fond au défilement, ouverture du menu) est dans assets/js/principal.js.
 *
 * @package Digitrail
 */

$dgt_classes_entete = 'entete' . ( dgt_avec_hero() ? '' : ' est-fixe' );
?>
<header class="<?php echo esc_attr( $dgt_classes_entete ); ?>" id="entete" data-theme="sombre">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="entete__logo" aria-label="<?php esc_attr_e( 'Digitrail, accueil', 'digitrail' ); ?>">
    <?php dgt_logo( '', 25 ); ?>
  </a>
  <nav class="nav" id="nav" aria-label="<?php esc_attr_e( 'Navigation principale', 'digitrail' ); ?>">
    <?php dgt_navigation_principale(); ?>
    <a href="<?php echo esc_url( dgt_lien_contact() ); ?>" class="bouton bouton--vert bouton--petit"><?php esc_html_e( 'Parlons de votre projet', 'digitrail' ); ?></a>
  </nav>
  <button
    class="bouton-menu"
    id="bouton-menu"
    type="button"
    aria-controls="nav"
    aria-expanded="false"
    aria-label="<?php esc_attr_e( 'Ouvrir le menu', 'digitrail' ); ?>"
    data-libelle-ouvrir="<?php esc_attr_e( 'Ouvrir le menu', 'digitrail' ); ?>"
    data-libelle-fermer="<?php esc_attr_e( 'Fermer le menu', 'digitrail' ); ?>"
  ><span></span></button>
</header>
