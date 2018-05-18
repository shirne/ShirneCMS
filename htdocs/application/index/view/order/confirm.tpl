<extend name="public:base" />
<block name="body">
    <div class="container order-body">
        <form method="post" name="orderForm" onsubmit="return checkForm(this)" action="" >

            <div class="card">
                <div class="card-header">购买产品</div>
                <div class="card-body">
                    <ul class="list-group">
                        <volist name="products" id="prod">
                        <li class="list-group-item"><input type="radio" name="product_id" value="{$prod.id}" checked/> {$prod.product_title} {$prod.count}&times;{$prod.product_price}</li>
                        </volist>
                    </ul>
                    <div class="form-group">
                        <label >订单总额</label>
                        <div>￥ {$total_price}</div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">收货地址</div>
                <div class="card-body">
                    <ul class="list-group address_box">
                        <foreach name="address" item="add">
                            <li class="list-group-item">
                                <label>
                                    <input type="radio" name="address_id" value="{$add.address_id}" {$add.is_default?'checked':''}/> <span>{$add.recive_name} / {$add.mobile}</span>
                                    <div class="text-muted">{$add.province}&nbsp;{$add.city}&nbsp;{$add.area}&nbsp;{$add.address}</div>
                                </label>
                            </li>
                        </foreach>
                    </ul>

                    <a href="jsvascript:" class="btn btn-block btn-outline-secondary mt-3 add-address">添加收货地址</a>

                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="content">下单备注</label>
                        <textarea class="form-control" name="remark"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="balance_pay"><input type="checkbox" name="balance_pay" checked disabled />使用余额</label>
                        <div>￥ {$user.money|showmoney}</div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-block btn-primary">提交订单</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</block>
<block name="script">
    <script type="text/plain" id="addressTpl">
        <form>
        <div class="form-group">
            <label for="recive_name">收货人</label>
            <input type="text" name="recive_name" class="form-control" placeholder="收货人姓名">
        </div>
        <div class="form-group">
            <label for="mobile">联系电话</label>
            <input type="text" name="mobile" class="form-control" placeholder="收货人联系电话">
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
    <script type="text/javascript" src="__STATIC__/js/location.js"></script>
    <script type="text/javascript" src="__STATIC__/js/ChinaArea.js"></script>
    <script type="text/javascript">
        jQuery(function($){
            $('.add-address').click(function (e) {
                var issending=false;
                var dlg=new Dialog({
                    onshow:function(body){
                        body.find(".ChinaArea").jChinaArea({
                            aspnet:true
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
                                        var tpl='<li class="list-group-item"><label><input type="radio" name="address_id" value="{@address_id}" checked/> {@province}&nbsp;{@city}&nbsp;{@area}&nbsp;{@address}</label></li>';
                                        $('.address_box').append(tpl.compile(json.data));
                                    });
                                }else{
                                    dialog.alert(json.msg);
                                    issending=false;
                                }
                            }
                        })
                        return false;
                    }
                }).show($('#addressTpl').text(),'添加收货地址');
            });
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
            if('{$user.money}' * 1<'{$total_price}'*100){
                alert('积分不足');
                return false;
            }
        }
    </script>
</block>