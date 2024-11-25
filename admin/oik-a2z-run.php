<?php // (C) Copyright Bobbing Wide 2016-2019

/**
 * Run oik-a2z batch processes
 * 
 * Syntax: oikwp oik-a2z.php post_type=post_type
 * from the plugin directory
 * 
 * For each post type with a "letter" taxonomy we need to 
 *
 * - create the empty terms
 * - and then set a value for each post
 * 
 * This routine runs out of memory for large databases
 * We need to be able to select the post type and/or taxonomy for which the terms should be set.
 * 
 * 
 */
function oik_a2z_lazy_run_oik_a2z() {
	//$terms = oik_a2z_get_letter_terms();
	oik_a2z_set_empty_terms();
	add_action( "oik_a2z_set_posts_terms_filters", "oik_a2z_set_posts_terms_filters", 10, 3 );
	$taxonomies = apply_filters( "query_post_type_letter_taxonomy_filters", array() );
	
	foreach ( $taxonomies as $post_type_taxonomy ) {
		$post_type = bw_array_get( $post_type_taxonomy, "post_type", null );
		$taxonomy = bw_array_get( $post_type_taxonomy, "taxonomy", null );
		$filter = bw_array_get( $post_type_taxonomy, "filter", null );
		if ( $post_type && $taxonomy ) {
			if ( oik_a2z_is_cli() ) {
				if ( oik_a2z_setting_required( $post_type, $taxonomy ) ) {
					oik_a2z_set_posts_terms( $post_type, $taxonomy, $filter );
				} else {
					echo "Not processing: $post_type, $taxonomy " . PHP_EOL;
				}
			}   else {
				oik_a2z_set_posts_terms( $post_type, $taxonomy, $filter );
			}
		}
	}

}

/**
 * Determines if the taxonomy terms should be reset.
 *
 * @param string $post_type
 * @param string $taxonomy
 * @return bool true if required
 */
function oik_a2z_setting_required( $post_type, $taxonomy ) {
	$required = false;
	$required_post_type = oik_batch_query_value_from_argv( "post_type", false );
	$required_taxonomy = oik_batch_query_value_from_argv( "taxonomy", false );
	echo "required_post_type $required_post_type" . PHP_EOL;
	if ( true === $required_post_type || $required_post_type == $post_type ) {
		$required = true;
	}	elseif ( true === $required_taxonomy || $required_taxonomy == $taxonomy ) {
		$required = true;
	}
	return( $required );
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
	oik_require( "includes/bw_posts.php" );
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
		echo implode( ",", $new_terms );
		echo PHP_EOL;
		if ( $new_terms <> $terms ) {
			echo "update_terms( $post->ID );" . PHP_EOL;
			$term_ids = oik_a2z_query_term_ids( $new_terms, $taxonomy );
			$result = wp_set_object_terms( $post->ID, $term_ids, $taxonomy, false );
			if ( is_wp_error( $result ) ) {
				bw_trace2( $result, "result", false );
				gob();
			}
		}	else {
			// No change so no need to update
		}
	}
}
