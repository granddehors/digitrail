<?php
/**
 * Page d'accueil. Tout le contenu vient des champs ACF du groupe « Page d'accueil ».
 *
 * @package Digitrail
 */

get_header();

get_template_part(
  'parts/hero',
  null,
  array(
    'image'            => get_field( 'hero_image' ),
    'titre'            => get_field( 'hero_titre' ),
    'chapo'            => get_field( 'hero_chapo' ),
    'bouton'           => get_field( 'hero_bouton' ),
    'references_titre' => get_field( 'hero_references_titre' ),
    'references'       => dgt_lignes( get_field( 'hero_references' ) ),
    'credit'           => get_field( 'hero_credit' ),
  )
);

get_template_part( 'parts/accueil-services' );
get_template_part( 'parts/accueil-profils' );
get_template_part( 'parts/accueil-agence' );

get_template_part(
  'parts/section-realisations',
  null,
  array(
    'surtitre' => get_field( 'realisations_surtitre' ),
    'titre'    => get_field( 'realisations_titre' ),
    'ids'      => (array) get_field( 'realisations_selection' ),
  )
);

get_template_part( 'parts/accueil-actus' );

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
