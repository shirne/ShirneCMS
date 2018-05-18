<extend name="public:base" />
<block name="body">
    <div class="container">
        <div class="page-header">
            <h1>收货地址</h1>
        </div>
        <form role="form" method="post" action="{:url('index/member/addressAdd',array('id'=>$address['address_id']))}">
            <div class="form-group">
                <label for="recive_name" class="col-2 control-label">收货人：</label>
                <div class="col-10">
                    <input type="text" class="form-control" name="recive_name" value="{$address.recive_name}"/>
                </div>
            </div>
            <div class="form-group">
                <label for="mobile" class="col-2 control-label">电话：</label>
                <div class="col-10">
                    <input type="text" class="form-control" name="mobile" value="{$address.mobile}"/>
                </div>
            </div>
            <div class="form-group">
                <label for="province_select" class="col-2 control-label">所在地区</label>
                <div class="col-10">
                <div class="input-group" id="ChinaArea">
                    <select name="province_select" class="form-control"></select>
                    <input type="hidden" name="province" value="{$address.province}"/>
                    <span class="input-group-addon"></span>
                    <select name="city_select" class="form-control"></select>
                    <input type="hidden" name="city" value="{$address.city}"/>
                    <span class="input-group-addon"></span>
                    <input type="hidden" name="area" value="{$address.area}"/>
                    <select name="area_select" class="form-control"></select>
                </div>
                </div>
            </div>
            <div class="form-group">
                <label for="code" class="col-2 control-label">邮政编码</label>
                <div class="col-10">
                <input type="text" name="code" class="form-control" value="{$address.code}">
                </div>
            </div>
            <div class="form-group">
                <label for="address" class="col-2 control-label">详细地址</label>
                <div class="col-10">
                <input type="text" name="address" class="form-control" value="{$address.address}" >
                </div>
            </div>
            <div class="form-group">
                <label for="is_default" class="col-2 control-label">是否默认</label>
                <div class="col-10">
                <input type="checkbox" name="is_default" value="1" {$address['is_default']?'checked':''} />
                </div>
            </div>
            <div class="form-group submitline">
                <div class="col-offset-2 col-10">
                    <button type="submit" class="btn btn-primary create">提交保存</button>
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
            $("#ChinaArea").jChinaArea({
                aspnet:true,
                s1:"{$model.province}",
                s2:"{$model.city}",
                s3:"{$model.area}"
            });
        })
    </script>
</block>