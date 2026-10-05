<?php
/**
 * Podcasts : type de contenu « podcast » alimenté par le flux RSS Ausha.
 *
 * Deux formats dans le même flux : La Skyline et l'interview digitale (champ ACF dgt_format).
 * Synchronisation quotidienne (WP-Cron) et à la demande (Podcasts > Synchroniser).
 * Un épisode existant n'est jamais écrasé sur ce que l'équipe peut modifier (titre, extrait, format, invité) :
 * seuls l'audio, la durée et le lien Ausha sont mis à jour.
 *
 * @package Digitrail
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * Déclare le type de contenu « Podcast ».
 */
function dgt_declarer_podcasts() {
  register_post_type(
    'podcast',
    array(
      'labels'        => array(
        'name'               => __( 'Podcasts', 'digitrail' ),
        'singular_name'      => __( 'Podcast', 'digitrail' ),
        'menu_name'          => __( 'Podcasts', 'digitrail' ),
        'add_new'            => __( 'Ajouter', 'digitrail' ),
        'add_new_item'       => __( 'Ajouter un épisode', 'digitrail' ),
        'edit_item'          => __( "Modifier l'épisode", 'digitrail' ),
        'view_item'          => __( "Voir l'épisode", 'digitrail' ),
        'all_items'          => __( 'Tous les épisodes', 'digitrail' ),
        'search_items'       => __( 'Rechercher un épisode', 'digitrail' ),
        'not_found'          => __( 'Aucun épisode', 'digitrail' ),
        'not_found_in_trash' => __( 'Aucun épisode dans la corbeille', 'digitrail' ),
        'featured_image'     => __( "Visuel de l'épisode", 'digitrail' ),
      ),
      'public'        => true,
      'has_archive'   => false,
      'rewrite'       => array(
        'slug'       => 'podcast',
        'with_front' => false,
      ),
      'menu_icon'     => 'dashicons-microphone',
      'menu_position' => 22,
      'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
      'show_in_rest'  => true,
    )
  );
}
add_action( 'init', 'dgt_declarer_podcasts' );

/* ---------------------------------------------------------------------------
 * Synchronisation avec le flux Ausha
 * ------------------------------------------------------------------------- */

/**
 * Durée iTunes (« 25:42 », « 1:02:03 » ou secondes) en secondes.
 *
 * @param string $duree Durée du flux.
 * @return int
 */
function dgt_duree_en_secondes( $duree ) {
  $parties = array_map( 'intval', explode( ':', (string) $duree ) );
  $total   = 0;
  foreach ( $parties as $partie ) {
    $total = $total * 60 + $partie;
  }
  return $total;
}

/**
 * Déduit le format, l'invité et le titre nettoyé à partir du titre Ausha.
 *
 * @param string $titre Titre de l'épisode dans le flux.
 * @return array { format, invite, titre, hors_serie }
 */
function dgt_analyser_titre_episode( $titre ) {
  $titre   = trim( $titre );
  $analyse = array(
    'format'     => 'skyline',
    'invite'     => '',
    'titre'      => $titre,
    'hors_serie' => false,
  );
  if ( preg_match( "/^L['’]interview Digitale\s*:?\s*(.+)$/iu", $titre, $m ) ) {
    $reste               = trim( $m[1] );
    $analyse['format']   = 'interview';
    $analyse['titre']    = mb_strtoupper( mb_substr( $reste, 0, 1 ) ) . mb_substr( $reste, 1 );
    // Invité : un nom seul (« Guillaume Lalu »), sinon laissé vide pour saisie manuelle.
    if ( preg_match( '/^[\p{Lu}][\p{L}\'’-]+(\s[\p{Lu}][\p{L}\'’-]+){1,2}$/u', $reste ) ) {
      $analyse['invite'] = $reste;
    }
  } elseif ( preg_match( '/^Hors S[ée]rie\s*-\s*(.+)$/iu', $titre, $m ) ) {
    $analyse['titre']      = trim( $m[1] );
    $analyse['hors_serie'] = true;
  }
  return $analyse;
}

