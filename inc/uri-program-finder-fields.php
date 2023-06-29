<?php
/**
 * Define the custom post type, its taxonomy, and its fields.
 *
 * @package uri-program-finder
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


/**
 * Declare the custom post type
 */
function uri_program_finder_create_program_post_type() {

	register_post_type(
		'program',
		array(
			'label' => 'Program',
			'description' => 'Majors, etc.',
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'capability_type' => 'post',
			'hierarchical' => true,
			'rewrite' => array( 'slug' => 'program' ),
			'query_var' => true,
			'has_archive' => true,
			'exclude_from_search' => false,
			'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'author' ),
			'taxonomies' => array( 'post_tag', 'category', 'careers' ),
			'labels' => array(
				'name' => 'Program',
				'singular_name' => 'Programs',
				'menu_name' => 'Programs',
				'add_new' => 'Add Program',
				'add_new_item' => 'Add New Program',
				'edit' => 'Edit',
				'edit_item' => 'Edit Program',
				'new_item' => 'New Program',
				'view' => 'View Program',
				'view_item' => 'View Program',
				'search_items' => 'Search Program',
				'not_found' => 'No Programs Found',
				'not_found_in_trash' => 'No Programs Found in Trash',
				'parent' => 'Parent Program',
			),
			'menu_icon'   => 'dashicons-welcome-learn-more',
		)
		);

	uri_program_finder_create_taxonomy();

}
add_action( 'init', 'uri_program_finder_create_program_post_type' );


/**
 * Create a custom career taxonomy for programs
 */
function uri_program_finder_create_taxonomy() {
	$taxonomy_args = array(
		'hierarchical' => false,
		'labels' => array(
			'name' => _x( 'Careers', 'taxonomy general name', 'uri' ),
			'singular_name' => _x( 'Career', 'taxonomy singular name', 'textduriomain' ),
			'search_items' => __( 'Search Careers', 'uri' ),
			'popular_items' => __( 'Popular Careers', 'uri' ),
			'all_items' => __( 'All Careers', 'uri' ),
			'parent_item' => null,
			'parent_item_colon' => null,
			'edit_item' => __( 'Edit Career', 'uri' ),
			'update_item' => __( 'Update Career', 'uri' ),
			'add_new_item' => __( 'Add New Career', 'uri' ),
			'new_item_name' => __( 'New Career Name', 'uri' ),
			'separate_items_with_commas' => __( 'Separate careers with commas (like tags)', 'uri' ),
			'add_or_remove_items' => __( 'Add or remove careers', 'uri' ),
			'choose_from_most_used' => __( 'Choose from the most used careers', 'uri' ),
			'not_found' => __( 'No careers found.', 'uri' ),
			'menu_name' => __( 'Careers', 'uri' ),
		),
		'show_ui' => true,
		'show_admin_column' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'career' ),
		'singular_label' => 'Career',
	);

	register_taxonomy( 'careers', array( 'program' ), $taxonomy_args );
}



/**
 * Define the custom fields
 */

