{extend name="public:base" /}
{block name="body"}
<div class="container">
    <div class="page-header">
        <h1>发票资料</h1>
    </div>
    <form role="form" method="post" action="">
        <div class="form-row form-group">
            <label for="title" class="col-2 control-label">发票抬头：</label>
            <div class="col-10">
                <input type="text" class="form-control" name="title" value="{$invoice.title}" />
            </div>
        </div>
        <div class="form-row form-group">
            <label for="type" class="col-2 control-label">发票类型</label>
            <div class="col-10">
                <select name="type" class="form-control">
                    <option value="0">普通发票</option>
                    <option value="1" {$invoice['type']==1?' selected':''}>增值税发票</option>
                </select>
            </div>
        </div>
        <div class="form-row form-group">
            <label for="tax_no" class="col-2 control-label">纳税人识别号</label>
            <div class="col-10">
                <input type="text" name="tax_no" class="form-control" value="{$invoice.tax_no}">
            </div>
        </div>
        <div class="form-row form-group type1">
            <label for="address" class="col-2 control-label">注册地址</label>
            <div class="col-10">
                <input type="text" name="address" class="form-control" value="{$invoice.address}">
            </div>
        </div>
        <div class="form-row form-group type1">
            <label for="telephone" class="col-2 control-label">注册电话</label>
            <div class="col-10">
                <input type="text" class="form-control" name="telephone" value="{$invoice.telephone}" />
            </div>
        </div>
        <div class="form-row form-group type1">
            <label for="bank" class="col-2 control-label">开户银行</label>
            <div class="col-10">
                <input type="text" class="form-control" name="bank" value="{$invoice.bank}" />
            </div>
        </div>
        <div class="form-row form-group type1">
            <label for="caedno" class="col-2 control-label">银行账号</label>
            <div class="col-10">
                <input type="text" class="form-control" name="caedno" value="{$invoice.caedno}" />
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
<script>
    jQuery(function ($) {
        $('[name=type]').change(function (e) {
            if ($(this).val() == 1) {
                $('.type1').show();
            } else {
                $('.type1').hide();
            }
        }).trigger('change');
    })
</script>
{/block}