/**
 * Lit le flux Ausha et crée ou met à jour les épisodes.
 *
 * @return array|WP_Error { crees, mis_a_jour, total }
 */
function dgt_synchroniser_podcasts() {
  $url = dgt_reglage( 'dgt_flux_podcast', 'https://feed.ausha.co/oww2GEhGK9kz' );
  $rep = wp_remote_get( $url, array( 'timeout' => 20 ) );
  if ( is_wp_error( $rep ) ) {
    return $rep;
  }
  if ( 200 !== (int) wp_remote_retrieve_response_code( $rep ) ) {
    return new WP_Error( 'dgt_flux', __( 'Le flux Ausha ne répond pas.', 'digitrail' ) );
  }

  $precedent = libxml_use_internal_errors( true );
  $flux      = simplexml_load_string( wp_remote_retrieve_body( $rep ), 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NONET );
  libxml_use_internal_errors( $precedent );
  if ( ! $flux || empty( $flux->channel->item ) ) {
    return new WP_Error( 'dgt_flux', __( 'Le flux Ausha est illisible.', 'digitrail' ) );
  }

  require_once ABSPATH . 'wp-admin/includes/media.php';
  require_once ABSPATH . 'wp-admin/includes/file.php';
  require_once ABSPATH . 'wp-admin/includes/image.php';

  $itunes = 'http://www.itunes.com/dtds/podcast-1.0.dtd';
  $bilan  = array(
    'crees'      => 0,
    'mis_a_jour' => 0,
    'total'      => 0,
  );

  foreach ( $flux->channel->item as $item ) {
    ++$bilan['total'];
    $it    = $item->children( $itunes );
    $guid  = sanitize_text_field( (string) $item->guid );
    $audio = esc_url_raw( (string) $item->enclosure['url'] );
    $duree = dgt_duree_en_secondes( (string) $it->duration );
    $lien  = esc_url_raw( (string) $item->link );
    if ( ! $guid || ! $audio ) {
      continue;
    }

    $existants = get_posts(
      array(
        'post_type'      => 'podcast',
        'post_status'    => 'any',
        'meta_key'       => 'dgt_guid',
        'meta_value'     => $guid,
        'posts_per_page' => 1,
        'fields'         => 'ids',
      )
    );

    if ( $existants ) {
      // Épisode connu : seules les données techniques suivent Ausha.
      update_field( 'field_dgt_pod_audio', $audio, $existants[0] );
      update_field( 'field_dgt_pod_duree', $duree, $existants[0] );
      update_field( 'field_dgt_pod_lien', $lien, $existants[0] );
      ++$bilan['mis_a_jour'];
      continue;
    }

    $analyse     = dgt_analyser_titre_episode( (string) $item->title );
    $description = dgt_nettoyer_description_episode( (string) $item->description );
    $date        = gmdate( 'Y-m-d H:i:s', strtotime( (string) $item->pubDate ) );
    $id          = wp_insert_post(
      array(
        'post_type'     => 'podcast',
        'post_status'   => 'publish',
        'post_title'    => sanitize_text_field( $analyse['titre'] ),
        'post_content'  => $description,
        'post_excerpt'  => wp_trim_words( wp_strip_all_tags( $description ), 40, '…' ),
        'post_date_gmt' => $date,
        'post_date'     => get_date_from_gmt( $date ),
      ),
      true
    );
    if ( is_wp_error( $id ) ) {
      continue;
    }
    update_field( 'field_dgt_pod_format', $analyse['format'], $id );
    update_field( 'field_dgt_pod_invite', $analyse['invite'], $id );
    update_field( 'field_dgt_pod_hors_serie', $analyse['hors_serie'] ? 1 : 0, $id );
    update_field( 'field_dgt_pod_audio', $audio, $id );
    update_field( 'field_dgt_pod_duree', $duree, $id );
    update_field( 'field_dgt_pod_lien', $lien, $id );
    update_field( 'field_dgt_pod_guid', $guid, $id );

    $image = isset( $it->image ) ? (string) $it->image->attributes()['href'] : '';
    if ( $image ) {
      $image_id = dgt_importer_visuel_episode( $image, $id, $analyse['titre'] );
      if ( $image_id ) {
        set_post_thumbnail( $id, $image_id );
      }
    }
    ++$bilan['crees'];
  }

  update_option( 'dgt_podcast_derniere_synchro', time(), false );
  return $bilan;
}

