<?php
/**
 * Section « Niveaux » : cartes côte à côte (ex. Installé, En construction, Un projet).
 *
 * @package Digitrail
 */

$dgt_s = $args;
?>
<section <?php echo dgt_attributs_section( $dgt_s['fond'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé dans la fonction. ?>>
  <div class="conteneur">
    <?php get_template_part( 'parts/sections/tete', null, $dgt_s ); ?>
    <?php if ( $dgt_s['items'] ) : ?>
      <div class="segments">
        <?php foreach ( $dgt_s['items'] as $dgt_rang => $dgt_item ) : ?>
          <div class="segment apparition"<?php echo $dgt_rang ? ' data-d="' . esc_attr( min( $dgt_rang, 3 ) ) . '"' : ''; ?>>
            <?php if ( $dgt_item['intro'] ) : ?>
              <p class="segment__intro donnee"><?php echo esc_html( $dgt_item['intro'] ); ?></p>
            <?php endif; ?>
            <h3><?php echo esc_html( $dgt_item['titre'] ); ?></h3>
            <p><?php echo esc_html( $dgt_item['texte'] ); ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
