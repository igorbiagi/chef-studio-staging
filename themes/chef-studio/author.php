<?php<br />
/**<br />
 * The template for displaying Author Archive pages.<br />
 *<br />
 */<br />
<br />
get_header(); ?><br />
<br />
        <section id="primary"><br />
            <div id="content" role="main"><br />
<br />
            <?php if ( have_posts() ) : ?><br />
<br />
                <?php<br />
                    /* Queue the first post, that way we know<br />
                     * what author we're dealing with (if that is the case).<br />
                     *<br />
                     * We reset this later so we can run the loop<br />
                     * properly with a call to rewind_posts().<br />
                     */<br />
                    the_post();<br />
                ?><br />
<br />
                <header class="page-header"><br />
                    <h1 class="page-title author"><?php printf( __( 'Author Archives: %s', 'twentyeleven' ), '<span class="vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( "ID" ) ) ) . '" title="' . esc_attr( get_the_author() ) . '" rel="me">' . get_the_author() . '</a></span>' ); ?></h1><br />
                </header><br />
<br />
                <?php<br />
                    /* Since we called the_post() above, we need to<br />
                     * rewind the loop back to the beginning that way<br />
                     * we can run the loop properly, in full.<br />
                     */<br />
                    rewind_posts();<br />
                ?><br />
<br />
                <?php twentyeleven_content_nav( 'nav-above' ); ?><br />
<br />
                <?php<br />
                // If a user has filled out their description, show a bio on their entries.<br />
                if ( get_the_author_meta( 'description' ) ) : ?><br />
                <div id="author-info"><br />
                    <div id="author-avatar"><br />
                        <?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'twentyeleven_author_bio_avatar_size', 60 ) ); ?><br />
                    </div><!-- #author-avatar --><br />
                    <div id="author-description"><br />
                        <h2><?php printf( __( 'About %s', 'twentyeleven' ), get_the_author() ); ?></h2><br />
                        <?php the_author_meta( 'description' ); ?><br />
                    </div><!-- #author-description --><br />
                </div><!-- #entry-author-info --><br />
                <?php endif; ?><br />
<br />
                <?php /* Start the Loop */ ?><br />
                <?php while ( have_posts() ) : the_post(); ?><br />
<br />
                    <?php<br />
                        /* Include the Post-Format-specific template for the content.<br />
                         * If you want to overload this in a child theme then include a file<br />
                         * called content-___.php (where ___ is the Post Format name) and that will be used instead.<br />
                         */<br />
                        get_template_part( 'content', get_post_format() );<br />
                    ?><br />
<br />
                <?php endwhile; ?><br />
<br />
                <?php twentyeleven_content_nav( 'nav-below' ); ?><br />
<br />
            <?php else : ?><br />
<br />
                <article id="post-0" class="post no-results not-found"><br />
                    <header class="entry-header"><br />
                        <h1 class="entry-title"><?php _e( 'Nothing Found', 'twentyeleven' ); ?></h1><br />
                    </header><!-- .entry-header --><br />
<br />
                    <div class="entry-content"><br />
                        <p><?php _e( 'Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'twentyeleven' ); ?></p><br />
                        <?php get_search_form(); ?><br />
                    </div><!-- .entry-content --><br />
                </article><!-- #post-0 --><br />
<br />
            <?php endif; ?><br />
<br />
            </div><!-- #content --><br />
        </section><!-- #primary --><br />
<br />
<?php get_footer(); ?>