<?php
/**
 * Section « Questions fréquentes » : questions dépliables (details/summary)
 * et données structurées FAQPage pour les moteurs de recherche.
 *
 * @package Digitrail
 */

$dgt_s         = $args;
$dgt_questions = array_filter(
  (array) $dgt_s['questions'],
  function ( $question ) {
    return ! empty( $question['question'] ) && ! empty( $question['reponse'] );
  }
);
if ( ! $dgt_questions ) {
  return;
}
?>
<section <?php echo dgt_attributs_section( $dgt_s['fond'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé dans la fonction. ?>>
  <div class="conteneur">
    <?php get_template_part( 'parts/sections/tete', null, $dgt_s ); ?>
    <div class="faq">
      <?php foreach ( $dgt_questions as $dgt_question ) : ?>
        <details class="question apparition">
          <summary><?php echo esc_html( $dgt_question['question'] ); ?></summary>
          <p><?php echo esc_html( $dgt_question['reponse'] ); ?></p>
        </details>
      <?php endforeach; ?>
    </div>
  </div>
  <?php
  $dgt_schema = array(
    '@context'   => 'https://schema.org',
    '@type'      => 'FAQPage',
    'mainEntity' => array_values(
      array_map(
        function ( $question ) {
          return array(
            '@type'          => 'Question',
            'name'           => $question['question'],
            'acceptedAnswer' => array(
              '@type' => 'Answer',
              'text'  => $question['reponse'],
            ),
          );
        },
        $dgt_questions
      )
    ),
  );
  ?>
  <script type="application/ld+json"><?php echo wp_json_encode( $dgt_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG ); ?></script>
</section>
