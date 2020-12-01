<?php
/**
 * Extend the Display Posts plugin.
 *
 * @package uri-program-finder
 * @see https://displayposts.com/docs/the-output-filter/
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


/**
 * Extend shortcode to display program details in output
 *
 * @param str $output Current output of post.
 * @param arr $original_atts Original attributes passed to shortcode.
 * @param str $image The image markup.
 * @param str $title The title markup.
 * @param str $date The date markup.
 * @param str $excerpt The exerpt markup.
 * @param str $inner_wrapper The inner wrapper tag name.
 * @param str $content The content.
 * @param arr $class The class list.
 * @param str $author The author markup.
 * @param arr $category_display_text Taxonomic elements.
 * @return str $output
 */
function uri_program_finder_display_posts_output_details( $output, $original_atts, $image, $title, $date, $excerpt, $inner_wrapper, $content, $class, $author, $category_display_text ) {

	if ( empty( $original_atts['program_finder_details'] ) ) {
		return $output;
	}

	if ( has_category( 'accelerated' ) || has_category( 'online' ) ) {

		$details = '<div class="program-details">';

		if ( has_category( 'accelerated' ) ) {
			$details .= '<div class="icon accelerated" title="Bachelor&#39;s to Master&#39;s">Bachelor&#39;s to Master&#39;s</div>';
		}

		if ( has_category( 'online' ) ) {
			$details .= '<div class="icon online" title="Online Program">Online Program</div>';
		}

		$details .= '</div>';

		$title = '<a class="title" href="' . get_the_permalink() . '"><span>' . get_the_title() . '</span>' . $details . '</a>';

	}

	array_push( $class, 'programs-list-has-details' );

	$output = '<' . $inner_wrapper . ' class="' . implode( ' ', $class ) . '">' . $image . $title . $date . $author . $category_display_text . $excerpt . $content . '</' . $inner_wrapper . '>';
	return $output;

}
add_action( 'display_posts_shortcode_output', 'uri_program_finder_display_posts_output_details', 10, 11 );
