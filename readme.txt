=== woocommerce-paygate-jt ===
Contributors: Jeong daeho
Tags: woocommerce, korea, paygate, paymentgateway
Requires at least: 3.5
Tested up to: 3.6
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html


== Description ==

Woocommerce-paygate-jt 플러그인은 

쇼핑몰 플러그인인 Woocommerce에 추가되어 한국에서도 Paygate(PG)를 통해 결제를 할 수 있도록 합니다.

[플러그인을 설치하기에 앞서]

1. Woocommerce 플러그인이 설치되어 있어야만 하며, 설치되어 있지 않다면 동작하지 않습니다.

2 .Paygate를 통해 별도로 계약을 진행하고 플러그인의 설정 페이지를 통해 설정을 마친 후에 결제가 가능합니다.


[플러그인을 사용하기에 앞서]

결제의 보안을 위해 Patgate측의 암호화된 API 인증값을 사용하기를 권장합니다.

<API 인증값을 사용하지 않을 경우 최소한의 보안 처리만 가능하여 보안상 취약점이 생길 수 있습니다.>



제작된 플러그인이 한국 워드프레스 발전에 도움이 되길 기원합니다.

= GET INVOLVED =

[woocommerce-paygate-jt GitHub Repository](https://github.com/Jeongdaeho/woocommerce-paygate-jt).


== Installation ==

플러그인을 설치순서

1. 다운로드 받은 플러그인을 FTP를 통해 Plugins 폴더로 업로드 하거나, 워드프레스 관리자-> 플러그인 탭 에서 woocommerce-paygate-jt 검색후 설치 
2. woocommerce-paygate-jt 활성화
3. 좌측 메뉴 woocommerce 선택   
4. 지불게이트웨이 탭 -> 활성화 시킬 결제 수단중 paygate 관련 결제를 활성화
5. 활성화 시킨 결제 수단으로 이동 
5. paygate 와 계약된 내용을 입력, API 인증값의 경우 paygate 상점 관리자 페이지에서 변경가능(자세한 내용은 paygate 측에 문의)
6. 배송 탭 이동 -> 활성화 시킬 배송 수단중 조건부 무료 선택(선택사항이며 꼭 해야하진 않습니다.)
7. 조건부 무료텝 이동후 배송비와 무료 배송 최소금액을 입력
8. woocommerce 결제 테스트 및 사용

== Screenshots ==

1. 지불게이트 웨이에 paygate 결제가 추가된 화면
2. paygate 결제 설정 화면
3. 조건부 무료가 추가된 배송 옵션 화면
4. 조건부 무료 옵션 화면

== Changelog ==

= 0.3.0 =
* 배송 클레스 추가

= 0.3.1 =
스크린 샷 추가
Installation 설명 추가

= 0.4 =
* 설정관련 변수명이 바뀌었기 때문에 기존 설정과 호환이 되지 않습니다. 설정값을 다시 입력 해주세요
* script 중복 적용 되는 버그 수정
* 쿠폰 할인 금액이 적용 되지 않는 버그 수정
* 실시간 계좌이체가 되지 않던 버그 수정( 실시간 계좌이체는 ActiveX 방식이며 IE 에서만 결제 가능)
* 각 결제별 허용 화폐적용
* 관리자 템플렛 파일을 하나로 통합
* 카드 결제의 지원 화폐를 기본 4가지로 구성 (KRW, USD, RMB, JPY)
* 카드 결제의 추가 지원 화폐를 설정할 수 있습니다.
* 실시간 계좌이체 결제를 위한 상품명 정보의 특수문자 제거
ex)
function wc_korea_pack_paygate_currencies_args_card( $card_args ){
    // woocommerce 에 정의된 화폐이름이 EUR 일경우
    $card_args['EUR'] = array(
        'goodcurrency' => 'EUR',    //paygate 에서 대응 되는 화폐의 코드값
        'langcode' => 'US'          //paygate 에서 지원하는 언어 코드값
    );
    return $card_args;
}
//필터 추가
add_filter('wc_korea_pack_paygate_currencies_args_card', 'wc_korea_pack_paygate_currencies_args_card');


테마의 functions.php 파일에 추가 하시면 적용됩니다.

