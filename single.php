<?php
/**
 * Article seul.
 *
 * @package Digitrail
 */

get_header();

while ( have_posts() ) :
  the_post();
  ?>
  <article <?php post_class( 'page-simple' ); ?>>
    <div class="conteneur">
      <header class="page-simple__entete">
        <p class="surtitre"><?php esc_html_e( 'Actualités', 'digitrail' ); ?></p>
        <h1><?php the_title(); ?></h1>
        <p class="page-simple__meta">
          <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
        </p>
      </header>
      <?php if ( has_post_thumbnail() ) : ?>
        <figure class="page-simple__visuel">
          <?php
          the_post_thumbnail(
            'full',
            array(
              'alt'           => '',
              'loading'       => 'eager',
              'fetchpriority' => 'high',
              'sizes'         => '(max-width: 980px) 100vw, 900px',
            )
          );
          ?>
        </figure>
      <?php endif; ?>
      <div class="contenu-texte">
        <?php the_content(); ?>
      </div>
    </div>
  </article>
  <?php
endwhile;

get_template_part( 'parts/bandeau-contact' );

get_footer();
