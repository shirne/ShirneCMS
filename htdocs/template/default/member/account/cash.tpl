<extend name="public:base" />
<block name="body">
    <div class="container">
        <div class="page-header"><h1>我要提现</h1></div>
        <div class="container">
            <form action="" method="post" onsubmit="return checkMoney(this)" class="form-horizontal container-fluid">
                <div class="form-group">
                    可提现金额：￥{$user.money|showmoney}
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                        <span class="input-group-text">提现金额：</span>
                        </div>
                        <input class="form-control amount" name="amount"/>
                    </div>
                    <div class="help-block text-muted" id="helpContent"></div>
                </div>
                <if condition="empty($cards)">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">银行名称：</span>
                            </div>
                            <input type="text" name="bank" class="form-control" placeholder="请填写银行名称">
                            <div class="input-group-middle"><span class="input-group-text">快速填写</span></div>
                            <select id="cardlist" class="form-control" onchange="if(this.value)this.form.bank.value=this.value;">
                                <option value="">从列表中选择自动填写</option>
                                <foreach name="banklist" item="v">
                                    <option value="{$v}" >{$v}</option>
                                </foreach>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">开&ensp;户&ensp;行：</span>
                        </div>
                        <input type="text" name="bankname" class="form-control" placeholder="请填写开户名">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">开户名称：</span>
                        </div>
                        <input type="text" name="cardname" class="form-control" placeholder="请填写开户名">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">银行卡号：</span>
                        </div>
                        <input type="text" name="cardno" class="form-control" placeholder="请填写卡号">
                        </div>
                    </div>
                    <else/>
                    <ul class="list-group">
                        <foreach name="cards" item="card">
                            <li class="list-group-item"><label><input type="radio" name="card_id" value="{$card.id}" {$card['is_default']?'checked':''}/> {$card.bank}&nbsp;{$card.cardno|showcardno}</label></li>
                        </foreach>
                    </ul>
                </if>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">备注说明：</span>
                        </div>
                        <input class="form-control" name="remark"/>
                    </div>
                </div>
                <div class="form-group"><input type="submit" class="btn btn-primary" value="提交" /></div>
            </form>
        </div>
    </div>
</block>
<block name="script">
    <script type="text/javascript">
        var cash_fee=parseInt('{$config.cash_fee}');
        var cash_limit=parseInt('{$config.cash_limit}');;
        var balance=parseInt('{$user.money}|showmoney');
        var pass=false;
        $('.amount').bind('input',function(e){
            pass=false;
            var v=$(this).val();
            if(v){
                v=parseFloat(v);
                if(v<cash_limit){
                    $('#helpContent').html('最低提现金额￥'+cash_limit);
                    return;
                }
                if(cash_power>0 && v % cash_power>0){
                    $('#helpContent').html('提现金额必需是'+cash_power+'的倍数');
                    return;
                }
                if(v>balance){
                    $('#helpContent').html('可提现金额不足');
                }else{
                    var fee=v*cash_fee*.01;
                    var release=v-fee;
                    $('#helpContent').html((cash_fee>0?('手续费：￥'+fee+'，'):'')+'实际到账：￥'+release);
                    pass=true;
                }
            }else{
                $('#helpContent').html('请输入提现金额（最低金额 ￥'+cash_limit+'）'+(cash_fee>0?('，手续费 '+cash_fee+'%'):'')+'。');
            }
        }).trigger('input');
        function checkMoney(form){
            if(!pass){
                return false;
            }
            return true;
        }
    </script>
</block>