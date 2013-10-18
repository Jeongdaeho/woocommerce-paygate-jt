<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

 /**
 * PayGate .
 *
 * @class 			WC_Gateway_PayGate_ars
 * @extends		WC_Payment_Gateway
 * @version		0.1.0
 * @author 		studio-jt
 */

 if ( !class_exists( 'WC_Gateway_PayGate_ars' ) ) :
	 
class WC_Gateway_PayGate_ars extends WC_Gateway_PayGate {
	
	function __construct(){
		
		$this->id 					= 'paygate-ars';
		$this->method 				= '803';
		$this->class_name			= str_replace('-', '_', __CLASS__);
		$this->icon 				= '';
		$this->method_title 		= 'PayGate [ars]';
		$this->method_description	= 'paygate_ars';
		$this->supported_currencies = array('KRW');
        
		parent::__construct();
	}

	public function init_form_fields() {
		parent::init_form_fields();
		
		$this->form_fields = array_merge( $this->form_fields, array(
			'title' => array(
				'title'         => __('Title', 'wc_korea_pack'),
				'type'          => 'text',
				'description'   => __('This controls the title which the user sees during checkout.', 'wc_korea_pack'),
				'default'       => __('ARS Payment', 'wc_korea_pack'),
				'desc_tip'      => true,
			),
		));
	}
	
    public function is_valid_for_use() {

        if ( !in_array( get_woocommerce_currency(), apply_filters( 'wc_korea_pack_supported_currencies_ars', $this->supported_currencies ) ) ) {
            return false;
        }
        
        return true;
    }

	public function get_paygate_args( ) {
    	$args = array(
			'goodcurrency'		=> 'WON',
		);

		
		return $args;
	}
}
endif;