<?php
class WC_Korea_Pack_options {
    
    function __construct(){
        $this->settings = array(
            array( 'title' => __( 'WooCommerce Paygate JT', 'wc_korea_pack' ), 'type' => 'title', 'desc' => '', 'id' => 'wckp_general_options' ),
            array(
                'title'     => __( '하나의이름', 'wc_korea_pack' ),
                'desc'      => __( '결제 정보 입력시 이름에 하나의 입력 값만을 적용합니다.', 'wc_korea_pack' ),
                'id'        => 'woocommerce_wckp_namefix',
                'default'   => 'no',
                'type'      => 'checkbox'
            ),
            array(
                'title'     => __( '회사이름삭제', 'wc_korea_pack' ),
                'desc'      => __( '회사 이름을 사용하지 않습니다.', 'wc_korea_pack' ),
                'id'        => 'woocommerce_wckp_companyfix',
                'default'   => 'no',
                'type'      => 'checkbox'
            ),
            array(
                'title'     => __( '우편번호검색', 'wc_korea_pack' ),
                'desc'      => __( '한국 우편번호 검색을 사용.', 'wc_korea_pack' ),
                'id'        => 'woocommerce_wckp_zip_code',
                'default'   => 'no',
                'desc_tip'  =>  __( '사용할 경우 도시 이름, 국가 선택 입력박스를 제거하며, 주소 입력 순서가 변경됩니다.', 'wc_korea_pack' ),
                'type'      => 'checkbox'
            ),
            array(
                'title'     => __( '디버그 모드', 'wc_korea_pack' ),
                'desc'      => __( 'paygate JT의 디버그 모드 적용', 'wc_korea_pack' ),
                'id'        => 'woocommerce_wckp_mode_debug',
                'default'   => 'no',
                'desc_tip'  =>  __( '활성화시 woocommerce의 로그를 사용합니다.<br /> woocommerce의 로그는 woocommerce/logs/ 디렉토리에 txt 파일 형태로 저장 됩니다.', 'wc_korea_pack' ),
                'type'      => 'checkbox'
            ),

            array( 'type' => 'sectionend', 'id' => 'wckp_options')
        );
        
        //우커머스 관리자 탭추가
        add_filter('woocommerce_settings_tabs_array', array($this, 'settings_tabs_array') );
        //관리자 옵션 페이지
        add_action('woocommerce_settings_tabs_wckp', array($this, 'admin_option_page') );
        // 관리자 옵션 저장
        add_action('woocommerce_update_options_wckp', array($this, 'update_option') );
        //주소 필드 수정
        add_filter('woocommerce_default_address_fields', array($this, 'default_address_fields') );
        // 우편 번호 스크립트        
        add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
        // 우편 번호 필드 출력
        //add_filter('woocommerce_form_field_search_postcode', array( $this, 'search_postcode_field'), 10, 4);

        // 주소 출력 
        add_filter('woocommerce_my_account_my_address_formatted_address', array($this, 'my_address_formatted_address') );
    }
    
    //우커머스 관리자 탭추가
    function settings_tabs_array($tabs){
        $tabs['wckp'] =  __( 'Paygate JT', 'wc_korea_pack' );
        return $tabs;
    }
    
    //관리자 옵션 페이지
    function admin_option_page(){
        woocommerce_admin_fields( $this->settings );    
    }
    
    //관리자 옵션 저장
    function update_option(){
        woocommerce_update_options( $this->settings );   
    }
    
    //주소 필드 수정
    function default_address_fields($fields){
        $new_fields = array();
        
        if( get_option('woocommerce_wckp_namefix') == 'yes' ){
            $fields['first_name']['class'] = array( 'form-row-wide' );
            unset($fields['last_name']);
        }

        if( get_option('woocommerce_wckp_companyfix') == 'yes' ){
            unset($fields['company']);
        }

        if( get_option('woocommerce_wckp_zip_code') == 'yes' ){
            unset($fields['city']);
            unset($fields['country']);
            unset($fields['state']);
            

            $fields['postcode']['class'] = array('form-row-wide');
            $fields['postcode']['clear'] = false;
            
            $fields['address_1']['placeholder'] = '동(읍/면) 까지의 주소';
            $fields['address_1']['label'] = '동(읍/면) 까지의 주소';

            $fields['address_2']['placeholder'] = '동(읍/면) 이후의 주소';
            $fields['address_2']['label'] = '동(읍/면) 이후의 주소';
            $fields['address_2']['required'] = true;
            
            
            foreach($fields as $key => $val ){
                
                if( $key == 'address_1'){
                    $new_fields['postcode'] = $fields['postcode'];    

                } else if( $key == 'postcode' ){
                    continue;
                }
                
                $new_fields[$key] = $val;
            }
            $fields = $new_fields;
        }
        
        return $fields;        
    }

    function my_address_formatted_address($fields){
        $new_fields = array();
        
        if( get_option('woocommerce_wckp_namefix') == 'yes' ){
            unset($fields['last_name']);
        }

        if( get_option('woocommerce_wckp_companyfix') == 'yes' ){
            unset($fields['company']);
        }

        if( get_option('woocommerce_wckp_zip_code') == 'yes' ){
            unset($fields['city']);
            unset($fields['country']);
            unset($fields['state']);
        }
        
        return $fields;        
    }

    function scripts(){
        global $woocommerce, $wp_scripts;
        if( get_option('woocommerce_wckp_zip_code') == 'yes' ){
            if ( is_checkout() || is_account_page() ) {
                $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
                
                wp_enqueue_script( 'daum-post-code',  '//dmaps.daum.net/map_js_init/postcode.js', 'jquery', '1.0', TRUE );
                wp_enqueue_script( 'wckp-checkout', plugin_dir_url ( __FILE__ ) . 'checkout.js', array( 'jquery', 'daum-post-code' ), wc_korea_pack()->version, true );
            }
        }
    }
}