/**
 * Description d'un épisode : HTML filtré, sans la mention d'hébergement ajoutée par Ausha.
 *
 * @param string $html Description du flux.
 * @return string
 */
function dgt_nettoyer_description_episode( $html ) {
  $html = preg_replace( '#<p>\s*Hébergé par Ausha.*?</p>#isu', '', $html );
  return trim( wp_kses_post( $html ) );
}

/**
 * Importe le visuel d'un épisode : version 400×400 d'Ausha, convertie en WebP avant l'ajout à la médiathèque.
 *
 * @param string $url   Adresse de l'image dans le flux.
 * @param int    $id    Épisode parent.
 * @param string $titre Titre de l'épisode (nom du fichier).
 * @return int Identifiant du fichier importé, 0 en cas d'échec.
 */
function dgt_importer_visuel_episode( $url, $id, $titre ) {
  $url = preg_replace( '/_1400x1400\./', '_400x400.', $url );
  $tmp = download_url( $url, 20 );
  if ( is_wp_error( $tmp ) ) {
    return 0;
  }
  $fichier = array(
    'name'     => sanitize_file_name( sanitize_title( $titre ) . '.jpg' ),
    'tmp_name' => $tmp,
  );
  $editeur = wp_get_image_editor( $tmp );
  if ( ! is_wp_error( $editeur ) ) {
    $webp = $editeur->save( $tmp . '.webp', 'image/webp' );
    if ( ! is_wp_error( $webp ) ) {
      wp_delete_file( $tmp );
      $fichier = array(
        'name'     => sanitize_file_name( sanitize_title( $titre ) . '.webp' ),
        'tmp_name' => $webp['path'],
      );
    }
  }
  $image_id = media_handle_sideload( $fichier, $id, $titre );
  if ( is_wp_error( $image_id ) ) {
    wp_delete_file( $fichier['tmp_name'] );
    return 0;
  }
  update_post_meta( $image_id, '_wp_attachment_image_alt', '' );
  return (int) $image_id;
}

/**
 * Tâche quotidienne.
 */
function dgt_planifier_synchro_podcasts() {
  if ( ! wp_next_scheduled( 'dgt_synchro_podcasts' ) ) {
    wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'dgt_synchro_podcasts' );
  }
}
add_action( 'init', 'dgt_planifier_synchro_podcasts' );
add_action( 'dgt_synchro_podcasts', 'dgt_synchroniser_podcasts' );

/**
 * Désactive la tâche quand le thème change.
 */
function dgt_deplanifier_synchro_podcasts() {
  wp_clear_scheduled_hook( 'dgt_synchro_podcasts' );
}
add_action( 'switch_theme', 'dgt_deplanifier_synchro_podcasts' );

/* ---------------------------------------------------------------------------
 * Administration : page « Synchroniser », colonnes
 * ------------------------------------------------------------------------- */

/**
 * Sous-menu Podcasts > Synchroniser.
 */
function dgt_menu_synchro_podcasts() {
  add_submenu_page(
    'edit.php?post_type=podcast',
    __( 'Synchroniser avec Ausha', 'digitrail' ),
    __( 'Synchroniser', 'digitrail' ),
    'edit_posts',
    'dgt-synchro-podcasts',
    'dgt_page_synchro_podcasts'
  );
}
add_action( 'admin_menu', 'dgt_menu_synchro_podcasts' );

