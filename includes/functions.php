<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


function wc_korea_pack_paygate_args( $args ) {

    extract($args);
    $paygate_currency = array('KRW' => 'KR', 'USD' => 'US');

    if( $paygate_currency[ $goodcurrency ] ) {
        $args['langcode'] = $paygate_currency[ $goodcurrency ];
    }
    
    return $args;
}

//paygate_args filter
add_filter('wc_korea_pack_paygate_args', 'wc_korea_pack_paygate_args');

