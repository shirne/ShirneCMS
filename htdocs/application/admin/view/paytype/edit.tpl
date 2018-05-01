<extend name="Public:Base" />

<block name="body">

<include file="Public/bread" menu="paytype_index" section="系统" title="付款方式" />

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
                <input type="radio" name="status" id="status" value="1" <if condition="$model['status']==1">checked="checked"</if>>显示
            </label>
            <label class="radio-inline">
                <input type="radio" name="status" id="status" value="0" <if condition="$model['status']!=1">checked="checked"</if>>隐藏
            </label>
        </div>
        <div class="form-group">
            <label for="bb">付款方式类型</label>
            <foreach name="paytypes" item="type">
                <label class="radio-inline"><input type="radio" name="type" value="{$key}" {$key==$model['type']?'checked="checked"':''}>&nbsp;{$type} </label>
            </foreach>
        </div>
        <div class="form-group typebox type-wechat type-alipay">
            <label for="cc">二维码</label>
            <input type="hidden" name="qrcode" value="{$model.qrcode}" />
            <input type="file" name="qrcodefile" class="form-control" />
            <if condition="!empty($model['qrcode'])">
                <img src="{$model.qrcode}" style="max-height:80px;margin-top:10px;" />
            </if>
        </div>
        <div class="form-group typebox type-unioncard">
            <label for="cc">银行名称</label>
            <div class="input-group">
            <input type="text" name="bank" class="form-control" placeholder="请填写银行名称" value="{$model.bank}">
                <span class="input-group-addon">快速填写</span>
                <select id="cardlist" class="form-control" onchange="if(this.value)this.form.bank.value=this.value;">
                    <option value="">从列表中选择自动填写</option>
                    <foreach name="banklist" item="v">
                    <option value="{$v}" {$v==$model['bank']?'selected':''}>{$v}</option>
                    </foreach>
                </select>
            </div>
        </div>
        <div class="form-group typebox type-unioncard">
            <label for="cc">开户名称</label>
            <input type="text" name="cardname" class="form-control" placeholder="请填写开户名" value="{$model.cardname}">
        </div>
        <div class="form-group typebox type-unioncard">
            <label for="cc">银行卡号</label>
            <input type="text" name="cardno" class="form-control" placeholder="请填写卡号" value="{$model.cardno}">
        </div>
        <div class="form-group">
            <input type="hidden" name="id" value="{$model.id}">
            <button type="submit" class="btn btn-primary">保存</button>
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
        });
        $('[name=type]:checked').trigger('click');
    });
</script>
</block>