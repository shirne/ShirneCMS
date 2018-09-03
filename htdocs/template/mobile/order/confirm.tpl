<extend name="public:base"/>

<block name="body">
    <div class="page panel">
        <div class="page__hd">
            <h3 class="page__title">下单确认</h3>
        </div>
        <div class="page__bd" style="padding-bottom:80px;">
            <form method="post" name="orderForm" onsubmit="return checkForm(this)" action="" >
            <div class="weui-panel weui-panel_access">
                <div class="weui-panel__hd">购买产品</div>
                <div class="weui-panel__bd">
                    <volist name="products" id="prod">
                    <a href="javascript:void(0);" class="weui-media-box weui-media-box_appmsg">
                        <div class="weui-media-box__hd">
                            <img class="weui-media-box__thumb" src="{$prod.product_image}" alt="">
                        </div>
                        <div class="weui-media-box__bd">
                            <h4 class="weui-media-box__title">{$prod.product_title} </h4>
                            <p class="weui-media-box__desc">{$prod.count}&times;{$prod.product_price}</p>
                        </div>
                    </a>
                    </volist>
                    <a href="javascript:void(0);" class="weui-cell">
                        <div class="weui-cell__bd"><span class="float-right">￥ {$total_price}</span>订单总额</div>
                    </a>
                </div>
            </div>

                <div class="weui-cells__title">收货地址</div>
                <div class="weui-cells weui-cells_checkbox">
                    <volist name="addresses" id="address">
                        <label class="weui-cell weui-check__label" >
                            <div class="weui-cell__hd">
                                <input type="radio" class="weui-check" name="address_id" value="{$address.address_id}" {$add.is_default?'checked':''}>
                                <i class="weui-icon-checked"></i>
                            </div>
                            <div class="weui-cell__bd">
                                <p>{$address.recive_name} / {$address.mobile}</p>
                                <p>{$address.province}&nbsp;{$address.city}&nbsp;{$address.area}&nbsp;{$address.address}</p>
                            </div>
                        </label>
                    </volist>
                    <if condition="$isWechat">
                        <a href="javascript:" class="weui-cell weui-cell_link usewcaddress">
                            <div class="weui-cell__bd">微信地址导入</div>
                        </a>
                    </if>
                    <a href="{:url('index/member/address')}" class="weui-cell weui-cell_link">
                        <div class="weui-cell__bd">添加新地址</div>
                    </a>
                </div>
                <div class="weui-cells__title">下单备注</div>
                <div class="weui-cells weui-cells_form">
                    <div class="weui-cell">
                        <div class="weui-cell__bd">
                            <textarea class="weui-textarea" name="remark" placeholder="请输入文本" rows="3"></textarea>
                            <div class="weui-textarea-counter"><span class="text_counter">0</span>/200</div>
                        </div>
                    </div>
                </div>

                <div class="weui-cells__title">支付方式</div>
                <div class="weui-cells weui-cells_checkbox">
                    <if condition="$user['money'] EGT $total_price*100">
                        <label class="weui-cell weui-check__label">
                            <div class="weui-cell__hd">
                                <input type="radio" class="weui-check" name="pay_type" value="balance" checked="checked">
                                <i class="weui-icon-checked"></i>
                            </div>
                            <div class="weui-cell__bd">
                                <p>余额支付 {$user.money|showmoney}</p>
                            </div>
                        </label>
                        <else/>

                        <label class="weui-cell weui-check__label weui-disabled">
                            <div class="weui-cell__hd">
                                <input type="radio" class="weui-check" name="pay_type" value="balance" disabled>
                                <i class="weui-icon-checked"></i>
                            </div>
                            <div class="weui-cell__bd">
                                <p>余额支付 {$user.money|showmoney}</p>
                            </div>
                        </label>
                    </if>
                    <label class="weui-cell weui-check__label">
                        <div class="weui-cell__hd">
                            <input type="radio" class="weui-check" name="pay_type" value="wechat" {$user['money'] < $total_price*100?'checked':''}>
                            <i class="weui-icon-checked"></i>
                        </div>
                        <div class="weui-cell__bd">
                            <p>微信支付</p>
                        </div>
                    </label>
                </div>

                <div class="weui-btn-area">
                    <button class="weui-btn weui-btn_primary" type="submit" id="showTooltips">提交订单</button>
                </div>
            </form>
        </div>
    </div>
</block>
<block name="script">
    <script type="text/javascript" src="__STATIC__/js/location.js"></script>
    <script type="text/javascript">
        jQuery(function ($) {
            var issending=false;
            var addtpl='<label class="weui-cell weui-check__label" for="s11">\n' +
                '                            <div class="weui-cell__hd">\n' +
                '                                <input type="checkbox" class="weui-check" name="address_id" value="[@address_id]" checked="checked">\n' +
                '                                <i class="weui-icon-checked"></i>\n' +
                '                            </div>\n' +
                '                            <div class="weui-cell__bd">\n' +
                '                                <p>[@recive_name] / [@mobile]</p>\n' +
                '<p>[@province]&nbsp;[@city]&nbsp;[@area]&nbsp;[@address]</p>\n'+
                '                            </div>\n' +
                '                        </label>';
            $('.usewcaddress').click(function (e) {
                var self=this;
                wx.openAddress({
                    success: function (res) {

                        if(issending)return false;
                        issending=true;
                        var data={
                            recive_name : res.userName, // 收货人姓名
                            mobile : res.telNumber, // 收货人手机号码
                            code : res.postalCode, // 邮编
                            province : res.provinceName, // 国标收货地址第一级地址（省）
                            city : res.cityName, // 国标收货地址第二级地址（市）
                            area : res.countryName, // 国标收货地址第三级地址（国家）
                            address : res.detailInfo // 详细收货地址信息
                            //nationalCode : res.nationalCode // 收货地址国家码
                        };
                        $.ajax({
                            url:'{:url("index/member/addressAdd")}',
                            type:'POST',
                            dataType:'JSON',
                            data:data,
                            success:function(json){
                                if(json.code==1){
                                    $(self).before(addtpl.replace(/\[@([\w\d]+)\]/g,function (a,k) {
                                        return json.data[k]===undefined?'':json.data[k];
                                    }));
                                }else{
                                    weui.alert(json.msg);
                                    issending=false;
                                }
                            }
                        });
                    }
                });
            })
        });
        function checkForm(form) {
            if (!$('[name=address_id]:checked').val()) {
                alert('请选择收货地址');
                return false;
            }
            if($('[name=pay_type]:checked').val()=='balance') {
                if ('{$user.money}' * 1 < '{$total_price}' * 100) {
                    alert('余额不足');
                    return false;
                }
            }
        }
    </script>
</block>