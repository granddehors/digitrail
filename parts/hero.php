<?php
/**
 * Héro photo sombre, un seul par page.
 * La page qui l'utilise doit déclarer dgt_avec_hero (en-tête transparent).
 *
 * Arguments :
 *   image (id), surtitre, titre (<em> autorisé), chapo, bouton (lien ACF secondaire),
 *   references_titre, references (liste), credit, variante ('' ou 'sous-page'),
 *   actions (false : pas de boutons, ex. sur la page de contact).
 *
 * @package Digitrail
 */

$dgt_hero = wp_parse_args(
  $args ?? array(),
  array(
    'image'            => 0,
    'surtitre'         => '',
    'titre'            => '',
    'chapo'            => '',
    'bouton'           => null,
    'references_titre' => '',
    'references'       => array(),
    'credit'           => '',
    'variante'         => '',
    'actions'          => true,
  )
);
$dgt_classes_hero = 'hero' . ( 'sous-page' === $dgt_hero['variante'] ? ' hero--sous-page' : '' );
?>
<section class="<?php echo esc_attr( $dgt_classes_hero ); ?>" data-theme="sombre">
  <?php if ( $dgt_hero['image'] ) : ?>
    <div class="hero__fond">
      <?php
      // Première image visible : chargée en priorité, jamais en différé.
      echo wp_get_attachment_image(
        $dgt_hero['image'],
        'full',
        false,
        array(
          'alt'           => '',
          'loading'       => 'eager',
          'fetchpriority' => 'high',
          'sizes'         => '100vw',
        )
      );
      ?>
    </div>
  <?php endif; ?>
  <div class="conteneur hero__int">
    <?php if ( $dgt_hero['surtitre'] ) : ?>
      <p class="surtitre"><?php echo esc_html( $dgt_hero['surtitre'] ); ?></p>
    <?php endif; ?>
    <h1><?php echo dgt_titre_em( $dgt_hero['titre'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- filtré par wp_kses. ?></h1>
    <?php if ( $dgt_hero['chapo'] ) : ?>
      <p class="chapo"><?php echo esc_html( $dgt_hero['chapo'] ); ?></p>
    <?php endif; ?>
    <?php if ( $dgt_hero['actions'] ) : ?>
    <div class="hero__actions">
      <a href="<?php echo esc_url( dgt_lien_contact() ); ?>" class="bouton bouton--vert"><?php esc_html_e( 'Parlons de votre projet', 'digitrail' ); ?> <?php dgt_icone_fleche(); ?></a>
      <?php dgt_bouton_acf( $dgt_hero['bouton'], 'bouton bouton--contour' ); ?>
    </div>
    <?php endif; ?>
    <?php if ( $dgt_hero['references'] ) : ?>
      <div class="hero__references">
        <?php if ( $dgt_hero['references_titre'] ) : ?>
          <p class="donnee"><?php echo esc_html( $dgt_hero['references_titre'] ); ?></p>
        <?php endif; ?>
        <ul>
          <?php foreach ( $dgt_hero['references'] as $dgt_reference ) : ?>
            <li><?php echo esc_html( $dgt_reference ); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
  </div>
  <?php if ( $dgt_hero['credit'] ) : ?>
    <p class="hero__credit"><?php echo esc_html( $dgt_hero['credit'] ); ?></p>
  <?php endif; ?>
</section>
