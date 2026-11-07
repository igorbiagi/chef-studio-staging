<?php
/**
 * The template for displaying all single tagged posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Chef Studio
 */

get_header(); 
?>
    
<div class="contenitore">
		<?php
			if ( function_exists('yoast_breadcrumb') ) {
			yoast_breadcrumb('<p id="breadcrumbs">','</p>');
			}
		?>	
</div> <!-- fine contenitore -->

<div class="contenitore">
    <div class="contenuto">
		
		<div class="txt_center">
			<h1>Stai visualizzando i post del Blog con il tag:</h1>
			<div class="tag-title"><?php single_tag_title(); ?></div>
		</div>
		<div class="anteprima-container">
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<?php $posttags = get_the_tags(); ?>
				<div class="anteprima">
	                <a href="<?php echo get_the_permalink(get_the_ID() ); ?>">
	                    <img class="flex" src="<?php echo get_the_post_thumbnail_url( get_the_ID(), 'post-preview' ); ?>" title="<?php echo get_post(get_post_thumbnail_id( get_the_ID()))->post_title; ?>" alt="<?php echo get_post(get_post_thumbnail_id( get_the_ID()))->post_title; ?>">
	                </a>
	                <div class="testo">
		<a href="<?php echo get_the_permalink(get_the_ID() ); ?>">
   			<div class="titolo titolo_post"><?php echo the_title() ?></div>
		</a>
<div class="tag">
<?php 
                           $tagCounter = 1 ;
                           $virgola =',';
                       ?>
                   <?php if(is_array($posttags)) : ?>
                       <?php foreach ($posttags as $posttag) : ?>
                           <?php if ($tagCounter == count($posttags)) $virgola = ''?>
                           <a href="<?php echo get_tag_link($posttag->term_id); ?>"><?php echo $posttag->name; ?></a><?php echo $virgola; ?>
                           <?php $tagCounter++; ?>
                       <?php endforeach; ?>
                    <?php endif; ?>
</div> 
    <div class="anteprima-testo">
        <p>
            <?php echo substr( strip_tags( get_the_content() ), 0, 200 ) ?>[...]
        </p> 
    </div>
    
       	<div class="read-more">
           	<a href="<?php echo get_the_permalink(get_the_ID() ); ?>">
     	<button class="btn btn-primary trans-color">
     	Leggi l'Articolo 
     	</button>
     	</a>
</div>
   	</div>
   </div>

<?php endwhile; ?>

		<!-- <div class="navigation">
					<div class="next-posts"><?php next_posts_link(); ?></div>
					<div class="prev-posts"><?php previous_posts_link(); ?></div>
				</div> -->

			<?php endif; ?>
		</div>
		
		<div class="floatstop"></div>
        <div class="tag-container">
            <?php $tags =  get_tags( );?>
            <?php
                $tagCounter = 1;
                $virgola = ' /';
            ?>
            <strong>I nostri Temi</strong><br/><br/>
            <?php foreach ($tags as $tag) : ?>
            <?php if ($tagCounter == count($tags)) $virgola = ''; ?>
            <a href="<?php echo get_tag_link($tag->term_id); ?>"><?php echo $tag->name ?></a><?php echo $virgola; ?>
            <?php $tagCounter++ ?>
            <?php endforeach; ?>
        </div>




	</div>
</div>

<?php
get_footer();
