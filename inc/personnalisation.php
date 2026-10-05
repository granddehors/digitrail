<?php
/**
 * Couleurs et polices réglables dans Apparence > Personnaliser > Charte graphique.
 *
 * Les valeurs par défaut sont celles de la charte 2027 (assets/css/variables.css).
 * Seules les valeurs modifiées sont écrites, en surcharge des variables CSS :
 * tant que rien n'est changé, le site ne reçoit aucun style supplémentaire.
 *
 * @package Digitrail
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * Couleurs d'identité réglables : variable CSS => libellé et valeur de la charte.
 * Les voiles, filets et rôles (fond, texte, surtitre…) en dérivent dans variables.css.
 *
 * @return array
 */
function dgt_couleurs_reglables() {
  return array(
    'vert-digitrail' => array(
      'libelle'     => __( 'Vert Digitrail', 'digitrail' ),
      'defaut'      => '#20cc6e',
      'description' => __( 'Boutons, accents et texte sur fond sombre. Jamais en petit texte sur fond clair.', 'digitrail' ),
    ),
    'vertprofond'    => array(
      'libelle'     => __( 'Vert profond', 'digitrail' ),
      'defaut'      => '#0f7a42',
      'description' => __( 'Liens et accents en texte sur fond clair.', 'digitrail' ),
    ),
    'bleu-nuit'      => array(
      'libelle'     => __( 'Bleu nuit', 'digitrail' ),
      'defaut'      => '#253d6b',
      'description' => __( 'Surtitres, filets, contour de focus, sections bleues.', 'digitrail' ),
    ),
    'anis'           => array(
      'libelle'     => __( 'Anis', 'digitrail' ),
      'defaut'      => '#aacc21',
      'description' => __( 'Couleur décorative.', 'digitrail' ),
    ),
    'encre'          => array(
      'libelle'     => __( 'Encre', 'digitrail' ),
      'defaut'      => '#0e1714',
      'description' => __( 'Texte courant, fonds sombres, voiles sur les photos.', 'digitrail' ),
    ),
    'grisclair'      => array(
      'libelle'     => __( 'Gris clair', 'digitrail' ),
      'defaut'      => '#f5f5f5',
      'description' => __( 'Fond des pages, texte sur fond sombre.', 'digitrail' ),
    ),
  );
}

/**
 * Polices disponibles : uniquement celles hébergées dans le thème (assets/polices/).
 * Clé => libellé, pile CSS et fichier à précharger.
 *
 * @return array
 */
function dgt_polices_disponibles() {
  return array(
    'archivo'         => array(
      'libelle' => 'Archivo',
      'pile'    => '"Archivo", system-ui, sans-serif',
      'fichier' => 'archivo-variable.woff2',
    ),
    'archivo-narrow'  => array(
      'libelle' => 'Archivo Narrow',
      'pile'    => '"Archivo Narrow", "Archivo", system-ui, sans-serif',
      'fichier' => 'archivo-narrow-variable.woff2',
    ),
    'instrument-sans' => array(
      'libelle' => 'Instrument Sans',
      'pile'    => '"Instrument Sans", system-ui, sans-serif',
      'fichier' => 'instrument-sans-variable.woff2',
    ),
    'systeme'         => array(
      'libelle' => __( 'Police du système (aucun téléchargement)', 'digitrail' ),
      'pile'    => 'system-ui, sans-serif',
      'fichier' => '',
    ),
  );
}

/**
 * Rôles typographiques réglables : variable CSS => libellé, police de la charte, préchargement.
 *
 * @return array
 */
function dgt_roles_typographiques() {
  return array(
    'display' => array(
      'libelle'    => __( 'Titres', 'digitrail' ),
      'defaut'     => 'archivo',
      'precharger' => true,
    ),
    'f-util'  => array(
      'libelle'    => __( 'Surtitres, boutons, navigation', 'digitrail' ),
      'defaut'     => 'archivo-narrow',
      'precharger' => false,
    ),
    'f-texte' => array(
      'libelle'    => __( 'Texte courant', 'digitrail' ),
      'defaut'     => 'instrument-sans',
      'precharger' => true,
    ),
  );
}

/**
 * Police retenue pour un rôle (clé de dgt_polices_disponibles()).
 *
 * @param string $role Variable CSS du rôle, sans les tirets initiaux.
 * @return string
 */
function dgt_police_du_role( $role ) {
  $roles   = dgt_roles_typographiques();
  $polices = dgt_polices_disponibles();
  $choix   = get_theme_mod( 'dgt_police_' . $role, $roles[ $role ]['defaut'] );
  return isset( $polices[ $choix ] ) ? $choix : $roles[ $role ]['defaut'];
}

/**
 * Fichiers de police à précharger : ceux des titres et du texte courant.
 *
 * @return string[]
 */
function dgt_polices_prechargees() {
  $polices  = dgt_polices_disponibles();
  $fichiers = array();
  foreach ( dgt_roles_typographiques() as $role => $reglage ) {
    $fichier = $polices[ dgt_police_du_role( $role ) ]['fichier'];
    if ( $reglage['precharger'] && $fichier ) {
      $fichiers[] = $fichier;
    }
  }
  return array_unique( $fichiers );
}

