{extend name="public:base" /}
{block name="body"}
    <div class="container">
        <div class="page-header">
            <h1>收货地址</h1>
        </div>
        <form role="form" method="post" action="">
            <div class="form-row form-group">
                <label for="receive_name" class="col-2 control-label">收货人：</label>
                <div class="col-10">
                    <input type="text" class="form-control" name="receive_name" value="{$address.receive_name}"/>
                </div>
            </div>
            <div class="form-row form-group">
                <label for="mobile" class="col-2 control-label">电话：</label>
                <div class="col-10">
                    <input type="text" class="form-control" name="mobile" value="{$address.mobile}"/>
                </div>
            </div>
            <div class="form-row form-group">
                <label for="province_select" class="col-2 control-label">所在地区</label>
                <div class="col-10" id="ChinaArea">
                    <div class="input-group">
                        <select name="province_select" class="form-control"></select>
                        <select name="city_select" class="form-control"></select>
                        <select name="area_select" class="form-control"></select>
                    </div>
                    <input type="hidden" name="province" value="{$address.province}"/>
                    <input type="hidden" name="city" value="{$address.city}"/>
                    <input type="hidden" name="area" value="{$address.area}"/>
                </div>
            </div>
            <div class="form-row form-group">
                <label for="code" class="col-2 control-label">邮政编码</label>
                <div class="col-10">
                <input type="text" name="code" class="form-control" value="{$address.code}">
                </div>
            </div>
            <div class="form-row form-group">
                <label for="address" class="col-2 control-label">详细地址</label>
                <div class="col-10">
                <input type="text" name="address" class="form-control" value="{$address.address}" >
                </div>
            </div>
            <div class="form-row form-group">
                <label for="is_default" class="col-2 control-label">是否默认</label>
                <div class="col-10">
                <input type="checkbox" name="is_default" value="1" {$address['is_default']?'checked':''} />
                </div>
            </div>
            <div class="form-row form-group align-content-center submitline">
                <div class="col-12">
                    <button type="submit" class="btn btn-block btn-primary create">提交保存</button>
                </div>
            </div>
        </form>
    </div>
{/block}
{block name="script"}
    <script type="text/javascript" src="__STATIC__/js/location.min.js"></script>
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
{/block}