/**
 * alg-wc-cog-bulk-edit-tool.js.
 *
 * @version 1.3.4
 * @since   1.3.3
 * @author  WPFactory
 */

jQuery( document ).ready( function() {
	jQuery( ".alg_wc_cog_bet_input" ).on( "focus", function() {
		jQuery( this ).closest( "tr" ).addClass( "alg_wc_cog_bet_active_row" );
	} );
	jQuery( ".alg_wc_cog_bet_input" ).on( "focusout", function() {
		jQuery( this ).closest( "tr" ).removeClass( "alg_wc_cog_bet_active_row" );
	} );
	jQuery( ".alg_wc_cog_bet_input" ).on( "change", function() {
		if ( jQuery( this ).attr( "initial-value" ) != jQuery( this ).val() ) {
			jQuery( this ).closest( "td" ).addClass( "alg_wc_cog_bet_modified_row" );
		} else {
			jQuery( this ).closest( "td" ).removeClass( "alg_wc_cog_bet_modified_row" );
		}
	} );
} );
