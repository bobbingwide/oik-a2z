<?php 
/**
Plugin Name: oik-a2z
Plugin URI: https://www.oik-plugins.com/oik-plugins/oik-a2z
Description: Letter taxonomy pagination
Version: 0.2.0
Author: bobbingwide
Author URI: https://www.oik-plugins.com/author/bobbingwide
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

    Copyright 2016-2019, 2024 Bobbing Wide (email : herb@bobbingwide.com )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2,
    as published by the Free Software Foundation.

    You may NOT assume that you can use any other version of the GPL.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    The license for this software can likely be found here:
    http://www.gnu.org/licenses/gpl-2.0.html

*/

oik_a2z_loaded();

/**
 * Function to run when loaded
 */
function oik_a2z_loaded() {
	add_action( "init", "oik_a2z_init" );
	add_action( "oik_fields_loaded", "oik_a2z_oik_fields_loaded" );
	add_action( "oik_a2z_display", "oik_a2z_display", 10, 2 );
	add_action( "run_oik-a2z.php", "oik_a2z_run_oik_a2z" );
	// Add further batch files if needed
	//add_action( "run_filename", "oik_a2z_run_filename" );
	add_action( "oik_add_shortcodes", "oik_a2z_oik_add_shortcodes" );
	add_action( "oik_admin_menu", "oik_a2z_oik_admin_menu" );
}

/**
 * Implement "init" for oik-a2z
 */
function oik_a2z_init() {
}

/**
 * Implement "oik_fields_loaded" for oik-a2z
 * 
 * Here we register the Letter taxonomy to the 'post' post type
 * For any other post type this needs to be added either programmatically or using oik-types or a similar plugin.
 *  
 */
function oik_a2z_oik_fields_loaded() {
	bw_register_custom_tags( "letters", "post", "Letters" );
	add_filter( "query_post_type_letter_taxonomy_filters", "oik_a2z_query_post_type_letter_taxonomy_filters" );
	add_action( "wp_insert_post", "oik_a2z_wp_insert_post", 10, 3 );
}

/**
 * Implement "oik_a2z_display" for oik-a2z
 *
 * Use this action to display links for all the categories in the selected taxonomy.
 * 
 * @param string $taxonomy
 * @param array $atts - additional parameters - see [bw_terms] shortcode
 */
function oik_a2z_display( $taxonomy="letters", $atts=array() ) {
	oik_require( "includes/class-oik-a2z-display.php", "oik-a2z" );
	$oik_a2z_display = new OIK_a2z_display();
	$oik_a2z_display->display( $taxonomy, $atts );
}

/**
 * Return an array of terms 
 * 
 * Letter | Meaning
 * ------ | -----------------------
 * A-Z    | Lower case or upper case letter including accented
 * #      | Representing the range of numeric digits 0-9
 * _      | When it really is an underscore
 * ?      | For punctuation and anything else
 *
 * @TODO Allow user selection of the letter terms - by applying a filter
 * 
 */
function oik_a2z_get_letter_terms( $terms=null ) {
	$letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ#_?";
	$count = strlen( $letters );
	for ( $l = 0; $l < $count; $l++ ) {
		$terms[] = substr( $letters, $l, 1 );
	}
	return( $terms );
}

/**
 * Implements "query_post_type_letter_taxonomy_filters" for oik-a2z
 *
 * Returns the hook names to invoke to set the letter taxonomy for each post type to which it is associated.
 * Other plugins can implement their own filter routines, using this filter as the trigger for loading and attaching 
 * their own filter functions. 
 * 
 * Note: If a different letter based taxonomy is associated to a post type but the filter function is not defined
 * then the value will be set to whatever the user chose.
 * 
 * @param array $taxonomies
 * @return array updated with the standard first letter filters 
 */
function oik_a2z_query_post_type_letter_taxonomy_filters( $taxonomies ) {
	$taxonomies = oik_a2z_add_post_type_letter_taxonomy_filters( $taxonomies, "letters", "oik_a2z_first_letter" );
	return( $taxonomies );
}

/**
 * Adds the post type letter taxonomy filters.
 *
 * @param array $taxonomies
 * @param string $taxonomy
 * @param string $filter
 * @return array with our hooks added
 */
function oik_a2z_add_post_type_letter_taxonomy_filters( $taxonomies, $taxonomy="letters", $filter="oik_a2z_first_letter" ) {
	$post_types = get_post_types();
	foreach ( $post_types as $post_type ) { 
		//echo $post_type . PHP_EOL;
		if ( is_object_in_taxonomy( $post_type, $taxonomy ) ) {
			$hook = oik_a2z_set_posts_terms_filters( $post_type, $taxonomy, $filter );
			$taxonomies[ $hook ] = array( "post_type" => $post_type, "taxonomy" => $taxonomy, "filter" => $filter );
		}	
	}
	return( $taxonomies );
}

/**
 * Registers a post type letter taxonomy filter function.
 *
 * Note: When implementing your own hook for "query_post_type_letter_taxonomy_filters" use 
 * this function to add your own letter taxonomy hook function.
 * 
 * @param string $post_type
 * @param string $taxonomy 
 * @param string $filter
 * @return hook
 */
function oik_a2z_set_posts_terms_filters( $post_type, $taxonomy, $filter ) {
	$hook = "oik_a2z_query_terms_" . $post_type . "_" . $taxonomy;
	add_filter( $hook, $filter, 10, 2 ); 
	return( $hook );
}

/**
 * Run oik-a2z.php in batch
 *
 * 
 */
