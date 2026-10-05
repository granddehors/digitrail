<?php
/**
 * Feuilles de style, scripts, polices préchargées et favicon.
 *
 * @package Digitrail
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * Version d'un fichier du thème : date de modification, pour vider le cache à chaque changement.
 *
 * @param string $chemin Chemin relatif au dossier du thème.
 * @return string
 */
function dgt_version_fichier( $chemin ) {
  $fichier = DGT_DOSSIER . '/' . $chemin;
  return file_exists( $fichier ) ? (string) filemtime( $fichier ) : DGT_VERSION;
}

/**
 * Charge les styles et le script du site.
 */
function dgt_charger_ressources() {
  wp_enqueue_style( 'dgt-variables', DGT_URL . '/assets/css/variables.css', array(), dgt_version_fichier( 'assets/css/variables.css' ) );
  wp_enqueue_style( 'dgt-principal', DGT_URL . '/assets/css/principal.css', array( 'dgt-variables' ), dgt_version_fichier( 'assets/css/principal.css' ) );

  wp_enqueue_script(
    'dgt-principal',
    DGT_URL . '/assets/js/principal.js',
    array(),
    dgt_version_fichier( 'assets/js/principal.js' ),
    array(
      'in_footer' => true,
      'strategy'  => 'defer',
    )
  );
}
add_action( 'wp_enqueue_scripts', 'dgt_charger_ressources' );

/**
 * Tout début du <head> : classe js, préchargement des polices affichées au premier écran, favicon.
 */
function dgt_entete_document() {
  // Remplace no-js par js avant l'affichage, pour les animations d'apparition.
  echo "<script>document.documentElement.className=document.documentElement.className.replace('no-js','js');</script>\n";

  $polices = dgt_polices_prechargees();
  foreach ( $polices as $police ) {
    printf(
      '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
      esc_url( DGT_URL . '/assets/polices/' . $police )
    );
  }

  // Favicon SVG du thème, sauf si une icône de site est définie dans l'administration.
  if ( ! has_site_icon() ) {
    printf(
      '<link rel="icon" href="%s" type="image/svg+xml">' . "\n",
      esc_url( DGT_URL . '/assets/img/favicon.svg' )
    );
  }
}
add_action( 'wp_head', 'dgt_entete_document', 1 );
