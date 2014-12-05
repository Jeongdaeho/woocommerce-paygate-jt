<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'WC_Gateway_PayGate' ) ) :

define( 'WCKP_PAYGATE_PLUGIN_DIR',  plugin_dir_path( __FILE__ ) );
define( 'WCKP_PAYGATE_PLUGIN_URL',  plugin_dir_url ( __FILE__ ) );
define( 'WCKP_PAYGATE_TEMPLATES_PATH',  trailingslashit( WCKP_PAYGATE_PLUGIN_DIR.'templates') );

class WC_Gateway_PayGate extends WC_Payment_Gateway {
    /*
     * 스크립트 로드 여부
     * */
    public static $is_script_already = false;

	function __construct() {
		global $woocommerce;

		$this->has_fields 			= false;
		$this->templates_path 		= WCKP_PAYGATE_TEMPLATES_PATH;

		// wckp options
		$this->debug 				= get_option('woocommerce_wckp_mode_debug');

		// Define user set variables
		$this->title 				= $this->get_option('title');
		$this->description 			= $this->get_option('description');
		$this->order_description 	= $this->get_option('order_description');
		$this->pg_skin 				= $this->get_option('pg_skin');
		$this->use_escrow			= $this->get_option('use_escrow');

		$this->access_key 			= $this->get_option('access_key');
		$this->api_key 				= $this->get_option('api_key');

		// load form fields.
		$this->init_form_fields();

		// load settings (via WC_Settings_API)
		$this->init_settings();

		// Logs
		if ( 'yes' == $this->debug )
			//$this->log = $woocommerce->logger();
			$this->log = new WC_LOGGER();

		if ( ! $this->is_valid_for_use() ) $this->enabled = false;

		//add pay script
		add_action('wp_enqueue_scripts', array( $this, 'script' ) );

		// Actions
		add_action('woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action('woocommerce_receipt_'.$this->id, array( $this, 'receipt_page' ) );

		// 결제 모듈 ajax 사용이 가능해질 경우.
		//add_action( 'wp_ajax_wpkp_paygate_response'.$this->id, array( $this, 'process_payment_response' ) );

	}

	public function init_form_fields() {

		$this->form_fields = array(
			'enabled' => array(
				'title' => __('Enable/Disable', 'wc_korea_pack'),
				'type' => 'checkbox',
				'label' => __('Enable', 'wc_korea_pack'),
				'default' => 'no'
			),
			'access_key' => array(
				'title' => __('상점 아이디', 'wc_korea_pack'),
				'type' => 'text',
				'description' => __('상점아이디 paygate로 부터 발급받으신 상점 아이디(로그인 아이디)를 입력하세요) <br /> 설정 값이 없는 경우 paygate 테스트 상점아이디가 적용됩니다.', 'wc_korea_pack'),
				'default' => '',
				'desc_tip' => true,
			),
			'description' => array(
				'title' => __('Customer Message', 'wc_korea_pack'),
				'type' => 'textarea',
				'description' => __( 'This controls the description which the user sees during checkout.', 'wc_korea_pack' ),
				'default' => __( 'This controls the description which the user sees during checkout.', 'wc_korea_pack' ),
			),
			'api_key' => array(
				'title' => __('API 인증값', 'wc_korea_pack'),
				'type' => 'text',
				'description' => __( '상점관리자 로그인후 맴버관리 > 자기정보관리 > API인증값 <br/> <a href="https://km.paygate.net/display/CS/Transaction+Hash+Verification%28SHA-256%29">참조</a>', 'wc_korea_pack' ),
				'default' => '',
				'desc_tip' => true,
			),
			'pg_skin' => array(
				'title' => __('결제창 스킨', 'wc_korea_pack'),
				'type' => 'select',
				'options' => array('0' => 'style0', '1' => 'style1', '2' => 'style2', '3' => 'style3', '4' => 'style4', '5' => 'style5'),
				'default' => '5',
				'description' => __( 'paygate 에서 제공하는 스킨 입니다. <br/> 마음에 드는 스킨이 없을 경우 별도 css 를 추가 하시기 바랍니다.', 'wc_korea_pack'),
				'desc_tip' => true,
			),
			'use_escrow' => array(
				'title' => __('에스크로 강제', 'wc_korea_pack'),
				'type' => 'checkbox',
				'default' => 'no',
				'description' => __('서비스 옵션에서 매매보호 이용함으로 설정된 경우 10만원 이상 현금거래시 유저가 매매보호 거래를 선택할 수 있는 화면이 제시됩니다. ', 'wc_korea_pack'),
				'desc_tip' => true,
			),
		);
	}

	/* *
	 * 	script
	 *  Inject the paygate javascript into the page.
	 * */

	public function script() {
	    if( WC_Gateway_PayGate::$is_script_already === true ) return;

        if ($this->enabled == 'yes' ) {

            WC_Gateway_PayGate::$is_script_already = true;

		    $order_id  = isset( $_GET['order'] ) ? absint( $_GET['order'] ) : 0;
            $order_key = isset( $_GET['key'] ) ? woocommerce_clean( $_GET['key'] ) : '';

            $order = new WC_Order( $order_id );

            $thanks_url = get_permalink( $order->get_checkout_order_received_url() );
            $thanks_url = add_query_arg( 'key', $order->order_key, add_query_arg( 'order', $order->id, $thanks_url ) );

			//wp_enqueue_script( 'wc_paygate_remote', 'https://api.paygate.net/ajax/common/OpenPayAPI.js',null, null,true);
			#ie 7 지원
			echo '<script language="javascript" src="https://api.paygate.net/ajax/common/OpenPayAPI.js" charset="utf-8"></script>';

			wp_enqueue_script( 'wc_paygate_main', WCKP_PAYGATE_PLUGIN_URL.'assets/paygate.js', array('jquery'), wc_korea_pack()->version, true);
			wp_localize_script( 'wc_paygate_main', 'wckp', array(
				'thanks_url' => $thanks_url,
				'cart_url' => get_permalink(woocommerce_get_page_id( 'cart' )),
				'message_failure' => __('결제가 실패했습니다. 다시 이용해 주세요', 'wc_korea_pack')
			) );
			wp_register_style( 'wc_paygate_main', WCKP_PAYGATE_PLUGIN_URL.'assets/style.css', '', wc_korea_pack()->version);
			wp_enqueue_style( 'wc_paygate_main' );
		}
	}

	public function format_settings($value) {
		return ( is_array($value)) ? $value : html_entity_decode($value);
	}

	/* *
	 * 	admin option page
	 * */

	public function admin_options() {
		require $this->templates_path . 'admin-woocommerce-paygate.php';
	}

	public function validate_fields() {
		return parent::is_available();
	}

	public function process_payment( $order_id ) {

		$order = new WC_Order( $order_id );

		return array(
			'result' 	=> 'success',
			'redirect'	=> add_query_arg('order', $order->id, add_query_arg( 'key', $order->order_key, $order->get_checkout_order_received_url() ) )
		);
	}

    /* *
     *  paygate 결제 요청 페이지
     * */
    public function receipt_page( $order_id ) {
        global $woocommerce;

        $order = new WC_Order( $order_id );

        $item_names = array();

        if ( sizeof( $order->get_items() ) > 0 )
            foreach ( $order->get_items() as $item )
                if ( $item['qty'] )
                    $item_names[] = $item['name'] . ' x ' . $item['qty'];

        $goodname = sprintf( __( 'Order %s' , 'woocommerce'), $order->get_order_number() ) . "  " . implode( ', ', $item_names );
        $goodname = preg_replace ("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "",  $goodname);

        $paygate_args = $this->get_paygate_args( $order );
        $paygate_args = array_merge( array(
                'charset'       => 'UTF-8',
                'mid'           => $this->access_key,
                'paymethod'     => $this->method,
                'receipttoname' => $order->billing_last_name.$order->billing_first_name,
                'goodname'      => $goodname,
                'unitprice'     => (int)$order->get_total(),
                'replycode'     => '',
                'replyMsg'      => '',
                'mb_serial_no'  => $order_id,
                'kindcss'       => $this->pg_skin
            ), $paygate_args
        );

        if( $this->api_key ){
            $paygate_args['hashresult'] = '';
            $paygate_args['tid'] = '';
        }

        $paygate_args['loanSt'] = '';
        if( $this->use_escrow == 'yes' ){
            $paygate_args['loanSt'] = 'escrow';
        }

        $paygate_args_array = array();

        foreach( $paygate_args as $key => $value ) {
            if( $key == 'cardtype' && is_array($value) && count($value) > 0 ){
                $cardtype_options = array();
                foreach( $value as $option_value ){
                    $cardtype_options[] = '<option value="'.$option_value.'">'.$this->card_list[$option_value].'</option>';
                }
                $paygate_args_array[] = '<label for="'.$key.'">결제카드 </label><select name="'.$key.'">'.implode('',$cardtype_options).'</select>';
            } else {
            $paygate_args_array[] = '<input type="hidden" name="'.esc_attr( $key ).'" id="'.esc_attr( $key ).'" value="'.esc_attr( $value ).'" />';
            }
        }

        $notify_url= add_query_arg('order', $order->id, add_query_arg( 'key', $order->order_key, $this->notify_url ) );
        $output = '
            <div id="PGIOscreen" ></div>
            <form method="post" name ="PGIOForm" id="PGIOForm" action="'.$notify_url.'">' . wp_nonce_field('process_payment_response', true, false). implode( '', $paygate_args_array) . '
            <a class="button alt" href="#" id="submit_paygate_payment_form">' . __( '결제', 'wc_korea_pack' ) . '</a>
            </form>
        ';

        echo $output;
    }

	public function process_payment_response() {
		global $woocommerce;

        if ( 'yes' == $this->debug ) {
            $this->log->add( $this->id, '주문번호:'. $order_id . '[결제 처리 시작]' );
        }

		#nonce check!
		$woocommerce->verify_nonce( 'process_payment_response' );

		$order_id  = isset( $_GET['order'] ) ? absint( $_GET['order'] ) : 0;
		$order_key = isset( $_GET['key'] ) ? woocommerce_clean( $_GET['key'] ) : '';

		$order = new WC_Order( $order_id );

		#order key check!!
		if ( $order_id > 0 ) {
			if ( $order->order_key != $order_key ) {
				$woocommerce->add_error( __('주문번호 검증 실패', 'wc_korea_pack') );
			}
		} else {
			$woocommerce->add_error( __('주문번호 검증 실패', 'wc_korea_pack') );
		}

        #order price check!!
        if( !isset( $_POST['unitprice'] ) || $_POST['unitprice'] <= 0 || (int)$order->get_total() != $_POST['unitprice'] ) {
            $woocommerce->add_error( __('주문금액 검증 실패', 'wc_korea_pack') );
        }

		if ( $woocommerce->error_count() == 0 ) {

			#check SHA256!!!
			if( $this->check_salt( $order_id ) === true ) {

				$order->payment_complete();
				$woocommerce->cart->empty_cart();

			} else {
				$woocommerce->add_error( __('주문번호 검증 실패', 'wc_korea_pack') );
			}
		}

		if ( $woocommerce->error_count() == 0 ) {
			if ( 'yes' == $this->debug ) {
			    $this->log->add( $this->id, '주문번호:'. $order_id . '[결제 정상처리]' );
            }
            /* 결제 모듈 ajax 사용이 가능해질 경우.
            echo '<!--WCKP_START-->' . json_encode(
                array(
                    'result'    => 'success'
                )
            ) . '<!--WCKP_END-->';*/
			wp_redirect( $this->get_return_url( $order ) );

		} else {
            $errors = $woocommerce->get_errors();

            if ( 'yes' == $this->debug ) {
                foreach ( $errors as $error ){
                    $this->log->add( $this->id, '주문번호:'. $order_id . '[결제 실패]'. wp_kses_post( $error ) );
                }
            }

            echo '<script>
                alert("'.implode(', ', $errors).'\n장바구니 이동합니다.");
                window.location="'.get_permalink(woocommerce_get_page_id( 'cart' )).'";
            </script>';

			/* 결제 모듈 ajax 사용이 가능해질 경우.
			$woocommerce->show_messages();
            $messages = ob_get_clean();
            echo '<!--WCKP_START-->' . json_encode(
				array(
					'result'	=> 'failure',
					'messages' 	=> $messages,
				)
			) . '<!--WCKP_END-->';*/

		}
        die();
	}

	//결제검증
	public function check_salt( $order_id ) {
		global $woocommerce;

		if( $this->api_key ) {
			$order 	= new WC_Order( $order_id );

			// paygate 에서는 WON 을 주지만 KRW 로 처리된 해쉬를 보낸다.
			if( $_POST['goodcurrency'] == 'WON' ) {
				$_POST['goodcurrency'] = 'KRW';
			}

			$data = $_POST['replycode'].$_POST['tid'].$order_id.$_POST['unitprice'].$_POST['goodcurrency'];

			$hashReuslt = hash('sha256',$this->api_key.$data);

            if ( 'yes' == $this->debug ) {
                $this->log->add( $this->id, '주문번호:'. $order_id . str_repeat('*', 10).' paygate 전송 값'.str_repeat('*', 10) );
                $this->log->add( $this->id, '주문번호:'. $order_id . '[paygate 결제결과코드] '.$_POST['replycode'] );
                $this->log->add( $this->id, '주문번호:'. $order_id . '[paygate 고유번호] '.$_POST['tid'] );
                $this->log->add( $this->id, '주문번호:'. $order_id . '[paygate 상품가격] '.$_POST['unitprice'] );
                $this->log->add( $this->id, '주문번호:'. $order_id . '[paygate 화폐단위] '.$_POST['goodcurrency'] );
                $this->log->add( $this->id, '주문번호:'. $order_id . '[paygate 리턴해쉬값] '.$_POST['hashresult'] );

                $this->log->add( $this->id, '주문번호:'. $order_id . '[설정 api_key] '.$this->api_key );
                $this->log->add( $this->id, '주문번호:'. $order_id . '[검증해쉬값] '.$hashReuslt );
            }

			if( $hashReuslt != $_POST['hashresult'] ) {
				$woocommerce->add_error( __( '비정상적인 결제 시도', 'wc_korea_pack') );
				return false;
			}

		}

		return true;
	}

}

class wckp_PayGate {

	private $methods;


	function __construct() {
		$this->setup_globals();
		$this->includes();
		$this->setup_actions();
	}

	private function setup_globals() {
		$this->methods		= array(
			'alipay',
			'ars',
			'bank',
			'cup',
			'mobile',
			'phonebill',
			'card',
		);
	}

	private function includes() {
		foreach( $this->methods as $method ) {
			require_once WCKP_PAYGATE_PLUGIN_DIR.'class-wc-gateway-paygate-'.$method.'.php';
		}
	}

	public function setup_actions() {
		add_filter( 'woocommerce_payment_gateways', array( $this, 'add_paygate_class' ) );
	}

	public function add_paygate_class( $methods ) {

		foreach( $this->methods as $method ) {
			array_unshift( $methods, 'WC_Gateway_PayGate_'.$method );
		}

		return $methods;
	}
}

$GLOBALS['paygate'] = new wckp_PayGate();

endif;
