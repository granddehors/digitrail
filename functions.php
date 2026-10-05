<?php
/**
 * Digitrail : point d'entrée du thème.
 *
 * Chaque fichier de inc/ regroupe une responsabilité.
 *
 * @package Digitrail
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

define( 'DGT_VERSION', wp_get_theme()->get( 'Version' ) );
define( 'DGT_DOSSIER', get_template_directory() );
define( 'DGT_URL', get_template_directory_uri() );

require DGT_DOSSIER . '/inc/configuration.php';
require DGT_DOSSIER . '/inc/personnalisation.php';
require DGT_DOSSIER . '/inc/ressources.php';
require DGT_DOSSIER . '/inc/nettoyage.php';
require DGT_DOSSIER . '/inc/types-contenu.php';
require DGT_DOSSIER . '/inc/menus.php';
require DGT_DOSSIER . '/inc/gabarits.php';
require DGT_DOSSIER . '/inc/acf.php';
require DGT_DOSSIER . '/inc/contact.php';
require DGT_DOSSIER . '/inc/podcast.php';
