<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'WC_Shipping_Condition_On_free' ) ) :

class WC_Shipping_Condition_On_free extends WC_Shipping_Method {
	
	public function __construct() {
		$this->id                 = 'condition_on_free'; // Id for your shipping method. Should be unique.
		$this->method_title       = __( '조건부 무료' );  // Title shown in admin
		$this->method_description = __( '조건부 무료' ); // Description shown in admin
 
		$this->init();
	}
	
	/**
	 * Init your settings
	 *
	 * @access public
	 * @return void
	 */
	function init() {
		// Load the settings API
		$this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
		$this->init_settings(); // This is part of the settings API. Loads settings you previously init.
 
 		// Define user set variables
        $this->enabled		= $this->get_option( 'enabled' );
		$this->title 		= $this->get_option( 'title' );
		$this->free_min_amount 	= $this->get_option( 'free_min_amount', 0 );
		$this->availability = $this->get_option( 'availability' );
		$this->countries 	= $this->get_option( 'countries' );
		$this->fee			= $this->get_option( 'fee' );
 
		// Save settings in admin if you have any defined
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
	}

    function init_form_fields() {
    	global $woocommerce;

    	$this->form_fields = array(
			'enabled' => array(
							'title' 		=> __( 'Enable/Disable', 'woocommerce' ),
							'type' 			=> 'checkbox',
							'label' 		=> __( '조건부 무료 배송 활성화', 'woocommerce' ),
							'default' 		=> 'yes'
						),
			'title' => array(
							'title' 		=> __( 'Method Title', 'woocommerce' ),
							'type' 			=> 'text',
							'description' 	=> __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
							'default'		=> __( '조건부 무료', 'woocommerce' ),
							'desc_tip'      => true,
						),
			'availability' => array(
							'title' 		=> __( 'Method availability', 'woocommerce' ),
							'type' 			=> 'select',
							'default' 		=> 'all',
							'class'			=> 'availability',
							'options'		=> array(
								'all' 		=> __( 'All allowed countries', 'woocommerce' ),
								'specific' 	=> __( 'Specific Countries', 'woocommerce' )
							)
						),
			'countries' => array(
							'title' 		=> __( 'Specific Countries', 'woocommerce' ),
							'type' 			=> 'multiselect',
							'class'			=> 'chosen_select',
							'css'			=> 'width: 450px;',
							'default' 		=> '',
							'options'		=> $woocommerce->countries->countries
						),
			'fee' => array(
				'title' 		=> __( 'Delivery Fee', 'woocommerce' ),
				'type' 			=> 'number',
				'custom_attributes' => array(
					'step'	=> '100',
					'min'	=> '0'
				),
				'description' 	=> __( '조건부 무료 배송에 얼마의 배송료를 원하시나요? 무료를 선택하면 무시하세요. 비활성화 하려면 비워두세요.', 'woocommerce' ),
				'default'		=> '',
				'desc_tip'      => true,
				'placeholder'	=> '0'
			),
			'free_min_amount' => array(
							'title' 		=> __( '무료 배송 최소 금액', 'woocommerce' ),
							'type' 			=> 'number',
							'custom_attributes' => array(
								'step'	=> '100',
								'min'	=> '0'
							),
							'description' 	=> __( '설정한 금액보다 주문금액이 클경우 배송료는 무료입니다.', 'woocommerce' ),
							'default' 		=> '0',
							'desc_tip'      => true,
							'placeholder'	=> '0'
						)
			);

    }


	/**
	 * calculate_shipping function.
	 *
	 * @access public
	 * @param mixed $package
	 * @return void
	 */
	function calculate_shipping( $package = array() ) {
		global $woocommerce;

		$shipping_total = 0;

		if( $package['contents_cost'] < $this->free_min_amount ) {
			$shipping_total 	= $this->fee;	
		}
		
		
		$rate = array(
			'id' 		=> $this->id,
			'label' 	=> $this->title,
			'cost' 		=> $shipping_total
		);

		$this->add_rate($rate);
	}
}
 
function add_condition_on_free( $methods ) {
	$methods[] = 'WC_Shipping_Condition_On_free';
	return $methods;
}
add_filter( 'woocommerce_shipping_methods', 'add_condition_on_free' );
endif;
