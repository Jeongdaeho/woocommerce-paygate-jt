function getPGIOresult() {
        verifyReceived(getPGIOElement("tid"));
        var replycode = getPGIOElement('replycode');
        var replyMsg = getPGIOElement('replyMsg');
        
		if( replycode=='0000' ){
			 document.PGIOForm.action = wckp.notify_url;
			jQuery('#PGIOForm').submit();
	  	} else {
			//거래 실패 처리
			 alert("[" + replyMsg + "]" + wckp.message_failure );
	    	window.location=woocommerce_params.cart_url;
		}
}

jQuery(document).ready(function($) {

	/* 
	 * AJAX Form Submission
	 * 
	 * woocommerce
	 * */
	/*$('#PGIOForm')
	.submit( function() {
		var $form = $(this);

		if ( $form.is('.processing') )
			return false;

		$form.addClass('processing');

		var form_data = $form.data();

		if ( form_data["blockUI.isBlocked"] != 1 )
			$form.block({message: null, overlayCSS: {background: '#fff url(' + woocommerce_params.ajax_loader_url + ') no-repeat center', backgroundSize: '16px 16px', opacity: 0.6}});


		$.ajax({
			type: 	'POST',
			url: 		wckp.notify_url,
			data: 	$form.serialize(),
			success: 	function( code ) {
				console.log(code);
				return false;
					try {
						// Get the valid JSON only from the returned string
						if ( code.indexOf("<!--WCKP_START-->") >= 0 )
							code = code.split("<!--WCKP_START-->")[1];

						if ( code.indexOf("<!--WCKP_END-->") >= 0 )
							code = code.split("<!--WCKP_END-->")[0];

						// Parse
						var result = $.parseJSON( code );

						if (result.result=='success') {

							window.location = wckp.thanks_url;

						} else if (result.result=='failure') {
						        
						        $('.woocommerce-error, .woocommerce-message').remove();
						        $form.prepend( result.messages );
						        $form.removeClass('processing').unblock();

							$('html, body').animate({
							    scrollTop: ($('form.checkout').offset().top - 100)
							}, 1000);
							
							setTimeout( function() {
								window.location = woocommerce_params.cart_url;       
							}, 2000 );
							

						} else {
							throw "Invalid response";
						}
					}
					catch(err) {
						$('.woocommerce-error, .woocommerce-message').remove();
					  	$form.prepend( code );
						$form.removeClass('processing').unblock();
						$form.find( '.input-text, select' ).blur();

						$('html, body').animate({
						    scrollTop: ($('form.checkout').offset().top - 100)
						}, 1000);
						
                        setTimeout( function() {
                                window.location = woocommerce_params.cart_url;       
                        }, 2000 );

					}
				},
			dataType: 	"html"
		});

		return false;
	});*/
	
    $("#submit_paygate_payment_form").click(function (){
            $(this).hide();
            doTransaction(document.PGIOForm);
    });

});