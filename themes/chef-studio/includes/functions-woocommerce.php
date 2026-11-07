<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Se WooCommerce non è attivo, non caricare niente di questo file
if ( ! class_exists('WooCommerce') ) {
  return;
}

// =====================================================
// 1. SUPPORTI E FILTRI GLOBALI
// =====================================================
add_theme_support( 'wc-product-gallery-slider' );
add_theme_support( 'wc-product-gallery-zoom' );
add_theme_support( 'wc-product-gallery-lightbox' );

add_filter( 'woocommerce_show_page_title', '__return_false' );
add_filter( 'widget_text', 'shortcode_unautop' );
add_filter( 'widget_text', 'do_shortcode' );

remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wc_generator_tag');


// =====================================================
// 2. MENU: ICONA ACCOUNT E CARRELLO
// =====================================================
add_filter( 'wp_nav_menu_objects', 'woocommerce_menu_icon', 10, 2 );
function woocommerce_menu_icon( $items, $args ) {
    if ( $args->theme_location == 'menu-chef' ) {
        foreach ( $items as $item ) {
            if ( $item->title == 'Account' ) {
                $item->title = '<span class="icona-account-chef"></span><span class="parola-account-nel-menu">' . $item->title . '</span>';
                break;
            }
        }
    }
    return $items;
}

add_filter( 'wp_nav_menu_objects', 'woocommerce_menu_account', 10, 2 );
function woocommerce_menu_account( $items, $args ) {
    if ( $args->theme_location == 'menu-chef' ) {
        foreach ( $items as $item ) {
            if ( $item->title == '<i class="icona-account-chef">XXXXXX</i>' ) {
                $item->classes[] = 'woocommerce-menu-item';
                break;
            }
        }
    }
    return $items;
}

add_filter( 'wp_nav_menu_items','add_cart_icon_to_menu', 10, 2 );
function add_cart_icon_to_menu( $items, $args ) {
    if ( $args->theme_location == 'menu-chef' ) {
        $items .= '<li class="menu-item">'.woocommerce_cart_link().'</li>';
    }
    return $items;
}

function woocommerce_cart_link() {
    $cart_url = wc_get_cart_url();
    $cart_count = WC()->cart->get_cart_contents_count();
    return "<a class='icona-carrello-chef' href='$cart_url'><span class='cart-contents-count'>$cart_count</span></a>";
}

add_action( 'wp_head', 'add_cart_icon_to_mobile_menu', 999 );
function add_cart_icon_to_mobile_menu() {
    echo '<div class="cart-container-mobile">'.woocommerce_cart_link().'</div>';
}

add_filter( 'woocommerce_add_to_cart_fragments', 'my_header_add_to_cart_fragment' );
function my_header_add_to_cart_fragment( $fragments ) {
    ob_start(); ?>
    <a class="cart-contents" href="<?php echo WC()->cart->get_cart_url(); ?>">
        <?php if ( WC()->cart->get_cart_contents_count() > 0 ): ?>
            <span class="cart-contents-count"><?php echo esc_html(WC()->cart->get_cart_contents_count()); ?></span>
        <?php endif; ?>
    </a>
    <?php
    $fragments['a.cart-contents'] = ob_get_clean();
    return $fragments;
}


// =====================================================
// 3. CHECKOUT: STEP VISUALI
// =====================================================
if ( ! function_exists( 'visualizza_checkout_steps' ) ) {
    function visualizza_checkout_steps() {
        ?>
        <div class="cs-checkout-steps">
            <ul>
                <li class="step-cart <?php echo is_cart() ? 'step-active' : 'step-inactive'; ?>">
                    <a href="<?php echo esc_url( wc_get_cart_url() ); ?>"><span><?php esc_html_e('Carrello', 'chef-studio'); ?></span></a>
                </li>
                <li class="step-checkout <?php echo (is_checkout() && ! is_order_received_page()) ? 'step-active' : 'step-inactive'; ?>">
                    <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>"><span><?php esc_html_e('Pagamento', 'chef-studio'); ?></span></a>
                </li>
                <li class="step-complete <?php echo is_order_received_page() ? 'step-active' : 'step-inactive'; ?>">
                    <span><?php esc_html_e('Stato dell\'ordine', 'chef-studio'); ?></span>
                </li>
            </ul>
        </div>
        <?php
    }
}


