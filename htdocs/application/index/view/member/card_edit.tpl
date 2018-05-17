<extend name="public:base" />
<block name="body">
    <div class="main-content">
        <div class="page-header">
            <h1>银行卡</h1>
        </div>
        <form class="form-horizontal registerForm" role="form" method="post" action="{:url('index/member/cardedit',array('id'=>$card['id']))}">
            <div class="form-group">
                <label for="bank">银行名称</label>
                <div class="input-group">
                    <input type="text" name="bank" value="{$card.bank}" class="form-control" placeholder="请填写银行名称">
                    <span class="input-group-addon">快速填写</span>
                    <select id="cardlist" class="form-control" onchange="if(this.value)this.form.bank.value=this.value;">
                        <option value="">从列表中选择自动填写</option>
                        <foreach name="banklist" item="v">
                            <option value="{$v}" >{$v}</option>
                        </foreach>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="bankname">开户行</label>
                <input type="text" name="bankname" value="{$card.bankname}" class="form-control" placeholder="请填写开户名">
            </div>
            <div class="form-group">
                <label for="cardname">开户名称</label>
                <input type="text" name="cardname" value="{$card.cardname}" class="form-control" placeholder="请填写开户名">
            </div>
            <div class="form-group">
                <label for="cardno">银行卡号</label>
                <input type="text" name="cardno" value="{$card.cardno}" class="form-control" placeholder="请填写卡号">
            </div>
            <div class="form-group">
                <label for="is_default" class="col-2 control-label">是否默认</label>
                <div class="col-10">
                <input type="checkbox" name="is_default" value="1" {$card['is_default']?'checked':''} />
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
</block>