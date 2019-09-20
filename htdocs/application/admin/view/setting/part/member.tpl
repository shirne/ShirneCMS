
<div class="form-row form-group">
    <label for="v-m_open" class="col-3 col-md-2 text-right align-middle">会员系统</label>
    <div class="col-9 col-md-8 col-lg-6">
        <div class="btn-group btn-group-toggle mopengroup" data-toggle="buttons">
            <foreach name="setting.m_open.data" item="value" key="k">
                <if condition="$k==$setting['m_open']['value']">
                    <label class="btn btn-outline-secondary active">
                        <input type="radio" name="v-m_open" value="{$k}" autocomplete="off" checked> {$value}
                    </label>
                    <else />
                    <label class="btn btn-outline-secondary">
                        <input type="radio" name="v-m_open" value="{$k}" autocomplete="off"> {$value}
                    </label>
                </if>
            </foreach>
        </div>
    </div>
</div>
<div class="form-row form-group">
    <label for="v-m_register_open" class="col-3 col-md-2 text-right align-middle">开启注册</label>
    <div class="col-9 col-md-8 col-lg-6">
        <div class="btn-group btn-group-toggle mregopengroup" data-toggle="buttons">
            <foreach name="setting.m_register_open.data" item="value" key="k">
                <if condition="$k==$setting['m_register_open']['value']">
                    <label class="btn btn-outline-secondary active">
                        <input type="radio" name="v-m_register_open" value="{$k}" autocomplete="off" checked> {$value}
                    </label>
                    <else />
                    <label class="btn btn-outline-secondary">
                        <input type="radio" name="v-m_register_open" value="{$k}" autocomplete="off"> {$value}
                    </label>
                </if>
            </foreach>
        </div>
    </div>
</div>
<div class="form-row form-group regsetrow">
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
        <div class="text-muted">强制注册开启时，当用户从第三方授权登录(如：微信，QQ等)后，会进入临时账号状态，需要绑定或注册系统账号才能正常使用</div>
    </div>
</div>
<div class="form-row form-group regsetrow">
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
        <div class="text-muted">邀请注册关闭后，所有通过分享关系进入的会员，将不再绑定。开启时有分享人则绑定。强制邀请注册时则只能从系统的邀请码注册</div>
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
    <label for="v-autoaudit" class="col-3 col-md-2 text-right align-middle">{$setting['autoaudit']['title']}</label>
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
    <label for="v-commission_type" class="col-3 col-md-2 text-right align-middle">{$setting['commission_type']['title']}</label>
    <div class="col-9 col-md-8 col-lg-6">
        <div class="btn-group btn-group-toggle" data-toggle="buttons">
            <foreach name="setting.commission_type.data" item="value" key="k">
                <if condition="$k==$setting['commission_type']['value']">
                    <label class="btn btn-outline-secondary active">
                        <input type="radio" name="v-commission_type" value="{$k}" autocomplete="off" checked> {$value}
                    </label>
                    <else />
                    <label class="btn btn-outline-secondary">
                        <input type="radio" name="v-commission_type" value="{$k}" autocomplete="off"> {$value}
                    </label>
                </if>
            </foreach>
        </div>
        <div >
            <div class="text-muted">购买价为会员实际购买时的价格，可能有会员等级的折扣，等级特价等，销售价和成本价为产品规格中设置的价格</div>
        </div>
    </div>
</div>

<div class="form-row form-group">
    <label for="v-agent_start" class="col-3 col-md-2 text-right align-middle">{$setting['agent_start']['title']}</label>
    <div class="col-9 col-md-8 col-lg-6">
        <div class="btn-group btn-group-toggle" data-toggle="buttons">
            <foreach name="setting.agent_start.data" item="value" key="k">
                <if condition="$k==$setting['agent_start']['value']">
                    <label class="btn btn-outline-secondary active">
                        <input type="radio" name="v-agent_start" value="{$k}" autocomplete="off" checked> {$value}
                    </label>
                    <else />
                    <label class="btn btn-outline-secondary">
                        <input type="radio" name="v-agent_start" value="{$k}" autocomplete="off"> {$value}
                    </label>
                </if>
            </foreach>
        </div>
    </div>
