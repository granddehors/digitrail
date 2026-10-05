<?php
/**
 * Section « Méthode » : étapes numérotées. Ancre #methode (cible du bouton du héro).
 *
 * @package Digitrail
 */

$dgt_s = $args;
?>
<section id="methode" <?php echo dgt_attributs_section( $dgt_s['fond'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé dans la fonction. ?>>
  <div class="conteneur">
    <?php get_template_part( 'parts/sections/tete', null, $dgt_s ); ?>
    <?php if ( $dgt_s['etapes'] ) : ?>
      <ol class="etapes">
        <?php foreach ( $dgt_s['etapes'] as $dgt_rang => $dgt_etape ) : ?>
          <li class="etape apparition">
            <span class="etape__numero" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $dgt_rang + 1 ) ); ?></span>
            <div>
              <h3><?php echo esc_html( $dgt_etape['titre'] ); ?></h3>
              <p><?php echo esc_html( $dgt_etape['texte'] ); ?></p>
            </div>
          </li>
        <?php endforeach; ?>
      </ol>
    <?php endif; ?>
  </div>
</section>
