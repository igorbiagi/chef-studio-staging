<?php
/**
 * The Template for displaying products in a product category. Simply includes the archive template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/taxonomy-product-cat.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     4.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();
$actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$current_cat = get_queried_object();

$testo_intro_categoria = get_field('testo_intro_categoria', $current_cat);
$testo_fine_categoria = get_field('testo_fine_categoria', $current_cat);


$cat_thumb_id = get_woocommerce_term_meta( $current_cat->term_id, 'thumbnail_id', true );
$image_size = 'medium_large'; // specifica la dimensione desiderata, ad esempio 'large', 'medium', 'thumbnail' o dimensioni personalizzate definite nel file functions
$cat_thumb_url = wp_get_attachment_image_src( $cat_thumb_id, $image_size );
// L'URL sarà l'elemento 0 nell'array restituito da wp_get_attachment_image_src()
$cat_thumb_url = $cat_thumb_url[0];

$image_data = wp_get_attachment_image_src( $cat_thumb_id );
$alt_text = get_post_meta( $cat_thumb_id, '_wp_attachment_image_alt', true ); // Prendo l'URL dell'img di categoria

$special_ids = array(19, 88, 18);
?>


<div class="contenitore">
	<div class="contenuto-no-padding">
		<?php
			if ( function_exists('yoast_breadcrumb') ) {
			yoast_breadcrumb('<p id="breadcrumbs">','</p>');
			}
		?>	
	</div>
</div> <!-- fine contenitore -->


<div class="contenitore">
	<div class="contenuto-padding-small">
	

		<?php 
$special_ids = array(19, 88, 18);

if (is_product_category()): ?>

    <?php if (in_array($current_cat->term_id, $special_ids)): ?>
        <!-- Output per le categorie specificate -->
		<h1><?php echo $current_cat->name; ?></h1>
        
    <?php else: ?>
        <!-- Output per tutte le altre categorie -->
        <h1>Monete <?php echo $current_cat->name; ?></h1>
    <?php endif; ?>

<?php endif; ?>
	
		
		<div class="struttura-container struttura-2-standard">
			<div class="div-1">
				<img src="<?php echo $cat_thumb_url; ?>"  alt="<?php echo $alt_text; ?>">
			</div>
		
			<div class="div-2">
				<?php echo $testo_intro_categoria; ?>
    		</div>	
	
		</div><!--contenuto-->
	</div><!--contenuto-->
</div><!--contenitore-->


<div class="contenitore">	
	<div class="contenuto-con-scroll-x ">
		<div class="categorie-h-scroll">
			<?php echo do_shortcode('[child_product_categories]'); ?>  <!-- mio shortcode -->
		</div>
    </div>
</div><!-- contenitore -->
					

<div class="contenitore">
	<div class="contenuto-no-padding-laterale">
		<div class="struttura-side struttura-side-left"> 
			
			<div class="sidebar">
				<?php echo do_shortcode('[yith_wcan_filters slug="default-preset"]'); ?>
			</div>
		
			<div class="contenuto-side">
				
				<div class="contenitore">
					<div class="contenuto-padding-small">	
						<div class="contenitore-prodotti-4" id="products-container">
    						<?php woocommerce_content(); ?>
						</div>
					</div>
				</div>
				
				<div class="contenitore">
					<div class="contenuto-padding-small">	
						<?php my_pagination();?>
					</div>
				</div>
						
				<div class="contenitore">
					<div class="contenuto-padding-small">	
						<div class="testo-largo">
							<?php echo $testo_fine_categoria; ?>
						</div>
					</div>
				</div>

				<div class="floatstop"></div>	
				
			</div><!--/contenuto side -->
				
	
		</div><!--/struttura-side -->

	</div><!--/contenuto -->	
</div><!--/contenitoere-->

<?php
get_footer();