</div>

<div class="form-row form-group">
    <label for="v-commission_delay" class="col-3 col-md-2 text-right align-middle">{$setting['commission_delay']['title']}</label>
    <div class="col-9 col-md-8 col-lg-6">
        <div class="btn-group btn-group-toggle" data-toggle="buttons">
            <foreach name="setting.commission_delay.data" item="value" key="k">
                <if condition="$k==$setting['commission_delay']['value']">
                    <label class="btn btn-outline-secondary active">
                        <input type="radio" name="v-commission_delay" value="{$k}" autocomplete="off" checked> {$value}
                    </label>
                    <else />
                    <label class="btn btn-outline-secondary">
                        <input type="radio" name="v-commission_delay" value="{$k}" autocomplete="off"> {$value}
                    </label>
                </if>
            </foreach>
        </div>
    </div>
</div>
<div class="form-row form-group">
    <label for="v-commission_delay_days" class="col-3 col-md-2 text-right align-middle">{$setting['commission_delay_days']['title']}</label>
    <div class="col-9 col-md-8 col-lg-6">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">达到发放时间后</span>
            </div>
            <input type="text" class="form-control" name="v-commission_delay_days" value="{$setting['commission_delay_days']['value']}" placeholder="">
            <div class="input-group-append">
                <span class="input-group-text">天</span>
            </div>
        </div>
    </div>
</div>
<div class="form-row form-group">
    <label for="v-cash_types" class="col-3 col-md-2 text-right align-middle">{$setting['cash_types']['title']}</label>
    <div class="col-9 col-md-8 col-lg-6">
        <div class="btn-group btn-group-toggle" data-toggle="buttons">
            <foreach name="setting.cash_types.data" item="value" key="k">
                <if condition="in_array($k,$setting['cash_types']['value'])">
                    <label class="btn btn-outline-secondary active">
                        <input type="checkbox" name="v-cash_types[]" value="{$k}" autocomplete="off" checked> {$value}
                    </label>
                    <else />
                    <label class="btn btn-outline-secondary">
                        <input type="checkbox" name="v-cash_types[]" value="{$k}" autocomplete="off"> {$value}
                    </label>
                </if>
            </foreach>
        </div>
        <div >
            <div class="text-muted">企业付款、微信红包、小程序红包，需要在微信支付中开通对应的功能才可以启用。<br />微信红包、小程序红包限额200,日限额10个/1000元。<br />支付商户开通条件：1、入驻时间超过90天 2、连续正常交易30天。</div>
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
            <div class="input-group-middle">
                <span class="input-group-text">金额倍数</span>
            </div>
            <input type="text" class="form-control" name="v-cash_power" value="{$setting['cash_power']['value']}" placeholder="">
        </div>
    </div>
</div>
<div class="form-row form-group">
    <label for="v-cash_fee" class="col-3 col-md-2 text-right align-middle">提现手续费</label>
    <div class="col-9 col-md-8 col-lg-6">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">手续费</span>
            </div>
            <input type="text" class="form-control" name="v-cash_fee" value="{$setting['cash_fee']['value']}" placeholder="">
            <div class="input-group-middle">
                <span class="input-group-text">%  最低</span>
            </div>
            <input type="text" class="form-control" name="v-cash_fee_min" value="{$setting['cash_fee_min']['value']}" placeholder="">
            <div class="input-group-middle">
                <span class="input-group-text">封顶</span>
            </div>
            <input type="text" class="form-control" name="v-cash_fee_max" value="{$setting['cash_fee_max']['value']}" placeholder="">
        </div>
    </div>
</div>