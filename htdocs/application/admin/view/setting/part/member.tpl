<div class="form-row form-group">
    <label for="v-m_invite" class="col-3 col-md-2 text-right align-middle">邀请注册</label>
    <div class="col-9 col-md-8 col-lg-6">
        <div class="btn-group btn-group-toggle" data-toggle="buttons">
            <foreach name="setting.m_invite.data" item="value" key="k">
                <if condition="$k==$setting['m_invite']['value']">
                    <label class="btn btn-outline-secondary active">
                        <input type="radio" name="v-m_invite" value="{$k}" autocomplete="off" checked> {$value}
                    </label>
                    <else />
                    <label class="btn btn-outline-secondary">
                        <input type="radio" name="v-m_invite" value="{$k}" autocomplete="off"> {$value}
                    </label>
                </if>
            </foreach>
        </div>
    </div>
</div>
<div class="form-row form-group">
    <label for="v-m_register" class="col-3 col-md-2 text-right align-middle">强制注册</label>
    <div class="col-9 col-md-8 col-lg-6">
        <div class="btn-group btn-group-toggle" data-toggle="buttons">
        <foreach name="setting.m_register.data" item="value" key="k">
            <if condition="$k==$setting['m_register']['value']">
                <label class="btn btn-outline-secondary active">
                    <input type="radio" name="v-m_register" value="{$k}" autocomplete="off" checked> {$value}
                </label>
                <else />
                <label class="btn btn-outline-secondary">
                    <input type="radio" name="v-m_register" value="{$k}" autocomplete="off"> {$value}
                </label>
            </if>
        </foreach>
        </div>
    </div>
</div>
<div class="form-row form-group">
    <label for="v-m_checkcode" class="col-3 col-md-2 text-right align-middle">验证码</label>
    <div class="col-9 col-md-8 col-lg-6">
        <div class="btn-group btn-group-toggle" data-toggle="buttons">
        <foreach name="setting.m_checkcode.data" item="value" key="k">
            <if condition="$k==$setting['m_checkcode']['value']">
                <label class="btn btn-outline-secondary active">
                    <input type="radio" name="v-m_checkcode" value="{$k}" autocomplete="off" checked> {$value}
                </label>
                <else />
                <label class="btn btn-outline-secondary">
                    <input type="radio" name="v-m_checkcode" value="{$k}" autocomplete="off"> {$value}
                </label>
            </if>
        </foreach>
        </div>
    </div>
</div>
<div class="form-row form-group">
    <label for="v-autoaudit" class="col-3 col-md-2 text-right align-middle">订单自动审核</label>
    <div class="col-9 col-md-8 col-lg-6">
        <div class="btn-group btn-group-toggle" data-toggle="buttons">
        <foreach name="setting.autoaudit.data" item="value" key="k">
            <if condition="$k==$setting['autoaudit']['value']">
                <label class="btn btn-outline-secondary active">
                    <input type="radio" name="v-autoaudit" value="{$k}" autocomplete="off" checked> {$value}
                </label>
                <else />
                <label class="btn btn-outline-secondary">
                    <input type="radio" name="v-autoaudit" value="{$k}" autocomplete="off"> {$value}
                </label>
            </if>
        </foreach>
        </div>
    </div>
</div>
<div class="form-row form-group">
    <label for="v-cash_fee" class="col-3 col-md-2 text-right align-middle">提现限制</label>
    <div class="col-9 col-md-8 col-lg-6">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">最低</span>
            </div>
            <input type="text" class="form-control" name="v-cash_limit" value="{$setting['cash_limit']['value']}" placeholder="">
            <div class="input-group-middle">
                <span class="input-group-text">最高</span>
            </div>
            <input type="text" class="form-control" name="v-cash_max" value="{$setting['cash_max']['value']}" placeholder="">
        </div>
    </div>
</div>
<div class="form-row form-group">
    <label for="v-cash_fee" class="col-3 col-md-2 text-right align-middle">提现限制</label>
    <div class="col-9 col-md-8 col-lg-6">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">手续费</span>
            </div>
            <input type="text" class="form-control" name="v-cash_fee" value="{$setting['cash_fee']['value']}" placeholder="">
            <div class="input-group-middle">
                <span class="input-group-text">金额倍数</span>
            </div>
            <input type="text" class="form-control" name="v-cash_power" value="{$setting['cash_power']['value']}" placeholder="">
        </div>
    </div>
</div>

