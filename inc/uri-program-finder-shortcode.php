<?php
/**
 * URI Program Finder Shortcode
 *
 * @package uri-program-finder
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode callback -- generates a form, loads a script
 */
function uri_program_finder_shortcode( $attributes, $content, $shortcode ) {

	uri_program_finder_enqueues();

	// normalize attribute keys, lowercase
	$attributes = array_change_key_case( (array) $attributes, CASE_LOWER );

	// echo '<pre>', print_r($attributes['categories'], TRUE), '</pre>';
	// override default attributes with user attributes
	$attributes = shortcode_atts(
		array(
			'title' => 'Categories', // slug, slug2, slug3
			'quote' => '',
			'citation' => '',
			'photo_id' => '',
		),
		$attributes,
		$shortcode
		);

	$args = array(
		'before_widget' => '<div class="widget %s">',
		'after_widget' => '</div>',
		'before_title' => '<h2 class="widget-title">',
		'after_title' => '</h2>',
	);

	// specify what categories to remove, by slug
	$removecats = array(
		'college',
	);
	$exclude = array();
	foreach ( $removecats as $r ) {
		$exclude[] = get_term_by( 'slug', $r, 'category' )->term_id;
	}
	$categories = uri_program_finder_get_children( 0, $exclude );

	// specify how the remaining categories should be sorted, by slug
	$catorder = array(
		'program-type',
		'interest-area',
		'location',
	);
	usort(
		$categories,
		function ( $a, $b ) use ( $catorder ) {
			$pos_a = array_search( $a['slug'], $catorder );
			$pos_b = array_search( $b['slug'], $catorder );
			return $pos_a - $pos_b;
		}
		);

	// build the form
	return uri_program_finder_make_form( $categories );

}
add_shortcode( 'uri-program-finder', 'uri_program_finder_shortcode' );
