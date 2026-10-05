<?php
/**
 * Section « Ce que nous ne faisons pas » : colonnes à filet vert, chapô à droite du titre.
 *
 * @package Digitrail
 */

$dgt_s = $args;
?>
<section <?php echo dgt_attributs_section( $dgt_s['fond'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé dans la fonction. ?>>
  <div class="conteneur">
    <?php get_template_part( 'parts/sections/tete', null, array_merge( $dgt_s, array( 'chapo_a_droite' => true ) ) ); ?>
    <?php if ( $dgt_s['items'] ) : ?>
      <div class="refus">
        <?php foreach ( $dgt_s['items'] as $dgt_rang => $dgt_item ) : ?>
          <div class="refus__item apparition"<?php echo $dgt_rang ? ' data-d="' . esc_attr( min( $dgt_rang, 3 ) ) . '"' : ''; ?>>
            <h3><?php echo esc_html( $dgt_item['titre'] ); ?></h3>
            <p><?php echo esc_html( $dgt_item['texte'] ); ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
