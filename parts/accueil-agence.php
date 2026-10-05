<?php
/**
 * Accueil : l'agence, l'équipe et le collectif.
 * L'équipe et le collectif viennent de « Réglages du site > Équipe » (parts/equipe.php).
 *
 * @package Digitrail
 */

?>
<section class="section section--bleu" id="agence" data-theme="sombre">
  <div class="conteneur agence">
    <div class="apparition">
      <p class="surtitre"><?php echo esc_html( get_field( 'agence_surtitre' ) ); ?></p>
      <h2><?php echo esc_html( get_field( 'agence_titre' ) ); ?></h2>
      <div class="agence__texte">
        <?php echo wp_kses_post( get_field( 'agence_texte' ) ); ?>
      </div>
      <?php dgt_bouton_acf( get_field( 'agence_bouton' ), 'bouton bouton--vert', true ); ?>
    </div>

    <div>
      <?php get_template_part( 'parts/equipe' ); ?>
    </div>
  </div>
</section>
