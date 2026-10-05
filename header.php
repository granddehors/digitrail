<?php
/**
 * Début du document : <head>, en-tête du site, ouverture du contenu principal.
 *
 * @package Digitrail
 */

?>
<!doctype html>
<html <?php language_attributes(); ?> class="no-js">
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="lien-evitement" href="#contenu"><?php esc_html_e( 'Aller au contenu', 'digitrail' ); ?></a>

<?php get_template_part( 'parts/entete' ); ?>

<main id="contenu" class="site-principal" tabindex="-1">