if ( function_exists( 'register_field_group' ) ) {
	register_field_group(
		array(
			'id' => 'acf_program-fields',
			'title' => 'Program Fields',
			'fields' => array(
				array(
					'key' => 'field_5eab14e914046',
					'label' => 'Accreditation',
					'name' => 'accreditation',
					'instructions' => 'List any program accreditation(s)',
					'type' => 'wysiwyg',
					'default_value' => '',
					'toolbar' => 'full',
					'media_upload' => 'no',
				),
				array(
					'key' => 'field_5eab151814047',
					'label' => 'Specializations',
					'name' => 'specializations',
					'instructions' => 'List any degree specializations',
					'type' => 'wysiwyg',
					'default_value' => '',
					'toolbar' => 'full',
					'media_upload' => 'no',
				),
				array(
					'key' => 'field_5eab155214048',
					'label' => 'Classes Offered',
					'name' => 'classes_offered',
					'instructions' => 'List when and where courses for this program are offered',
					'type' => 'wysiwyg',
					'default_value' => '',
					'toolbar' => 'full',
					'media_upload' => 'no',
				),
				array(
					'key' => 'field_5a9078363b98f',
					'label' => 'Time to Completion',
					'name' => 'time_to_completion',
					'type' => 'wysiwyg',
					'default_value' => '',
					'toolbar' => 'full',
					'media_upload' => 'no',
				),
				array(
					'key' => 'field_5a90787f3b990',
					'label' => 'Entry Term',
					'name' => 'application_deadline',
					'type' => 'wysiwyg',
					'default_value' => '',
					'toolbar' => 'full',
					'media_upload' => 'no',
				),
				array(
					'key' => 'field_5a9077420046a',
					'label' => 'Course Schedule',
					'name' => 'course_schedule',
					'type' => 'text',
					'instructions' => 'URL to course schedule(s)',
					'default_value' => 'https://uri.edu/course-schedule',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'formatting' => 'html',
					'maxlength' => '',
				),
				array(
					'key' => 'field_5a9076e700469',
					'label' => 'Course Descriptions',
					'name' => 'course_descriptions',
					'type' => 'text',
					'instructions' => 'URL to course descriptions',
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'formatting' => 'html',
					'maxlength' => '',
				),
				array(
					'key' => 'field_5a907649020b3',
					'label' => 'Curriculum Sheets / Advising',
					'name' => 'curriculum_sheets',
					'type' => 'text',
					'instructions' => 'URL to advising (undergrad default display is "Curriculum Sheets" and links to uri.edu/advising/curriculum-sheets-all)',
					'default_value' => 'https://web.uri.edu/advising/curriculum-sheets-all/',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'formatting' => 'html',
					'maxlength' => '',
				),
				array(
					'key' => 'field_5a90756aaed61',
					'label' => 'Catalog Info',
					'name' => 'catalog_info',
					'type' => 'text',
					'instructions' => 'URL of the corresponding catalog page',
					'default_value' => '',
					'placeholder' => 'https://web.uri.edu/catalog/music/',
					'prepend' => '',
					'append' => '',
					'formatting' => 'html',
					'maxlength' => '',
				),
				array(
					'key' => 'field_5a907505aed60',
					'label' => 'Department Website',
					'name' => 'department_website',
					'type' => 'text',
					'instructions' => 'URL of the program\'s home department',
					'default_value' => '',
					'placeholder' => 'https://uri.edu/department',
					'prepend' => '',
					'append' => '',
					'formatting' => 'none',
					'maxlength' => '',
				),
				array(
					'key' => 'field_5a907649020b1',
					'label' => 'Admission Info',
					'name' => 'admission_info',
					'type' => 'text',
					'instructions' => 'URL to admission information',
					'default_value' => 'https://uri.edu/admission',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'formatting' => 'html',
					'maxlength' => '',
				),
				array(
					'key' => 'field_5a907670020b2',
					'label' => 'Apply',
					'name' => 'apply',
					'type' => 'select',
					'instructions' => 'Select application portal based on degree type',
					'choices' => array(
						'https://apply.commonapp.org/Login?ma=376&tref=program-finder' => 'Undergraduate',
						'https://web.uri.edu/graduate-school/admission/' => 'Graduate',
					),
					'default_value' => 'undergraduate',
					'allow_null' => 0,
					'multiple' => 0,
				),
				array(
					'key' => 'field_5f7f3423326e4',
					'label' => 'Accelerated program language',
					'name' => 'accelerated_language',
					'type' => 'text',
					'instructions' => 'Modify the display language for accelerated programs (if applicable)',
					'default_value' => '',
					'placeholder' => 'Optional bachelor&#39;s to master&#39;s in five years',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				),
				array(
					'key' => 'field_649dad533ea3c',
					'label' => 'Accelerated program link',
					'name' => 'accelerated_link',
					'type' => 'text',
					'instructions' => 'URL to accelerated program department page (if applicable)',
					'default_value' => '',
					'placeholder' => 'https://www.uri.edu/programs/abm/',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'program',
						'order_no' => 0,
						'group_no' => 0,
					),
				),
			),
			'options' => array(
				'position' => 'normal',
				'layout' => 'default',
				'hide_on_screen' => array(),
			),
			'menu_order' => 0,
		)
		);
}
