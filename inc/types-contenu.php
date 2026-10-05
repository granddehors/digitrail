<?php
/**
 * Types de contenu : réalisations.
 * Les actualités utilisent les articles de WordPress.
 *
 * @package Digitrail
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * Déclare le type de contenu « Réalisation ».
 * Contenu saisi dans les champs ACF (acf-json/group_dgt_realisation.json), pas dans l'éditeur.
 */
function dgt_declarer_realisations() {
  register_post_type(
    'realisation',
    array(
      'labels'        => array(
        'name'               => __( 'Réalisations', 'digitrail' ),
        'singular_name'      => __( 'Réalisation', 'digitrail' ),
        'add_new'            => __( 'Ajouter', 'digitrail' ),
        'add_new_item'       => __( 'Ajouter une réalisation', 'digitrail' ),
        'edit_item'          => __( 'Modifier la réalisation', 'digitrail' ),
        'new_item'           => __( 'Nouvelle réalisation', 'digitrail' ),
        'view_item'          => __( 'Voir la réalisation', 'digitrail' ),
        'search_items'       => __( 'Rechercher une réalisation', 'digitrail' ),
        'not_found'          => __( 'Aucune réalisation', 'digitrail' ),
        'not_found_in_trash' => __( 'Aucune réalisation dans la corbeille', 'digitrail' ),
        'all_items'          => __( 'Toutes les réalisations', 'digitrail' ),
        'featured_image'     => __( 'Image de la vignette', 'digitrail' ),
        'set_featured_image' => __( "Choisir l'image de la vignette", 'digitrail' ),
      ),
      'public'        => true,
      'has_archive'   => 'realisations',
      'rewrite'       => array(
        'slug'       => 'realisations',
        'with_front' => false,
      ),
      'menu_icon'     => 'dashicons-awards',
      'menu_position' => 21,
      'supports'      => array( 'title', 'thumbnail', 'page-attributes' ),
      'show_in_rest'  => true,
    )
  );
}
add_action( 'init', 'dgt_declarer_realisations' );

/**
 * Destination d'une réalisation au clic : sa page ou le site du client.
 * Si « site du client » est choisi sans adresse, on retombe sur la page.
 *
 * @param int|WP_Post|null $realisation Réalisation (courante par défaut).
 * @return array { url: string, externe: bool }
 */
function dgt_lien_realisation( $realisation = null ) {
  $realisation = get_post( $realisation );
  $site        = function_exists( 'get_field' ) ? get_field( 'dgt_site_client', $realisation->ID ) : '';
  $clic        = function_exists( 'get_field' ) ? get_field( 'dgt_destination_clic', $realisation->ID ) : 'page';

  if ( 'site' === $clic && $site ) {
    return array(
      'url'     => $site,
      'externe' => true,
    );
  }
  return array(
    'url'     => get_permalink( $realisation ),
    'externe' => false,
  );
}

/**
 * Rend l'adresse du site client obligatoire quand le clic doit y mener.
 *
 * @param bool|string $valide Résultat de validation.
 * @param mixed       $valeur Valeur saisie.
 * @return bool|string
 */
function dgt_valider_site_client( $valide, $valeur ) {
  if ( true !== $valide ) {
    return $valide;
  }
  // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce vérifié par ACF avant la validation.
  $champs = isset( $_POST['acf'] ) ? wp_unslash( $_POST['acf'] ) : array();
  $clic   = $champs['field_dgt_destination_clic'] ?? 'page';
  if ( 'site' === $clic && empty( $valeur ) ) {
    return __( "Indiquez l'adresse du site : le clic sur la vignette doit y mener.", 'digitrail' );
  }
  return $valide;
}
add_filter( 'acf/validate_value/key=field_dgt_site_client', 'dgt_valider_site_client', 10, 2 );

/**
 * Colonne « Au clic » dans la liste des réalisations de l'administration.
 *
 * @param array $colonnes Colonnes.
 * @return array
 */
function dgt_colonnes_realisations( $colonnes ) {
  $colonnes['dgt_clic'] = __( 'Au clic', 'digitrail' );
  return $colonnes;
}
add_filter( 'manage_realisation_posts_columns', 'dgt_colonnes_realisations' );

/**
 * Contenu de la colonne « Au clic ».
 *
 * @param string $colonne Nom de la colonne.
 * @param int    $id      Identifiant de la réalisation.
 */
function dgt_contenu_colonne_realisations( $colonne, $id ) {
  if ( 'dgt_clic' !== $colonne ) {
    return;
  }
  $lien = dgt_lien_realisation( $id );
  echo esc_html( $lien['externe'] ? __( 'Site du client', 'digitrail' ) : __( 'Page de la réalisation', 'digitrail' ) );
}
add_action( 'manage_realisation_posts_custom_column', 'dgt_contenu_colonne_realisations', 10, 2 );

/**
 * Archive des réalisations : de la plus récente à la plus ancienne (date de réalisation), 12 par page.
 *
 * @param WP_Query $requete Requête principale.
 */
function dgt_ordonner_realisations( $requete ) {
  if ( is_admin() || ! $requete->is_main_query() || ! $requete->is_post_type_archive( 'realisation' ) ) {
    return;
  }
  $requete->set( 'posts_per_page', 12 );
  // Date de réalisation facultative : les réalisations sans date restent listées, après les autres.
  $requete->set(
    'meta_query',
    array(
      'relation'  => 'OR',
      'date_real' => array(
        'key'     => 'dgt_date_realisation',
        'compare' => 'EXISTS',
      ),
      array(
        'key'     => 'dgt_date_realisation',
        'compare' => 'NOT EXISTS',
      ),
    )
  );
  $requete->set(
    'orderby',
    array(
      'date_real' => 'DESC',
      'date'      => 'DESC',
    )
  );
}
add_action( 'pre_get_posts', 'dgt_ordonner_realisations' );
