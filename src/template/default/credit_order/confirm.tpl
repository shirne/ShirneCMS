{extend name="public:base" /}
{block name="body"}
    <div class="container order-body">
        <form method="post" name="orderForm" onsubmit="return checkForm(this)" action="" >

            <div class="card">
                <div class="card-header">兑换产品</div>
                <div class="card-body">
                    <ul class="list-group prod-list">
                        {volist name="goodss" id="prod"}
                        <li class="list-group-item">
                            <label>
                            <input type="radio" name="goods_id" value="{$prod.id}" checked/>
                            </label>
                            <div class="item-image">
                                <img src="{$prod.image}"/>
                            </div>
                            <div class="item-info">
                                <h3>{$prod.title}</h3>
                                <p class="float-right">{$prod.price}&times;{$prod.count}</p>
                                <p class="text-muted">{$prod.goods_no}</p>
                            </div>
                        </li>
                        {/volist}
                    </ul>
                    <div class="form-group price-row">
                        <label >订单总额</label>
                        <div><i class="ion-logo-buffer"></i> {$total_price}</div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">收货地址</div>
                <div class="card-body">
                    <ul class="list-group address_box">
                        {foreach name="addresses" item="add"}
                            <li class="list-group-item" data-province="{$add.province}" data-city="{$add.city}" data-area="{$add.area}">
                                <label>
                                    <input type="radio" name="address_id" value="{$add.address_id}" {$add.is_default?'checked':''}/> <span>{$add.recive_name} / {$add.mobile}</span>
                                    <div class="text-muted">{$add.province}&nbsp;{$add.city}&nbsp;{$add.area}&nbsp;{$add.address}</div>
                                </label>
                            </li>
                        {/foreach}
                    </ul>

                    <a href="javascript:" class="btn btn-block btn-outline-secondary mt-3 add-address">添加收货地址</a>

                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="remark">备注信息</label>
                        <textarea class="form-control" name="remark"></textarea>
                    </div>
                    <div class="form-group">
                        <ul class="list-group">
                            <li class="list-group-item">
                                <div class="float-right">
                                    <i class="ion-logo-buffer"></i>
                                    <span class="dec_credit">{$user.points|showmoney}</span>
                                </div>
                                <label >使用积分</label>
                            </li>
                            <li class="list-group-item need_pay_box d-none">
                                <div class="float-right">
                                    ￥<span class="need_pay"></span>
                                    <input type="hidden" name="need_pay" value="" />
                                </div>
                                <label >还需支付</label>
                            </li>
                        </ul>
                    </div>
                    <div class="form-group needpay d-none">
                        <ul class="list-group">
                            <li class="list-group-item">
                                <div class="float-right">￥ {$user.money|showmoney}</div>
                                <label ><input type="radio" name="pay_type" value="balance" checked />余额支付</label>
                            </li>
                            {if $isWechat AND !empty($config['mch_id'])}
                                <li class="list-group-item">
                                    <label ><input type="radio" name="pay_type" value="wechat" checked />微信支付</label>
                                </li>
                            {/if}
                        </ul>
                    </div>
                    {if USE_SEC_PASSWORD}
                    <div class="form-group sec_password d-none">
                        <label for="sec_password">安全密码</label>
                        <input type="password" class="form-control" name="sec_password" />
                    </div>
                    {/if}
                    <div class="form-group">
                        <button type="submit" class="btn btn-block btn-primary">提交订单</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
{/block}
{block name="script"}
    <script type="text/plain" id="addressTpl">
        <form>
        <div class="form-group">
            <label for="recive_name">收货人</label>
            <input type="text" name="recive_name" class="form-control" placeholder="收货人姓名">
        </div>
        <div class="form-group">
            <label for="mobile">联系电话</label>
            <input type="text" name="mobile" value="{$user.mobile}" class="form-control" placeholder="收货人联系电话">
        </div>
        <div class="form-group">
            <label for="province_select">所在地区</label>
            <div class="input-group ChinaArea">
                <select name="province_select" class="form-control"></select>
                <input type="hidden" name="province" />
                <span class="input-group-addon"></span>
                <select name="city_select" class="form-control"></select>
                <input type="hidden" name="city" />
                <span class="input-group-addon"></span>
                <input type="hidden" name="area" />
                <select name="area_select" class="form-control"></select>
            </div>
        </div>

        <div class="form-group">
            <label for="address">详细地址</label>
            <input type="text" name="address" class="form-control"  >
        </div>
        </form>
    </script>
    <script type="text/javascript" src="__STATIC__/js/location.min.js"></script>
    <script type="text/javascript">
        var total_credit=parseFloat('{$total_price}');
        var user_credit=parseInt('{$user.points}')/100;
        var need_pay=0;

        jQuery(function($){
            if(user_credit<total_credit){
                //$('.dec_credit').text(user_credit);
                $('.need_pay_box').removeClass('d-none');
                $('.needpay').removeClass('d-none');
                need_pay = total_credit-user_credit;
                $('.need_pay').text(need_pay);
                $('[name=need_pay]').val(need_pay);
            }else{
                //$('.dec_credit').text(total_credit);
            }


            $('.add-address').click(function (e) {
                var issending=false;
                var dlg=new Dialog({
                    onshow:function(body){
                        body.find(".ChinaArea").jChinaArea({
                            aspnet:true,
                            s1:"{$user.province}",
                            s2:"{$user.city}",
                            s3:"{$user.area}"
                        });
                    },
                    onsure:function(body){
                        if(issending)return false;
                        issending=true;
                        var data=body.find('form').serialize();
                        $.ajax({
                            url:'{:url("index/member/addressAdd")}',
                            type:'POST',
                            dataType:'JSON',
                            data:data,
                            success:function(json){
                                if(json.code==1){
                                    dlg.hide();
                                    dialog.alert(json.msg,function() {
                                        var tpl='<li class="list-group-item" data-province="{@province}" data-city="{@city}" data-area="{@area}"><label><input type="radio" name="address_id" value="{@address_id}" checked/> {@province}&nbsp;{@city}&nbsp;{@area}&nbsp;{@address}</label></li>';
                                        $('.address_box').append(tpl.compile(json.data));
                                        $('[name=address_id]:checked').trigger('click');
                                    });
                                }else{
                                    dialog.alert(json.msg);
                                    issending=false;
                                }
                            }
                        });
                        return false;
                    }
                }).show($('#addressTpl').text(),'添加收货地址');
            });
        });
        $('[name=pay_type]').click(function (e) {
            var pay_type=$(this).val();
            if(pay_type=='balance'){
                $('.sec_password').removeClass('d-none');
            }else{
                $('.sec_password').addClass('d-none');
            }
        }).filter(':checked').trigger('click');
        $('[name=address_id]').click(function (e) {
            var parent = $(this).parents('.list-group-item');

        }).filter(':checked').trigger('click');
        $('.servicebox').on('click','.list-group-item',function () {
            $('.servicebox .list-group-item').removeClass('active');
            $(this).addClass('active');
            $('[name=community_id]').val($(this).data('id'));
        });


        function checkForm(form) {
            if($('[name=address_id]').length>0 ) {
                if (!$('[name=address_id]:checked').val()) {
                    alert('请选择收货地址');
                    return false;
                }
            }else {
                if (!$('[name=recive_name]').val() || !$('[name=mobile]').val() || !$('[name=address]').val()) {
                    alert('请填写收货信息');
                    return false;
                }
            }
            if(need_pay>0) {
                var pay_type = $('[name=pay_type]:checked').val();
                if (pay_type == 'balance') {
                    if ('{$user.money}' * 1 < need_pay * 100) {
                        alert('余额不足');
                        return false;
                    }

                    if (window.use_sec_password && !$('[name=sec_password]').val()) {
                        alert('请填写安全密码');
                        return false;
                    }
                }
            }
        }
    </script>
{/block}