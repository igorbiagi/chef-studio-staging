<?php
/*
Template Name: Pagina neutro
*/

get_header();

/// var
$posts = query_posts('posts_per_page=3'); 



// add extra params to iframe src
$params = array(
    'controls'    => 0,
    'hd'        => 1,
    'autohide'    => 1,
    'autoplay' =>0,
    'background' => 1
);

// get the current taxonomy term
$term = get_queried_object();
?>

<style type="text/css">
	.apertura-small {
		background-image: url(<?php echo get_the_post_thumbnail_url(get_the_ID(),'apertura-piccola') ?>);
	}
</style>

    <section>
        <div class="contenuto">
            <div class="apertura-small"></div>
        </div>
    </section>
	
    
  <section>
        <div class="contenuto">   
<?php
// ID of the current item in the WordPress Loop
$id = get_the_ID();

// controlla se il contenuto flessibile ha delle righe
if ( have_rows( 'layout_flessibile_1', $id ) ) :

    // avvia il loop con le singole righe del layout flessibile e ottieni i relativi file dei singoli pezzi (chiamali uguali alle righe di acf)
    while ( have_rows( 'layout_flessibile_1', $id ) ) : the_row();

        get_template_part( 'parti/' . get_row_layout() );

    endwhile;

elseif ( get_the_content() ) :

    // no layouts found

endif;

?>

       </div><!--/fine contenuto--> 
       </section>

<?php 
get_footer();
?>
