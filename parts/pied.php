<?php
/**
 * Pied de page : présentation, ligne de lieu, trois colonnes de menus, liens légaux.
 * Textes modifiables dans « Réglages du site », menus dans Apparence > Menus.
 *
 * @package Digitrail
 */

$dgt_presentation = dgt_reglage(
  'dgt_pied_presentation',
  __( "L'agence de communication des sports outdoor. Installée à <b>Luz-Saint-Sauveur</b>, au cœur des Hautes-Pyrénées, entre le Tourmalet et le cirque de Gavarnie.", 'digitrail' )
);
$dgt_adresse = dgt_reglage( 'dgt_pied_adresse', __( '65120 Luz-Saint-Sauveur · Hautes-Pyrénées', 'digitrail' ) );
?>
<footer class="pied" data-theme="sombre">
  <div class="conteneur">
    <div class="pied__haut">
      <div>
        <?php dgt_logo( 'pied__logo', 24, __( 'Digitrail', 'digitrail' ) ); ?>
        <p class="pied__lieu"><?php echo wp_kses( $dgt_presentation, array( 'b' => array(), 'strong' => array() ) ); ?></p>
        <p class="pied__adresse"><?php echo esc_html( $dgt_adresse ); ?></p>
      </div>
      <?php
      dgt_colonne_pied( 'pied-services' );
      dgt_colonne_pied( 'pied-profils' );
      dgt_colonne_pied( 'pied-agence' );
      ?>
    </div>
    <div class="pied__bas">
      <span>© <?php echo esc_html( wp_date( 'Y' ) ); ?> Digitrail</span>
      <?php
      if ( has_nav_menu( 'pied-legal' ) ) {
        wp_nav_menu(
          array(
            'theme_location' => 'pied-legal',
            'container'      => 'nav',
            'container_aria_label' => __( 'Liens légaux', 'digitrail' ),
            'menu_class'     => '',
            'depth'          => 1,
            'fallback_cb'    => false,
          )
        );
      }
      ?>
    </div>
  </div>
</footer>
