<extend name="public:base"/>

<block name="body">
    <div class="weui-btn-area" style="margin:50px auto;">
        <a href="javascript:;" class="weui-btn weui-btn_primary" id="btngopay">发起支付</a>
    </div>
</block>
<block name="script">
    <script type="text/javascript">
        $('#btngopay').click(onBridgeReady);
        function onBridgeReady(){
            $('#btngopay').addClass('btn_disabled');
            WeixinJSBridge.invoke(
                'getBrandWCPayRequest', {
                    "appId":"{$paydata.appId}",     //公众号名称，由商户传入
                    "timeStamp":"{$paydata.timeStamp}",         //时间戳，自1970年以来的秒数
                    "nonceStr":"{$paydata.nonceStr}", //随机串
                    "package":"{$paydata.package}",
                    "signType":"{$paydata.signType}",         //微信签名方式：
                    "paySign":"{$paydata.paySign}" //微信签名
                },
                function(res){
                    alert(JSON.stringify(res));
                    if(res.err_msg == "get_brand_wcpay_request:ok" ){
                        // 使用以上方式判断前端返回,微信团队郑重提示：
                        //res.err_msg将在用户支付成功后返回ok，但并不保证它绝对可靠。
                    }
                });
        }
        if (typeof WeixinJSBridge == "undefined"){
            if( document.addEventListener ){
                document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
            }else if (document.attachEvent){
                document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
                document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
            }
        }else{
            onBridgeReady();
        }
    </script>
</block>