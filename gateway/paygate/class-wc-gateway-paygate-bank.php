<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

 /**
 * PayGate .
 *
 * @class			WC_Gateway_paygate_bank
 * @extends             WC_Payment_Gateway
 * @version             	0.1.0
 * @author              	studio-jt
 */
if ( !class_exists( 'WC_Gateway_PayGate_bank' ) ) :
	
class WC_Gateway_PayGate_bank extends WC_Gateway_PayGate {
        
    function __construct(){
                
        $this->id					= 'paygate_bank';
        $this->method              	= '4';
        $this->icon                 = '';
        $this->method_title			= 'PayGate ['.__('Realtime Bank Transfer', 'wc_korea_pack').']';
        $this->method_description   = 'paygate_bank';
        $this->supported_currencies = array('KRW');
        $this->notify_url           = str_replace('https:', 'http:', add_query_arg( 'wc-api', strtolower(__CLASS__), home_url( '/' ) ) ) ;
        
        //global $is_IE; IE 에서만 노출을 원하는 경우
        //if ( ! $is_IE ) $this->enabled = false;

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
               'default' => __('실시간 계좌이체 [ActiveX]', 'wc_korea_pack'),
               'desc_tip' => true,
            ),
        ));
    }
        
    public function is_valid_for_use() {

        if ( !in_array( get_woocommerce_currency(), apply_filters( 'wc_korea_pack_supported_currencies_bank', $this->supported_currencies ) ) ) {
            return false;
        }
        
        return true;
    }

    public function get_paygate_args( $order ) {
        $receipttoname = $order->billing_last_name.$order->billing_first_name;
        
        $args = array(
            'goodcurrency'  => 'WON',
            'socialnumber'	=> '',
            'receipttoname'	=> $receipttoname,
        );

        return $args;
    }
}
endif;