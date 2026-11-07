<div class="struttura-container struttura-2-imgright">
    <div class="div-1">
        <img src="<?php echo(get_sub_field('immagine_a_sinistra'))['sizes']['medium_large'] ?>" class="flex" alt="<?php echo get_post_meta( get_sub_field('immagine_a_sinistra')['ID'], '_wp_attachment_image_alt', true ); ?>">
    </div>		
    <div class="div-2">
        <img src="<?php echo(get_sub_field('immagine_a_destra'))['sizes']['medium_large'] ?>" class="flex" alt="<?php echo get_post_meta( get_sub_field('immagine_a_destra')['ID'], '_wp_attachment_image_alt', true ); ?>">
    </div>	
    <div class="floatstop"></div> 
</div>