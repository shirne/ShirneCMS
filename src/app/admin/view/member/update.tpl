{extend name="public:base" /}

{block name="body"}
    {include  file="public/bread" menu="member_index" title="会员信息"  /}

    <div id="page-wrapper">
        <div class="page-header">{if empty($model['id'])}添加{else/}编辑{/if}会员</div>
        <div id="page-content">
            <form action="" method="post">
                <div class="row">
                    <div class="col col-lg-6">
                        <div class="card">
                            <div class="card-header">主要资料</div>
                            <div class="card-body">
                                <div class="form-group form-row">
                                    <label class="form-label">用户名</label>
                                    <div class="col">
                                        <input class="form-control" type="text" name="username"
                                            value="{$model.username|default=''}" />
                                    </div>
                                </div>
                                <div class="form-group form-row">
                                    <label class="form-label">真实姓名</label>
                                    <div class="col">
                                        <input class="form-control" type="text" name="realname"
                                            value="{$model.realname|default=''}" />
                                    </div>
                                </div>
                                <div class="form-group form-row">
                                    <label class="form-label">邮箱</label>
                                    <div class="col">
                                        <input class="form-control" type="text" name="email" value="{$model.email|default=''}" />
                                    </div>
                                </div>
                                <div class="form-group form-row">
                                    <label class="form-label">手机号</label>
                                    <div class="col">
                                        <input class="form-control" type="text" name="mobile" value="{$model.mobile|default=''}" />
                                    </div>
                                </div>
                                {if !empty($model['id'])}
                                    <div class="form-group form-row">
                                        <label class="form-label">新密码</label>
                                        <div class="col">
                                            <input class="form-control" type="password" name="password"
                                                placeholder="不填写则不更改">
                                        </div>
                                    </div>
                                    {else /}
                                    <div class="form-group form-row">
                                        <label class="form-label">密码</label>
                                        <div class="col">
                                            <input class="form-control" type="password" name="password"
                                                placeholder="password">
                                        </div>
                                    </div>
                                    <div class="form-group form-row">
                                        <label class="form-label">确认密码</label>
                                        <div class="col">
                                            <input class="form-control" type="password" name="repassword"
                                                placeholder="repassword">
                                        </div>
                                    </div>
                                {/if}
                                <div class="form-group form-row">
                                    <label class="form-label">用户类型</label>
                                    <div class="col">
                                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                            {volist name="types" id="type" key="k"}
                                                <label
                                                    class="btn btn-outline-secondary{if isset($model['type']) && $key==$model['type']} active{/if}">
                                                    <input type="radio" name="type" value="{$key}" autocomplete="off"
                                                    {if isset($model['type']) && $key==$model['type']}checked{/if}>{$type}
                                                </label>
                                            {/volist}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group form-row">
                                    <label class="form-label">用户状态</label>
                                    <div class="col">
                                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                            <label class="btn btn-outline-secondary{$model['status']==1?' active':''}">
                                                <input type="radio" name="status" value="1" autocomplete="off"
                                                    {$model['status']==1?' checked':''}> 正常
                                            </label>
                                            <label class="btn btn-outline-secondary{$model['status']==0?' active':''}">
                                                <input type="radio" name="status" value="0" autocomplete="off"
                                                    {$model['status']==0?' checked':''}> 禁用
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col col-lg-6">
                        <div class="card">
                            <div class="card-header">社交资料</div>
                            <div class="card-body">
                                <div class="form-group form-row">
                                    <label class="form-label">头像</label>
                                    <div class="col"><img
                                            src="{$model.avatar|default='/static/images/avatar-default.png'}"
                                            class="rounded" width="40" height="40" alt="..."></div>
                                </div>
                                <div class="form-group form-row">
                                    <label class="form-label">昵称</label>
                                    <div class="col">
                                        <input class="form-control" type="text" name="nickname"
                                            value="{$model.nickname|default=''}" />
                                    </div>
                                </div>
                                <div class="form-group form-row">
                                    <label class="form-label">性别</label>
                                    <div class="col">
                                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                            <label class="btn btn-outline-secondary {if !empty($model['gender']) && $model['gender']==1}active{/if}">
                                                <input type="radio" name="gender" value="1" autocomplete="off"
                                                {if !empty($model['gender']) && $model['gender']==1}checked':''}> 男士
                                            </label>
                                            <label class="btn btn-outline-secondary {if !empty($model['gender']) && $model['gender']==2}active{/if}">
                                                <input type="radio" name="gender" value="2" autocomplete="off"
                                                {if !empty($model['gender']) && $model['gender']==2}checked{/if}> 女士
                                            </label>
                                            <label class="btn btn-outline-secondary {if empty($model['gender'])}active{/if}">
                                                <input type="radio" name="gender" value="0" autocomplete="off"
                                                {if empty($model['gender'])}checked{/if}> 其它
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group form-row">
                                    <label class="form-label">QQ</label>
                                    <div class="col">
                                        <input class="form-control" type="text" name="qq" value="{$model.qq|default=''}" />
                                    </div>
                                </div>
                                <div class="form-group form-row">
                                    <label class="form-label">微信号</label>
                                    <div class="col">
                                        <input class="form-control" type="text" name="wechat" value="{$model.wechat|default=''}" />
                                    </div>
                                </div>
                                <div class="form-group form-row">
                                    <label class="form-label">支付宝</label>
                                    <div class="col">
                                        <input class="form-control" type="text" name="alipay" value="{$model.alipay|default=''}" />
                                    </div>
                                </div>
                                <div class="form-group form-row">
                                    <label class="form-label">生日</label>
                                    <div class="col">
                                        <input class="form-control datepicker" type="text" name="birth"
                                            value="{$model.birth|default=''|showdate}" />
                                    </div>
                                </div>
                                <div class="form-group form-row areabox">
                                    <label class="form-label">地区</label>
                                    <div class="col">
                                            <input type="hidden" name="province" />
                                        <select name="province-id" class="form-control" id="province"></select>
                                    </div>
                                    <div class="col">
                                            <input type="hidden" name="city" />
                                        <select name="city-id" class="form-control" id="city"></select>
                                    </div>
                                    <div class="col">
                                            <input type="hidden" name="area" />
                                        <select name="area-id" class="form-control" id="area"></select>
                                    </div>
                                </div>
                                <div class="form-group form-row">
                                    <label class="form-label">地址</label>
                                    <div class="col">
                                        <input class="form-control" type="text" name="address"
                                            value="{$model.address|default=''}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <input type="hidden" name="id" value="{$model.id|default=''}">
                    <button class="btn btn-primary" type="submit">{if empty($model['id'])}添加{else/}保存{/if}</button>
                </div>


            </form>
        </div>
    </div>
{/block}
{block name="script"}
    <script type="text/javascript" src="__STATIC__/js/location.min.js"></script>
    <script type="text/javascript">
        jQuery(function () {
            var locobj = new Location()
            $(".areabox").jChinaArea({
                aspnet: true,
                s1:"{$model.province|default=''}",
                s2:"{$model.city|default=''}",
                s3:"{$model.area|default=''}"
            });
        })
    </script>
{/block}