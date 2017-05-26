<?php
/**
 * @author: John Pennypacker <jpennypacker@uri.edu>
 * @author: Brandon Fuller <bjcfuller@uri.edu>
 */

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

/**
 * Selects posts by category using AND instead of OR
 *
 * @param array $data Options for the function.
 * @return string|null Post title for the latest,  * or null if none.
 */
function uri_program_finder_api_callback( $data ) {

	$search = ( isset ( $data['s'] ) ) ? sanitize_title ( $data['s'] ) : '';
	$ids = uri_program_sanitize_ids( $data['ids'] );

	$program_type = uri_program_sanitize_ids($data['program-type']);
	$interest_area = uri_program_sanitize_ids($data['interest-area']);
	$location = uri_program_sanitize_ids($data['location']);
	
	$args = array(
		'post_type' => 'post',
		's' => $search,
		'orderby' => 'title',
		'order' => 'ASC',
		'nopaging' => TRUE,
	);
	
	// https://codex.wordpress.org/Class_Reference/WP_Query#Taxonomy_Parameters
	if($ids !== FALSE) {
		// if ids is passed, then select using ids across all categories
		$args['tax_query'] = array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'category',
				'field'    => 'term_id',
				'terms'    => explode(',', $ids),
				'operator' => 'AND',
			),
		);
	} else {
		// if not ids, then test for other categories and search for those

		if(!empty($program_type) || !empty($interest_area) || !empty($location)) {
			$args['tax_query'] = array(
				'relation' => 'AND',
			);
			if(!empty($program_type)) {
				$args['tax_query'][] = array(
					'taxonomy' => 'category',
					'field' => 'term_id',
					'terms' => explode( ',', $program_type ),
					'operator' => 'IN',
				);
			}
			if(!empty($interest_area)) {
				$args['tax_query'][] = array(
					'taxonomy' => 'category',
					'field' => 'term_id',
					'terms' => explode( ',', $interest_area ),
					'operator' => 'IN',
				);
			}
			if(!empty($location)) {
				$args['tax_query'][] = array(
					'taxonomy' => 'category',
					'field' => 'term_id',
					'terms' => explode( ',', $location ),
					'operator' => 'IN',
				);
			}
		}
	}

	
	$query = new WP_Query( $args );
		
	$result = array();
	// The Loop
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();

			$result[] = array(
				'id' => get_the_ID(),
				'title' => get_the_title(),
				'excerpt' => get_the_excerpt(),
				'link' => get_permalink(),
				'program_types' => uri_program_finder_get_post_categories( get_the_ID() ),
				'image' => get_the_post_thumbnail() // accepts image size as argument
			);
		}
		
		/* Restore original Post Data */
		wp_reset_postdata();
	} else {
		// no posts found
	}

	return $result;

}

/**
 * much like is_int, but allows commas too.
 * @param str $str is a GET param
 * @return str or bool false
 */
function uri_program_sanitize_ids($str) {
	return ( preg_match('/[\d,]+/', $str) == 1 ) ? $str : FALSE;
}

/**
 * Register the URL for the custom API calls
 */
function uri_program_finder_register_api() {
	// accept category IDs and allow commas to delinieate multiple integers
  //register_rest_route( 'uri-programs/v1', '/category/(?P<id>[\d,]+)', array(
  register_rest_route( 'uri-programs/v1', '/category', array(
    'methods' => 'GET',
    'callback' => 'uri_program_finder_api_callback',
  ) );

}
add_action( 'rest_api_init', 'uri_program_finder_register_api' );

