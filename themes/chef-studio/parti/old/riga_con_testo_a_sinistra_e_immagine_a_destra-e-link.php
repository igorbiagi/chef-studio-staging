<?php
$term_id = get_sub_field('link_a_una_categoria');
    if( $term_id):
        $term_url = get_category_link( $term_id ); ?>
   <?php endif; ?>   
   
<?php 
$term_id2 = get_sub_field('link_a_una_pagina');
    if( $term_id2):
        $term_url2 = get_category_link( $term_id2 ); ?>
   <?php endif; ?>   


<div class="struttura-container struttura-2-invertito">
    <div class="div-1">
        <img src="<?php echo(get_sub_field('immagine_a_destra'))['sizes']['medium_large'] ?>" class="flex" alt="<?php echo get_post_meta( get_sub_field('immagine_a_destra')['ID'], '_wp_attachment_image_alt', true ); ?>">
    </div>		
    
    
    
    
<!--- Se hai selezionato Galleria -->
<?php if( get_sub_field('immagine_singola_o_galleria') == 'Galleria' ) : ?>  
    
<!-- includi la slide presente nel file collegato -->
<?php include 'riga_con_slider_in_layout_a_due.php';?>



<!--- Altrimenti se hai selezionato Immagine singola -->
<?php elseif( get_sub_field('immagine_singola_o_galleria') == 'Galleria con Lightbox' ) : ?>

<!-- includi la slide presente nel file collegato -->
<?php include 'riga_con_slider_con_lightbox_in_sistema_a_due.php';?>



<!--- Altrimenti se hai selezionato Immagine singola -->
<?php elseif( get_sub_field('immagine_singola_o_galleria') == 'Immagine singola' ) : ?>
    
    <div class="div-2">
        
		<?php the_sub_field('testo_a_sinistra'); ?>
   				<a href="<?php if (isset($term_url)) { echo $term_url;} ?><?php if (isset($term_url2)) { echo $term_url2;} ?>">
			        <button class="btn btn-primary trans-color float-right margin-bot-15px">
     		           <?php echo get_sub_field('titolo_link'); ?>
     		       </button>
		         </a>
    </div>	
    
<!--- Altrimenti -->
<?php else : ?>
    C'è un problema con l'immagine
    
    
<?php endif; ?>
    
    
<div class="floatstop"></div> 
    
   </div>