<?php // (C) Copyright Bobbing Wide 2017

/**
 * Class: OIK_a2z_display 
 * 
 * Displays letter taxonomy links for the current URL
 * 
 * @TODO Cache the results having built an array of items then apply the class to the list item for the active term afterwards.
 * 
 */
class OIK_a2z_display {
	public $taxonomy;
	public $post_type;
	public $atts;
	public $count;

	function __construct() {
	}
	
	/** 
	 * Displays links for the selected taxonomy.
	 *	
 	 */
	function display( $taxonomy="letters", $atts=array() ) {
		if ( '' === $taxonomy ) {
			$taxonomy = "letters";
		}
		$this->taxonomy = $taxonomy;
		$this->parse_atts( $atts );
		$terms = $this->get_terms();
		if ( $terms && count( $terms ) ) {
			$this->display_term_list( $terms );
		}	
		//$this->enqueue_styles();
	}
	
	/** 
	 * Parses the attributes 
	 *
	 */
	function parse_atts( $atts ) {
		$this->atts = $atts;
		$this->count = bw_validate_torf( bw_array_get( $atts, "count", false ) );
		$this->post_type = bw_array_get( $atts, "post_type", false );
		
	}
	
	/**
	 * Determines the active term
	 * 
	 * This should be easy to find from the current query. 
	 * Note: There may not be an active term for the given taxonomy
	 * since this may not be a taxonomy archive... just the list to allow selection.
	 
	 * @param object $term the term object... or part thereof?
	 * @return bool true if this is the active term
	 */
	function query_active_term( $term ) {
		//bw_trace2();
		$is_tax = is_tax( $this->taxonomy, $term->term_id );
		bw_trace2( $is_tax, "is_tax" );
		return( $is_tax );	
	}
	
	/**
	 * Queries the term class
	 *
	 * @param object $term the term object... or part thereof?
	 * @return string the CSS classes to append to the term's list item
	 */
	function query_term_class( $term ) {
		$term_class = null;
		if ( $this->query_active_term( $term ) ) {
			$term_class = "active current";
		} 
		return( $term_class );
	}
	
	/**
	 * Retrieves the terms for the taxonomy
	 * 
	 * Note: The default orderby for get_terms is 'name' ASC.
	 * This can produce different results from wp_tag_cloud() which
	 * sorts by 'name' using uasort( $tags, '_wp_object_name_sort_cb' );
	 * 
	 * Perhaps we should do the same here! 
	 */
	function get_terms() {
		$args = array( "taxonomy" => $this->taxonomy
								 , "hide_empty" => true
								 );
		$terms = get_terms( $args ); 
		//uasort( $terms, '_wp_object_name_sort_cb' );
		if ( is_wp_error( $terms ) ) {
			return null;
		}
		return( $terms );
	}
	
	/**
	 * Displays the terms for the taxonomy.
	 *
	 */
	function display_term_list( $terms ) {
		echo "<div class=\"a2z wrap archive-pagination pagination\">";
		echo "<ul class=\"a2z {$this->taxonomy}\">";
		foreach ( $terms as $term ) {
			$this->display_term_item( $term );
		}
		echo "</ul>";
		echo "</div>";
	}
	
	/** 
	 * Displays a term item.
	
			 [0] => WP_Term Object
        (
            [term_id] => 222
            [name] => [
            [slug] => 222
            [term_group] => 0
            [term_taxonomy_id] => 222
            [taxonomy] => letter
            [description] => 
            [parent] => 0
            [count] => 28
            [filter] => raw
            [meta] => Array
                (
                )
								
		 @TODO Mark the currently selected term with class 'current'							
	 */
	function display_term_item( $term ) {
		if ( $term->count ) {
			$term_link = $this->get_term_link( $term );
			$term_string = $this->term_string( $term );
			$link = retlink( "a2z_term", esc_url( $term_link ), $term_string, $term->name );
		} else {
			bw_trace2( $term, "term empty?", false );
			$link = retlink( "a2z_term empty", null, $term->name );
		}
		$term_class = $this->query_term_class( $term );
		echo retstag( "li", $term_class );
		echo $link; 
		echo "</li>\n";
	}
	
	function get_term_link( $term ) {
		$term_link = get_term_link( $term, $this->taxonomy );
		if ( $this->post_type ) {
			$term_link = add_query_arg( "post_type", $this->post_type, $term_link );
		}
		return( $term_link );
	}
	
	/**
	 * Returns the term string
	 * 
	 * @param object $term
	 * @return string 
	 */
	function term_string( $term ) {	
		$term_string = $term->name;
		if ( $this->count ) {
			$term_string .= retstag( "span", "count" );
			$term_string .= $term->count;
			$term_string .= retetag( "span" ); 
		}			
		return( $term_string );
	}
	
	/**
	 * Enqueue the style sheet
	 * 
	 * @TODO Determine if this should be enqueued earlier to allow for quicker styling by the browser
	 */
	function enqueue_styles() {
		$timestamp = null;
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$timestamp = filemtime( oik_path( "css/oik-a2z.css", "oik-a2z") );
		}
		wp_register_style( "oik-a2z", oik_url( "css/oik-a2z.css", "oik-a2z" ), array(), $timestamp );
		wp_enqueue_style( "oik-a2z" );
	}
 
}
