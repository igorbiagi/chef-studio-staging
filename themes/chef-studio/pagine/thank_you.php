<?php 
/*
Template Name: Thank You Page
*/
get_header();
?>


<div class="contenitore">
	<div class="contenuto">
		
		<div class="header-thank-you">
			<img src="<?php echo get_template_directory_uri() ?>/img/messaggio-inviato.png" width="150" title="Messaggio inviato" alt="Messaggio inviato">
    	<h1>
    		Messaggio inviato! :)
    	</h1>
		</div>
    	<h2>
    		Grazie per averci contattati. Risponderemo al più presto.
    	</h2>
		
		<div class="txt-center">
           	<a href="<?php echo get_site_url(); ?>">
     	<button class="btn btn-primary">
     	Vai alla Home 
     	</button>
     	</a>
</div>
		
<div class="social-cont">        
	<p>
		Nell'attesa seguici sui social per novità e promozioni!
	</p>
	<ul>
		<li>
			<a target="_blank" href="#"><img src="<?php echo get_template_directory_uri() ?>/img/icona-facebook.png" class="social-icon" title="Facebook" alt="Icona Facebook"><br/></a>
		</li>
		<li >
			<a target="_blank" href="#"><img src="<?php echo get_template_directory_uri() ?>/img/icona-instagram.png" class="social-icon" title="Instagram" alt="Icona Instagram"><br/></a>
		</li>
	</ul>
	<br />
	<br />
</div><!--/fine social cont-->
		  
		  
		  
		  

	</div> <!-- fine contenuto -->		
</div> <!-- fine contenitore -->  


<?php 
get_footer();
?>