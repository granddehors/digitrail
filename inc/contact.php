<?php
/**
 * Formulaire de contact : traitement, anti-spam, envoi, états affichés par le gabarit.
 *
 * Aucune extension : admin-post.php reçoit le formulaire, wp_mail() envoie.
 * Anti-spam sans service externe : jeton WordPress, champ piège, délai minimal, limite par connexion.
 * Les saisies en erreur sont gardées 10 minutes côté serveur ; l'adresse ne porte qu'un identifiant.
 *
 * @package Digitrail
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/** Délai minimal de remplissage, en secondes (en dessous : robot). */
const DGT_CONTACT_DELAI_MIN = 3;

/** Nombre d'envois autorisés par connexion et par heure. */
const DGT_CONTACT_LIMITE = 5;

/**
 * Profils proposés dans le formulaire.
 *
 * @return array Valeur => libellé.
 */
function dgt_profils_contact() {
  return array(
    'athlete'      => __( 'Athlète', 'digitrail' ),
    'coach'        => __( "Coach ou pro de l'outdoor", 'digitrail' ),
    'organisateur' => __( 'Organisateur de course', 'digitrail' ),
    'autre'        => __( 'Autre', 'digitrail' ),
  );
}

/**
 * Adresse e-mail affichée publiquement.
 *
 * @return string
 */
function dgt_email_public() {
  return (string) dgt_reglage( 'dgt_email_public', 'contact@digitrail.run' );
}

/**
 * Horodatage signé posé dans le formulaire, pour mesurer le temps de remplissage.
 *
 * @return string
 */
function dgt_horodatage_contact() {
  $maintenant = (string) time();
  return $maintenant . '|' . wp_hash( 'dgt_contact' . $maintenant );
}

/**
 * Traite l'envoi du formulaire de contact.
 */
