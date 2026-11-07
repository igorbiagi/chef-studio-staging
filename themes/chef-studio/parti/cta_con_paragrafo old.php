<?php get_template_part('parti/layout_open'); ?>
    <div class="struttura-container struttura-2-preview chef-animazione-tutta-altezza">

      <div class="div-1">
        <?php if (get_sub_field('paragrafo')): ?>
      		<?php echo esc_html(get_sub_field('paragrafo')); ?>
    	<?php endif; ?>
      </div> <!-- div-1 -->

      <div class="div-2">
        <div class="call-to-action call-andrea neon">
          <div class="media-object">
            <div class="info-call-wrap">

              <div class="img-call">
                <div class="img-call-contenuto">
                  <?php $immagine = get_sub_field('immagine'); ?>
                  <?php if ($immagine): ?>
                    <img src="<?php echo esc_url($immagine['url']); ?>" alt="<?php echo esc_attr($immagine['alt']); ?>">
                  <?php endif; ?>
                </div>
              </div>

              <div class="testo-call">
                <div class="info">
                  <?php the_sub_field('testo_cta'); ?>
                </div>
              </div>

              <div class="button-call">
                <div class="media-object-btn">
                  <?php $link_cta = get_sub_field('link_cta'); ?>
                  <?php if ($link_cta): ?>
                    <a href="<?php echo esc_url($link_cta); ?>" class="btn btn-super">
                      <?php the_sub_field('testo_bottone'); ?>
                    </a>
                  <?php endif; ?>
                </div>
              </div>

            </div> <!-- info-call-wrap -->
          </div> <!-- media-object -->
        </div> <!-- call-to-action -->
      </div> <!-- div-2 -->

    </div> <!-- struttura-container -->
<?php get_template_part('parti/layout_close'); ?>