<?php
/**
 * Template Name: Page de contact
 *
 * Héro de sous-page, formulaire (traité par inc/contact.php), étapes « Et ensuite ? », coordonnées directes.
 * Textes : groupe ACF « Page de contact ». Coordonnées : « Réglages du site ».
 *
 * @package Digitrail
 */

$dgt_contact = dgt_etat_contact();
$dgt_erreurs = $dgt_contact['erreurs'];
$dgt_valeurs = $dgt_contact['valeurs'];
$dgt_etapes  = get_field( 'suite_etapes' );
$dgt_tel     = (string) dgt_reglage( 'dgt_telephone', '' );
$dgt_adresse = (string) dgt_reglage( 'dgt_adresse', '' );
$dgt_confid  = get_privacy_policy_url();

/**
 * Attributs d'un champ en erreur (aria-invalid, aria-describedby).
 *
 * @param string $champ Nom du champ.
 * @param string $aide  Identifiant du texte d'aide, le cas échéant.
 * @param array  $erreurs Erreurs en cours.
 */
$dgt_attributs_champ = function ( $champ, $aide, $erreurs ) {
  $decrit = array_filter( array( $aide, isset( $erreurs[ $champ ] ) ? 'erreur-' . $champ : '' ) );
  if ( $decrit ) {
    echo ' aria-describedby="' . esc_attr( implode( ' ', $decrit ) ) . '"';
  }
  if ( isset( $erreurs[ $champ ] ) ) {
    echo ' aria-invalid="true"';
  }
};

get_header();

