<?php
/**
 * Menus : rendu de la navigation principale au format des maquettes.
 *
 * Structure produite :
 *   <a href>Libellé</a>
 *   <div class="a-sous-menu"><a href>Libellé</a><div class="sous-menu"><a href>Libellé<small>Description</small></a></div></div>
 *
 * La description d'un élément de sous-menu se saisit dans Apparence > Menus
 * (activer « Description » dans les options de l'écran).
 * Les classes CSS saisies sur un élément sont reprises sur le lien (ex. « bouton bouton--vert »).
 *
 * @package Digitrail
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * Walker de la navigation principale.
 */
class Dgt_Walker_Navigation extends Walker_Nav_Menu {

  /**
   * Éléments de premier niveau ouverts avec un conteneur de sous-menu.
   *
   * @var array
   */
  private $conteneurs_ouverts = array();

  /**
   * Ouverture d'un sous-menu.
   *
   * @param string   $output Sortie HTML.
   * @param int      $depth  Profondeur.
   * @param stdClass $args   Arguments de wp_nav_menu().
   */
  public function start_lvl( &$output, $depth = 0, $args = null ) {
    $output .= '<div class="sous-menu">';
  }

  /**
   * Fermeture d'un sous-menu.
   *
   * @param string   $output Sortie HTML.
   * @param int      $depth  Profondeur.
   * @param stdClass $args   Arguments de wp_nav_menu().
   */
  public function end_lvl( &$output, $depth = 0, $args = null ) {
    $output .= '</div>';
  }

  /**
   * Ouverture d'un élément.
   *
   * @param string   $output            Sortie HTML.
   * @param WP_Post  $data_object       Élément de menu.
   * @param int      $depth             Profondeur.
   * @param stdClass $args              Arguments de wp_nav_menu().
   * @param int      $current_object_id Identifiant de l'objet courant.
   */
  public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) {
    $element = $data_object;

    if ( 0 === $depth && $this->has_children ) {
      $this->conteneurs_ouverts[ $element->ID ] = true;
      $ancre   = str_contains( (string) $element->url, '#' );
      $actif   = ( $element->current && ! $ancre ) || $element->current_item_ancestor || $element->current_item_parent;
      $output .= '<div class="a-sous-menu' . ( $actif ? ' est-actif' : '' ) . '">';
    }

    $output .= dgt_lien_menu( $element, 1 === $depth );
  }

  /**
   * Fermeture d'un élément.
   *
   * @param string   $output      Sortie HTML.
   * @param WP_Post  $data_object Élément de menu.
   * @param int      $depth       Profondeur.
   * @param stdClass $args        Arguments de wp_nav_menu().
   */
  public function end_el( &$output, $data_object, $depth = 0, $args = null ) {
    if ( isset( $this->conteneurs_ouverts[ $data_object->ID ] ) ) {
      unset( $this->conteneurs_ouverts[ $data_object->ID ] );
      $output .= '</div>';
    }
  }
}

/**
 * Construit le lien d'un élément de menu.
 *
 * @param WP_Post $element          Élément de menu.
 * @param bool    $avec_description Ajoute la description dans un <small>.
 * @return string
 */
function dgt_lien_menu( $element, $avec_description = false ) {
  $attributs = array( 'href' => $element->url ? $element->url : '#' );

  if ( $element->target ) {
    $attributs['target'] = $element->target;
  }
  if ( $element->xfn ) {
    $attributs['rel'] = $element->xfn;
  }
  if ( '_blank' === $element->target ) {
    $attributs['rel'] = trim( ( $attributs['rel'] ?? '' ) . ' noopener' );
  }
  if ( $element->attr_title ) {
    $attributs['title'] = $element->attr_title;
  }
  // Une ancre (#services…) n'est jamais « la page courante », même si WordPress la marque ainsi.
  if ( $element->current && ! str_contains( (string) $element->url, '#' ) ) {
    $attributs['aria-current'] = 'page';
  }

  // Seules les classes saisies dans l'administration sont reprises, pas celles générées par WordPress.
  $classes = array_filter(
    (array) $element->classes,
    function ( $classe ) {
      return $classe && ! preg_match( '/^(menu-item|current|page[_-]item)/', $classe );
    }
  );
  // Rubrique active : page d'un article (lien « Actualités ») ou d'une réalisation (lien vers leur liste).
  // WordPress marque la page des articles comme « parente » de tous les contenus : on se limite aux articles.
  $page_articles = (int) get_option( 'page_for_posts' );
  $articles      = is_singular( 'post' ) || is_category() || is_tag() || ( is_date() && ! is_post_type_archive() );
  $rubrique      = ( $page_articles && (int) $element->object_id === $page_articles && 'page' === $element->object && $articles )
    || ( 'post_type_archive' === $element->type && is_singular( $element->object ) );
  if ( $rubrique && ! $element->current && ! str_contains( (string) $element->url, '#' ) ) {
    $classes[] = 'est-actif';
  }

  if ( $classes ) {
    $attributs['class'] = implode( ' ', $classes );
  }

  $html = '<a';
  foreach ( $attributs as $nom => $valeur ) {
    $valeur = 'href' === $nom ? esc_url( $valeur ) : esc_attr( $valeur );
    $html  .= ' ' . $nom . '="' . $valeur . '"';
  }
  $html .= '>' . wp_kses_post( apply_filters( 'the_title', $element->title, $element->ID ) );

  if ( $avec_description && $element->description ) {
    $html .= '<small>' . esc_html( $element->description ) . '</small>';
  }

  return $html . '</a>';
}

/**
 * Affiche la navigation principale (sans conteneur : le <nav> est dans parts/entete.php).
 */
function dgt_navigation_principale() {
  wp_nav_menu(
    array(
      'theme_location' => 'principal',
      'container'      => false,
      'items_wrap'     => '%3$s',
      'depth'          => 2,
      'walker'         => new Dgt_Walker_Navigation(),
      'fallback_cb'    => false,
    )
  );
}

/**
 * Affiche une colonne de menu du pied de page, titrée par le nom du menu.
 *
 * @param string $emplacement Emplacement de menu.
 */
function dgt_colonne_pied( $emplacement ) {
  if ( ! has_nav_menu( $emplacement ) ) {
    return;
  }
  echo '<div>';
  echo '<h2>' . esc_html( wp_get_nav_menu_name( $emplacement ) ) . '</h2>';
  wp_nav_menu(
    array(
      'theme_location' => $emplacement,
      'container'      => false,
      'menu_class'     => '',
      'depth'          => 1,
      'fallback_cb'    => false,
    )
  );
  echo '</div>';
}
