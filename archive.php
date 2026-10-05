<?php
/**
 * Archives : catégories, étiquettes, dates, auteurs.
 *
 * @package Digitrail
 */

get_header();

get_template_part(
  'parts/liste-articles',
  null,
  array(
    'surtitre'    => __( 'Actualités', 'digitrail' ),
    'titre'       => wp_strip_all_tags( get_the_archive_title() ),
    'description' => get_the_archive_description(),
  )
);

get_footer();
