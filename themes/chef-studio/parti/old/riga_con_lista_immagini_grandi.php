<?php

// check if the repeater field has rows of data
if( have_rows('ripetitore_immagini_grandi') ): ?>
<ul class="struttura-2-percentuale">

  <?php  while ( have_rows('ripetitore_immagini_grandi') ) : the_row(); ?>
    
    <?php
    $immagine = get_sub_field('immagine');
    ?>
    
    <li>
            <div class="contenitore-marchio">
                <img class="flex" src="<?php echo $immagine['sizes']['loghi-piccoli']; ?>" alt="<?php echo $immagine['alt']; ?>" />
            </div>
    </li>
    
    <?php endwhile; ?>

</ul>

<?php else :
    // no rows found
endif;
?>
