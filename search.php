<?php
/**
 * Résultats de recherche.
 *
 * @package Digitrail
 */

get_header();

get_template_part(
  'parts/liste-articles',
  null,
  array(
    'surtitre' => __( 'Recherche', 'digitrail' ),
    /* translators: %s : termes recherchés. */
    'titre'    => sprintf( __( 'Résultats pour « %s »', 'digitrail' ), get_search_query( false ) ),
  )
);

get_footer();
