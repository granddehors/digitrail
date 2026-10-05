<?php
/**
 * Section « Constat » : une phrase atténuée, une phrase forte (titre), des colonnes.
 *
 * @package Digitrail
 */

$dgt_s = $args;
?>
<section <?php echo dgt_attributs_section( $dgt_s['fond'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé dans la fonction. ?>>
  <div class="conteneur">
    <div class="constat apparition">
      <?php if ( $dgt_s['surtitre'] ) : ?>
        <p class="surtitre"><?php echo esc_html( $dgt_s['surtitre'] ); ?></p>
      <?php endif; ?>
      <?php if ( $dgt_s['phrase'] ) : ?>
        <p class="constat__phrase"><?php echo esc_html( $dgt_s['phrase'] ); ?></p>
      <?php endif; ?>
      <h2 class="constat__phrase constat__phrase--forte"><?php echo esc_html( $dgt_s['titre'] ); ?></h2>
    </div>
    <?php if ( $dgt_s['colonnes'] ) : ?>
      <div class="deux-colonnes apparition">
        <?php foreach ( $dgt_s['colonnes'] as $dgt_colonne ) : ?>
          <div>
            <h3><?php echo esc_html( $dgt_colonne['titre'] ); ?></h3>
            <p><?php echo esc_html( $dgt_colonne['texte'] ); ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
