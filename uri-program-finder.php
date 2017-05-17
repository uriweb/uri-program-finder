<?php
/*
Plugin Name: URI Program Finder
Plugin URI: http://www.uri.edu
Description: Program finder tools
Version: 1.0
Author: John Pennypacker
Author URI: 
*/

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');
	
// This URL pull multiple categories (need to remove page title):
// https://www.uri.edu/programs/?cat=24,26


/**
 * Loads up the javascript
 */
function uri_program_finder_scripts() {
    $values = array(
		'base' => get_site_url()
	);
    
	$plugin_handle = 'uri-program-finder';
	// Register the script like this for a plugin:
	wp_register_script( $plugin_handle, plugins_url( '/js/program-finder.js', __FILE__ ) );
	wp_localize_script( $plugin_handle, 'URIProgramFinder', $values );
    
    $chosen_handle = 'uri-program-finder-chosen-js';
    wp_register_script( $chosen_handle, plugins_url( '/chosen/chosen.jquery.min.js', __FILE__ ) );
	wp_localize_script( $chosen_handle, 'URIProgramFinderChosenJS', $values );
	
	// For either a plugin or a theme, you can then enqueue the script:
	wp_enqueue_script( $plugin_handle );
    wp_enqueue_script( $chosen_handle );
}


/**
 * Loads up the css
 */
function uri_program_finder_styles() {
    wp_register_style( 'uri-program-finder-chosen-css', plugins_url( '/chosen/chosen.min.css', __FILE__ ) );
    wp_register_style( 'uri-program-finder-chosen-css-customize', plugins_url( '/css/chosen.customize.css', __FILE__ ) );
    
    wp_enqueue_style( 'uri-program-finder-chosen-css' );
    wp_enqueue_style( 'uri-program-finder-chosen-css-customize' );
}

/**
 * Selects posts by category using AND instead of OR
 *
 * @param array $data Options for the function.
 * @return string|null Post title for the latest,  * or null if none.
 */
function uri_program_finder_api_callback( $data ) {

	$args = array(
		'post_type' => 'post',
		'orderby' => 'title',
		'order' => 'ASC',
		'nopaging' => TRUE,
		'tax_query' => array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'category',
				'field'    => 'term_id',
				'terms'    => explode(',', $data['id']),
				'operator' => 'AND',
			),
		),
	);
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
				'image' => ''
			);
		}
		/* Restore original Post Data */
		wp_reset_postdata();
	} else {
		// no posts found
	}

	return $result;

}

add_action( 'rest_api_init', function () {
  register_rest_route( 'uri-programs/v1', '/category/(?P<id>[\d,]+)', array(
    'methods' => 'GET',
    'callback' => 'uri_program_finder_api_callback',
  ) );
} );


/**
 * Shortcode callback -- generates a form, loads a script
 */
function uri_program_finder_shortcode($attributes, $content, $shortcode) {

	uri_program_finder_scripts();
    uri_program_finder_styles();
	// normalize attribute keys, lowercase
	$attributes = array_change_key_case((array)$attributes, CASE_LOWER);
	
//		echo '<pre>', print_r($attributes['categories'], TRUE), '</pre>';

	// override default attributes with user attributes
	$attributes = shortcode_atts(array(
		'title' => 'Categories', // slug, slug2, slug3
		'quote' => '',
		'citation' => '',
		'photo_id' => ''
	), $attributes, $shortcode);


	$args = array(
		'before_widget' => '<div class="widget %s">', 
		'after_widget' => '</div>',
		'before_title' => '<h2 class="widget-title">',
		'after_title' => '</h2>'
	);

	ob_start();
	
	echo '<div class="program-finder">';
	$categories = uri_program_finder_get_children(0);
	foreach($categories as $c) {
		echo '<form action="' . get_site_url() . '" method="GET" class="program-finder-nojs">';
		echo '<fieldset>';
		echo '<legend>' . $c['name'] . '</legend>';
		$subcats = uri_program_finder_get_children($c['id']);
		array_unshift( $subcats, array('id'=>'', 'name' => $c['name']) );
		echo uri_program_finder_make_select( $subcats );
		echo '<input type="submit" value="Go" />';
		echo '</fieldset>';
		echo '</form>';
	}
	echo '</div>';
	
	//echo '<pre>', print_r($categories, TRUE), '</pre>';
	
	$output = ob_get_clean();
	return $output;
		
}
add_shortcode( 'programs-categories', 'uri_program_finder_shortcode' );


/**
 * Turn an array into a select element
 * return str
 */
function uri_program_finder_make_select($items) {
	$output = '<select name="cat">';
	foreach($items as $item) {
		$output .= '<option value="' . $item['id'] . '">' . $item['name'] . '</option>';
	}
	$output .= '</select>';
	return $output;
}


/**
 * Get children categories of a given category
 * return arr
 */
function uri_program_finder_get_children($id) {

	$args = array(
		'hierarchical' => TRUE,
		'parent' => $id
	);
	$categories = get_categories($args);
	$cats = array();
	
	foreach($categories as $c) {
		if($c->category_nicename == 'uncategorized') {
			continue;
		}
		$cats[] = array(
			'id' => $c->term_id,
			'name' => $c->cat_name,
			'slug' => $c->slug
		);
	}
	
	return $cats;
		
}
