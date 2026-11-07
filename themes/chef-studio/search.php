<?php
/*
Template di pagina di ricerca
*/
get_header();
$actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>

<div class="contenitore">
    <div class="contenuto-no-padding">
        <?php
        if (function_exists('yoast_breadcrumb')) {
            yoast_breadcrumb('<p id="breadcrumbs">', '</p>');
        }
        ?>
    </div>
</div> <!-- fine contenitore -->

<div class="contenitore">
	<div class="contenuto">
		
	 <?php
if (have_posts()) :
    echo '<h1>Risultati della ricerca:</h1>';
	echo '<div class="termine-di-ricerca">';
	echo get_search_query();
	echo '</div>';
	echo '<div class="contenitore-prodotti-4">';
    woocommerce_product_loop_start();
	
    while (have_posts()) : the_post();
        wc_get_template_part('content', 'product');
    endwhile;

    woocommerce_product_loop_end();
	 echo '</div>';
else :
    echo 'Nessun prodotto trovato.';
endif; 
	 
	 ?> 
	
<div class="floatstop"></div>	
</div><!-- fine contenuto -->		
</div><!-- fine contenitore -->	
	
<?php
get_footer();