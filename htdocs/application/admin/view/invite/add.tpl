<extend name="public:base"/>

<block name="body">

    <include file="public/bread" menu="invite_index" title="邀请码生成"/>

    <div id="page-wrapper">
        <div class="page-header">生成邀请码</div>
        <div id="page-content">
            <form action="{:url('Invite/add')}" method="post">
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">绑定邀请人</span></div>
                        <input class="form-control" type="text" name="member_id" placeholder="填写会员ID">
                        <div class="input-group-append">
                            <a class="btn btn-outline-secondary"><i class="ion-md-person"></i> 选择会员</a>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">绑定会员组</span></div>
                        <select name="level_id" class="form-control">
                            <option value="0">无</option>
                            <volist name="levels" id="lv">
                                <option value="{$lv['level_id']}" {$lv['is_default']==1?'selected':''}>
                                    {$lv['level_name']} ￥{$lv['level_price']}
                                    <if condition="$lv['is_default'] EQ 1">[默认]</if>
                                </option>
                            </volist>
                        </select>
                    </div>
                    <div class="form-text text-muted">绑定会员组后会员注册成功时将成为该会员组的成员</div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">有效期</span></div>
                        <input class="form-control datepicker" type="text" name="valid_date"
                               placeholder="不填写则不限制，格式 2016-09-18"/>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">邀请码长度</span></div>
                        <input class="form-control" type="number" name="length" value="16"
                               placeholder="要生成邀请码的长度(8-16)">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">生成数量</span></div>
                        <input class="form-control" type="number" max="1000" name="number"
                               placeholder="要生成邀请码的数量">
                    </div>
                </div>
                <div class="form-group">
                    <button class="btn btn-primary" type="submit">生成</button>
                </div>
            </form>
        </div>
    </div>

</block>