function oik_a2z_run_oik_a2z() {
	oik_require( "admin/oik-a2z-run.php", "oik-a2z" );
	oik_a2z_lazy_run_oik_a2z();
}

/**
 * Determines if running from Command Line Interface.
 * 
 * @TODO Implement as common method in oik-batch and find out why PHP was misbehaving.
 * 
 * I was getting different values for PHP_SAPI and php_sapi_name()
 * with the value of PHP_SAPI changing at run time. 
 *
 * @return bool true if Command Line Interface, false otherwise  
 */
function oik_a2z_is_cli() { 
	//echo "PHP_SAPI: ";
	//echo PHP_SAPI;
	//echo PHP_EOL;
	//echo "php_sapi_name: ";
	//echo php_sapi_name();
	//echo PHP_EOL;
	$cli = php_sapi_name();
	//$cli = PHP_SAPI;
	$is_cli = false;
	switch ( $cli ) {
		case 'cli':
			$is_cli = true;
		  break;
		
		case 'cgi-fcgi':
			$is_cli = true;
			bw_trace2( PHP_SAPI, "PHP_SAPI unexpected?", false );
			break;
		
		default:
	}
	return( $is_cli );
}


/**
 * Implements 'wp_insert_post' action for oik-a2z
 * 
 * Lazy loads the logic when it's not an auto-draft and the request contains taxonomy term input or when it's being run in batch.
 * Using the Block editor we may not see 'tax_input'
 * So how do we know when to set letter taxonomies
 * 
 * @param ID $post_ID ID of the post 
 * @param object $post the post object
 * @param bool $update true if it's an update
 */ 
function oik_a2z_wp_insert_post( $post_ID, $post, $update ) {
	if ( "auto-draft" !== $post->post_status ) {
		bw_trace2( null, null, true, BW_TRACE_VERBOSE );
		//if ( isset( $_REQUEST['tax_input'] ) || oik_a2z_is_cli() ) {
			oik_require( "admin/oik-a2z-letters.php", "oik-a2z" );
			oik_a2z_set_letter_taxonomies( $post_ID, $post, $update );
		//}
	}
}

/**
 * Implement "oik_a2z_query_terms_$post_type_$taxonomy" with default logic
 * 
 * e.g. "oik_a2z_query_terms_post_letters" for post_type: post, taxonomy: letter
 * 
 * Mapping of the first letter to a term is like this:
 * 
 * - Choose the first non blank value from post_title or post_content
 * - Simplify accented characters to A..Z
 * - Handle other special characters as we see fit.
 * 
 * Letter        | Term | Comments
 * -------       | ---- | -------------
 * A..Z          | same | uppercased first character passed through remove_accents() 
 * 0..9          | #    |
 * _             | _    | @TODO to be completed
 * [             | [    |
 * anything else | ?    | 
 
 * 
 * @param array $terms - current values - there may be more than one - can you think of a good reason?
 * @param object $post
 * @return array replaced by the new term name
 */
function oik_a2z_first_letter( $terms, $post ) {
	bw_trace2( null, null, true, BW_TRACE_VERBOSE );
	//$terms = bw_as_array( $terms );
	$string = trim( $post->post_title );
	
	//echo "PT" . $string; // **?**
	//echo PHP_EOL;
	if ( !$string ) {
		$string = trim( $post->post_content );
	}
	//echo "PC" . $string; // **?**
	//echo PHP_EOL;
	$new_term = substr( $string, 0, 1 );
	if ( ctype_digit( $new_term ) ) {
		$new_term = "#";
	}	else {
		$new_term = ucfirst( $new_term );
		//echo "New term: $new_term" . PHP_EOL ;
		$new_term = remove_accents( $new_term );
		
		//echo "New term: $new_term" . PHP_EOL ;
	}
	$new_term = esc_html( $new_term );
	$new_terms = array( $new_term );
	return( $new_terms );
}

/**
 * Queries term IDs for term names.
 * 
 * Note: This does not support hierarchical taxonomies.
 * 
 * @param array $term_names array of escaped term names
 * @param string $taxonomy the taxonomy name
 * @return array $term_ids
 */
function oik_a2z_query_term_ids( $term_names, $taxonomy ) {
	bw_trace2( null, null, true, BW_TRACE_VERBOSE );
	$term_ids = array();
	if ( is_array( $term_names ) ) {
		foreach ( $term_names as $name ) {
			$term_object = get_term_by( 'name', $name, $taxonomy );
			if ( $term_object ) {
				$term_ids[] = $term_object->term_id;
			}	else {
				// Term does not exist so it will need to be created. 
				$result = wp_insert_term( $name, $taxonomy );
					bw_trace2( $result, "result", false, BW_TRACE_VERBOSE );
				if ( is_wp_error( $result ) ) {
				} else {
					$term_ids[] = $result['term_id'];
				}
			}
		}	
	}	else { 
		//gob();
	}
	return( $term_ids );
}

/**
 * Implement "oik_add_shortcodes" to add our own shortcodes
 */
function oik_a2z_oik_add_shortcodes() {
    if ( function_exists('bw_add_shortcode')) {
        bw_add_shortcode('bw_terms', 'oik_a2z_terms', oik_path("shortcodes/oik-a2z-terms.php", "oik-a2z"), false);
    }
}


/**
 * Implement "oik_admin_menu" for oik-a2z
 *
 */
function oik_a2z_oik_admin_menu() {
  oik_require( "admin/oik-a2z-admin.php", "oik-a2z" );
  oik_a2z_lazy_admin_menu();
}