/**
 * Valide le choix d'une police.
 *
 * @param string $choix Clé envoyée par le personnaliseur.
 * @return string
 */
function dgt_valider_police( $choix ) {
  return array_key_exists( $choix, dgt_polices_disponibles() ) ? $choix : '';
}

/**
 * Déclare le panneau « Charte graphique » du personnaliseur.
 *
 * @param WP_Customize_Manager $personnaliseur Gestionnaire du personnaliseur.
 */
function dgt_personnaliseur( $personnaliseur ) {
  $personnaliseur->add_panel(
    'dgt_charte',
    array(
      'title'       => __( 'Charte graphique', 'digitrail' ),
      'description' => __( 'Les valeurs par défaut suivent la charte 2027. Le bouton « Par défaut » de chaque couleur rétablit la valeur de la charte.', 'digitrail' ),
      'priority'    => 30,
    )
  );

  $personnaliseur->add_section(
    'dgt_couleurs',
    array(
      'title'       => __( 'Couleurs', 'digitrail' ),
      'description' => __( 'Fonds, textes, filets et voiles suivent ces couleurs. Vérifiez le contraste : 4,5:1 au minimum pour le texte courant.', 'digitrail' ),
      'panel'       => 'dgt_charte',
    )
  );

  foreach ( dgt_couleurs_reglables() as $variable => $couleur ) {
    $personnaliseur->add_setting(
      'dgt_couleur_' . $variable,
      array(
        'default'           => $couleur['defaut'],
        'sanitize_callback' => 'sanitize_hex_color',
        'capability'        => 'edit_theme_options',
      )
    );
    $personnaliseur->add_control(
      new WP_Customize_Color_Control(
        $personnaliseur,
        'dgt_couleur_' . $variable,
        array(
          'label'       => $couleur['libelle'],
          'description' => $couleur['description'],
          'section'     => 'dgt_couleurs',
        )
      )
    );
  }

  $personnaliseur->add_section(
    'dgt_polices',
    array(
      'title'       => __( 'Polices', 'digitrail' ),
      'description' => __( 'Seules les polices hébergées dans le thème sont proposées.', 'digitrail' ),
      'panel'       => 'dgt_charte',
    )
  );

  $choix = wp_list_pluck( dgt_polices_disponibles(), 'libelle' );
  foreach ( dgt_roles_typographiques() as $role => $reglage ) {
    $personnaliseur->add_setting(
      'dgt_police_' . $role,
      array(
        'default'           => $reglage['defaut'],
        'sanitize_callback' => 'dgt_valider_police',
        'capability'        => 'edit_theme_options',
      )
    );
    $personnaliseur->add_control(
      'dgt_police_' . $role,
      array(
        'label'   => $reglage['libelle'],
        'section' => 'dgt_polices',
        'type'    => 'select',
        'choices' => $choix,
      )
    );
  }
}
add_action( 'customize_register', 'dgt_personnaliseur' );

/**
 * Surcharge des variables CSS pour les réglages qui s'écartent de la charte.
 *
 * @return string Bloc :root, ou chaîne vide si rien n'est modifié.
 */
function dgt_css_personnalise() {
  $declarations = array();

  foreach ( dgt_couleurs_reglables() as $variable => $couleur ) {
    $valeur = sanitize_hex_color( get_theme_mod( 'dgt_couleur_' . $variable, $couleur['defaut'] ) );
    if ( $valeur && strtolower( $valeur ) !== $couleur['defaut'] ) {
      $declarations[] = '--' . $variable . ':' . $valeur;
    }
  }

  $polices = dgt_polices_disponibles();
  foreach ( dgt_roles_typographiques() as $role => $reglage ) {
    $police = dgt_police_du_role( $role );
    if ( $police !== $reglage['defaut'] ) {
      $declarations[] = '--' . $role . ':' . $polices[ $police ]['pile'];
    }
  }

  return $declarations ? ':root{' . implode( ';', $declarations ) . '}' : '';
}

/**
 * Site : surcharge ajoutée juste après variables.css.
 */
function dgt_charger_css_personnalise() {
  $css = dgt_css_personnalise();
  if ( $css ) {
    wp_add_inline_style( 'dgt-variables', $css );
  }
}
add_action( 'wp_enqueue_scripts', 'dgt_charger_css_personnalise', 20 );

/**
 * Éditeur de blocs : même surcharge, pour que l'aperçu corresponde au site.
 *
 * @param array $reglages Réglages de l'éditeur.
 * @return array
 */
function dgt_css_personnalise_editeur( $reglages ) {
  $css = dgt_css_personnalise();
  if ( $css ) {
    $reglages['styles'][] = array( 'css' => $css );
  }
  return $reglages;
}
add_filter( 'block_editor_settings_all', 'dgt_css_personnalise_editeur' );

/**
 * Raccourci Apparence > Couleurs et polices, qui ouvre directement le panneau.
 */
function dgt_menu_charte() {
  add_theme_page(
    __( 'Couleurs et polices', 'digitrail' ),
    __( 'Couleurs et polices', 'digitrail' ),
    'edit_theme_options',
    'customize.php?autofocus[panel]=dgt_charte'
  );
}
add_action( 'admin_menu', 'dgt_menu_charte' );
