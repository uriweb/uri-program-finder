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


include( URI_PROGRAM_FINDER_PATH . 'inc/uri-program-finder-api.php' );

include( URI_PROGRAM_FINDER_PATH . 'inc/uri-program-finder-fields.php' );

include( URI_PROGRAM_FINDER_PATH . 'inc/uri-program-finder-templating.php' );

// This URL pull multiple categories (need to remove page title):
// https://www.uri.edu/programs/?cat=24,26
/**
 * Loads up the javascript
 */
function uri_program_finder_scripts() {
	$values = array(
		'base' => get_site_url(),
	);

	$plugin_handle = 'uri-program-finder';
	// Register the script like this for a plugin:
	wp_register_script( $plugin_handle, plugins_url( '/js/programs.built.js', __FILE__ ) );
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
 * Get a top-level category id by name
 *
 * @param str $needle   the name of the category.
 */
function uri_program_finder_get_program_category_id( $needle = 'Program Type' ) {
	// return 136; //14 on local
	static $parent = false;

	if ( false !== $parent ) {
		return $parent;
	}

	// get top-level categories
	$categories = uri_program_finder_get_children( 0 );

	foreach ( $categories as $c ) {
		if ( $c['name'] == $needle ) {
			$parent = $c['id'];
		}
	}

	return $parent;

}

/**
 * Get the program type categories for a given post
 *
 * @param int $id is the post id.
 */
function uri_program_finder_get_post_categories( $id ) {

	$args = array(
		'parent' => uri_program_finder_get_program_category_id(),
	);
	$cats = wp_get_post_categories( get_the_ID(), $args );
	$categories = array();
	foreach ( $cats as $c ) {
		$cat = get_term( $c );
		$categories[] = array(
			'term_id' => $cat->term_id,
			'name' => $cat->name,
			'description' => $cat->description,
			'slug' => $cat->slug,
		);
	}

	return $categories;

}



/**
 * Shortcode callback -- generates a form, loads a script
 */
function uri_program_finder_shortcode( $attributes, $content, $shortcode ) {

	uri_program_finder_scripts();
	uri_program_finder_styles();
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
add_shortcode( 'programs-categories', 'uri_program_finder_shortcode' );


/**
 * Create the HTML for the form
 *
 * @param arr $categories arrays used to build select menus.
 * @return str
 */
function uri_program_finder_make_form( $categories ) {

	// get the search string value from the url
	$query_value = '';
	if ( isset( $_GET['terms'] ) ) {
		$query_value = sanitize_title( $_GET['terms'] );
	}

	$text_input = '<input type="text" id="search-programs" name="s" value="' . $query_value . '" placeholder="Search Programs" />';

	$output = '<div id="program-finder">';

	$output .= '<form action="' . get_site_url() . '" method="GET" class="program-finder-nojs">';
	$output .= '<fieldset>';
	$output .= '<label for="search-programs">' . __( 'Search programs by keyword', 'uri' ) . '</label>';
	$output .= $text_input;
	$output .= '<input type="submit" value="Go" />';
	$output .= '</fieldset>';
	$output .= '</form>';

	foreach ( $categories as $c ) {
		$subcats = uri_program_finder_get_children( $c['id'] );
		array_unshift(
			 $subcats,
			array(
				'id' => '',
				'name' => $c['name'],
			)
			);

		$output .= '<form action="' . get_site_url() . '" method="GET" class="program-finder-nojs">';
		$output .= '<fieldset>';
		$output .= uri_program_finder_make_select( $c, $subcats, false );
		$output .= '<input type="submit" value="Go" />';
		$output .= '</fieldset>';
		$output .= '</form>';
	}

	// now create the js form
	$output .= '<form class="has-js" style="display: none;">';
	$output .= $text_input;
	$output .= '<button type="button" id="js-form-reset">Clear</button>';

	foreach ( $categories as $c ) {
		$subcats = uri_program_finder_get_children( $c['id'] );
		array_unshift(
			 $subcats,
			array(
				'id' => '',
				'name' => $c['name'],
			)
			);
		$output .= uri_program_finder_make_select( $c, $subcats, true );
	}
	$output .= '</form';
	$output .= '</div>';

	return $output;
}


/**
 * Turn an array into a select element
 *
 * @return str
 */
function uri_program_finder_make_select( $cat, $items, $is_js_form ) {
	$m = ( $is_js_form ) ? 'multiple' : '';
	$n = ( $is_js_form ) ? $cat['slug'] : 'cat';
	$output = '<label><span>' . $cat['name'] . '</span><select name="' . $n . '" data-placeholder="Choose ' . $cat['name'] . 's" ' . $m . '>';
	foreach ( $items as $item ) {
		$selected = ( uri_program_finder_is_selected( $item['id'], $cat['slug'] ) ) ? ' selected="selected"' : '';
		$output .= '<option value="' . $item['id'] . '"' . $selected . '>' . $item['name'] . '</option>';
	}
	$output .= '</select></label>';
	return $output;
}


/**
 * Checks if an id number is in the querystring under ids
 *
 * @return bool
 */
function uri_program_finder_is_selected( $id, $category_slug ) {
	$ids = array();
	if ( isset( $_GET[ $category_slug ] ) && preg_match( '/[\d,]*/', $_GET[ $category_slug ] ) ) {
		$ids = explode( ',', $_GET[ $category_slug ] );
	}
	return in_array( $id, $ids );
}


/**
 * Get children categories of a given category
 *
 * @param int   $id         the parent id to return.
 * @param mixed $exclude    (term ids to skip -- either an int or array of ints).
 * @return arr
 */
function uri_program_finder_get_children( $id, $exclude = array() ) {

	$args = array(
		'hierarchical' => true,
		'parent' => $id,
		'exclude_tree' => $exclude,
	);
	$categories = get_categories( $args );
	$cats = array();

	foreach ( $categories as $c ) {
		if ( 'uncategorized' == $c->category_nicename ) {
			continue;
		}
		$cats[] = array(
			'id' => $c->term_id,
			'name' => $c->cat_name,
			'slug' => $c->slug,
		);
	}

	return $cats;

}