/**
 * Écran de synchronisation.
 */
function dgt_page_synchro_podcasts() {
  $derniere = (int) get_option( 'dgt_podcast_derniere_synchro' );
  $bilan    = get_transient( 'dgt_podcast_bilan_' . get_current_user_id() );
  delete_transient( 'dgt_podcast_bilan_' . get_current_user_id() );
  ?>
  <div class="wrap">
    <h1><?php esc_html_e( 'Synchroniser avec Ausha', 'digitrail' ); ?></h1>
    <?php if ( is_array( $bilan ) ) : ?>
      <div class="notice notice-success"><p>
        <?php
        /* translators: 1 : épisodes créés, 2 : épisodes mis à jour. */
        echo esc_html( sprintf( __( '%1$d épisode(s) ajouté(s), %2$d mis à jour.', 'digitrail' ), $bilan['crees'], $bilan['mis_a_jour'] ) );
        ?>
      </p></div>
    <?php elseif ( is_string( $bilan ) ) : ?>
      <div class="notice notice-error"><p><?php echo esc_html( $bilan ); ?></p></div>
    <?php endif; ?>
    <p><?php esc_html_e( 'Les nouveaux épisodes publiés sur Ausha sont ajoutés automatiquement une fois par jour. Ce bouton lance la synchronisation tout de suite.', 'digitrail' ); ?></p>
    <p>
      <?php
      echo esc_html(
        $derniere
          /* translators: %s : date et heure. */
          ? sprintf( __( 'Dernière synchronisation : %s.', 'digitrail' ), wp_date( 'j F Y à H:i', $derniere ) )
          : __( 'Aucune synchronisation pour le moment.', 'digitrail' )
      );
      ?>
    </p>
    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
      <input type="hidden" name="action" value="dgt_synchro_podcasts">
      <?php wp_nonce_field( 'dgt_synchro_podcasts' ); ?>
      <?php submit_button( __( 'Synchroniser maintenant', 'digitrail' ), 'primary', 'submit', false ); ?>
    </form>
  </div>
  <?php
}

/**
 * Lance la synchronisation depuis l'écran d'administration.
 */
function dgt_action_synchro_podcasts() {
  if ( ! current_user_can( 'edit_posts' ) ) {
    wp_die( esc_html__( 'Action non autorisée.', 'digitrail' ) );
  }
  check_admin_referer( 'dgt_synchro_podcasts' );
  $bilan = dgt_synchroniser_podcasts();
  set_transient( 'dgt_podcast_bilan_' . get_current_user_id(), is_wp_error( $bilan ) ? $bilan->get_error_message() : $bilan, MINUTE_IN_SECONDS );
  wp_safe_redirect( admin_url( 'edit.php?post_type=podcast&page=dgt-synchro-podcasts' ) );
  exit;
}
add_action( 'admin_post_dgt_synchro_podcasts', 'dgt_action_synchro_podcasts' );

/**
 * Colonnes « Format » et « Durée » dans la liste des épisodes.
 *
 * @param array $colonnes Colonnes.
 * @return array
 */
function dgt_colonnes_podcasts( $colonnes ) {
  $date = $colonnes['date'];
  unset( $colonnes['date'] );
  $colonnes['dgt_format'] = __( 'Format', 'digitrail' );
  $colonnes['dgt_duree']  = __( 'Durée', 'digitrail' );
  $colonnes['date']       = $date;
  return $colonnes;
}
add_filter( 'manage_podcast_posts_columns', 'dgt_colonnes_podcasts' );

/**
 * Contenu des colonnes.
 *
 * @param string $colonne Colonne.
 * @param int    $id      Épisode.
 */
