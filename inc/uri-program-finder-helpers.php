<?php
/**
 * URI Program Finder Helpers
 *
 * @package uri-program-finder
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
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
