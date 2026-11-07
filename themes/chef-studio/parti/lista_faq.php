<div class="contenitore">
<div class="contenuto">

<div class="testo-container">
<div class="faq-content">
	
      <h2><?php the_sub_field('titolo'); ?></h2>
		
		<?php
$faq_schema_items = [];

// check if the repeater field has rows of data
if( have_rows('faq') ): ?>

  <?php  while ( have_rows('faq') ) : the_row(); ?>
    
    <?php
    $testo_domanda = get_sub_field('testo_domanda');
	$testo_risposta = get_sub_field('testo_risposta');
	$row_index = get_row_index();

    $faq_schema_items[] = [
      'question' => $testo_domanda,
      'answer'   => $testo_risposta,
    ];
    ?>
    
    <article class="faq-accordion">

          <input type="checkbox" class="tgg-title" id="tgg-title-<?php echo $row_index; ?>">

          <div class="faq-accordion-title">
            <label for="tgg-title-<?php echo $row_index; ?>">
              <h3><?php echo $testo_domanda; ?></h3>
              <span class="arrow-icon">
                <img src="<?php echo get_template_directory_uri() ?>/img/arrow-down-primary-color.svg" alt="arrow">
              </span>
            </label>
          </div>

			<div class="faq-accordion-content">
            	<?php echo $testo_risposta; ?>
			</div>

		
        
    </article>
    
    <?php endwhile; ?>
    
</div> <!-- faq content -->
</div><!--/testo-container -->	

<?php else :
    // no rows found
endif;

if (!empty($faq_schema_items) && function_exists('chefstudio_schema_collect_faq_items')) {
    chefstudio_schema_collect_faq_items($faq_schema_items, 'acf-lista-faq');
}
?>

</div> <!--- Contenuto -->
</div> <!--- Contenitore -->
