<?php
/*
Plugin Name: URI Program Finder
Plugin URI: http://www.uri.edu
Description: Program finder tools
Version: 1.0
Author: URI Web Communications
Author URI: 
@author: John Pennypacker <jpennypacker@uri.edu>
@author: Brandon Fuller <bjcfuller@uri.edu>
*/

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');


include( plugin_dir_path( __FILE__ ) . 'inc/uri-program-finder-api.php');

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
 * Get the program type parent category id
 * @param str the name of the category
 */
function uri_program_finder_get_program_category_id( $needle='Program Type' ) {
	// I don't like this function... it's not efficient, it should only run once, 
	// but it runs once for each program, and that's no good.  
	// so let's cheat.
	return 136; //14 on local
	
	// @todo: make this function efficient
	
	static $parent;
	$parent = FALSE;
	
	if($parent !== FALSE) {
		return $parent;
	}
	
	// get top-level categories
	$categories = uri_program_finder_get_children(0);
	
	foreach($categories as $c) {
		if($c['name'] == $needle) {
			print_r($c);
			$parent = $c['id'];
		}
	}

	//print_r($parent);

	return $parent;

}

/**
 * Get the program type categories for a given post
 * @param int $id is the post id
 */
function uri_program_finder_get_post_categories($id) {

	$args = array(
		'parent' => uri_program_finder_get_program_category_id()
	);
	$cats = wp_get_post_categories(get_the_ID(), $args);
	$categories = array();
	foreach($cats as $c) {
		$cat = get_term($c);
		$categories[] = array(
			'term_id' => $cat->term_id,
			'name' => $cat->name,
			'description' => $cat->description,
			'slug' => $cat->slug
		);
	}
	
	return $categories;

}



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

    $categories = uri_program_finder_get_children(0);
    
    // specify what categories to remove, by slug
    $removecats = array(
        'college'
    );
    
    // specify how the remaining categories should be sorted, by slug
	$catorder = array(
        'program-type',
        'interest-area',
        'location'
	);
    
    // perform remove and sort
    foreach($categories as $i => $cat) {
        foreach($removecats as $r) {
            if ($cat['slug'] == $r) {
                unset($categories[$i]);
            }
        }
    }
	usort($categories, function ($a, $b) use ($catorder) {
			$pos_a = array_search($a['slug'], $catorder);
			$pos_b = array_search($b['slug'], $catorder);
			return $pos_a - $pos_b;
	});


    // build the form
    
	ob_start();
	
	echo '<div class="program-finder">';

	echo '<form action="' . get_site_url() . '" method="GET" class="program-finder-nojs">';
	echo '<fieldset>';
	echo '<legend>' . __('Search programs by keyword', 'uri') . '</legend>';
	echo '<input type="text" name="s" placeholder="Search Programs" />';
	echo '<input type="submit" value="Go" />';
	echo '</fieldset>';
	echo '</form>';


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
