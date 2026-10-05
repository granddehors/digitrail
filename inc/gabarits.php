<?php
/**
 * Fonctions d'aide aux gabarits : héro, logo, icônes, réglages.
 *
 * @package Digitrail
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * La page courante commence-t-elle par un héro photo sombre ?
 * Si oui, l'en-tête est transparent en haut de page ; sinon il est plein et le contenu est décalé.
 *
 * @return bool
 */
function dgt_avec_hero() {
  $hero = ( is_front_page() && ! is_home() ) || dgt_est_page_construite();
  return (bool) apply_filters( 'dgt_avec_hero', $hero );
}

/**
 * Gabarits de page construits entièrement par des champs ACF (héro + sections).
 *
 * @return array
 */
function dgt_gabarits_construits() {
  return array( 'page-templates/profil.php', 'page-templates/contact.php', 'page-templates/podcast.php', 'page-templates/agence.php' );
}

/**
 * La page (courante ou donnée) utilise-t-elle un gabarit construit par champs ?
 *
 * @param int|WP_Post|null $page Page à tester (courante par défaut).
 * @return bool
 */
function dgt_est_page_construite( $page = null ) {
  $page = get_post( $page );
  return $page && 'page' === $page->post_type && in_array( get_page_template_slug( $page ), dgt_gabarits_construits(), true );
}

/**
 * Attributs d'une section selon son fond : classes et thème sombre.
 *
 * @param string $fond   clair, blanc, sombre ou bleu.
 * @param string $classe Classes supplémentaires.
 * @return string Attributs HTML échappés.
 */
function dgt_attributs_section( $fond, $classe = '' ) {
  $classes = array( 'section' );
  if ( 'blanc' === $fond ) {
    $classes[] = 'section--claire';
  } elseif ( 'bleu' === $fond ) {
    $classes[] = 'section--bleu';
  }
  if ( $classe ) {
    $classes[] = $classe;
  }
  $attributs = 'class="' . esc_attr( implode( ' ', $classes ) ) . '"';
  if ( in_array( $fond, array( 'sombre', 'bleu' ), true ) ) {
    $attributs .= ' data-theme="sombre"';
  }
  return $attributs;
}

/**
 * Classes du <body>.
 *
 * @param array $classes Classes existantes.
 * @return array
 */
function dgt_classes_body( $classes ) {
  if ( dgt_avec_hero() ) {
    $classes[] = 'avec-hero';
  }
  return $classes;
}
add_filter( 'body_class', 'dgt_classes_body' );

/**
 * Lit un réglage de la page d'options ACF, avec une valeur par défaut.
 *
 * @param string $nom    Nom du champ.
 * @param mixed  $defaut Valeur si le champ est vide ou ACF absent.
 * @return mixed
 */
function dgt_reglage( $nom, $defaut = '' ) {
  if ( ! function_exists( 'get_field' ) ) {
    return $defaut;
  }
  $valeur = get_field( $nom, 'option' );
  return ( null === $valeur || '' === $valeur || false === $valeur ) ? $defaut : $valeur;
}

/**
 * Adresse de la page de contact (réglage du site, sinon l'ancre du bandeau).
 *
 * @return string
 */
function dgt_lien_contact() {
  return dgt_reglage( 'dgt_page_contact', '#contact' );
}

/**
 * Logo pour fond sombre (en-tête et pied de page).
 *
 * @param string $classe  Classe CSS de l'image.
 * @param int    $hauteur Hauteur d'affichage en pixels.
 * @param string $alt     Texte alternatif (vide si le lien parent porte déjà le nom).
 */
function dgt_logo( $classe, $hauteur, $alt = '' ) {
  // Rapport largeur / hauteur de la zone de dessin du logo.
  $largeur = (int) round( $hauteur * 2389.099 / 452.684 );
  printf(
    '<img class="%1$s" src="%2$s" width="%3$d" height="%4$d" alt="%5$s">',
    esc_attr( $classe ),
    esc_url( DGT_URL . '/assets/img/logo-fond-sombre.svg' ),
    (int) $largeur,
    (int) $hauteur,
    esc_attr( $alt )
  );
}

/**
 * Flèche des boutons et liens d'action.
 */
function dgt_icone_fleche() {
  echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true" focusable="false"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';
}

/**
 * Découpe un champ texte « un élément par ligne » en liste.
 *
 * @param string|null $texte Texte saisi.
 * @return array
 */
function dgt_lignes( $texte ) {
  return array_values( array_filter( array_map( 'trim', preg_split( '/\R/', (string) $texte ) ) ) );
}

/**
 * Affiche un champ ACF « Lien » sous forme de bouton.
 *
 * @param array|null $lien    Valeur du champ (url, title, target).
 * @param string     $classes Classes du bouton.
 * @param bool       $fleche  Ajoute la flèche.
 */
function dgt_bouton_acf( $lien, $classes = 'bouton bouton--contour', $fleche = false ) {
  if ( empty( $lien['url'] ) || empty( $lien['title'] ) ) {
    return;
  }
  $externe = '_blank' === ( $lien['target'] ?? '' );
  printf(
    '<a href="%1$s" class="%2$s"%3$s>%4$s',
    esc_url( $lien['url'] ),
    esc_attr( $classes ),
    $externe ? ' target="_blank" rel="noopener"' : '',
    esc_html( $lien['title'] )
  );
  if ( $fleche ) {
    echo ' ';
    dgt_icone_fleche();
  }
  echo '</a>';
}

/**
 * Titre avec mise en valeur (<em>) et retour à la ligne (<br>) autorisés, pour les héros.
 *
 * @param string $titre Titre saisi.
 * @return string
 */
function dgt_titre_em( $titre ) {
  return wp_kses( $titre, array( 'em' => array(), 'br' => array() ) );
}

/**
 * Pages construites par champs ACF (accueil, gabarits construits) : pas d'éditeur de blocs.
 *
 * @param bool    $utiliser Utiliser l'éditeur de blocs.
 * @param WP_Post $post     Contenu édité.
 * @return bool
 */
function dgt_sans_blocs_accueil( $utiliser, $post ) {
  $accueil = 'page' === get_option( 'show_on_front' ) && (int) get_option( 'page_on_front' ) === (int) $post->ID;
  if ( $accueil || dgt_est_page_construite( $post ) ) {
    return false;
  }
  return $utiliser;
}
add_filter( 'use_block_editor_for_post', 'dgt_sans_blocs_accueil', 10, 2 );
