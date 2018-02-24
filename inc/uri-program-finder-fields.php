<?php
/**
 * Define the custom post type, its taxonomy, and its fields.
 */


/**
 * Declare the custom post type
 */

function uri_program_finder_post_type_maker() {

	register_post_type('program', array(
		'label' => 'Program',
		'description' => 'Majors, etc.',
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'capability_type' => 'post',
		'hierarchical' => true,
		'rewrite' => array('slug' => 'program'),
		'query_var' => true,
		'has_archive' => true,
		'exclude_from_search' => false,
		'supports' => array('title','editor','excerpt','thumbnail','revisions','author'),
		'taxonomies' => array('post_tag', 'category', 'careers' ),
		'labels' => array (
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
	));



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
		'rewrite' => array('slug' => 'career'),
		'singular_label' => 'Career'
	);
	
	register_taxonomy('careers', array( 'program' ), $taxonomy_args);


}
add_action('init', 'uri_program_finder_post_type_maker');



/**
 * Define the custom fields
 */

if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_program-fields',
		'title' => 'Program Fields',
		'fields' => array (
			array (
				'key' => 'field_5a907505aed60',
				'label' => 'Department Website',
				'name' => 'department_website',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => 'https://uri.edu/department',
				'prepend' => '',
				'append' => '',
				'formatting' => 'none',
				'maxlength' => '',
			),
			array (
				'key' => 'field_5a90756aaed61',
				'label' => 'Catalog Info',
				'name' => 'catalog_info',
				'type' => 'text',
				'instructions' => 'Enter the URL of the corresponding catalog page.',
				'default_value' => '',
				'placeholder' => 'https://web.uri.edu/catalog/music/',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_5a9076e700469',
				'label' => 'Course Descriptions',
				'name' => 'course_descriptions',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
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
			array (
				'key' => 'field_5a907670020b2',
				'label' => 'Apply',
				'name' => 'apply',
				'type' => 'select',
				'choices' => array (
					'undergraduate' => 'Undergraduate',
					'graduate' => 'Graduate',
				),
				'default_value' => 'undergraduate',
				'allow_null' => 0,
				'multiple' => 0,
			),
			array (
				'key' => 'field_5a9077420046a',
				'label' => 'Course Schedule',
				'name' => 'course_schedule',
				'type' => 'text',
				'instructions' => 'I\'m sorry.',
				'default_value' => 'https://uri.edu/course-schedule',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_5a9078363b98f',
				'label' => 'Time to Completion',
				'name' => 'time_to_completion',
				'type' => 'wysiwyg',
				'default_value' => '',
				'toolbar' => 'full',
				'media_upload' => 'no',
			),
			array (
				'key' => 'field_5a90787f3b990',
				'label' => 'Application Deadline',
				'name' => 'application_deadline',
				'type' => 'wysiwyg',
				'default_value' => '',
				'toolbar' => 'full',
				'media_upload' => 'no',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'program',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'default',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
}
