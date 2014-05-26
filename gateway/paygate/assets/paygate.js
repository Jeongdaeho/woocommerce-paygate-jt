function getPGIOresult() {
        verifyReceived(getPGIOElement("tid"));
        var replycode = getPGIOElement('replycode');
        var replyMsg = getPGIOElement('replyMsg');
        
		if( replycode=='0000' ){
			jQuery('#PGIOForm').submit();
	  	} else {
			//거래 실패 처리
			alert( replyMsg + "["+ replycode + "]" + wckp.message_failure );
	    	window.location=wckp.cart_url;
		}
}

jQuery(document).ready(function($) {	
    $("#submit_paygate_payment_form").click(function (){
        $('#PGIOForm').hide();
            doTransaction(document.PGIOForm);
    });

});
