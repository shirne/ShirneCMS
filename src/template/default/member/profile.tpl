{extend name="public:base" /}
{block name="body"}
{include file="member/side" /}
<div class="container">
    <div class="page-header">
        <h1>个人资料</h1>
    </div>
    <div class="page-content">
        <form role="form" method="post" action="{:aurl('index/member/profile')}">
            <div class="form-row">
                <label for="realname" class="col-2 control-label">真实姓名：</label>
                <div class="form-group col-10">
                    <input type="text" class="form-control" name="realname" value="{$user.realname}" />
                </div>
            </div>
            <div class="form-row">
                <label for="email" class="col-2 control-label">邮箱地址：</label>
                <div class="form-group col-10">
                    <input type="text" class="form-control" name="email" value="{$user.email}" />
                </div>
            </div>
            <div class="form-row">
                <label for="mobile" class="col-2 control-label">手机号码：</label>
                <div class="form-group col-10">
                    <input type="text" class="form-control" name="mobile" value="{$user.mobile}" />
                </div>
            </div>
            <div class="form-row">
                <label for="is_default" class="col-2 control-label">性别：</label>
                <div class="form-group col-10">
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <label class="btn btn-secondary {$user.gender==1?'active':''}">
                            <input type="radio" name="gender" value="1" autocomplete="off"
                                {$user.gender==1?'checked':''}> 男士
                        </label>
                        <label class="btn btn-secondary {$user.gender==2?'active':''}">
                            <input type="radio" name="gender" value="2" autocomplete="off"
                                {$user.gender==2?'checked':''}> 女士
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <label for="mobile" class="col-2 control-label">生日：</label>
                <div class="form-group col-10">
                    <input type="text" class="form-control datepicker" name="birth" value="{$user.birth}" />
                </div>
            </div>
            <div class="form-row">
                <label for="mobile" class="col-2 control-label">QQ号码：</label>
                <div class="form-group col-10">
                    <input type="text" class="form-control" name="qq" value="{$user.qq}" />
                </div>
            </div>
            <div class="form-row">
                <label for="mobile" class="col-2 control-label">微信号：</label>
                <div class="form-group col-10">
                    <input type="text" class="form-control" name="wechat" value="{$user.wechat}" />
                </div>
            </div>
            <div class="form-row align-content-center submitline">
                <div class="form-group col-12">
                    <button type="submit" class="btn btn-block btn-primary">提交保存</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script src="__STATIC__/moment/min/moment.min.js"></script>
<script src="__STATIC__/moment/locale/zh-cn.js"></script>
<script src="__STATIC__/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
{/block}