// =====================================================
// 4. ORDINAMENTO PRODOTTI PERSONALIZZATO
// =====================================================
add_action( 'woocommerce_before_shop_loop', 'aggiungi_selettore_ordinamento' );
function aggiungi_selettore_ordinamento() {
    $orderby = isset($_GET['orderby']) ? wc_clean($_GET['orderby']) : 'default';
    ?>
    <form class="orderby" method="get">
        <select name="orderby" class="orderby" id="selettore_ordinamento">
            <option value="default" <?php selected($orderby, 'default'); ?>>Ordina per</option>
            <option value="price-asc" <?php selected($orderby, 'price-asc'); ?>>Prezzo crescente</option>
            <option value="price-desc" <?php selected($orderby, 'price-desc'); ?>>Prezzo decrescente</option>
            <option value="date-asc" <?php selected($orderby, 'date-asc'); ?>>Dal più nuovo</option>
            <option value="date-desc" <?php selected($orderby, 'date-desc'); ?>>Dal più vecchio</option>
        </select>
    </form>
    <script>
        document.getElementById('selettore_ordinamento').addEventListener('change', function() {
            this.form.submit();
        });
    </script>
    <?php
}

add_filter( 'woocommerce_get_catalog_ordering_args', 'personalizza_ordinamento_prodotto' );
function personalizza_ordinamento_prodotto($args) {
    $orderby = isset($_GET['orderby']) ? wc_clean($_GET['orderby']) : 'default';
    $map = [
        'price-asc'  => ['price', 'asc'],
        'price-desc' => ['price', 'desc'],
        'date-asc'   => ['date', 'asc'],
        'date-desc'  => ['date', 'desc'],
    ];
    if ( isset( $map[$orderby] ) ) {
        $args['orderby'] = $map[$orderby][0];
        $args['order'] = $map[$orderby][1];
    }
    return $args;
}

add_action( 'pre_get_posts', 'personalizza_loop_prodotto' );
function personalizza_loop_prodotto($query) {
    if ( $query->is_main_query() && ! is_admin() && is_post_type_archive('product') ) {
        $orderby = isset($_GET['orderby']) ? wc_clean($_GET['orderby']) : 'default';
        if ( in_array( $orderby, ['price-asc', 'price-desc'] ) ) {
            $query->set('orderby', 'meta_value_num');
            $query->set('meta_key', '_price');
        } elseif ( $orderby === 'date-asc' || $orderby === 'date-desc' ) {
            $query->set('orderby', 'date');
        }
        $query->set('order', (strpos($orderby, 'asc') !== false) ? 'asc' : 'desc');
    }
}


// =====================================================
// 5. VISUAL CUSTOM E TEMPLATE
// =====================================================
remove_action( 'woocommerce_shop_loop_item_title','woocommerce_template_loop_product_title', 10 );
add_action('woocommerce_shop_loop_item_title', 'abChangeProductsTitle', 10 );
function abChangeProductsTitle() {
    echo '<span class="woocommerce-loop-product__title">' . get_the_title() . '</span>';
}

add_filter( 'body_class', 'add_search_classes' );
function add_search_classes( $classes ) {
    if ( is_search() ) {
        $classes[] = 'woocommerce-shop';
        $classes[] = 'woocommerce';
        $classes[] = 'woocommerce-page';
    }
    return $classes;
}


//
// =====================================================
// 6. SCHEDE, SHORTCODE, TASSONOMIE
// =====================================================
add_filter('woocommerce_product_tabs', 'aggiungi_scheda_attributi_prodotto');
function aggiungi_scheda_attributi_prodotto($tabs) {
    global $product;
    if ( $product->get_attributes() ) {
        $tabs['attributi'] = array(
            'title'    => __('Info', 'chef-studio'),
            'priority' => 20,
            'callback' => 'mostra_attributi_prodotto_tab',
        );
    }
    return $tabs;
}

function mostra_attributi_prodotto_tab() {
    global $product;
    echo wc_display_product_attributes($product);
}

add_filter( 'loop_shop_per_page', 'items_per_page_in_product_category_archives', 900 );
function items_per_page_in_product_category_archives( $limit ) {
    return is_product_category() ? 24 : $limit;
}

function woocommerce_get_product_category_of_subcategories( $category_slug ){
    $terms_html = array();
    $taxonomy = 'product_cat';
    $parent = get_term_by( 'slug', $category_slug, $taxonomy );
    $children_ids = get_term_children( $parent->term_id, $taxonomy );
    foreach($children_ids as $children_id){
        $term = get_term( $children_id, $taxonomy );
        $term_link = get_term_link( $term, $taxonomy );
        if ( is_wp_error( $term_link ) ) $term_link = '';
        $terms_html[] = '<a href="' . esc_url( $term_link ) . '" rel="tag" class="' . $term->slug . '">' . $term->name . '</a>';
    }
    return '<span class="subcategories-' . $category_slug . '">' . implode( ', ', $terms_html ) . '</span>';
}