function dgt_contenu_colonnes_podcasts( $colonne, $id ) {
  if ( 'dgt_format' === $colonne ) {
    echo esc_html( 'interview' === get_field( 'dgt_format', $id ) ? __( 'Interview digitale', 'digitrail' ) : __( 'La Skyline', 'digitrail' ) );
  } elseif ( 'dgt_duree' === $colonne ) {
    echo esc_html( dgt_duree_lisible( (int) get_field( 'dgt_duree', $id ) ) );
  }
}
add_action( 'manage_podcast_posts_custom_column', 'dgt_contenu_colonnes_podcasts', 10, 2 );

/* ---------------------------------------------------------------------------
 * Affichage
 * ------------------------------------------------------------------------- */

/**
 * Durée lisible : « 26 min ».
 *
 * @param int $secondes Durée.
 * @return string
 */
function dgt_duree_lisible( $secondes ) {
  /* translators: %d : minutes. */
  return sprintf( __( '%d min', 'digitrail' ), max( 1, (int) round( $secondes / 60 ) ) );
}

/**
 * Épisodes publiés d'un format, du plus récent au plus ancien.
 *
 * @param string $format skyline ou interview.
 * @return WP_Post[]
 */
function dgt_episodes( $format ) {
  return get_posts(
    array(
      'post_type'      => 'podcast',
      'posts_per_page' => -1,
      'meta_key'       => 'dgt_format',
      'meta_value'     => $format,
      'orderby'        => 'date',
      'order'          => 'DESC',
    )
  );
}

/**
 * Bouton « Écouter » : remplacé par le lecteur audio natif au clic (assets/js/principal.js).
 * Rien n'est chargé depuis Ausha avant ce clic.
 *
 * @param WP_Post $episode Épisode.
 */
function dgt_bouton_ecoute( $episode ) {
  $audio = get_field( 'dgt_audio', $episode->ID );
  if ( ! $audio ) {
    return;
  }
  $duree = dgt_duree_lisible( (int) get_field( 'dgt_duree', $episode->ID ) );
  printf(
    '<button type="button" class="bouton-ecoute" data-audio="%1$s" aria-label="%2$s"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="square" aria-hidden="true" focusable="false"><path d="M7 4.5v15l12-7.5z"/></svg><span>%3$s</span></button>',
    esc_url( $audio ),
    /* translators: 1 : titre, 2 : durée. */
    esc_attr( sprintf( __( 'Écouter : %1$s (%2$s)', 'digitrail' ), get_the_title( $episode ), $duree ) ),
    esc_html__( 'Écouter', 'digitrail' )
  );
}

/**
 * Ligne « date · durée » d'un épisode.
 *
 * @param WP_Post $episode Épisode.
 */
function dgt_meta_episode( $episode ) {
  printf(
    '<p class="episode__meta donnee"><time datetime="%1$s">%2$s</time> · %3$s</p>',
    esc_attr( get_the_date( 'Y-m-d', $episode ) ),
    esc_html( get_the_date( 'j F Y', $episode ) ),
    esc_html( dgt_duree_lisible( (int) get_field( 'dgt_duree', $episode->ID ) ) )
  );
}

/**
 * Liens « Écouter aussi sur » (réglages du site).
 */
function dgt_plateformes_podcast() {
  $plateformes = array_filter(
    (array) dgt_reglage( 'dgt_plateformes', array() ),
    function ( $plateforme ) {
      return ! empty( $plateforme['nom'] ) && ! empty( $plateforme['url'] );
    }
  );
  if ( ! $plateformes ) {
    return;
  }
  echo '<p class="plateformes">' . esc_html__( 'Écouter aussi sur', 'digitrail' );
  foreach ( $plateformes as $plateforme ) {
    printf( ' <a href="%1$s" rel="noopener">%2$s</a>', esc_url( $plateforme['url'] ), esc_html( $plateforme['nom'] ) );
  }
  echo '</p>';
}
