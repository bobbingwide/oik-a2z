<?php 

/**
Plugin Name: oik-a2z
Plugin URI: http://www.oik-plugins.com/oik-plugins/oik-a2z
Description: Pagination by letter
Version: 0.0.0
Author: bobbingwide
Author URI: http://www.oik-plugins.com/author/bobbingwide
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

    Copyright 2016 Bobbing Wide (email : herb@bobbingwide.com )

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

//define( 'DEFAULT_TAXONOMY', "letter" );

/**
 * Function to run when loaded
 */
function oik_a2z_loaded() {
	add_action( "init", "oik_a2z_init" );
	add_action( "oik_fields_loaded", "oik_a2z_oik_fields_loaded" );
	add_action( "oik_a2z_display", "oik_a2z_display" );
	add_action( "run_oik-a2z.php", "oik_a2z_run_oik_a2z" );
	// Add further batch files if needed
	//add_action( "run_filename", "oik_a2z_run_filename" );
}

/**
 * Implement "init" for oik-a2z
 */
function oik_a2z_init() {
}

/**
 * Implement "oik_fields_loaded" for oik-a2z
 */
function oik_a2z_oik_fields_loaded() {
	bw_register_custom_tags( "letter", "post", "Letter" );
}

/**
 * Implement "oik_a2z_display" for oik-a2z
 *
 * Use this action to display links for all the categories in the selected taxonomy
 * applying to the current URL.
 * 
 * This is a bit like pagination. 
 * 
 * url&letter=A
 * url&letter=B 
 * url&letter=C
 * 
 */
function oik_a2z_display( $taxonomy, $url=null ) {
	echo "Taxonomy display for $taxonomy";
}

/**
 * Return an array of terms 
 * 
 * Letter | Meaning
 * ------ | -----------------------
 * A-Z    | Lower case or upper case letter including accented
 * 0-9    | The actual digit
 * #      | Representing the range of numeric digits 0-9
 * _      | When it really is an underscore
 * ?      | For punctuation and anything else
 * 
 * 
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
 * Run oik-a2z.php in batch
 *
 * 
 */
function oik_a2z_run_oik_a2z() {
	oik_require( "admin/oik-a2z-run.php", "oik-a2z" );
	oik_a2z_lazy_run_oik_a2z();
	
}

