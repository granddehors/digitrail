<?php
/**
 * Accueil : les trois derniers articles.
 *
 * @package Digitrail
 */

$dgt_actus = new WP_Query(
  array(
    'post_type'           => 'post',
    'posts_per_page'      => 3,
    'ignore_sticky_posts' => true,
    'no_found_rows'       => true,
  )
);
if ( ! $dgt_actus->have_posts() ) {
  return;
}
$dgt_page_articles = (int) get_option( 'page_for_posts' );
?>
<section class="section section--claire" id="actus">
  <div class="conteneur">
    <div class="section__tete apparition">
      <div>
        <p class="surtitre"><?php echo esc_html( get_field( 'actus_surtitre' ) ); ?></p>
        <h2><?php echo esc_html( get_field( 'actus_titre' ) ); ?></h2>
      </div>
      <?php if ( $dgt_page_articles ) : ?>
        <a href="<?php echo esc_url( get_permalink( $dgt_page_articles ) ); ?>" class="bouton bouton--contour"><?php esc_html_e( 'Toutes les actualités', 'digitrail' ); ?></a>
      <?php endif; ?>
    </div>
    <div class="actus">
      <?php
      $dgt_rang = 0;
      while ( $dgt_actus->have_posts() ) :
        $dgt_actus->the_post();
        ?>
        <article class="actu apparition"<?php echo $dgt_rang ? ' data-d="' . esc_attr( $dgt_rang ) . '"' : ''; ?>>
          <?php
          if ( has_post_thumbnail() ) {
            the_post_thumbnail(
              'dgt-vignette',
              array(
                'class'   => 'actu__image',
                'alt'     => '',
                'loading' => 'lazy',
                'sizes'   => '(max-width: 640px) 100vw, (max-width: 1100px) 50vw, 420px',
              )
            );
          }
          ?>
          <div class="actu__corps">
            <time class="donnee" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'F Y' ) ); ?></time>
            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            <span class="lien-carte" aria-hidden="true"><?php esc_html_e( 'Lire', 'digitrail' ); ?></span>
          </div>
        </article>
        <?php
        ++$dgt_rang;
      endwhile;
      wp_reset_postdata();
      ?>
    </div>
  </div>
</section>
