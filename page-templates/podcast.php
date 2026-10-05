<?php
/**
 * Template Name: Page podcast
 *
 * La Skyline (épisode à la une, grille, épisodes suivants dépliables) et l'interview digitale.
 * Épisodes : type « podcast » synchronisé avec Ausha (inc/podcast.php). Textes : groupe ACF « Page podcast ».
 *
 * @package Digitrail
 */

$dgt_skyline    = dgt_episodes( 'skyline' );
$dgt_interviews = dgt_episodes( 'interview' );
$dgt_une        = array_shift( $dgt_skyline );
$dgt_visibles   = 6;

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
<section class="section" id="skyline">
  <div class="conteneur">
    <div class="section__tete apparition">
      <div>
        <p class="surtitre"><?php echo esc_html( get_field( 'skyline_surtitre' ) ); ?></p>
        <h2><?php echo esc_html( get_field( 'skyline_titre' ) ); ?></h2>
        <?php if ( get_field( 'skyline_chapo' ) ) : ?>
          <p class="chapo"><?php echo esc_html( get_field( 'skyline_chapo' ) ); ?></p>
        <?php endif; ?>
        <?php dgt_plateformes_podcast(); ?>
      </div>
    </div>

    <?php if ( $dgt_une ) : ?>
      <article class="episode-une apparition">
        <?php echo get_the_post_thumbnail( $dgt_une, 'full', array( 'class' => 'episode-une__image', 'alt' => '', 'loading' => 'lazy', 'sizes' => '(max-width: 860px) 100vw, 400px' ) ); ?>
        <div class="episode-une__corps">
          <p class="etiquette-episode"><?php esc_html_e( 'Dernier épisode', 'digitrail' ); ?></p>
          <?php dgt_meta_episode( $dgt_une ); ?>
          <h3><a href="<?php echo esc_url( get_permalink( $dgt_une ) ); ?>"><?php echo esc_html( get_the_title( $dgt_une ) ); ?></a></h3>
          <p class="episode-une__resume"><?php echo esc_html( get_the_excerpt( $dgt_une ) ); ?></p>
          <div class="episode__lecteur"><?php dgt_bouton_ecoute( $dgt_une ); ?></div>
        </div>
      </article>
    <?php endif; ?>

    <?php if ( $dgt_skyline ) : ?>
      <div class="episodes" id="episodes-skyline">
        <?php foreach ( $dgt_skyline as $dgt_rang => $dgt_episode ) : ?>
          <article class="episode<?php echo $dgt_rang < $dgt_visibles ? ' apparition' : ' episode--suivant'; ?>"<?php echo $dgt_rang % 3 ? ' data-d="' . esc_attr( $dgt_rang % 3 ) . '"' : ''; ?>>
            <?php echo get_the_post_thumbnail( $dgt_episode, 'full', array( 'class' => 'episode__image', 'alt' => '', 'loading' => 'lazy', 'sizes' => '(max-width: 560px) 100vw, 300px' ) ); ?>
            <div class="episode__corps">
              <?php if ( get_field( 'dgt_hors_serie', $dgt_episode->ID ) ) : ?>
                <p class="etiquette-episode"><?php esc_html_e( 'Hors-série', 'digitrail' ); ?></p>
              <?php endif; ?>
              <?php dgt_meta_episode( $dgt_episode ); ?>
              <h3><a href="<?php echo esc_url( get_permalink( $dgt_episode ) ); ?>"><?php echo esc_html( get_the_title( $dgt_episode ) ); ?></a></h3>
              <div class="episode__lecteur"><?php dgt_bouton_ecoute( $dgt_episode ); ?></div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
      <?php if ( count( $dgt_skyline ) > $dgt_visibles ) : ?>
        <div class="episodes__suite">
          <button type="button" class="bouton bouton--contour" data-deplier="episodes-skyline" aria-controls="episodes-skyline" aria-expanded="false">
            <?php
            /* translators: %d : nombre d'épisodes masqués. */
            echo esc_html( sprintf( __( 'Voir les %d autres épisodes', 'digitrail' ), count( $dgt_skyline ) - $dgt_visibles ) );
            ?>
          </button>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</section>

<?php if ( $dgt_interviews ) : ?>
  <?php
  $dgt_moyenne = array_sum(
    array_map(
      function ( $episode ) {
        return (int) get_field( 'dgt_duree', $episode->ID );
      },
      $dgt_interviews
    )
  ) / count( $dgt_interviews );
  ?>
  <section class="section section--bleu" id="interviews" data-theme="sombre">
    <div class="conteneur">
      <div class="section__tete apparition">
        <div>
          <p class="surtitre"><?php echo esc_html( get_field( 'interviews_surtitre' ) ); ?></p>
          <h2><?php echo esc_html( get_field( 'interviews_titre' ) ); ?></h2>
          <?php if ( get_field( 'interviews_chapo' ) ) : ?>
            <p class="chapo"><?php echo esc_html( get_field( 'interviews_chapo' ) ); ?></p>
          <?php endif; ?>
          <p class="interviews__bilan donnee">
            <?php
            /* translators: 1 : nombre d'interviews, 2 : durée moyenne. */
            echo esc_html( sprintf( __( '%1$d interviews · %2$s en moyenne', 'digitrail' ), count( $dgt_interviews ), dgt_duree_lisible( (int) $dgt_moyenne ) ) );
            ?>
          </p>
        </div>
      </div>
      <div class="interviews">
        <?php foreach ( $dgt_interviews as $dgt_rang => $dgt_episode ) : ?>
          <?php $dgt_invite = get_field( 'dgt_invite', $dgt_episode->ID ); ?>
          <article class="interview apparition"<?php echo $dgt_rang % 2 ? ' data-d="1"' : ''; ?>>
            <?php echo get_the_post_thumbnail( $dgt_episode, 'thumbnail', array( 'class' => 'interview__image', 'alt' => '', 'loading' => 'lazy' ) ); ?>
            <div class="interview__corps">
              <?php dgt_meta_episode( $dgt_episode ); ?>
              <h3><a href="<?php echo esc_url( get_permalink( $dgt_episode ) ); ?>"><?php echo esc_html( $dgt_invite ? $dgt_invite : get_the_title( $dgt_episode ) ); ?></a></h3>
              <div class="episode__lecteur"><?php dgt_bouton_ecoute( $dgt_episode ); ?></div>
            </div>
          </article>
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
