<?php
/**
 * Sobriété : nettoyage du <head>, XML-RPC, tailles d'image, conversion WebP.
 *
 * @package Digitrail
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * Retire du <head> ce dont le site n'a pas besoin : emojis, oEmbed, generator, RSD, lien court.
 */
function dgt_nettoyer_entete() {
  // Emojis
  remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
  remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
  remove_action( 'wp_print_styles', 'print_emoji_styles' );
  remove_action( 'admin_print_styles', 'print_emoji_styles' );
  remove_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' );
  remove_action( 'admin_enqueue_scripts', 'wp_enqueue_emoji_styles' );
  remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
  remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
  remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
  add_filter( 'emoji_svg_url', '__return_false' );

  // oEmbed : liens de découverte et script d'intégration (l'intégration de vidéos dans les articles reste possible).
  remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
  remove_action( 'wp_head', 'wp_oembed_add_host_js' );

  // Generator, RSD, lien court
  remove_action( 'wp_head', 'wp_generator' );
  remove_action( 'wp_head', 'rsd_link' );
  remove_action( 'wp_head', 'wp_shortlink_wp_head' );
  add_filter( 'the_generator', '__return_empty_string' );
}
add_action( 'init', 'dgt_nettoyer_entete' );

/**
 * Retire l'extension emoji de l'éditeur classique (TinyMCE) quand il est utilisé.
 *
 * @param array $extensions Extensions TinyMCE.
 * @return array
 */
function dgt_retirer_emoji_tinymce( $extensions ) {
  return is_array( $extensions ) ? array_diff( $extensions, array( 'wpemoji' ) ) : array();
}
add_filter( 'tiny_mce_plugins', 'dgt_retirer_emoji_tinymce' );

// XML-RPC désactivé, en-tête X-Pingback retiré.
add_filter( 'xmlrpc_enabled', '__return_false' );
add_filter( 'xmlrpc_methods', '__return_empty_array' );

/**
 * Retire l'en-tête HTTP X-Pingback.
 *
 * @param array $entetes En-têtes HTTP.
 * @return array
 */
function dgt_retirer_pingback( $entetes ) {
  unset( $entetes['X-Pingback'] );
  return $entetes;
}
add_filter( 'wp_headers', 'dgt_retirer_pingback' );

/**
 * Ne génère pas les tailles d'image intermédiaires inutilisées.
 * Conservées : thumbnail, medium, large.
 *
 * @param array $tailles Tailles à générer.
 * @return array
 */
function dgt_limiter_tailles_image( $tailles ) {
  unset( $tailles['medium_large'], $tailles['1536x1536'], $tailles['2048x2048'] );
  return $tailles;
}
add_filter( 'intermediate_image_sizes_advanced', 'dgt_limiter_tailles_image' );

/**
 * Convertit les JPEG et PNG envoyés dans la médiathèque en WebP.
 *
 * @param array $formats Correspondance format source => format de sortie.
 * @return array
 */
function dgt_sortie_webp( $formats ) {
  $formats['image/jpeg'] = 'image/webp';
  $formats['image/png']  = 'image/webp';
  return $formats;
}
add_filter( 'image_editor_output_format', 'dgt_sortie_webp' );

// Chargement des styles de blocs uniquement pour les blocs présents dans la page.
add_filter( 'should_load_separate_core_block_assets', '__return_true' );

/**
 * Qualité des WebP générés par WordPress : 75, bon compromis poids et netteté pour les photos de terrain.
 *
 * @param int    $qualite Qualité proposée.
 * @param string $format  Type MIME de sortie.
 * @return int
 */
function dgt_qualite_webp( $qualite, $format ) {
  return 'image/webp' === $format ? 75 : $qualite;
}
add_filter( 'wp_editor_set_quality', 'dgt_qualite_webp', 10, 2 );
