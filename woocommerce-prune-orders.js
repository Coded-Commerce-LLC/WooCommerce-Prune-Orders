
// DOM Loaded
jQuery( document ).ready( function( $ ) {

	// Select All Plugin Buttons
	$(
		'a.prune_cancelled_orders, ' +
		'a.prune_cancelled_orders, ' +
		'a.prune_completed_orders, ' +
		'a.prune_failed_orders, ' +
		'a.prune_pending_orders, ' +
		'a.prune_refunded_orders'

	// On Click
	).click( function() {

		// Prompt For Cutoff Date
		var button = $( this );
		var today = new Date();
		var date = prompt(
			"Please enter a cutoff date MM/DD/YYYY to trim orders up to.",
			today.getMonth() + '/' + today.getDate() + '/' + today.getFullYear()
		);

		// Handle Cancellation
		if( date == null || date == '' ) {
			return false;
		}

		// Send Value To PHP
		var _href = button.attr( 'href' );
		button.attr( 'href', _href + '&post_date=' + date );
		return true;

	} );

} );
