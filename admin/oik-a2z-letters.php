<?php // (C) Copyright Bobbing Wide 2017

/**
 * Set the values for the "letter" taxonomies when saving or updating a post
 * 
 * When a post is saved the _REQUEST contains the following:
 * `
 *     [tax_input] => Array
 *       (
 *           [post_tag] => CSS.,property
 *           [letter] => Z
 *       )
 * `
 * 
 * There is also a similar array called newtag, but this is only used in the JavaScript.
 * 
 * We should be able to use the tax_input values to determine whether or not we need to automatically
 * set a missing/incorrect value from the content considering:
 * 
 * Field | Use
 * ----- | ----------------------------
 * post_type | to determine which letter taxonomies are associated and which filters are needed
 * content | To find the letter if the title is not set
 * post_title | To find the first letter
 * 
 * We could try filtering on "pre_post_tax_input" or "tax_input_pre" - both invoked by sanitize_post(),
 * but this requires us to work with the taxonomy term ids and doesn't provide other fields
 * ... which we'll have to find ourselves ( from $_REQUEST? )
 * so we may just as well hook into `wp_insert_post` or `save_post` and be done with.
 * 
 * Note: This logic will set the terms for other taxonomies with their own special filter functions.
 * e.g. The letter for an API may exclude the API's prefix.
 * 
 * @param ID $post_ID ID of the post 
 * @param object $post the post object
 * @param bool $update true if it's an update
 */ 
function oik_a2z_set_letter_taxonomies( $post_ID, $post, $update ) {
	//bw_trace2();
	/**
	 * 
	 * $taxonomies is an array keyed by the hook name with the following fields: "post_type" =>  "taxonomy" =>  "filter" =>
	 */
	$taxonomies = apply_filters( "query_post_type_letter_taxonomy_filters", array() );
	foreach ( $taxonomies as $post_type_taxonomy_filter => $data ) {
		$hook = $post_type_taxonomy_filter;
		$terms = apply_filters( $post_type_taxonomy_filter, $_REQUEST['tax_input'][ $data['taxonomy']], $post );
		//bw_trace2( $terms, "terms" );
    wp_set_post_terms( $post_ID, $terms, $data['taxonomy'] );
		
	}
}
	
	
	
	
