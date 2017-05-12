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
 * 
 */
function uri_program_finder_shortcode($attributes, $content, $shortcode) {
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
		echo 'Cats go here.';
		$output = ob_get_clean();
		return $output;
		
}
add_shortcode( 'programs-categories', 'uri_program_finder_shortcode' );