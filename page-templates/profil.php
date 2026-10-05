<?php
/**
 * Template Name: Page de profil
 *
 * Pages « Vous êtes athlète », « Vous êtes coach ou pro »… :
 * héro de sous-page, sections modulables (groupe ACF « Page de profil »), bandeau de contact.
 *
 * @package Digitrail
 */

get_header();

get_template_part(
  'parts/hero',
  null,
  array(
    'image'    => get_field( 'hero_image' ),
    'surtitre' => get_field( 'hero_surtitre' ),
    'titre'    => get_field( 'hero_titre' ),
    'chapo'    => get_field( 'hero_chapo' ),
    'bouton'   => get_field( 'hero_bouton' ),
    'credit'   => get_field( 'hero_credit' ),
    'variante' => 'sous-page',
  )
);

$dgt_sections = get_field( 'sections' );
if ( $dgt_sections ) {
  foreach ( $dgt_sections as $dgt_section ) {
    if ( 'realisations' === $dgt_section['acf_fc_layout'] ) {
      get_template_part(
        'parts/section-realisations',
        null,
        array(
          'surtitre' => $dgt_section['surtitre'],
          'titre'    => $dgt_section['titre'],
          'ids'      => (array) $dgt_section['selection'],
        )
      );
      continue;
    }
    get_template_part( 'parts/sections/' . $dgt_section['acf_fc_layout'], null, $dgt_section );
  }
}

get_template_part(
  'parts/bandeau-contact',
  null,
  array_filter(
    array(
      'titre' => get_field( 'bandeau_titre' ),
      'texte' => get_field( 'bandeau_texte' ),
    )
  )
);

get_footer();
