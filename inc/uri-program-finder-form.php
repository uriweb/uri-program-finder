<?php
/**
 * URI Program Finder Form
 *
 * @package uri-program-finder
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


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
	$output .= '<div class="keyword-search">';
	$output .= $text_input;
	$output .= '<button type="button" id="js-form-reset">Clear</button>';
	$output .= '</div>';

	$output .= '<div class="facets">';
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
	$output .= '</div>';
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
