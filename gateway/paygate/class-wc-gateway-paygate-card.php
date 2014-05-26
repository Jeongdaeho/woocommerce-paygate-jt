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
        $this->card_list    = array(
                                '310' => '비씨(BC)카드', 
                                '410' => '신한(LG)카드', 
                                '510' => '삼성카드', 
                                '610' => '현대카드', 
                                '110' => '국민(KB)카드', 
                                '710' => '롯데카드', 
                                '210' => '외환카드',
                                '912' => '농협(NH)카드',
                                '923' => '씨티카드',
                                '916' => '하나SK카드',
                                '913' => '우리카드',
                                '918' => '광주카드',
                                '920' => '전북카드',
                                '925' => '수협카드',
                                '610' => '신협(현대)카드',
                                '921' => '제주카드',
                                '511' => '삼성올앳카드',
                            );

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
			'openpay_card_list' => array(
                'title'     => __( '오픈결제 카드', 'wc_korea_pack' ),
                'description'      => __( 'ie를 제외한 브라우져에서 결제 가능한 카드 설정(paygate 공지사항 참조)', 'wc_korea_pack' ),
                'options'   => $this->card_list,
                'default'   => '',
                'desc_tip'  =>  __( '', 'wc_korea_pack' ),
                'type'      => 'multiselect'
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
        global $is_winIE;
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
        $openpay_card_list = $this->get_option('openpay_card_list');
        if( !$is_winIE ){
            if( is_array($openpay_card_list) && count($openpay_card_list) > 0 ){
                    $args['cardtype'] = $openpay_card_list;    
            }
        }

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