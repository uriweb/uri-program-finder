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
	$ids = ( preg_match('/[\d,]+/', $data['ids']) == 1 ) ? $data['ids'] : FALSE;
	
	$args = array(
		'post_type' => 'post',
		's' => $search,
		'orderby' => 'title',
		'order' => 'ASC',
		'nopaging' => TRUE,
	);
	if($ids !== FALSE) {
		$args['tax_query'] = array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'category',
				'field'    => 'term_id',
				'terms'    => explode(',', $ids),
				'operator' => 'AND',
			),
		);
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

