jQuery(
	function() {
		jQuery( 'body' ).on(
			'change',
			'input[name="payment_method"]',
			function() {
				jQuery( 'body' ).trigger( 'update_checkout' );
			}
		);
	}
);




/* (function( $ ) {
	'use strict';

	// (js source: #quaife_payment_iframe -> target: .payment_box.payment_method_woo-quaife-pg)
	jQuery(document).ready( function() {

		if ($('#quaife_payment_iframe').length){
			$('.payment_box.payment_method_woo-quaife-pg').append(
				$('#quaife_payment_iframe').html()
			);
		}
	});

})( jQuery );
 */