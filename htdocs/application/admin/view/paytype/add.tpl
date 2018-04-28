<extend name="Public:Base" />

<block name="body">
<include file="Public/bread" menu="paytype_index" section="系统" title="付款方式" />

<div id="page-wrapper">
    <div class="page-header">添加付款方式</div>
    <div class="page-content">
    <form method="post" action="{:U('Paytype/add')}" enctype="multipart/form-data">
        <div class="form-group">
            <label for="aa">付款方式名称</label>
            <input type="text" name="title" class="form-control" id="aa" placeholder="输入付款方式名称">
        </div>
        <div class="form-group">
            <label for="cc">状态</label>
            <label class="radio-inline">
                <input type="radio" name="status" id="status" value="1" checked="checked">显示
            </label>
            <label class="radio-inline">
                <input type="radio" name="status" id="status" value="0" >隐藏
            </label>
        </div>
        <div class="form-group">
            <label for="bb">付款方式类型</label>
            <foreach name="paytypes" item="type">
                <label class="radio-inline"><input type="radio" name="type" value="{$key}">&nbsp;{$type} </label>
            </foreach>
        </div>
        <div class="form-group typebox type-wechat type-alipay">
            <label for="cc">二维码</label>
            <input type="file" name="qrcodefile" class="form-control" />
        </div>
        <div class="form-group typebox type-unioncard">
            <label for="cc">银行名称</label>
            <div class="input-group">
            <input type="text" name="bank" class="form-control" placeholder="请填写银行名称">
                <span class="input-group-addon">快速填写</span>
                <select id="cardlist" class="form-control" onchange="if(this.value)this.form.bank.value=this.value;">
                    <option value="">从列表中选择自动填写</option>
                    <foreach name="banklist" item="v">
                        <option value="{$v}" >{$v}</option>
                    </foreach>
                </select>
            </div>
        </div>
        <div class="form-group typebox type-unioncard">
            <label for="cc">开户名称</label>
            <input type="text" name="cardname" class="form-control" placeholder="请填写开户名">
        </div>
        <div class="form-group typebox type-unioncard">
            <label for="cc">银行卡号</label>
            <input type="text" name="cardno" class="form-control" placeholder="请填写卡号">
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">提交</button>
        </div>
    </form>
        </div>
</div>
</block>
<block name="script">
<script type="text/javascript">
    jQuery(function($){
        $('[name=type]').click(function() {
            var type=$(this).val();
            $('.typebox').hide();
            $('.type-'+type).show();
        }).eq(0).trigger('click');
    });

</script>
</block>