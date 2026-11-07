<?php
/*
Template di pagina standard
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
		<?php woocommerce_content(); ?>
		<div class="floatstop"></div>	
	</div> <!-- fine contenuto -->		
</div> <!-- fine contenitore -->	
	
<?php
get_footer();