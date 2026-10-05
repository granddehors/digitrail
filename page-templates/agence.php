<?php
/**
 * Template Name: Page L'agence
 *
 * Présentation, équipe et collectif (partagés avec l'accueil), ce que nous faisons, avis clients.
 * Textes : groupe ACF « Page L'agence ». Équipe : « Réglages du site > Équipe ».
 *
 * @package Digitrail
 */

$dgt_chiffres = get_field( 'chiffres' );
$dgt_photo    = get_field( 'presentation_photo' );
$dgt_legende  = get_field( 'presentation_legende' );
$dgt_metiers  = get_field( 'metiers' );
$dgt_avis     = get_field( 'avis' );

/**
 * Étoiles d'un avis : pleines jusqu'à la note, en trait au-delà.
 *
 * @param int $note Note sur 5.
 */
$dgt_etoiles = function ( $note ) {
  $note = max( 1, min( 5, (int) $note ) );
  /* translators: %d : note sur 5. */
  echo '<p class="avis__note" role="img" aria-label="' . esc_attr( sprintf( __( 'Note : %d sur 5', 'digitrail' ), $note ) ) . '">';
  for ( $i = 1; $i <= 5; $i++ ) {
    echo '<svg viewBox="0 0 24 24"' . ( $i > $note ? ' class="vide"' : '' ) . ' aria-hidden="true" focusable="false"><path d="M12 2.8l2.8 5.9 6.4.8-4.7 4.4 1.2 6.4L12 17.2l-5.7 3.1 1.2-6.4-4.7-4.4 6.4-.8z"/></svg>';
  }
  echo '</p>';
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
    'bouton'   => get_field( 'hero_bouton' ),
    'variante' => 'sous-page',
  )
);
?>

<section class="section">
  <div class="conteneur presentation">
    <div class="presentation__texte apparition">
      <p class="surtitre"><?php echo esc_html( get_field( 'presentation_surtitre' ) ); ?></p>
      <h2><?php echo esc_html( get_field( 'presentation_titre' ) ); ?></h2>
      <?php echo wp_kses_post( get_field( 'presentation_texte' ) ); ?>
      <?php if ( $dgt_chiffres ) : ?>
        <ul class="presentation__chiffres">
          <?php foreach ( $dgt_chiffres as $dgt_chiffre ) : ?>
            <li><b><?php echo esc_html( $dgt_chiffre['valeur'] ); ?></b><span><?php echo esc_html( $dgt_chiffre['libelle'] ); ?></span></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
    <?php if ( $dgt_photo ) : ?>
      <figure class="presentation__photo apparition" data-d="1">
        <?php echo wp_get_attachment_image( $dgt_photo, 'large', false, array( 'loading' => 'lazy', 'sizes' => '(max-width: 980px) 100vw, 600px' ) ); ?>
        <?php if ( $dgt_legende ) : ?>
          <figcaption><?php echo esc_html( $dgt_legende ); ?></figcaption>
        <?php endif; ?>
      </figure>
    <?php else : ?>
      <figure class="photo-a-venir apparition" data-d="1">
        <?php if ( $dgt_legende ) : ?>
          <figcaption><?php echo esc_html( $dgt_legende ); ?></figcaption>
        <?php endif; ?>
      </figure>
    <?php endif; ?>
  </div>
</section>

<section class="section section--bleu" id="equipe" data-theme="sombre">
  <div class="conteneur">
    <?php
    get_template_part(
      'parts/sections/tete',
      null,
      array(
        'surtitre'       => get_field( 'equipe_surtitre' ),
        'titre'          => get_field( 'equipe_titre' ),
        'chapo'          => get_field( 'equipe_chapo' ),
        'chapo_a_droite' => true,
      )
    );
    get_template_part(
      'parts/equipe',
      null,
      array(
        'liens'  => true,
        'classe' => 'equipe--agence',
      )
    );
    ?>
  </div>
</section>

<?php if ( $dgt_metiers ) : ?>
  <section class="section" id="metiers">
    <div class="conteneur">
      <?php
      get_template_part(
        'parts/sections/tete',
        null,
        array(
          'surtitre' => get_field( 'metiers_surtitre' ),
          'titre'    => get_field( 'metiers_titre' ),
          'chapo'    => get_field( 'metiers_chapo' ),
        )
      );
      ?>
      <div class="metiers">
        <?php foreach ( $dgt_metiers as $dgt_rang => $dgt_metier ) : ?>
          <article class="metier apparition"<?php echo $dgt_rang % 3 ? ' data-d="' . esc_attr( $dgt_rang % 3 ) . '"' : ''; ?>>
            <h3><?php echo esc_html( $dgt_metier['titre'] ); ?></h3>
            <p><?php echo esc_html( $dgt_metier['texte'] ); ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
<?php endif; ?>

<?php if ( $dgt_avis ) : ?>
  <section class="section" id="avis" data-theme="sombre">
    <div class="conteneur">
      <div class="section__tete apparition">
        <div>
          <p class="surtitre"><?php echo esc_html( get_field( 'avis_surtitre' ) ); ?></p>
          <h2><?php echo esc_html( get_field( 'avis_titre' ) ); ?></h2>
        </div>
        <a href="<?php echo esc_url( get_post_type_archive_link( 'realisation' ) ); ?>" class="bouton bouton--contour"><?php esc_html_e( 'Voir nos réalisations', 'digitrail' ); ?></a>
      </div>
      <div class="avis">
        <?php foreach ( $dgt_avis as $dgt_rang => $dgt_un_avis ) : ?>
          <figure class="avis__item apparition"<?php echo $dgt_rang % 4 ? ' data-d="' . esc_attr( $dgt_rang % 4 ) . '"' : ''; ?>>
            <?php $dgt_etoiles( $dgt_un_avis['note'] ); ?>
            <blockquote><p><?php echo esc_html( $dgt_un_avis['texte'] ); ?></p></blockquote>
            <figcaption><b><?php echo esc_html( $dgt_un_avis['nom'] ); ?></b><span><?php echo esc_html( $dgt_un_avis['profil'] ); ?></span></figcaption>
          </figure>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
<?php endif; ?>

<?php
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