function dgt_traiter_contact() {
  // phpcs:disable WordPress.Security.NonceVerification.Missing -- nonce vérifié juste en dessous.
  $page_id = isset( $_POST['dgt_page'] ) ? absint( $_POST['dgt_page'] ) : 0;
  $retour  = $page_id ? get_permalink( $page_id ) : home_url( '/' );
  if ( ! $retour ) {
    $retour = home_url( '/' );
  }

  $saisie = array(
    'nom'       => isset( $_POST['nom'] ) ? sanitize_text_field( wp_unslash( $_POST['nom'] ) ) : '',
    // Texte brut nettoyé : une adresse incomplète est réaffichée telle quelle pour être corrigée.
    'email'     => isset( $_POST['email'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['email'] ) ) ) : '',
    'telephone' => isset( $_POST['telephone'] ) ? sanitize_text_field( wp_unslash( $_POST['telephone'] ) ) : '',
    'profil'    => isset( $_POST['profil'] ) ? sanitize_key( wp_unslash( $_POST['profil'] ) ) : '',
    'message'   => isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '',
  );
  $piege       = isset( $_POST['site'] ) ? sanitize_text_field( wp_unslash( $_POST['site'] ) ) : '';
  $horodatage  = isset( $_POST['dgt_debut'] ) ? sanitize_text_field( wp_unslash( $_POST['dgt_debut'] ) ) : '';
  $jeton_nonce = isset( $_POST['dgt_jeton'] ) ? sanitize_text_field( wp_unslash( $_POST['dgt_jeton'] ) ) : '';
  // phpcs:enable

  $succes = add_query_arg( 'contact', 'envoye', $retour ) . '#message-envoye';

  // Jeton WordPress expiré ou absent : on garde la saisie et on demande de renvoyer.
  if ( ! wp_verify_nonce( $jeton_nonce, 'dgt_contact' ) ) {
    dgt_rediriger_erreur_contact( $retour, array( 'general' => 'jeton' ), $saisie );
  }

  // Robot : champ piège rempli, ou formulaire rempli trop vite. On simule un succès, sans rien envoyer.
  list( $debut, $signature ) = array_pad( explode( '|', $horodatage ), 2, '' );
  $horodatage_valide         = $debut && hash_equals( wp_hash( 'dgt_contact' . $debut ), $signature );
  if ( '' !== $piege || ! $horodatage_valide || ( time() - (int) $debut ) < DGT_CONTACT_DELAI_MIN ) {
    wp_safe_redirect( $succes );
    exit;
  }

  // Limite d'envois par connexion (adresse IP hachée, jamais stockée en clair).
  $ip        = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
  $cle_ip    = 'dgt_contact_ip_' . substr( wp_hash( $ip ), 0, 20 );
  $nb_envois = (int) get_transient( $cle_ip );
  if ( $nb_envois >= DGT_CONTACT_LIMITE ) {
    dgt_rediriger_erreur_contact( $retour, array( 'general' => 'limite' ), $saisie );
  }

  // Validation des champs.
  $erreurs = array();
  if ( '' === $saisie['nom'] || mb_strlen( $saisie['nom'] ) > 100 ) {
    $erreurs['nom'] = 'nom';
  }
  if ( is_email( $saisie['email'] ) ) {
    $saisie['email'] = sanitize_email( $saisie['email'] );
  } else {
    $erreurs['email'] = 'email';
  }
  if ( '' !== $saisie['telephone'] && ! preg_match( '/^[0-9+().\s-]{6,25}$/', $saisie['telephone'] ) ) {
    $erreurs['telephone'] = 'telephone';
  }
  if ( ! array_key_exists( $saisie['profil'], dgt_profils_contact() ) ) {
    $saisie['profil'] = '';
  }
  if ( mb_strlen( $saisie['message'] ) < 10 || mb_strlen( $saisie['message'] ) > 5000 ) {
    $erreurs['message'] = 'message';
  }
  if ( $erreurs ) {
    dgt_rediriger_erreur_contact( $retour, $erreurs, $saisie );
  }

  // Envoi.
  $profils      = dgt_profils_contact();
  $profil       = $saisie['profil'] ? $profils[ $saisie['profil'] ] : __( 'Non précisé', 'digitrail' );
  $destinataire = dgt_reglage( 'dgt_email_reception', get_option( 'admin_email' ) );
  /* translators: 1 : nom, 2 : profil. */
  $sujet = sprintf( __( '[Digitrail] Message de %1$s (%2$s)', 'digitrail' ), $saisie['nom'], $profil );
  $corps = implode(
    "\n",
    array(
      __( 'Nouveau message depuis le formulaire de contact du site.', 'digitrail' ),
      '',
      __( 'Nom :', 'digitrail' ) . ' ' . $saisie['nom'],
      __( 'E-mail :', 'digitrail' ) . ' ' . $saisie['email'],
      __( 'Téléphone :', 'digitrail' ) . ' ' . ( $saisie['telephone'] ? $saisie['telephone'] : __( 'non renseigné', 'digitrail' ) ),
      __( 'Profil :', 'digitrail' ) . ' ' . $profil,
      '',
      __( 'Message :', 'digitrail' ),
      $saisie['message'],
      '',
      '---',
      __( 'Répondez directement à cet e-mail pour écrire à la personne.', 'digitrail' ),
    )
  );
  $entetes = array(
    'Content-Type: text/plain; charset=UTF-8',
    'Reply-To: ' . str_replace( array( '"', "\r", "\n" ), '', $saisie['nom'] ) . ' <' . $saisie['email'] . '>',
  );

  if ( ! wp_mail( $destinataire, $sujet, $corps, $entetes ) ) {
    dgt_rediriger_erreur_contact( $retour, array( 'general' => 'envoi' ), $saisie );
  }

  set_transient( $cle_ip, $nb_envois + 1, HOUR_IN_SECONDS );
  wp_safe_redirect( $succes );
  exit;
}
add_action( 'admin_post_nopriv_dgt_contact', 'dgt_traiter_contact' );
add_action( 'admin_post_dgt_contact', 'dgt_traiter_contact' );

