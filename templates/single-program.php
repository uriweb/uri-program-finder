<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package uri-modern
 */

get_header();
?>

    <main id="main" class="site-main" role="main">
        


        <?php
        while ( have_posts() ) : the_post();
        ?>


				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<header class="entry-header">
						<?php
						if ( is_single() ) :
							the_title( '<h1 class="entry-title">', '</h1>' );
						else :
							the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
						endif;
						?>
						<?php
						/*
						<?php if ( 'post' === get_post_type() ) : ?>
						<div class="entry-meta">
							<?php uri_modern_posted_on(); ?>
						</div><!-- .entry-meta -->
						<?php endif; ?>
						*/
						?>
					</header><!-- .entry-header -->

		
					<div class="entry-content">
						<?php
			
							the_content( sprintf(
								/* translators: %s: Name of current post. */
								wp_kses( __( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'uri-modern' ), array( 'span' => array( 'class' => array() ) ) ),
								the_title( '<span class="screen-reader-text">"', '"</span>', false )
							) );

							wp_link_pages( array(
								'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'uri-modern' ),
								'after'  => '</div>',
							) );
						?>

					<?php if(get_field('department_website')) { ?>

						<a href="<?php the_field('department_website'); ?>">Department Website</a>
						
					<?php } ?>

					</div><!-- .entry-content -->


					<?php


            the_post_navigation();

            // If comments are open or we have at least one comment, load up the comment template.
            if ( comments_open() || get_comments_number() ) :
                comments_template();
            endif;

        endwhile; // End of the loop.
        ?>

    </main><!-- #main -->

<?php
get_footer();
