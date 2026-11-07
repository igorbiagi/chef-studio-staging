<div class="struttura-container struttura-2-imgright">

        <div class="div-1">
            <h2 class="<?php if( get_sub_field('classe_titolo_sx') ): ?><?php $ciao = the_sub_field('classe_titolo_sx'); echo $ciao; ?><?php endif; ?>"><?php echo get_sub_field('titolo_sx') ?></h2>
            <div><?php echo get_sub_field('paragrafo_sx') ?></div>
            <?php
            // check if the repeater field has rows of data
            if( have_rows('ripetitore_sx') ): ?>
            
            <ul>
            
            <?php
            // loop through the rows of data
            while ( have_rows('ripetitore_sx') ) : the_row(); 
            $file_sx = get_sub_field('file_sx');
            $nome_file_sx = get_sub_field('nome_file_sx');
            ?>
            
            <li>
                <a href="<?php echo $file_sx; // è scritto così perché nel campo ACF del file ho impostato URL come valore di ritorno ?>">
                    <span class="glyphicon glyphicon-download margin-right-5px"></span><?php echo $nome_file_sx; ?>
                </a>
            </li>
            <?php endwhile; ?>
            <?php else : ?>
             <?php
             // no rows found
             endif;
             ?>
             </ul>
    	</div>	
    

    
        <div class="div-2">
            <h2 class="<?php if( get_sub_field('classe_titolo_dx') ): ?><?php the_sub_field('classe_titolo_dx'); ?><?php endif; ?>"><?php echo get_sub_field('titolo_dx') ?></h2>
            <div><?php echo get_sub_field('paragrafo_dx') ?></div>
            <?php
            // check if the repeater field has rows of data
            if( have_rows('ripetitore_dx') ): ?>
            
            <ul>
            
            <?php
            // loop through the rows of data
            while ( have_rows('ripetitore_dx') ) : the_row(); 
            $file_dx = get_sub_field('file_dx');
            $nome_file_sx = get_sub_field('nome_file_dx');
            ?>
            
            <li>
                <a href="<?php echo $file_dx; // è scritto così perché nel campo ACF del file ho impostato URL come valore di ritorno ?>">
                    <span class="glyphicon glyphicon-download margin-right-5px"></span><?php echo $nome_file_sx; ?>
                </a>
            </li>
            <?php endwhile; ?>
            <?php else : ?>
             <?php
             // no rows found
             endif;
             ?>
             </ul>
</div>
		
</div><!--/fine struttura-2 preview-->