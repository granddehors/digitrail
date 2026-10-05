<?php
/**
 * Carte d'article dans une liste. Toute la carte est cliquable par son titre.
 *
 * Arguments : premier (bool) pour la première carte visible : image chargée en priorité.
 *
 * @package Digitrail
 */

$dgt_premier = ! empty( $args['premier'] );
?>
<article <?php post_class( 'carte-article' ); ?>>
  <?php
  if ( has_post_thumbnail() ) {
    the_post_thumbnail(
      'dgt-vignette',
      array(
        'class'         => 'carte-article__image',
        'alt'           => '',
        'loading'       => $dgt_premier ? 'eager' : 'lazy',
        'fetchpriority' => $dgt_premier ? 'high' : 'auto',
        'sizes'         => '(max-width: 640px) 100vw, (max-width: 1100px) 50vw, 420px',
      )
    );
  }
  ?>
  <div class="carte-article__corps">
    <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    <?php if ( has_excerpt() || get_the_content() ) : ?>
      <p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 25 ) ); ?></p>
    <?php endif; ?>
  </div>
</article>
