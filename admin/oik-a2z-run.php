<?php // (C) Copyright Bobbing Wide 2016

/**
 * Run oik-a2z batch processes
 * 
 *
 * 
 * For each post type with a "letter" taxonomy we need to 
 *
 * - create the empty terms
 * - and then set a value for each post
 * 
 */
function oik_a2z_lazy_run_oik_a2z() {

	//$terms = oik_a2z_get_letter_terms();
	oik_a2z_set_empty_terms();
	add_action( "oik_a2z_set_posts_terms_filters" "oik_a2z_set_posts_terms_filter", 10, 3 );
	
	$taxonomies = array( array( "post_type" => "post", "taxonomy" => "letter", "filter" => "oik_a2z_first_letter" ) );
	foreach ( $taxonomies as $post_type_taxonomy ) {
		$post_type = bw_array_get( $post_type_taxonomy, "post_type", null );
		$taxonomy = bw_array_get( $post_type_taxonomy, "taxonomy", null );
		$filter = bw_array_get( $post_type_taxonomy, "filter", null );
		if ( $post_type && $taxonomy ) {
			oik_a2z_set_posts_terms( $post_type, $taxonomy, $filter );
		}

}

/**
 * Set empty terms
 *
 * Create entries for each term we'd expect to find in a letter category.
 *
 * @TODO Allow user selection of the letter terms
 *
 * @param string $taxonomy
 * @param array|string $terms 
 */
function oik_a2z_set_empty_terms( $taxonomy='letter', $terms=null ) {
	$terms = oik_a2z_get_letter_terms( $terms );
	foreach ( $terms as $term ) {
		//wp_create_term( $tag_name, $taxonomy );
		$args = array( "name" => $term
								 , "taxonomy" => $taxonomy
								 , "description" => "$taxonomy $term"
								 );

		wp_insert_term( $term, $taxonomy, $args ); 
	}
}

/**
 * Set posts terms
 *
 * @param string $post_type
 * @param string $taxonomy
 *  
 */
function oik_a2z_set_posts_terms( $post_type, $taxonomy ); 
	do_action( "oik_a2z_set_posts_terms_filters", $post_type, $taxonomy, $filter );
	$args = array( "post_type" => $post_type 
							 , "numberposts" => -1
							 );
	$posts = bw_get_posts( $args );
	foreach ( $posts as $post ) {
		$terms = get_terms( $post->ID, $taxonomy );
		$new_terms = apply_filters( "oik_a2z_query_terms_" . $post_type . "_". $taxonomy, $terms, $post );
		if ( $new_terms <> $terms ) {
			echo "update_terms( $post->ID );" . PHP_EOL;
		}
}

/**
 * Implement "oik_a2z_set_post_terms_filters" for oik-a2z
 */

function oik_a2z_set_posts_terms_filters( $post_type, $taxonomy, $filter ) {
	add_filter( "oik_a2z_query_terms_" . $post_type . "_" . $taxonomy, $filter, 10, 2 ); 
}

/**
 * 
 */
function oik_a2z_first_letter( $terms, $post ) {
	return( $terms );
}
		
 

