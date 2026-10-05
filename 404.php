<?php
/**
 * Page introuvable.
 *
 * @package Digitrail
 */

get_header();
?>
<div class="page-simple">
  <div class="conteneur">
    <header class="page-simple__entete">
      <p class="surtitre"><?php esc_html_e( 'Erreur 404', 'digitrail' ); ?></p>
      <h1><?php esc_html_e( 'Ce sentier ne mène nulle part.', 'digitrail' ); ?></h1>
    </header>
    <div class="contenu-texte">
      <p><?php esc_html_e( "La page que vous cherchez n'existe pas ou a changé d'adresse.", 'digitrail' ); ?></p>
      <p><a class="bouton bouton--vert" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( "Revenir à l'accueil", 'digitrail' ); ?> <?php dgt_icone_fleche(); ?></a></p>
    </div>
  </div>
</div>
<?php
get_footer();
