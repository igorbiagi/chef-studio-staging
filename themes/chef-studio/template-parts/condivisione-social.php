<?php
$actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>
<div class="social-cont outline-icon">
  <span class="share">
    <?php _e('Condividi', 'dedalo-igor') ?>
    <img src="<?php echo get_template_directory_uri() ?>/img/svg/share.svg" alt="condividi">
  </span>
  <ul class="condividi">
    <li class="trans-color">
      <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo($actual_link) ?>">
        <img src="<?php echo get_template_directory_uri() ?>/img/svg/facebook.svg" class="social-icon" alt="icona facebook"><br />
      </a>
    </li>
    <li class="trans-color">
      <a target="_blank" href="https://twitter.com/home?status=<?php echo($actual_link) ?>">
        <img src="<?php echo get_template_directory_uri() ?>/img/svg/x.svg" class="social-icon" alt="icona x"><br />
      </a>
    </li>
    <li class="trans-color">
      <a target="_blank" href="https://pinterest.com/pin/create/button/?url=<?php echo($actual_link) ?>&media=<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full') ?>&description=<?php echo get_the_title() ?>">
        <img src="<?php echo get_template_directory_uri() ?>/img/svg/pinterest.svg" class="social-icon" alt="icona pinterest"><br />
      </a>
    </li>
    <li class="trans-color">
      <a target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo($actual_link) ?>&title=<?php echo get_the_title() ?>">
        <img src="<?php echo get_template_directory_uri() ?>/img/svg/linkedin.svg" class="social-icon" alt="icona link"><br />
      </a>
    </li>
	  
	  <li class="trans-color">
                    <a target="_blank" href="whatsapp://send?text=Guarda questo articolo interessante.<?php echo '%0A%0A'.  get_the_title() . '%0A%0A' . urlencode( get_the_permalink() ); ?>" data-action="share/whatsapp/share">
                        <img src="<?php echo get_template_directory_uri() ?>/img/svg/whatsapp.svg" class="social-icon" title="Condividi l'articolo di <?php bloginfo('name'); ?> su Whatsapp" alt="Condividi l'articolo su Whatsapp"><br/>
                    </a>
                </li>
                <li class="trans-color">
                    <a target="_blank" href="mailto:?subject=Guarda%20questo%20articolo&amp;body=Ciao,%20ti%20consiglio%20questo%20post%0A<?php echo($actual_link) ?>">
                        <img src="<?php echo get_template_directory_uri() ?>/img/svg/e-mail.svg" class="social-icon" title="Condividi l'articolo di <?php bloginfo('name'); ?> via email" alt="Condividi l'articolo via email"><br/>
                    </a>
                </li>
	  
  </ul>
</div>