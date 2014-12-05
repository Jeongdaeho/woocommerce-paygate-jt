<?php
 /**
 * Plugin Name: WooCommerce Paygate JT
 * Plugin URI: http://www.studio-jt.co.kr
 * Description: woocommerce paygate 결제모듈
 * Version: 0.6.4
 * Author: 스튜디오 제이티 (support@studio-jt.co.kr)
 * Author URI: studio-jt.co.kr
 *
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'WC_Korea_Pack' ) ) :

class WC_Korea_Pack {

    public $version = '0.6.4';

    private static $instance;
    //public $gateway_items = array( 'openxpay', 'paygate' );
    public $gateway_items = array( 'paygate' );
    public $shipping_items = array( 'condition-on-free' );

    private function __construct() { /* Do nothing here */ }

    public static function getInstance() {
        if( !class_exists( 'Woocommerce' ) ) {
            return null;
        } else if( ! isset( self::$instance ) ) {
            self::$instance = new WC_Korea_Pack;
            self::$instance->setup_globals();
            self::$instance->includes();
            self::$instance->setup_actions();
        }
        return self::$instance;
    }

    private function setup_globals() {

        //domain
        $this->domain           = 'wc_korea_pack';
        //plugins
        $this->file             = __FILE__;
        $this->plugin_dir       = apply_filters( 'wc_korea_pack_plugin_dir_path',  plugin_dir_path( $this->file ) );
        $this->plugin_url       = apply_filters( 'wc_korea_pack_plugin_dir_url',   plugin_dir_url ( $this->file ) );

        // Includes
        $this->includes_dir     = apply_filters( 'wc_korea_pack_includes_dir', trailingslashit( $this->plugin_dir . 'includes'  ) );
        $this->includes_url     = apply_filters( 'wc_korea_pack_includes_url', trailingslashit( $this->plugin_url . 'includes'  ) );

        //gateway
        $this->gateway_dir      = apply_filters( 'wc_korea_pack_gateway_dir', trailingslashit( $this->plugin_dir . 'gateway'  ) );
        $this->gateway_url      = apply_filters( 'wc_korea_pack_gateway_url', trailingslashit( $this->plugin_url . 'gateway'  ) );

        //Gateway list item
        $this->gateway_items    = apply_filters( 'wc_korea_pack_gateway', $this->gateway_items );

        //shipping
        $this->shipping_dir     = apply_filters( 'wc_korea_pack_shipping_dir', trailingslashit( $this->plugin_dir . 'shipping'  ) );
        $this->shipping_url     = apply_filters( 'wc_korea_pack_shipping_url', trailingslashit( $this->plugin_url . 'shipping'  ) );

        // Languages
        $this->lang_dir         = apply_filters( 'wc_korea_pack_lang_dir',     trailingslashit( $this->plugin_dir . 'languages' ) );

    }

    private function includes() {

        require_once( $this->includes_dir . 'functions.php' );
        require_once( $this->includes_dir . 'options.php' );

        //gateway load
        foreach( $this->gateway_items as $gateway_item ) {
            require_once( $this->gateway_dir . $gateway_item .'/'. $gateway_item .'.php' );
        }

        //shipping load
        foreach( $this->shipping_items as $shipping_item ){
            require_once( $this->shipping_dir . $shipping_item .'.php' );
        }
    }

    private function setup_actions() {
        // init action
        add_action('init', array( $this, 'wc_korea_pack_init' ) );

        //load textdomain
        add_action('wc_korea_pack_init', array( $this, 'wc_korea_pack_load_textdomain' ), 5 );
        add_action('wc_korea_pack_init', array( $this, 'wc_korea_pack_load_options' ), 10 );
    }

    public function wc_korea_pack_init() {
        do_action('wc_korea_pack_init');
    }

    public function wc_korea_pack_load_textdomain() {
        // Traditional WordPress plugin locale filter
        $locale        = apply_filters( 'plugin_locale',  get_locale(), $this->domain );
        $mofile        = sprintf( '%1$s-%2$s.mo', $this->domain, $locale );

        // Setup paths to current locale file
        $mofile_local  = $this->lang_dir . $mofile;
        $mofile_global = WP_LANG_DIR . $this->domain . $mofile;

        if ( file_exists( $mofile_global ) ) {
            return load_textdomain( $this->domain, $mofile_global );
        } elseif ( file_exists( $mofile_local ) ) {
            return load_textdomain( $this->domain, $mofile_local );
        }

        // Nothing found
        return false;
    }

    public function wc_korea_pack_load_options(){
        $wckorea_pack_options = new WC_Korea_Pack_options();
    }

}

function wc_korea_pack() {
        return WC_Korea_Pack::getInstance();
}

add_action( 'plugins_loaded', 'wc_korea_pack', 0 );
endif;
