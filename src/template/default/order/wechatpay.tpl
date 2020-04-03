{extend name="public:base"/}

{block name="body"}
    <div class="card">
        <div class="card-header">
            <h3 class="text-center"><img src="__STATIC__/images/wechatpay.png" style="margin:20px 0;" /> </h3>
        </div>
        <div class="card-body" style="padding-bottom:80px;">
            <div class="pay_amount" style="padding:20px;text-align: center;color: #222;font-size:18px;">付款金额：{$payorder['pay_amount']|showmoney}</div>
            <div class="weui-btn-area" style="margin:50px;">
                <a href="javascript:;" class="btn btn-block btn-success" id="btngopay">发起支付</a>
                {if $payorder['order_type'] EQ 'recharge'}
                    <a href="{:url('index/member.account/rechargeList')}" class="btn btn-block btn-secondary orderlink" >查看订单</a>
                    {else/}
                    <a href="{:url('index/member.order/detail',['id'=>$payorder['order_id']])}" class="btn btn-block btn-secondary orderlink" >查看订单</a>
                {/if}
            </div>
        </div>
    </div>
{/block}
{block name="script"}
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
                    if(res.err_msg == "get_brand_wcpay_request:ok" ){
                        // 使用以上方式判断前端返回,微信团队郑重提示：
                        //res.err_msg将在用户支付成功后返回ok，但并不保证它绝对可靠。
                        weui.alert('支付成功',function () {
                            location.href=$('.orderlink').attr('href');
                        });
                    }else{
                        alert(res.err_desc||res.err_msg||res.errMsg);
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
{/block}