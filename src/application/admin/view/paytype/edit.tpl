{extend name="public:base" /}

{block name="body"}

{include file="public/bread" menu="paytype_index" title="付款方式配置" /}

<div id="page-wrapper">
    <div class="page-header">修改付款方式</div>
    <div class="page-content">
    <form method="post" action=""  enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">付款方式名称</label>
            <input type="text" name="title" class="form-control" value="{$model.title}" placeholder="输入付款方式名称">
        </div>
        <div class="form-group">
            <label for="cc">状态</label>
            <label class="radio-inline">
                <input type="radio" name="status" id="status" value="1" {if $model['status']==1}checked="checked"{/if}>显示
            </label>
            <label class="radio-inline">
                <input type="radio" name="status" id="status" value="0" {if $model['status']!=1}checked="checked"{/if}>隐藏
            </label>
        </div>
        <div class="form-group">
            <label for="bb">付款方式类型</label>
            {foreach $paytypes as $key => $type}
                <label class="radio-inline"><input type="radio" name="type" value="{$key}" {$key==$model['type']?'checked="checked"':''}>&nbsp;{$type} </label>
            {/foreach}
        </div>
        <div class="form-group typebox type-wechat type-alipay">
            <label for="cc">二维码</label>
            <div class="input-group">
                <div class="custom-file">
                    <input type="file" class="custom-file-input" name="upload_qrcode"/>
                    <label class="custom-file-label" for="upload_qrcode">选择文件</label>
                </div>
            </div>
            {if !empty($model['qrcode'])}
                <figure class="figure">
                    <img src="{$model.qrcode}" class="figure-img img-fluid rounded" alt="image">
                    <figcaption class="figure-caption text-center">{$model.qrcode}</figcaption>
                </figure>
                <input type="hidden" name="delete_qrcode" value="{$model.qrcode}"/>
            {/if}
        </div>
        <div class="form-group typebox type-unioncard">
            <label for="cc">银行名称</label>
            <div class="input-group">
                <input type="text" name="bank" class="form-control" placeholder="请填写银行名称" value="{$model.bank}">
                <select id="cardlist" class="form-control" onchange="if(this.value)this.form.bank.value=this.value;">
                    <option value="">从列表中选择自动填写</option>
                    {foreach $banklist as $key => $v}
                    <option value="{$v}" {$v==$model['bank']?'selected':''}>{$v}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="form-group typebox type-unioncard">
            <label for="cardname">开户名称</label>
            <input type="text" name="cardname" class="form-control" placeholder="请填写开户名" value="{$model.cardname}">
        </div>
        <div class="form-group typebox type-unioncard">
            <label for="cardno">银行卡号</label>
            <input type="text" name="cardno" class="form-control" placeholder="请填写卡号" value="{$model.cardno}">
        </div>
        <div class="form-group">
            <input type="hidden" name="id" value="{$model.id}">
            <button type="submit" class="btn btn-primary">保存</button>
        </div>
    </form>
    </div>
</div>
{/block}
{block name="script"}
<script type="text/javascript">
    jQuery(function($){
        $('[name=type]').click(function() {
            var type=$(this).val();
            $('.typebox').hide();
            $('.type-'+type).show();
        });
        $('[name=type]:checked').trigger('click');
    });
</script>
{/block}