/**
 * Garde la saisie et les erreurs 10 minutes, puis renvoie vers le formulaire.
 *
 * @param string $retour  Adresse de la page de contact.
 * @param array  $erreurs Codes d'erreur par champ (ou 'general').
 * @param array  $saisie  Valeurs saisies.
 */
function dgt_rediriger_erreur_contact( $retour, $erreurs, $saisie ) {
  $cle = wp_generate_password( 20, false );
  set_transient(
    'dgt_contact_saisie_' . $cle,
    array(
      'erreurs' => $erreurs,
      'valeurs' => $saisie,
    ),
    10 * MINUTE_IN_SECONDS
  );
  wp_safe_redirect(
    add_query_arg(
      array(
        'contact' => 'erreur',
        'saisie'  => $cle,
      ),
      $retour
    ) . '#erreurs'
  );
  exit;
}

/**
 * État du formulaire pour l'affichage : envoyé, erreurs, valeurs à réafficher.
 *
 * @return array { etat: string, erreurs: array, valeurs: array }
 */
function dgt_etat_contact() {
  $etat = array(
    'etat'    => '',
    'erreurs' => array(),
    'valeurs' => array_fill_keys( array( 'nom', 'email', 'telephone', 'profil', 'message' ), '' ),
  );
  // phpcs:disable WordPress.Security.NonceVerification.Recommended -- simple lecture d'état, aucune action.
  $statut = isset( $_GET['contact'] ) ? sanitize_key( wp_unslash( $_GET['contact'] ) ) : '';
  if ( 'envoye' === $statut ) {
    $etat['etat'] = 'envoye';
  } elseif ( 'erreur' === $statut && isset( $_GET['saisie'] ) ) {
    $donnees = get_transient( 'dgt_contact_saisie_' . sanitize_key( wp_unslash( $_GET['saisie'] ) ) );
    if ( is_array( $donnees ) ) {
      $etat['etat']    = 'erreur';
      $etat['erreurs'] = $donnees['erreurs'];
      $etat['valeurs'] = array_merge( $etat['valeurs'], $donnees['valeurs'] );
    }
  }
  // phpcs:enable
  return $etat;
}

/**
 * Textes des erreurs.
 *
 * @param string $code Code d'erreur.
 * @return string
 */
function dgt_texte_erreur_contact( $code ) {
  $textes = array(
    'nom'       => __( 'Indiquez votre nom.', 'digitrail' ),
    'email'     => __( 'Indiquez une adresse e-mail valide.', 'digitrail' ),
    'telephone' => __( 'Vérifiez le numéro de téléphone.', 'digitrail' ),
    'message'   => __( 'Écrivez quelques mots sur votre projet.', 'digitrail' ),
    'jeton'     => __( 'La page est restée ouverte longtemps. Votre message est toujours là : renvoyez-le.', 'digitrail' ),
    'limite'    => __( 'Plusieurs messages sont déjà partis depuis votre connexion. Réessayez dans une heure, ou écrivez-nous directement.', 'digitrail' ),
    /* translators: %s : adresse e-mail de l'agence. */
    'envoi'     => sprintf( __( "Le message n'a pas pu partir. Écrivez-nous directement à %s.", 'digitrail' ), dgt_email_public() ),
  );
  return $textes[ $code ] ?? '';
}

/**
 * Titre du résumé d'erreurs (nombres en lettres, charte éditoriale).
 *
 * @param array $erreurs Erreurs par champ.
 * @return string
 */
function dgt_titre_erreurs_contact( $erreurs ) {
  if ( isset( $erreurs['general'] ) ) {
    return __( 'Le message n\'est pas parti.', 'digitrail' );
  }
  $nombres = array( 1 => 'Un', 2 => 'Deux', 3 => 'Trois', 4 => 'Quatre' );
  $nombre  = count( $erreurs );
  return 1 === $nombre
    ? __( 'Un champ est à compléter.', 'digitrail' )
    /* translators: %s : nombre en lettres. */
    : sprintf( __( '%s champs sont à compléter.', 'digitrail' ), $nombres[ $nombre ] ?? (string) $nombre );
}
