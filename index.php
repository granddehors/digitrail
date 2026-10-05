<?php
/**
 * Gabarit par défaut : liste des articles (page des articles).
 *
 * @package Digitrail
 */

get_header();

get_template_part(
  'parts/liste-articles',
  null,
  array(
    'surtitre' => __( 'Actualités', 'digitrail' ),
    'titre'    => is_home() && ! is_front_page() ? single_post_title( '', false ) : __( 'Ce qui nous occupe', 'digitrail' ),
  )
);

get_footer();
