<?php
/**
 * Accueil : entrées par profil (athlète, coach et pro, organisateur, institutionnel).
 * Les deux premiers profils s'affichent en grand, les suivants en bandeau.
 * Sans page de profil, le lien mène à la page contact.
 *
 * @package Digitrail
 */

$dgt_profils = get_field( 'profils' );
?>
<section class="section" id="profils" data-theme="sombre">
  <div class="conteneur">
    <div class="section__tete apparition">
      <div>
        <p class="surtitre"><?php echo esc_html( get_field( 'profils_surtitre' ) ); ?></p>
        <h2><?php echo esc_html( get_field( 'profils_titre' ) ); ?></h2>
      </div>
      <?php if ( get_field( 'profils_chapo' ) ) : ?>
        <p class="chapo"><?php echo esc_html( get_field( 'profils_chapo' ) ); ?></p>
      <?php endif; ?>
    </div>

    <?php if ( $dgt_profils ) : ?>
      <div class="profils">
        <?php
        foreach ( $dgt_profils as $dgt_rang => $dgt_profil ) :
          $dgt_classe = 'profil apparition ' . ( $dgt_rang < 2 ? 'profil--principal' : 'profil--secondaire' );
          $dgt_lien   = $dgt_profil['lien'] ? $dgt_profil['lien'] : dgt_lien_contact();
          ?>
          <a class="<?php echo esc_attr( $dgt_classe ); ?>" href="<?php echo esc_url( $dgt_lien ); ?>"<?php echo $dgt_rang ? ' data-d="' . esc_attr( min( $dgt_rang, 3 ) ) . '"' : ''; ?>>
            <h3><?php echo esc_html( $dgt_profil['titre'] ); ?></h3>
            <p><?php echo esc_html( $dgt_profil['texte'] ); ?></p>
            <?php if ( $dgt_profil['libelle'] ) : ?>
              <span class="lien-carte"><?php echo esc_html( $dgt_profil['libelle'] ); ?> <?php dgt_icone_fleche(); ?></span>
            <?php endif; ?>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
