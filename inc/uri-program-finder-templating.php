<?php
/**
 * Code to find the appropriate template
 * This allows templates to be included with the plugin, but overridden by a theme.
 *
 * @package uri-program-finder
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


/**
 * Template loader.
 *
 * The template loader will check if WP is loading a template
 * for a specific Post Type and will try to load the template
 * from out 'templates' directory.
 *
 * @param   string $template    Template file that is being loaded.
 * @return  string              Template file that should be loaded.
 */
function uri_program_finder_template_loader( $template ) {
	
	if ( is_single() && get_post_type() === 'program' ) {
	
		// if it's a program page, then override $template with the custom one
		if( is_embed() ) {
			$file = 'embed-program.php';
		} else {
			$file = 'single-program.php';
		}

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
 * 1. /themes/theme/template-parts/$template_name
 * 2. /themes/theme/$template_name
 * 3. /plugins/uri-program-finder/template-parts/$template_name.
 *
 * http://jeroensormani.com/how-to-add-template-files-in-your-plugin/
 *
 * @param   string $template_name          Template to load.
 * @param   string $template_path          Path to templates.
 * @param   string $default_path           Default path to template files.
 * @return  string                         Path to the template file.
 */
function uri_program_finder_locate_template( $template_name, $template_path = '', $default_path = '' ) {

	// echo '<pre>Template Name: ';
	// var_dump( $template_name );
	// echo '</pre>';
	// Set variable to search in woocommerce-plugin-templates folder of theme.
	if ( ! $template_path ) :
		$template_path = 'template-parts/';
	endif;

	// Set default plugin templates path.
	if ( ! $default_path ) :
		$default_path = URI_PROGRAM_FINDER_PATH . 'template-parts/'; // Path to the template folder
	endif;

	// Search template file in theme folder.
	$template = locate_template(
		 array(
			 $template_path . $template_name,
			 $template_name,
		 )
		);

	// Get plugins template file.
	if ( ! $template ) :
		$template = $default_path . $template_name;
	endif;

	return apply_filters( 'uri_program_finder_locate_template', $template, $template_name, $template_path, $default_path );

}
