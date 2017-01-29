<?php // (C) Copyright Bobbing Wide 2017

/**
 * Class: OIK_a2z_display 
 * 
 * Displays letter taxonomy links for the current URL
 * 
 * @TODO - set current class for the currently selected taxonomy term
 * 
 *
 */
class OIK_a2z_display {
	public $taxonomy;
	public $post_type;

	function __construct() {
	}
	
	/** 
	 * Displays links for the selected taxonomy
	 *	
 	 */
	function display( $taxonomy="letter" ) {
		if ( '' === $taxonomy ) {
			$taxonomy = "letter";
		}
		$this->taxonomy = $taxonomy;
		$terms = $this->get_terms();
		$this->display_term_list( $terms );
		//$this->enqueue_styles();
	}
	
	/**
	 * Retrieves the terms for the taxonomy
	 */
	function get_terms() {
		$args = array( "taxonomy" => $this->taxonomy
								 , "hide_empty" => true
								 );
		$terms = get_terms( $args ); 
		return( $terms );
	}
	
	/**
	 * Displays the terms for the taxonomy
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
			$term_link = get_term_link( $term, $this->taxonomy );
			$term_string = $this->term_string( $term );
			$link = retlink( "a2z_term", esc_url( $term_link ), $term_string, $term->name );
		} else {
			$link = retlink( "a2z_term empty", null, $term->name );
		}
		echo "<li>";
		echo $link; 
		echo "</li>\n";
	}
	
	/**
	 * Returns the term string
	 * 
	 * @param object $term
	 * @return string 
	 */
	function term_string( $term ) {	
		$term_string = $term->name;
		$term_string .= retstag( "span", "count" );
		$term_string .= $term->count;
		$term_string .= retetag( "span" );
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