// Shortcode per mostrare le sottocategorie della categoria prodotto attuale
function custom_child_product_categories_shortcode() {
    // Esegui solo se siamo in una pagina di categoria prodotto
    if ( is_product_category() ) {
        global $wp_query;

        // Ottieni l'ID della categoria corrente
        $current_cat_id = $wp_query->get_queried_object_id();

        // Ottieni le sottocategorie (figlie) di quella categoria
        $children = get_terms('product_cat', array(
            'parent' => $current_cat_id,
            'hide_empty' => false, // mostra anche se vuote
        ));

        // Se ci sono sottocategorie, le mostra in griglia tipo shop
        if ( ! empty( $children ) ) {
            $output = '<div class="woocommerce columns-4"><ul class="products columns-4">';

            foreach ( $children as $child ) {
                // Ottieni l’immagine della categoria, o usa placeholder
                $thumbnail_id = get_term_meta( $child->term_id, 'thumbnail_id', true );
                $image = $thumbnail_id
                    ? wp_get_attachment_image( $thumbnail_id, 'articolo-400px' )
                    : '<img src="' . wc_placeholder_img_src() . '" alt="' . esc_attr( $child->name ) . '" />';

                // HTML del box categoria
                $output .= '<li class="product-category product">';
                $output .= '<a aria-label="Visita categoria ' . $child->name . '" href="' . get_term_link( $child ) . '">
                                <div class="contenitore-img">' . $image . '</div>
                                <h2 class="woocommerce-loop-category__title">' . $child->name . ' <mark class="count">(' . $child->count . ')</mark></h2>
                            </a>';
                $output .= '</li>';
            }

            $output .= '</ul></div>';
            return $output;
        }
    }

    // Se non siamo in categoria o non ci sono figli
    return '';
}
add_shortcode( 'child_product_categories', 'custom_child_product_categories_shortcode' );




// Shortcode per mostrare categorie/tag prodotti specifici in griglia
function custom_product_categories_shortcode( $atts ) {
    // Prende gli ID passati nello shortcode
    $atts = shortcode_atts( array(
        'ids' => '', // es: ids="23,45,67"
    ), $atts, 'custom_product_categories' );

    $ids = explode(',', $atts['ids']); // separa gli ID

    $terms = array();
    foreach ($ids as $id) {
        // Ottiene ogni termine (categoria o tag) dall’ID
        $term = get_term($id);
        if ($term && !is_wp_error($term)) {
            $terms[] = $term;
        }
    }

    // HTML iniziale
    ob_start();
    ?>
    <div class="woocommerce columns-4">
        <ul class="products columns-4">
        <?php foreach ( $terms as $term ) : ?>
            <li class="product-category product first">
                <?php
                $thumbnail_id = get_term_meta( $term->term_id, 'thumbnail_id', true ); // per le categorie
                $tag_thumbnail_url = get_term_meta( $term->term_id, 'product_tag_thumbnail', true ); // per i tag

                // Immagine: usa ID se c'è, altrimenti URL, altrimenti placeholder
                if ( $thumbnail_id ) {
                    $img = wp_get_attachment_image( $thumbnail_id, 'thumbnail' );
                } elseif ( $tag_thumbnail_url ) {
                    $img = '<img src="' . esc_url( $tag_thumbnail_url ) . '" alt="' . esc_attr( $term->name ) . '" />';
                } else {
                    $img = '<img src="' . wc_placeholder_img_src() . '" alt="' . esc_attr( $term->name ) . '" />';
                }

                // HTML output
                echo '<a href="' . esc_url( get_term_link( $term ) ) . '">
                        <div class="contenitore-img">' . $img . '</div>
                        <span class="woocommerce-loop-category__title">' . esc_html( $term->name ) . '</span>
                      </a>';
                ?>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'custom_product_categories', 'custom_product_categories_shortcode' );



add_action( 'woocommerce_before_subcategory_title', 'add_div_before_category_thumbnail', 9 );
add_action( 'woocommerce_after_subcategory_title', 'close_div_after_category_thumbnail', 11 );
function add_div_before_category_thumbnail( $category ) {
    echo '<div class="contenitore-img">';
}
function close_div_after_category_thumbnail( $category ) {
    echo '</div>';
}

add_filter( 'subcategory_archive_thumbnail_size', 'custom_product_category_thumbnail_size' );
function custom_product_category_thumbnail_size( $size ) {
    return 'large';
}
