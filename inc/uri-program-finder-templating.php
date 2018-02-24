<?php
/**
 * Code to find the appropriate template
 */

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');




/**
 * Template loader.
 *
 * The template loader will check if WP is loading a template
 * for a specific Post Type and will try to load the template
 * from out 'templates' directory.
 *
 *
 * @param	string	$template	Template file that is being loaded.
 * @return	string				Template file that should be loaded.
 */
function uri_program_finder_template_loader( $template ) {
	
	if ( is_single() && get_post_type() === 'program' ) {
	
		// if it's a people page, then override $template with the custom one
		// use "people" instead of "person" for backwards compatability.
		$file = 'single-program.php';

		if ( file_exists( uri_program_finder_locate_template( $file ) ) ) {
			$template = uri_program_finder_locate_template( $file );
		}

	}

	return $template;

}
add_filter( 'template_include', 'uri_program_finder_template_loader', 99 );



/**
 * Locate page template.
 *
 * Locate the called template.
 * Search Order:
 * 1. /themes/theme/templates/$template_name
 * 2. /themes/theme/$template_name
 * 3. /plugins/uri-people-tool/templates/$template_name.
 *
 * http://jeroensormani.com/how-to-add-template-files-in-your-plugin/
 *
 * @param 	string 	$template_name			Template to load.
 * @param 	string 	$string $template_path	Path to templates.
 * @param 	string	$default_path			Default path to template files.
 * @return 	string	Path to the template file.
 */
function uri_program_finder_locate_template( $template_name, $template_path = '', $default_path = '' ) {

// 	echo '<pre>Template Name: ';
// 	var_dump( $template_name );
// 	echo '</pre>';

	// Set variable to search in woocommerce-plugin-templates folder of theme.
	if ( ! $template_path ) :
		$template_path = 'templates/';
	endif;

	// Set default plugin templates path.
	if ( ! $default_path ) :
		$default_path = URI_PROGRAM_FINDER_PATH . 'templates/'; // Path to the template folder
	endif;

	// Search template file in theme folder.
	$template = locate_template( array(
		$template_path . $template_name,
		$template_name
	) );

	// Get plugins template file.
	if ( ! $template ) :
		$template = $default_path . $template_name;
	endif;

	return apply_filters( 'uri_program_finder_locate_template', $template, $template_name, $template_path, $default_path );

}

/**
 * Get template.
 *
 * Search for the template and include the file.
 *
 * @since 1.0.0
 *
 * @see wcpt_locate_template()
 *
 * @param string 	$template_name			Template to load.
 * @param array 	$args					Args passed for the template file.
 * @param string 	$string $template_path	Path to templates.
 * @param string	$default_path			Default path to template files.
 */
function uri_program_finder_get_template( $template_name, $args = array(), $tempate_path = '', $default_path = '' ) {

	if ( is_array( $args ) && isset( $args ) ) :
		extract( $args );
	endif;

	$template_file = uri_program_finder_locate_template( $template_name, $tempate_path, $default_path );

	if ( ! file_exists( $template_file ) ) :
		_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $template_file ), '1.0.0' );
		return;
	endif;


	include $template_file;

}

