<?php
/**
 * ACF Pro : synchronisation JSON des groupes de champs et page de réglages du site.
 *
 * @package Digitrail
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * Enregistre les groupes de champs dans le thème (acf-json/), pour les versionner.
 *
 * @return string
 */
function dgt_acf_dossier_enregistrement() {
  return DGT_DOSSIER . '/acf-json';
}
add_filter( 'acf/settings/save_json', 'dgt_acf_dossier_enregistrement' );

/**
 * Charge les groupes de champs depuis le thème.
 *
 * @param array $dossiers Dossiers de chargement.
 * @return array
 */
function dgt_acf_dossiers_chargement( $dossiers ) {
  return array( DGT_DOSSIER . '/acf-json' );
}
add_filter( 'acf/settings/load_json', 'dgt_acf_dossiers_chargement' );

/**
 * Page « Réglages du site » : informations communes à toutes les pages (pied de page, contact).
 */
function dgt_acf_page_reglages() {
  if ( ! function_exists( 'acf_add_options_page' ) ) {
    return;
  }
  acf_add_options_page(
    array(
      'page_title' => __( 'Réglages du site', 'digitrail' ),
      'menu_title' => __( 'Réglages du site', 'digitrail' ),
      'menu_slug'  => 'dgt-reglages',
      'capability' => 'edit_theme_options',
      'icon_url'   => 'dashicons-admin-site-alt3',
      'position'   => 59,
      'redirect'   => false,
    )
  );
}
add_action( 'acf/init', 'dgt_acf_page_reglages' );
