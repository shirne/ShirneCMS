{extend name="public:base" /}
{block name="body"}
<div class="container">
    <div class="page-header">
        <h1>我要充值</h1>
    </div>
    <div class="page-content">
        {article:notice var="notice" name="member_recharge" /}
        {if !empty($notice)}
        <div class="alert alert-secondary" role="alert">
            {$notice.summary}
        </div>
        {/if}
        <form action="" method="post" onsubmit="return checkMoney(this)" class="form-horizontal"
            enctype="multipart/form-data">
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">充值金额</span>
                    </div>
                    <input class="form-control amount" name="amount" />
                </div>
                <div class="help-block text-muted" id="helpContent"></div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text">充值方式</span></div>
                    <select name="type_id" class="form-control">
                        <option value="">请选择充值方式</option>
                        {if !empty($config['appid'])}
                        <option value="wechat">微信支付(在线支付)</option>
                        {/if}
                        {foreach $types as $key => $v}
                        <option value="{$v.id}" data-paydata="{$v|json_encode}">{$v.title}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="help-block text-muted" id="cardContent"></div>
            </div>
            <div class="form-group transbill">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">转账单据</span>
                    </div>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="pay_bill">
                        <label class="custom-file-label" for="upload_qrcode">截图转账凭证在此处上传</label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">备注说明</span>
                    </div>
                    <input class="form-control" name="remark" />
                </div>
            </div>
            <div class="form-group"><input type="submit" class="btn btn-primary btn-block" value="提交"
                    {$hasRecharge?'disabled':''} /></div>
        </form>
    </div>
</div>
{/block}
{block name="script"}
<script type="text/javascript">
    if ('{$hasRecharge}' > 0) {
        dialog.alert('您有充值申请正在处理中，请等待处理完成再进行充值');
    }
    var recharge_limit = parseInt('{$config.recharge_limit}');
    var recharge_power = parseInt('{$config.recharge_power}');
    var recharge_max = parseInt('{$config.recharge_max}');
    var pass = false;
    $('.amount').bind('input', function (e) {
        pass = false;
        var v = $(this).val();
        if (v) {
            v = parseFloat(v);
            if (v < recharge_limit) {
                $('#helpContent').html('最低充值金额￥' + recharge_limit);
                return;
            }
            if (recharge_max > 0 && v > recharge_max) {
                $('#helpContent').html('最高充值金额￥' + recharge_max);
                return;
            }
            if (recharge_power > 0 && v % recharge_power > 0) {
                $('#helpContent').html('充值金额必需是' + recharge_power + '的倍数');
                return;
            }
            pass = true;
        } else {
            $('#helpContent').html('请输入充值金额（最低金额 ￥' + recharge_limit + '）。');
        }
    }).trigger('input');
    function checkMoney(form) {
        if (!pass) {
            dialog.alert('请按要求填写金额');
            return false;
        }
        var paytype = $('[name=type_id]').val();
        if (!paytype) {
            dialog.alert('请选择付款方式');
            return false;
        }
        return true;
    }
    $('[name=type_id]').change(function (e) {
        var data = $(this).find('option:selected').data('paydata');
        //console.log(data);
        if (data) {
            $('.transbill').show();
            if (data.type == 'unioncard') {
                $('#cardContent').html('<div>' +
                    '<p>开户银行：' + data.bank + '</p>' +
                    '<p>开户名称：' + data.cardname + '</p>' +
                    '<p>银行卡号：' + data.cardno + '</p>' +
                    '</div>');
            } else {
                $('#cardContent').html('<div>' +
                    '<figure class="figure">' +
                    '<img src="' + data.qrcode + '" class="figure-img img-fluid rounded" alt="' + data.title + '">' +
                    '<figcaption class="figure-caption">请扫描二维码付款</figcaption>' +
                    '</figure>' +
                    '</div>');
            }
        } else if ($(this).val() == 'wechat') {
            $('#cardContent').html('<div>' +
                '<p>微信在线支付，无需审核</p>' +
                '</div>');
            $('[name=pay_bill]').val('');
            $('.transbill').hide();
        } else {
            $('.transbill').show();
            $('#cardContent').html('');
        }
    })
</script>
{/block}