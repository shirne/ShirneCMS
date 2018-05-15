<extend name="public:base" />
<block name="body">
    <include file="member/side" />
    <div class="container">
        <form method="post" name="orderForm" onsubmit="return checkForm(this)" action="{:U('apply')}" >

            <div class="card">
                <div class="card-header">购买产品</div>
                <div class="card-body">
                    {$model.content|htmlspecialchars_decode}
                </div>
            </div>
            <div class="card">
                <div class="card-header">下单兑换</div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item"><input type="radio" name="product_id" value="{$model.id}" checked/> {$model.title}</li>
                    </ul>
                    <div class="form-group">
                        <label >订单总积分</label>
                        <div><i class="fa fa-circle-o"></i> {$model.price}</div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">收货地址</div>
                <div class="card-body">
                    <if condition="empty($address)">
                        <div class="form-group">
                            <label for="recive_name">收货人</label>
                            <input type="text" name="recive_name" class="form-control" placeholder="收货人姓名">
                        </div>
                        <div class="form-group">
                            <label for="mobile">联系电话</label>
                            <input type="text" name="mobile" class="form-control" value="{$model.mobile}" placeholder="收货人联系电话">
                        </div>
                        <div class="form-group">
                            <label for="province_select">所在地区</label>
                            <div class="input-group" id="ChinaArea">
                                <select name="province_select" class="form-control"></select>
                                <input type="hidden" name="province" value="{$model.province}"/>
                                <span class="input-group-addon"></span>
                                <select name="city_select" class="form-control"></select>
                                <input type="hidden" name="city" value="{$model.city}"/>
                                <span class="input-group-addon"></span>
                                <input type="hidden" name="area" value="{$model.area}"/>
                                <select name="area_select" class="form-control"></select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address">详细地址</label>
                            <input type="text" name="address" class="form-control" value="{$model.address}" >
                        </div>
                        <else/>
                        <ul class="list-group">
                            <foreach name="address" item="add">
                                <li class="list-group-item"><label><input type="radio" name="address_id" value="{$add.address_id}" {$add.is_default?'checked':''}/> {$add.province}&nbsp;{$add.city}&nbsp;{$add.area}&nbsp;{$add.address}</label></li>
                            </foreach>
                        </ul>
                    </if>

                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="content">下单备注</label>
                        <textarea class="form-control" name="content"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="balance_pay"><input type="checkbox" name="balance_pay" checked disabled />使用积分</label>
                        <div><i class="fa fa-circle-o"></i> {$user.money|showmoney}</div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">提交订单</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</block>
<block name="script">
    <script type="text/javascript" src="__STATIC__/js/location.js"></script>
    <script type="text/javascript" src="__STATIC__/js/ChinaArea.js"></script>
    <script type="text/javascript">
        jQuery(function($){
            $(document).ready(function() {
                $("#ChinaArea").jChinaArea({
                    aspnet:true,
                    s1:"{$model.province}",
                    s2:"{$model.city}",
                    s3:"{$model.area}"
                });
            });
        })
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
            if('{$user.money}' * 1<'{$model.price}'*100){
                alert('积分不足');
                return false;
            }
        }
    </script>
</block>