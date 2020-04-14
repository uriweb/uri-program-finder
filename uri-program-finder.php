<?php
/**
 * Plugin Name: URI Program Finder
 * Plugin URI: http://www.uri.edu
 * Description: Program finder tools
 * Version: 1.3.0
 * Author: URI Web Communications
 * Author URI:
 *
 * @package uri-program-finder
 * @author: John Pennypacker <jpennypacker@uri.edu>
 * @author: Brandon Fuller <bjcfuller@uri.edu>
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

define( 'URI_PROGRAM_FINDER_PATH', plugin_dir_path( __FILE__ ) );


/**
 * Returns version from package.json to be used for cache busting
 *
 * @return str
 */
function uri_program_finder_cache_buster() {
	static $cache_buster;
	if ( empty( $cache_buster ) && function_exists( 'get_plugin_data' ) ) {
		$values = get_plugin_data( URI_PROGRAM_FINDER_PATH . 'uri-program-finder.php', false );
		$cache_buster = $values['Version'];
	} else {
		$cache_buster = gmdate( 'Ymd', strtotime( 'now' ) );
	}
	return $cache_buster;
}


/**
 * Include js and css
 * These are enqueued only when the shortcode is run
 */
function uri_program_finder_enqueues() {
	$values = array(
		'base' => get_site_url(),
	);

	$plugin_handle = 'uri-program-finder';
	wp_register_script( $plugin_handle, plugins_url( '/js/programs.built.js', __FILE__ ), array(), uri_program_finder_cache_buster(), true );
	wp_localize_script( $plugin_handle, 'URIProgramFinder', $values );
	wp_enqueue_script( $plugin_handle );

	wp_register_script( 'uri-program-finder-chosen-js', plugins_url( '/chosen/chosen.jquery.min.js', __FILE__ ), array(), uri_program_finder_cache_buster(), true );
	wp_enqueue_script( 'uri-program-finder-chosen-js' );

	wp_register_style( 'uri-program-finder', plugins_url( '/css/programs.built.css', __FILE__ ), array(), uri_program_finder_cache_buster(), 'all' );
	wp_enqueue_style( 'uri-program-finder' );

	wp_register_style( 'uri-program-finder-chosen-css', plugins_url( '/chosen/chosen.min.css', __FILE__ ), array(), uri_program_finder_cache_buster(), 'all' );
	wp_enqueue_style( 'uri-program-finder-chosen-css' );
}

// Include API
include( URI_PROGRAM_FINDER_PATH . 'inc/uri-program-finder-api.php' );

// Include custom fields
include( URI_PROGRAM_FINDER_PATH . 'inc/uri-program-finder-fields.php' );

// Include template
include( URI_PROGRAM_FINDER_PATH . 'inc/uri-program-finder-templating.php' );

// Include helpers
include( URI_PROGRAM_FINDER_PATH . 'inc/uri-program-finder-helpers.php' );

// Include form
include( URI_PROGRAM_FINDER_PATH . 'inc/uri-program-finder-form.php' );

// Include shortcode
include( URI_PROGRAM_FINDER_PATH . 'inc/uri-program-finder-shortcode.php' );
