<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

 /**
 * PayGate .
 *
 * @class 		WC_Gateway_paygate_card
 * @extends		WC_Payment_Gateway
 * @version		0.1.0
 * @author 		studio-jt
 */
 if ( !class_exists( 'WC_Gateway_PayGate_card' ) ) :
	 
class WC_Gateway_PayGate_card extends WC_Gateway_PayGate {
	
	function __construct(){
		
		$this->id 					= 'paygate_card';
		$this->method 				= 'card';
		$this->icon 				= '';
		$this->method_title 		= 'PayGate [Card]';
		$this->method_description	= 'paygate_card';
        $this->notify_url           = str_replace('https:', 'http:', add_query_arg( 'wc-api', strtolower(__CLASS__), home_url( '/' ) ) ) ;

        // Payment listener/API hook
        add_action( 'woocommerce_api_'.strtolower(__CLASS__), array( $this, 'process_payment_response' ) );
        
        add_filter('wc_korea_pack_paygate_card_args', array($this, 'paygate_card_args') );
        $this->paygate_currencies_args    = apply_filters('wc_korea_pack_paygate_currencies_args_card', array(
            'KRW' => array( 
                    'goodcurrency' => 'WON',
                    'langcode' => 'KR'
                ),
            'USD' => array(
                    'goodcurrency' => 'USD',
                    'langcode' => 'US'
            ),
            'RMB' => array(
                'goodcurrency' => 'CNY',
                'langcode' => 'CN',
            ),
            'JPY' => array(
                'goodcurrency' => 'JPY',
                'langcode' => 'JP',
            ),
        ));
        
        $this->supported_currencies = array_keys( $this->paygate_currencies_args );
        
		parent::__construct();
	}

	public function init_form_fields() {
		parent::init_form_fields();
		
		$this->form_fields = array_merge( $this->form_fields, array(
			'title' => array(
				'title' => __('Title', 'wc_korea_pack'),
				'type' => 'text',
				'description' => __('This controls the title which the user sees during checkout.', 'wc_korea_pack'),
				'default' => __('Card Payment', 'wc_korea_pack'),
				'desc_tip' => true,
			),
		));
	}

    public function is_valid_for_use() {

        if ( !in_array( get_woocommerce_currency(), $this->supported_currencies ) ) {
            return false;
        }
        
        return true;
    }
	
    public function get_paygate_args( $order ) {
        
		$args = array(
            'goodcurrency'      => get_woocommerce_currency(),
            'langcode'          => '',
			'cardquota'			=> '',
			'cardexpiremonth' 	=> '',
			'cardexpireyear' 	=> '',
			'cardsecretnumber' 	=> '',
			'cardownernumber' 	=> '',
			'cardtype' 			=> '',
			'cardnumber' 		=> '',
			'cardauthcode' 		=> '',
		);

        $args = apply_filters('wc_korea_pack_paygate_card_args', $args, 1);
		
		return $args;
	}
    
    public function paygate_card_args( $args ) {
        
        if( $paygate_currency = $this->paygate_currencies_args[ $args['goodcurrency'] ] ) {
            
            $args['langcode']       = $paygate_currency['langcode'];
            $args['goodcurrency']   = $paygate_currency['goodcurrency'];
        }
        
        return $args;
    }
    
}
endif;