get_template_part(
  'parts/hero',
  null,
  array(
    'image'    => get_field( 'hero_image' ),
    'surtitre' => get_field( 'hero_surtitre' ),
    'titre'    => get_field( 'hero_titre' ),
    'chapo'    => get_field( 'hero_chapo' ),
    'variante' => 'sous-page',
    'actions'  => false,
  )
);
?>
<section class="section" id="formulaire">
  <div class="conteneur contact">

    <div>
      <p class="surtitre"><?php echo esc_html( get_field( 'formulaire_surtitre' ) ); ?></p>
      <h2 class="contact__titre"><?php echo esc_html( get_field( 'formulaire_titre' ) ); ?></h2>

      <?php if ( 'envoye' === $dgt_contact['etat'] ) : ?>

        <div class="message-etat message-etat--succes" id="message-envoye" role="status" tabindex="-1">
          <h3><?php echo esc_html( get_field( 'succes_titre' ) ); ?></h3>
          <p><?php echo esc_html( get_field( 'succes_texte' ) ); ?></p>
        </div>

      <?php else : ?>

        <?php if ( $dgt_erreurs ) : ?>
          <div class="message-etat message-etat--erreur" id="erreurs" role="alert" tabindex="-1">
            <h3><?php echo esc_html( dgt_titre_erreurs_contact( $dgt_erreurs ) ); ?></h3>
            <ul>
              <?php foreach ( $dgt_erreurs as $dgt_champ => $dgt_code ) : ?>
                <li>
                  <?php if ( 'general' === $dgt_champ ) : ?>
                    <?php echo esc_html( dgt_texte_erreur_contact( $dgt_code ) ); ?>
                  <?php else : ?>
                    <a href="#champ-<?php echo esc_attr( $dgt_champ ); ?>"><?php echo esc_html( dgt_texte_erreur_contact( $dgt_code ) ); ?></a>
                  <?php endif; ?>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form class="formulaire" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" novalidate>
          <input type="hidden" name="action" value="dgt_contact">
          <input type="hidden" name="dgt_page" value="<?php echo esc_attr( get_the_ID() ); ?>">
          <input type="hidden" name="dgt_debut" value="<?php echo esc_attr( dgt_horodatage_contact() ); ?>">
          <?php wp_nonce_field( 'dgt_contact', 'dgt_jeton', false ); ?>

          <div class="champ<?php echo isset( $dgt_erreurs['nom'] ) ? ' est-invalide' : ''; ?>">
            <label for="champ-nom"><?php esc_html_e( 'Nom et prénom', 'digitrail' ); ?></label>
            <input id="champ-nom" name="nom" type="text" autocomplete="name" required maxlength="100" value="<?php echo esc_attr( $dgt_valeurs['nom'] ); ?>"<?php $dgt_attributs_champ( 'nom', '', $dgt_erreurs ); ?>>
            <?php if ( isset( $dgt_erreurs['nom'] ) ) : ?>
              <p class="champ__erreur" id="erreur-nom"><?php echo esc_html( dgt_texte_erreur_contact( 'nom' ) ); ?></p>
            <?php endif; ?>
          </div>

          <div class="champ<?php echo isset( $dgt_erreurs['email'] ) ? ' est-invalide' : ''; ?>">
            <label for="champ-email"><?php esc_html_e( 'E-mail', 'digitrail' ); ?></label>
            <input id="champ-email" name="email" type="email" autocomplete="email" required value="<?php echo esc_attr( $dgt_valeurs['email'] ); ?>"<?php $dgt_attributs_champ( 'email', '', $dgt_erreurs ); ?>>
            <?php if ( isset( $dgt_erreurs['email'] ) ) : ?>
              <p class="champ__erreur" id="erreur-email"><?php echo esc_html( dgt_texte_erreur_contact( 'email' ) ); ?></p>
            <?php endif; ?>
          </div>

          <div class="champ<?php echo isset( $dgt_erreurs['telephone'] ) ? ' est-invalide' : ''; ?>">
            <label for="champ-telephone"><?php esc_html_e( 'Téléphone', 'digitrail' ); ?> <span class="champ__facultatif"><?php esc_html_e( '(facultatif)', 'digitrail' ); ?></span></label>
            <input id="champ-telephone" name="telephone" type="tel" autocomplete="tel" maxlength="25" value="<?php echo esc_attr( $dgt_valeurs['telephone'] ); ?>"<?php $dgt_attributs_champ( 'telephone', 'aide-telephone', $dgt_erreurs ); ?>>
            <p class="champ__aide" id="aide-telephone"><?php esc_html_e( 'Si vous préférez être rappelé.', 'digitrail' ); ?></p>
            <?php if ( isset( $dgt_erreurs['telephone'] ) ) : ?>
              <p class="champ__erreur" id="erreur-telephone"><?php echo esc_html( dgt_texte_erreur_contact( 'telephone' ) ); ?></p>
            <?php endif; ?>
          </div>

          <fieldset class="champ champ--large">
            <legend><?php esc_html_e( 'Vous êtes', 'digitrail' ); ?></legend>
            <div class="choix">
              <?php foreach ( dgt_profils_contact() as $dgt_valeur => $dgt_libelle ) : ?>
                <input type="radio" id="profil-<?php echo esc_attr( $dgt_valeur ); ?>" name="profil" value="<?php echo esc_attr( $dgt_valeur ); ?>"<?php checked( $dgt_valeurs['profil'], $dgt_valeur ); ?>>
                <label for="profil-<?php echo esc_attr( $dgt_valeur ); ?>"><?php echo esc_html( $dgt_libelle ); ?></label>
              <?php endforeach; ?>
            </div>
          </fieldset>

          <div class="champ champ--large<?php echo isset( $dgt_erreurs['message'] ) ? ' est-invalide' : ''; ?>">
            <label for="champ-message"><?php esc_html_e( 'Votre projet', 'digitrail' ); ?></label>
            <textarea id="champ-message" name="message" required maxlength="5000"<?php $dgt_attributs_champ( 'message', 'aide-message', $dgt_erreurs ); ?>><?php echo esc_textarea( $dgt_valeurs['message'] ); ?></textarea>
            <p class="champ__aide" id="aide-message"><?php echo esc_html( get_field( 'formulaire_aide' ) ); ?></p>
            <?php if ( isset( $dgt_erreurs['message'] ) ) : ?>
              <p class="champ__erreur" id="erreur-message"><?php echo esc_html( dgt_texte_erreur_contact( 'message' ) ); ?></p>
            <?php endif; ?>
          </div>

          <div class="champ champ--piege" aria-hidden="true">
            <label for="champ-site"><?php esc_html_e( 'Ne pas remplir ce champ', 'digitrail' ); ?></label>
            <input id="champ-site" name="site" type="text" tabindex="-1" autocomplete="off">
          </div>

          <div class="formulaire__pied">
            <p class="formulaire__mention">
              <?php echo esc_html( get_field( 'formulaire_mention' ) ); ?>
              <?php if ( $dgt_confid ) : ?>
                <a href="<?php echo esc_url( $dgt_confid ); ?>"><?php esc_html_e( 'Politique de confidentialité', 'digitrail' ); ?></a>
              <?php endif; ?>
            </p>
            <button type="submit" class="bouton bouton--vert"><?php esc_html_e( 'Envoyer le message', 'digitrail' ); ?> <?php dgt_icone_fleche(); ?></button>
          </div>
        </form>

      <?php endif; ?>
    </div>

    <aside class="suite" aria-labelledby="titre-suite">
      <h2 id="titre-suite"><?php echo esc_html( get_field( 'suite_titre' ) ); ?></h2>
      <?php if ( $dgt_etapes ) : ?>
        <ol class="suite__etapes">
          <?php foreach ( $dgt_etapes as $dgt_rang => $dgt_etape ) : ?>
            <li class="suite__etape">
              <span class="suite__numero" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $dgt_rang + 1 ) ); ?></span>
              <div>
                <h3><?php echo esc_html( $dgt_etape['titre'] ); ?></h3>
                <p><?php echo esc_html( $dgt_etape['texte'] ); ?></p>
              </div>
            </li>
          <?php endforeach; ?>
        </ol>
      <?php endif; ?>

      <div class="suite__lieu" data-theme="sombre">
        <p class="donnee"><?php esc_html_e( 'Nous joindre directement', 'digitrail' ); ?></p>
        <ul class="coordonnees">
          <li>
            <span class="coordonnees__libelle"><?php esc_html_e( 'E-mail', 'digitrail' ); ?></span>
            <a href="<?php echo esc_url( 'mailto:' . dgt_email_public() ); ?>"><?php echo esc_html( dgt_email_public() ); ?></a>
          </li>
          <?php if ( $dgt_tel ) : ?>
            <li>
              <span class="coordonnees__libelle"><?php esc_html_e( 'Téléphone', 'digitrail' ); ?></span>
              <a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $dgt_tel ) ); ?>"><?php echo esc_html( $dgt_tel ); ?></a>
            </li>
          <?php endif; ?>
          <?php if ( $dgt_adresse ) : ?>
            <li>
              <span class="coordonnees__libelle"><?php esc_html_e( 'Adresse', 'digitrail' ); ?></span>
              <span><?php echo wp_kses( $dgt_adresse, array( 'br' => array() ) ); ?></span>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </aside>

  </div>
</section>
<?php
get_footer();
