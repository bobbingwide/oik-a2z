<?php // (C) Copyright Bobbing Wide 2017

/**
 * Implements [bw_terms] shortcode 
 * 
 * The oik_a2z_display action echos the output
 * This is not what we want for a shortcode.
 * So we can either use output buffer logic or change the action.d 
 
 *
 * @param array $atts shortcode parameters
 * @param string $content not expected
 * @param string $tag - shortcode tag
 * @return string generated HTML
 */
function oik_a2z_terms( $atts, $content, $tag ) {
	$taxonomy = bw_array_get_from( $atts, "taxonomy,0", "letter" ); 
	ob_start();
	do_action( "oik_a2z_display", $taxonomy, $atts );
	$contents = ob_get_contents();
	ob_end_clean();
	return( $contents );
}

/**
 * Help hook for bw_terms
 */
function bw_terms__help( $shortcode="bw_terms" ) {
	return( "Display taxonomy terms links" );
}


/**
 * Syntax hook for bw_terms
 *
 * @TODO Do we need orderby and order parms?
 
 *
 */
function bw_terms__syntax( $shortcode="bw_terms" ) {
	$syntax = array( "taxonomy" => bw_skv( "letter", "category|post_tag|<i>taxonomy_name</i>", "Taxonomy to display" )
								 , "count" => bw_skv( false, true, "Include term count" )
								 , "class" => bw_skv( "class", "<i>text</i>", "CSS class names" )
								 );
	return( $syntax );
}							
