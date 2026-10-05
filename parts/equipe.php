<?php
/**
 * Équipe et collectif, partagés entre l'accueil et la page L'agence.
 * Contenu : « Réglages du site > Équipe ».
 *
 * Arguments : liens (bool) affiche Strava, Instagram, Facebook ; classe (classe supplémentaire de la grille).
 *
 * @package Digitrail
 */

$dgt_options     = wp_parse_args(
  $args ?? array(),
  array(
    'liens'  => false,
    'classe' => '',
  )
);
$dgt_membres     = (array) dgt_reglage( 'dgt_membres', array() );
$dgt_competences = dgt_lignes( dgt_reglage( 'dgt_collectif_competences', '' ) );
$dgt_nombre      = dgt_reglage( 'dgt_collectif_nombre', '' );
$dgt_reseaux     = array(
  'strava'    => 'Strava',
  'instagram' => 'Instagram',
  'facebook'  => 'Facebook',
);
?>
<?php if ( $dgt_membres ) : ?>
  <div class="equipe<?php echo $dgt_options['classe'] ? ' ' . esc_attr( $dgt_options['classe'] ) : ''; ?>">
    <?php foreach ( $dgt_membres as $dgt_rang => $dgt_membre ) : ?>
      <div class="membre apparition"<?php echo $dgt_rang ? ' data-d="' . esc_attr( min( $dgt_rang, 3 ) ) . '"' : ''; ?>>
        <div class="membre__photo">
          <?php
          if ( $dgt_membre['photo'] ) {
            echo wp_get_attachment_image(
              $dgt_membre['photo'],
              'dgt-portrait',
              false,
              array(
                'alt'     => $dgt_membre['nom'],
                'loading' => 'lazy',
                'sizes'   => '(max-width: 560px) 100vw, (max-width: 980px) 50vw, 300px',
              )
            );
          }
          ?>
        </div>
        <h3><?php echo esc_html( $dgt_membre['nom'] ); ?></h3>
        <?php if ( $dgt_membre['role'] ) : ?>
          <p class="membre__role"><?php echo esc_html( $dgt_membre['role'] ); ?></p>
        <?php endif; ?>
        <?php if ( $dgt_membre['bio'] ) : ?>
          <p><?php echo esc_html( $dgt_membre['bio'] ); ?></p>
        <?php endif; ?>
        <?php $dgt_etiquettes = dgt_lignes( $dgt_membre['etiquettes'] ); ?>
        <?php if ( $dgt_etiquettes ) : ?>
          <ul class="etiquettes">
            <?php foreach ( $dgt_etiquettes as $dgt_etiquette ) : ?>
              <li class="etiquette"><?php echo esc_html( $dgt_etiquette ); ?></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
        <?php
        $dgt_liens = $dgt_options['liens'] ? array_filter( array_intersect_key( $dgt_membre, $dgt_reseaux ) ) : array();
        if ( $dgt_liens ) :
          ?>
          <?php /* translators: %s : nom de la personne. */ ?>
          <ul class="membre__liens" aria-label="<?php echo esc_attr( sprintf( __( '%s en ligne', 'digitrail' ), $dgt_membre['nom'] ) ); ?>">
            <?php foreach ( $dgt_liens as $dgt_reseau => $dgt_url ) : ?>
              <li><a href="<?php echo esc_url( $dgt_url ); ?>" rel="noopener"><?php echo esc_html( $dgt_reseaux[ $dgt_reseau ] ); ?></a></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php if ( $dgt_nombre || $dgt_competences ) : ?>
  <div class="collectif apparition">
    <p class="collectif__tete">
      <b><?php echo esc_html( $dgt_nombre ); ?></b>
      <span><?php echo esc_html( dgt_reglage( 'dgt_collectif_texte', '' ) ); ?></span>
    </p>
    <?php if ( $dgt_competences ) : ?>
      <ul class="competences">
        <?php foreach ( $dgt_competences as $dgt_competence ) : ?>
          <li class="competence"><?php echo esc_html( $dgt_competence ); ?></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
<?php endif; ?>
