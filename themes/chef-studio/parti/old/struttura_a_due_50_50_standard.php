<div class="struttura-container struttura-2-standard">
    <div class="div-1">
    
        <!--- Se hai selezionato Testo -->
        <?php if( get_sub_field('sinistra-_testo_o_immagine') == 'Testo' ) : ?>  
        <?php the_sub_field('testo_a_sinistra'); ?>
        
        <!--- Se hai selezionato Immagine -->
        <?php elseif( get_sub_field('sinistra-_testo_o_immagine') == 'Immagine' ) : ?>  
                    <img src="<?php echo(get_sub_field('immagine_a_sinistra'))['sizes']['medium_large'] ?>" class="flex" alt="<?php echo get_post_meta( get_sub_field('immagine_a_sinistra')['ID'], '_wp_attachment_image_alt', true ); ?>">
        <?php endif; ?>
        
    </div>		
    
    
    <div class="div-2">
       
       <!--- Se hai selezionato Testo -->
        <?php if( get_sub_field('destra-_testo_o_immagine') == 'Testo' ) : ?>  
        <?php the_sub_field('testo_a_destra'); ?>
        
        <!--- Se hai selezionato Immagine -->
        <?php elseif( get_sub_field('destra-_testo_o_immagine') == 'Immagine' ) : ?>  
                    <img src="<?php echo(get_sub_field('immagine_a_destra'))['sizes']['medium_large'] ?>" class="flex" alt="<?php echo get_post_meta( get_sub_field('immagine_a_destra')['ID'], '_wp_attachment_image_alt', true ); ?>">
        <?php endif; ?>
        
    </div>	
        
    <div class="floatstop"></div> 
    
</div>