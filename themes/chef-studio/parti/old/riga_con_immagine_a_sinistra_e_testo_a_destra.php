<div class="struttura-container struttura-2-invertito">
    <div class="div-1">
        <?php the_sub_field('testo_a_destra'); ?>
    </div>		
    

 
 <!--- Se hai selezionato Galleria -->
<?php if( get_sub_field('immagine_singola_o_galleria') == 'Galleria' ) : ?>  
    
<!-- includi la slide presente nel file collegato -->
<?php include 'riga_con_slider_in_layout_a_due.php';?>



<!--- Altrimenti se hai selezionato Galleria con Lightbox -->
<?php elseif( get_sub_field('immagine_singola_o_galleria') == 'Galleria con Lightbox' ) : ?>

<!-- includi la slide presente nel file collegato -->
<?php include 'riga_con_slider_con_lightbox_in_sistema_a_due.php';?>



<!--- Altrimenti se hai selezionato Immagine singola -->
<?php elseif( get_sub_field('immagine_singola_o_galleria') == 'Immagine singola' ) : ?>
    
     <div class="div-2">
        <img src="<?php echo(get_sub_field('immagine_a_sinistra'))['sizes']['medium_large'] ?>" class="flex" alt="<?php echo get_post_meta( get_sub_field('immagine_a_sinistra')['ID'], '_wp_attachment_image_alt', true ); ?>">
    </div>	
    
<!--- Altrimenti -->
<?php else : ?>
    C'è un problema con l'immagine
    
    
<?php endif; ?>
    
    
<div class="floatstop"></div> 
    
   </div>