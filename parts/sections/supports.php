<?php
/**
 * Section « Supports par métier » : pour chaque métier, le problème et le support que nous construisons.
 * Ancre #metiers (cible du bouton du héro).
 *
 * @package Digitrail
 */

$dgt_s = $args;
?>
<section id="metiers" <?php echo dgt_attributs_section( $dgt_s['fond'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé dans la fonction. ?>>
  <div class="conteneur">
    <?php get_template_part( 'parts/sections/tete', null, $dgt_s ); ?>
    <?php if ( $dgt_s['items'] ) : ?>
      <div class="supports">
        <?php foreach ( $dgt_s['items'] as $dgt_item ) : ?>
          <article class="support apparition">
            <div class="support__metier">
              <h3><?php echo esc_html( $dgt_item['metier'] ); ?></h3>
              <?php if ( $dgt_item['probleme'] ) : ?>
                <p class="support__probleme"><?php echo esc_html( $dgt_item['probleme'] ); ?></p>
              <?php endif; ?>
            </div>
            <div class="support__solution">
              <p class="support__libelle donnee"><?php esc_html_e( 'Le support', 'digitrail' ); ?></p>
              <p class="support__nom"><?php echo esc_html( $dgt_item['support'] ); ?></p>
              <p class="support__texte"><?php echo esc_html( $dgt_item['texte'] ); ?></p>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
