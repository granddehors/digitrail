<?php
/**
 * Bandeau de contact vert, en bas des pages de présentation.
 *
 * Utilisation : get_template_part( 'parts/bandeau-contact', null, array( 'titre' => '…', 'texte' => '…' ) );
 *
 * @package Digitrail
 */

$dgt_bandeau = wp_parse_args(
  $args ?? array(),
  array(
    'titre'   => __( 'Tout commence par une discussion, le reste suit.', 'digitrail' ),
    'texte'   => __( 'Racontez-nous votre saison, votre course ou votre projet. On vous dit franchement si on est les bons interlocuteurs.', 'digitrail' ),
    'libelle' => __( 'Prendre rendez-vous', 'digitrail' ),
    'lien'    => dgt_lien_contact(),
  )
);
?>
<section class="bandeau" id="contact" aria-labelledby="bandeau-titre">
  <div class="conteneur bandeau__int">
    <div class="apparition">
      <h2 id="bandeau-titre"><?php echo esc_html( $dgt_bandeau['titre'] ); ?></h2>
      <p><?php echo esc_html( $dgt_bandeau['texte'] ); ?></p>
    </div>
    <a href="<?php echo esc_url( $dgt_bandeau['lien'] ); ?>" class="bouton bouton--encre apparition" data-d="1"><?php echo esc_html( $dgt_bandeau['libelle'] ); ?> <?php dgt_icone_fleche(); ?></a>
  </div>
</section>
