<div id="form-anchor"></div>

<?php get_template_part('parti/layout_open'); ?>

<section class="struttura-container struttura-2-standard">

    <div class="div-1">

        <?php if (get_sub_field('testo')): ?>
            <?php echo get_sub_field('testo'); ?>
        <?php endif; ?>

        <div class="local-container">

            <?php 
            $immagine_icon_chiama = get_sub_field('immagine_icon_chiama');
            if ($immagine_icon_chiama): ?>
                <figure class="img-icon">
                    <img src="<?php echo esc_url($immagine_icon_chiama['url']); ?>" alt="<?php echo esc_attr($immagine_icon_chiama['alt']); ?>">
                </figure>
            <?php endif; ?>

            <div>
                <?php if (get_sub_field('testo_chiama')): ?>
                    <?php echo get_sub_field('testo_chiama'); ?><br/>
                <?php endif; ?>

                <img class="telefono-icon" src="<?php echo get_stylesheet_directory_uri(); ?>/img/svg/phone.svg" alt="" aria-hidden="true"/>

                <?php 
                $telefono = get_field('telefono', 'option');
                $telefono_link = '';

                if ($telefono): 
                    $telefono_link = preg_replace('/[^0-9+]/', '', $telefono);
                ?>
                    <a href="tel:<?php echo esc_attr($telefono_link); ?>">
                        <?php echo esc_html($telefono); ?>
                    </a>
                <?php endif; ?>

                <?php if ($telefono_link): ?>
                    <a target="_blank" href="https://wa.me/<?php echo esc_attr($telefono_link); ?>">
                        <div class="icona-whatsapp-tonda pulsa">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/svg/whatsapp.svg" alt="" aria-hidden="true">
                        </div>
                    </a>
                <?php else: ?>
                    <div class="icona-whatsapp-tonda pulsa">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/svg/whatsapp.svg" alt="" aria-hidden="true">
                    </div>
                <?php endif; ?>

            </div>

        </div>

        <?php $img = get_sub_field('immagine_contatti'); ?>
        <?php if (!empty($img)) : ?>
            <figure>
                <img class="immagine-contatti"
                     src="<?php echo esc_url($img['url']); ?>"
                     alt="<?php echo esc_attr($img['alt']); ?>">
            </figure>
        <?php endif; ?>

    </div>

    <section class="div-2 back-box">
        <div class="contatti">
            <?php if (get_sub_field('testo_prima_del_form')): ?>
                <?php echo get_sub_field('testo_prima_del_form'); ?>
            <?php endif; ?>

            <?php gravity_form(1, false, false, false, '', true, 0); ?>
        </div>
    </section>

</section>

<?php get_template_part('parti/layout_close'); ?>
