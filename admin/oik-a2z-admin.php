<?php // (C) Copyright Bobbing Wide 2017

/**
 * Lazy implementation for "oik-a2z" admin menu
 *
 */
function oik_a2z_lazy_admin_menu() {
	
	
  add_submenu_page( 'oik_menu', 'oik-a2z settings', "Letter taxonomies", 'manage_categories', 'oik_a2z', "oik_a2z_options_do_page" );
}

function oik_a2z_options_do_page() {
	h2( "Letter taxonomies" );
	
	
  oik_box( null, null, "Batch run", "oik_a2z_batch_run" );
	
	bw_flush();
	


}

/**
 * Control the batch run processing
 * 
 * Display a submit button to perform the batch processing
 * If submitted then run it. 
 */

function oik_a2z_batch_run() {
	e( "Set the letter taxonomy for all posts" );
	oik_a2z_perform_batch_run();
	bw_form();
	e( wp_nonce_field( "_oik_a2z_set_letters", "oik_a2z_nonce", false, false ) );
  p( isubmit( "_oik_a2z_set_letters", "Set letters", null, "button-secondary" ) );
  etag( "form" );
  bw_flush();

}

/**
 * Performs batch run to set letters
 * 
 */
function oik_a2z_perform_batch_run() {
	$set_letters = bw_array_get( $_REQUEST, "_oik_a2z_set_letters", null );
	if ( $set_letters ) {
		$verified = bw_verify_nonce( "_oik_a2z_set_letters", "oik_a2z_nonce" );
		if ( $verified ) {
			oik_a2z_wrap_batch_run();
		} else {
			echo "Not verified";
		}
	}
}

/**
 * Wraps the oik-a2z batch run logic
 * 
 */
function oik_a2z_wrap_batch_run() {
	echo "<p>Performing batch routine to set letter taxonomy terms</p>";
	echo "<pre>";
	do_action( "run_oik-a2z.php" );
	echo "</pre>";
}	
	
