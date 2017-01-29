<?php // (C) Copyright Bobbing Wide 2016, 2017

/**
 * Implement "query_post_type_letter_taxonomy_filters" for oik-a2z
 *
 * @param array $taxonomies
 * @return array updated with the standard first letter filter 
 */
function oik_a2z_query_post_type_letter_taxonomy_filters( $taxonomies ) {
	$post_types = get_post_types();
	foreach ( $post_types as $post_type ) { 
		echo $post_type . PHP_EOL;
		if ( is_object_in_taxonomy( $post_type, "letter" ) ) {
			$taxonomies[] = array( "post_type" => $post_type, "taxonomy" => "letter", "filter" => "oik_a2z_first_letter" );
		}	
	}
	return( $taxonomies );
}

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
 * 
 */
function oik_a2z_lazy_run_oik_a2z() {
	//$terms = oik_a2z_get_letter_terms();
	oik_a2z_set_empty_terms();
	add_action( "oik_a2z_set_posts_terms_filters", "oik_a2z_set_posts_terms_filters", 10, 3 );
	add_filter( "query_post_type_letter_taxonomy_filters", "oik_a2z_query_post_type_letter_taxonomy_filters" );
	$taxonomies = apply_filters( "query_post_type_letter_taxonomy_filters", array() );
	
	foreach ( $taxonomies as $post_type_taxonomy ) {
		$post_type = bw_array_get( $post_type_taxonomy, "post_type", null );
		$taxonomy = bw_array_get( $post_type_taxonomy, "taxonomy", null );
		$filter = bw_array_get( $post_type_taxonomy, "filter", null );
		if ( $post_type && $taxonomy ) {
			oik_a2z_set_posts_terms( $post_type, $taxonomy, $filter );
		}
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
 * @param string $filter
 */
function oik_a2z_set_posts_terms( $post_type, $taxonomy, $filter ) { 
	echo "$post_type $taxonomy $filter " . PHP_EOL;
	do_action( "oik_a2z_set_posts_terms_filters", $post_type, $taxonomy, $filter );
	$args = array( "post_type" => $post_type 
							 , "numberposts" => -1
							 , "post_parent" => '.'
							 , "post_status" => "any"
							 );
	oik_require( "includes/bw_posts.inc" );							
	$posts = bw_get_posts( $args );
	foreach ( $posts as $post ) {
		$terms = wp_get_object_terms( $post->ID, $taxonomy, array( "fields" => "names" ) );
		echo $post->post_type;
		echo " ";
		echo $post->ID;
		echo " ";
		echo $post->post_title;
		echo " ";
		echo bw_array_get( $terms, 0, null );
		$new_terms = apply_filters( "oik_a2z_query_terms_" . $post_type . "_". $taxonomy, $terms, $post );
		if ( $new_terms <> $terms ) {
			echo "update_terms( $post->ID );" . PHP_EOL;
			wp_set_object_terms( $post->ID, $new_terms, $taxonomy, false );
			//gob();
		}	else {
			// No change so no need to update
		}
	}
}

/**
 * Implement "oik_a2z_set_post_terms_filters" action for oik-a2z
 *
 * @param string $post_type
 * @param string $taxonomy 
 * @param string $filter
 */
function oik_a2z_set_posts_terms_filters( $post_type, $taxonomy, $filter ) {
	add_filter( "oik_a2z_query_terms_" . $post_type . "_" . $taxonomy, $filter, 10, 2 ); 
}

/**
 * Implement "oik_a2z_query_terms_post_filter" with default logic
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
 * 0..9          | same |
 * _             | _    | @TODO to be completed
 * [             | [    |
 * anything else | ?    | 
 
 * 
 * @param array $terms - current values - there may be more than one - can you think of a good reason?
 * @param object $post
 * @return array replaced by the new term
 */
function oik_a2z_first_letter( $terms, $post ) {
	//print_r( $terms );
	//print_r( $post );
	$string = trim( $post->post_title );
	if ( !$string ) {
		$string = trim( $post->post_content );
	}
	$new_term = ucfirst( substr( $string, 0, 1 ) );
	$new_term = remove_accents( $new_term );
	echo "New term: $new_term" . PHP_EOL ;
	$terms[0] = $new_term;
	//print_r( $terms );
	return( $terms );
}
