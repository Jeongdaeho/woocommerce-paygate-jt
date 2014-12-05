<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

 /**
 * PayGate .
 *
 * @class       WC_Gateway_PayGate_virtual
 * @extends     WC_Payment_Gateway
 * @version     0.1.0
 * @author      studio-jt
 */

 if ( !class_exists( 'WC_Gateway_PayGate_virtual' ) ) :

class WC_Gateway_PayGate_virtual extends WC_Gateway_PayGate {

    function __construct(){

        $this->id                   = 'paygate_virtual';
        $this->method               = '7';
        $this->icon                 = '';
        $this->method_title         = 'PayGate [가상계좌]';
        $this->method_description   = 'paygate_virtual';
        $this->supported_currencies = array('KRW');
        $this->notify_url           = str_replace('https:', 'http:', add_query_arg( 'wc-api', strtolower(__CLASS__), home_url( '/' ) ) ) ;

        // Payment listener/API hook
        add_action( 'woocommerce_api_'.strtolower(__CLASS__), array( $this, 'process_payment_response' ) );

        parent::__construct();
    }

    public function init_form_fields() {
        parent::init_form_fields();

        $this->form_fields = array_merge( $this->form_fields, array(
            'title' => array(
                'title' => __('Title', 'wc_korea_pack'),
                'type' => 'text',
                'description' => __('This controls the title which the user sees during checkout.', 'wc_korea_pack'),
                'default' => __('가상계좌', 'wc_korea_pack'),
                'desc_tip' => true,
            ),
        ));
    }

    public function is_valid_for_use() {

        if ( !in_array( get_woocommerce_currency(), apply_filters( 'wc_korea_pack_supported_currencies_virtual', $this->supported_currencies ) ) ) {
            return false;
        }

        return true;
    }

    public function get_paygate_args( $order ) {
            $args = array(
            'goodcurrency'          => 'WON',
            'bankcode'              => '',
            'bankaccount'           => '',
            'bankexpyear'           => '',
            'receipttosocialnumber' => '',
        );


        return $args;
    }
}
endif;