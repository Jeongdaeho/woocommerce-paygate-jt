jQuery(document).ready(function($) {
    $('select#billing_postcode').ajaxChosen({
        method:         'GET',
        url:            woocommerce_params.ajax_url,
        dataType:       'json',
        afterTypeDelay: 500,
        minTermLength:  2,
        data:       {
            action:     'wckp_json_search_zipcode',
        }
    }, function (data) {

        var terms = {};

        $.each(data, function (i, val) {
            terms[i] = val;
        });

        return terms;
    });
    
    $('select#billing_postcode').change(function (){
        
        var arr_addr = $( 'select#billing_postcode option:selected' ).text().split(" ");
        arr_addr.shift();
        if( arr_addr[arr_addr.length -1].search('~') > 0 ){
            arr_addr.pop();
        }
        
        $('#billing_address_1').val( arr_addr.join(' ') );

    });
    
    
});