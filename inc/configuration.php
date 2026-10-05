<?php
/**
 * Fonctionnalités du thème, emplacements de menus, éditeur de blocs.
 *
 * @package Digitrail
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * Déclare les fonctionnalités prises en charge par le thème.
 */
function dgt_configurer_theme() {
  load_theme_textdomain( 'digitrail', DGT_DOSSIER . '/languages' );

  add_theme_support( 'title-tag' );
  add_theme_support( 'automatic-feed-links' );
  add_theme_support( 'post-thumbnails' );
  add_theme_support(
    'html5',
    array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
  );

  // Éditeur de blocs : l'aperçu reprend les jetons et polices du site.
  add_theme_support( 'editor-styles' );
  add_theme_support( 'responsive-embeds' );
  add_theme_support( 'align-wide' );
  add_editor_style( array( 'assets/css/variables.css', 'assets/css/editeur.css' ) );

  // Vignettes de réalisation et portraits d'équipe : 3:4, recadrés.
  add_image_size( 'dgt-portrait', 640, 853, true );
  // Cartes d'actualité : 16:9, recadrées.
  add_image_size( 'dgt-vignette', 640, 360, true );

  register_nav_menus(
    array(
      'principal'     => __( 'Navigation principale', 'digitrail' ),
      'pied-services' => __( 'Pied de page : services', 'digitrail' ),
      'pied-profils'  => __( 'Pied de page : votre profil', 'digitrail' ),
      'pied-agence'   => __( 'Pied de page : agence', 'digitrail' ),
      'pied-legal'    => __( 'Pied de page : liens légaux', 'digitrail' ),
    )
  );
}
add_action( 'after_setup_theme', 'dgt_configurer_theme' );

/**
 * Largeur du contenu utilisée par les médias intégrés (≈ 68 caractères).
 */
function dgt_largeur_contenu() {
  $GLOBALS['content_width'] = 720;
}
add_action( 'after_setup_theme', 'dgt_largeur_contenu', 0 );
