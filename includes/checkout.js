jQuery(document).ready(function($) {
    if( typeof daum == "object" && typeof daum.Postcode == "function" ){
        
        $("#billing_postcode").on('click', function(event){
            event.preventDefault ? event.preventDefault() : event.returnValue = false;
            new daum.Postcode({
                oncomplete: function(data) {
                    // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.
                    // 우편번호와 주소 정보를 해당 필드에 넣고, 커서를 상세주소 필드로 이동한다.
                    $('#billing_postcode').val(data.postcode1+'-'+data.postcode2);
                    $('#billing_address_1').val(data.address);
                    
                    //전체 주소에서 연결 번지 및 ()로 묶여 있는 부가정보를 제거하고자 할 경우,
                    //아래와 같은 정규식을 사용해도 된다. 정규식은 개발자의 목적에 맞게 수정해서 사용 가능하다.
                    //var addr = data.address.replace(/(\s|^)\(.+\)$|\S+~\S+/g, '');
                    //document.getElementById('addr').value = addr;
                    $('#billing_address_2').focus();
            }
            }).open();
        });

        $("#shipping_postcode").on('click', function(event){
            event.preventDefault ? event.preventDefault() : event.returnValue = false;
            new daum.Postcode({
                oncomplete: function(data) {
                    // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.
                    // 우편번호와 주소 정보를 해당 필드에 넣고, 커서를 상세주소 필드로 이동한다.
                    $('shipping_postcode').val(data.postcode1+'-'+data.postcode2);
                    $('#shipping_address_1').val(data.address);
            
                    //전체 주소에서 연결 번지 및 ()로 묶여 있는 부가정보를 제거하고자 할 경우,
                    //아래와 같은 정규식을 사용해도 된다. 정규식은 개발자의 목적에 맞게 수정해서 사용 가능하다.
                    //var addr = data.address.replace(/(\s|^)\(.+\)$|\S+~\S+/g, '');
                    //document.getElementById('addr').value = addr;
                    $('#shipping_address_2').focus();
        }
            }).open();
            });
        }

});