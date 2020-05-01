<?php
/**
 * The template for displaying single programs
 * If you'd like to customize this file, copy it to your theme, and make changes in your theme.
 *
 * @package uri-program-finder
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 */

get_header();
?>

	<main id="main" class="site-main" role="main">

		<?php
		while ( have_posts() ) :
			the_post();
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
					</header><!-- .entry-header -->


					<div class="featured-image">
						<figure>

							<?php
							$width = ( is_single() ) ? 1200 : 300;
							the_post_thumbnail( array( $width, null ) );

							$figcaption = get_the_post_thumbnail_caption();
							if ( ( is_single() || is_page() ) && ! empty( $figcaption ) ) :
								?>
							<figcaption class="wp-caption"><?php print $figcaption; ?></figcpation>
							<?php endif; ?>
						</figure>
					</div>


					<div class="entry-content">
						<?php

						the_content(
							sprintf(
							/* translators: %s: Name of current post. */
							wp_kses( __( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'uri-modern' ), array( 'span' => array( 'class' => array() ) ) ),
							the_title( '<span class="screen-reader-text">"', '"</span>', false )
						)
							);

						wp_link_pages(
							 array(
								 'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'uri-modern' ),
								 'after'  => '</div>',
							 )
							);
						?>

						<?php if ( $accreditation = uri_modern_get_field( 'accreditation' ) ) { ?>
						<div class="accreditation">
							<h3>Accreditation</h3>
							<?php print $accreditation; ?>
						</div>
						<?php } ?>

						<?php if ( $specializations = uri_modern_get_field( 'specializations' ) ) { ?>
						<div class="specializations">
							<h3>Specializations</h3>
							<?php
							$specializations = explode( ',', $specializations );
							print '<ul>';
							foreach ( $specializations as $s ) {
								print '<li>' . trim( $s ) . '</li>';
							}
							print '</ul>';
							?>
						</div>
						<?php } ?>

						<?php if ( $classes_offered = uri_modern_get_field( 'classes_offered' ) ) { ?>
							<div class="classes-offered">
								<h3>Classes Offered</h3>
								<?php print $classes_offered; ?>
							</div>
							<?php } ?>

						<?php if ( $time_to_completion = get_field( 'time_to_completion' ) ) { ?>
						<div class="time-to-completion">
							<h3>Time to Completion</h3>
							<?php print $time_to_completion; ?>
						</div>
						<?php } ?>

						<?php if ( $application_deadline = get_field( 'application_deadline' ) ) { ?>
						<div class="application-deadline">
							<h3>Application Deadline</h3>
							<?php print $application_deadline; ?>
						</div>
						<?php } ?>


						<?php if ( $department_website = get_field( 'department_website' ) ) { ?>
						<div class="department-website">
							<a href="<?php print $department_website; ?>">Department Website</a>
						</div>
						<?php } ?>

						<?php if ( $catalog_info = get_field( 'catalog_info' ) ) { ?>
						<div class="catalog-info">
							<a href="<?php print $catalog_info; ?>">Catalog Information</a>
						</div>
						<?php } ?>

						<?php if ( $course_descriptions = get_field( 'course_descriptions' ) ) { ?>
						<div class="course-descriptions">
							<a href="<?php print $course_descriptions; ?>">Course Descriptions</a>
						</div>
						<?php } ?>

						<?php if ( $course_schedule = get_field( 'course_schedule' ) ) { ?>
						<div class="course-schedule">
							<a href="<?php print $course_schedule; ?>">Course Schedule</a>
						</div>
						<?php } ?>

						<?php if ( $admission_info = get_field( 'admission_info' ) ) { ?>
						<div class="admission-info">
							<a href="<?php print $admission_info; ?>">Admission Information</a>
						</div>
						<?php } ?>

						<?php
						$curriculum_sheets = get_field( 'curriculum_sheets' );
						if ( null != $curriculum_sheets || ! empty( $curriculum_sheets ) ) {
							echo '<div class="advising">';
							echo do_shortcode( '[cl-button link="' . $curriculum_sheets . '" text="Advising"]' );
							echo '</div>';
						} else if ( has_category( 'bachelors' ) ) {
							echo '<div class="curriculum-sheets">';
							echo do_shortcode( '[cl-button link="https://web.uri.edu/advising/curriculum-sheets-all/" text="Curriculum Sheets"]' );
							echo '</div>';
						}
						?>

						<?php if ( $apply = get_field( 'apply' ) ) { ?>
						<div class="apply">
							<a href="<?php print $apply; ?>">Apply</a>
						</div>
						<?php } ?>


					</div><!-- .entry-content -->


					<?php
					the_post_navigation();

					// If comments are open or we have at least one comment, load up the comment template.
					if ( comments_open() || get_comments_number() ) :
						comments_template();
					endif;
					?>

		<?php endwhile; // End of the loop. ?>

	</main><!-- #main -->

<?php
get_footer();
