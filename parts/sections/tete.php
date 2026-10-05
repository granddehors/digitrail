<?php
/**
 * Tête de section commune : surtitre, titre, chapô.
 *
 * Arguments : surtitre, titre, chapo, chapo_a_droite (bool : chapô en colonne de droite).
 *
 * @package Digitrail
 */

$dgt_tete = wp_parse_args(
  $args ?? array(),
  array(
    'surtitre'       => '',
    'titre'          => '',
    'chapo'          => '',
    'chapo_a_droite' => false,
  )
);
?>
<div class="section__tete apparition">
  <div>
    <?php if ( $dgt_tete['surtitre'] ) : ?>
      <p class="surtitre"><?php echo esc_html( $dgt_tete['surtitre'] ); ?></p>
    <?php endif; ?>
    <h2><?php echo esc_html( $dgt_tete['titre'] ); ?></h2>
    <?php if ( $dgt_tete['chapo'] && ! $dgt_tete['chapo_a_droite'] ) : ?>
      <p class="chapo"><?php echo esc_html( $dgt_tete['chapo'] ); ?></p>
    <?php endif; ?>
  </div>
  <?php if ( $dgt_tete['chapo'] && $dgt_tete['chapo_a_droite'] ) : ?>
    <p class="chapo"><?php echo esc_html( $dgt_tete['chapo'] ); ?></p>
  <?php endif; ?>
</div>
