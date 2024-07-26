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
                        <a class="nav-link {$active==0?'active':''}"
                            href="{:aurl('index/member.message/index')}">收到的消息</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {$active==1?'active':''}"
                            href="{:aurl('index/member.message/sendlist')}">发送的消息</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{:aurl('index/member.message/send')}">发送消息</a>
                    </li>
                </ul>
            </div>

            <div class="page-content">
                <ul class="list-group">
                    <li class="list-group-item">
                        <div class="media">
                            {if empty($message['from_avatar'])}
                            <svg class="bd-placeholder-img mr-3" width="64" height="64"
                                xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: 64x64"
                                preserveAspectRatio="xMidYMid slice" focusable="false">
                                <style>
                                    .avatar_text {
                                        font: 28px sans-serif;
                                    }
                                </style>
                                <title>Placeholder</title>
                                <rect width="100%" height="100%" fill="#6c757d"></rect><text x="50%" y="50%"
                                    fill="#dee2e6" dy=".3em" dx="-.5em"
                                    class="avatar_text">{$message.from_nickname|default='系'|mb_substr=0,1}</text>
                            </svg>
                            {else/}
                            <img src="{$message['from_avatar']}?w=64&h=64" style="width: 64px; height: 64px;"
                                class="avatar mr-3" alt="...">
                            {/if}
                            <div class="media-body">
                                <span class="float-right">{$message.create_time|friendlytime}</span>
                                <h5 class="mt-0">{if
                                    $message['from_member_id']==0}系统消息{else}{$message.from_nickname}{/if}</h5>
                                <p class="text-secondary ">{$message.title|default=''}</p>
                                <p class="text-secondary ">{$message.content|default=''|raw}</p>
                                <div class="float-right">
                                    <a class="btn btn-outline-secondary btn-confirm" data-confirm="确定删除该消息"
                                        href="{:aurl('index/member.message/del',array('id'=>$message['message_id']))}">删除</a>
                                </div>
                            </div>
                        </div>
                    </li>
                    {foreach $lists as $key => $add}
                    <li class="list-group-item">
                        <div class="media">
                            {if empty($add['from_avatar'])}
                            <svg class="bd-placeholder-img mr-3" width="64" height="64"
                                xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: 64x64"
                                preserveAspectRatio="xMidYMid slice" focusable="false">
                                <style>
                                    .avatar_text {
                                        font: 28px sans-serif;
                                    }
                                </style>
                                <title>Placeholder</title>
                                <rect width="100%" height="100%" fill="#6c757d"></rect><text x="50%" y="50%"
                                    fill="#dee2e6" dy=".3em" dx="-.5em"
                                    class="avatar_text">{$add.from_nickname|default='系'|mb_substr=0,1}</text>
                            </svg>
                            {else/}
                            <img src="{$add['from_avatar']}?w=64&h=64" style="width: 64px; height: 64px;"
                                class="avatar mr-3" alt="...">
                            {/if}
                            <div class="media-body">
                                <span class="float-right">{$add.create_time|friendlytime}</span>
                                <h5 class="mt-0">{if $add['from_member_id']==0}系统消息{else}{$add.from_nickname}{/if}</h5>
                                <p class="text-secondary ">{$add.title|default=''}</p>
                                <p class="text-secondary ">{$add.content|default=''|raw}</p>
                                <div class="float-right">
                                    <a class="btn btn-outline-secondary btn-confirm" data-confirm="确定删除该消息"
                                        href="{:aurl('index/member.message/del',array('id'=>$add['message_id']))}">删除</a>
                                </div>
                            </div>
                        </div>
                    </li>
                    {/foreach}
                </ul>
                {if $message['from_member_id']>0}
                <form role="form" class="mt-5" method="post" action="{:aurl('index/member.message/send')}">
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
                            <input type="hidden" name="reply_id" value="{$message.message_id}" />
                            <button type="submit" class="btn btn-primary create pl-5 pr-5">回复</button>
                        </div>
                    </div>
                </form>
                {/if}
            </div>
        </div>
    </div>
</div>
{/block}
{block name="script"}
<script src="__STATIC__/js/bs-custom-file-input.min.js"></script>
<script>
    jQuery(function ($) {
        bsCustomFileInput.init()
        $('.media').unbind().click(function () {
            var id = $(this).data('id')
            if (id) {
                $.ajax({
                    url: "{:aurl('index/member.message/read')}",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        id: id
                    },
                    success: function (json) {

                    }
                })
            }
        })
    })
</script>
{/block}