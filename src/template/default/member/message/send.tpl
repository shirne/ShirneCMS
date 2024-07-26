{extend name="public:base" /}
{block name="body"}
<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-3">
            {include file="member:_side" /}
        </div>
        <div class="col-9">
            <div class="page-header" style="border:0">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link" href="{:aurl('index/member.message/index')}">收到的消息</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{:aurl('index/member.message/sendlist')}">发送的消息</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="javascript:">发送消息</a>
                    </li>
                </ul>
            </div>
            <div class="page-content">
                <form role="form" method="post" action="">
                    <div class="form-row form-group">
                        <label for="toUsername" class="col-2 control-label">收信人：</label>
                        <div class="col-8">
                            <input type="hidden" class="form-control" name="member_id" value="{$member_id}" />
                            <div class="input-group">
                                <input type="text" class="form-control" id="toUsername" readonly
                                    value="{$toMember.username|default=''}" />
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary pickUser" type="button"
                                        id="button-addon2">选择收信人</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row form-group">
                        <label for="title" class="col-2 control-label">标题：</label>
                        <div class="col-8">
                            <input type="text" class="form-control" name="title" />
                        </div>
                    </div>
                    <div class="form-row form-group">
                        <label for="attachment" class="col-2 control-label">附件</label>
                        <div class="col-5">
                            <div class="custom-file">
                                <input type="file" name="attachment" class="custom-file-input" />
                                <label class="custom-file-label" for="customFile"></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-row form-group">
                        <label for="content" class="col-2 control-label">内容：</label>
                        <div class="col-8">
                            <textarea name="content" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="form-row align-content-center submitline">
                        <div class="form-group  offset-2">
                            <button type="submit" class="btn btn-primary create pl-5 pr-5">发送</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
{/block}
{block name="script"}
<script src="__STATIC__/js/bs-custom-file-input.min.js"></script>
<script type="text/javascript">
    jQuery(function ($) {
        bsCustomFileInput.init()
        $('.pickUser').click(function () {
            dialog.pickUser(function (user) {
                $('#toUsername').val(user.username + '/' + user.nickname + '/' + user.mobile)
                $('[name=member_id]').val(user.id)
            })
        })
    })
</script>
{/block}