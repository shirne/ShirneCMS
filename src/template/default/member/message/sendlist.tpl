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
                        <a class="nav-link active" href="javascript:">发送的消息</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{:aurl('index/member.message/send')}">发送消息</a>
                    </li>
                </ul>
            </div>

            <div class="page-content">{if empty($lists)}
                <div class="empty">
                    <p>暂无消息</p>
                </div>
                {/if}
                <ul class="list-group">
                    {foreach $lists as $key => $add}
                    <li class="list-group-item">
                        <div class="media">
                            {if empty($add['to_avatar'])}
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
                                    class="avatar_text">{$add.to_nickname|default='系'|mb_substr=0,1}</text>
                            </svg>
                            {else/}
                            <img src="{$add['to_avatar']}?w=64&h=64" style="width: 64px; height: 64px;"
                                class="avatar mr-3" alt="...">
                            {/if}
                            <div class="media-body">
                                <span class="float-right">{$add.create_time|friendlytime}</span>
                                <h5 class="mt-0">{if $add['from_member_id']==0}系统消息{else}{$add.to_nickname}{/if}</h5>
                                <p class="text-secondary "><a
                                        href="{:aurl('index/member.message/list',['id'=>$add['message_id']])}">{$add.title|default=''}</a>
                                </p>
                                <p class="text-secondary ">{$add.content|default=''|raw}</p>
                                <div class="float-right">
                                    <a class="btn btn-outline-primary"
                                        href="{:aurl('index/member.message/list',['id'=>$add['message_id']])}">查看</a>
                                    <a class="btn btn-outline-secondary btn-confirm" data-confirm="确定删除该消息及相关回复？"
                                        href="{:aurl('index/member.message/del',['id'=>$add['message_id']])}">删除</a>
                                </div>
                            </div>
                        </div>
                    </li>
                    {/foreach}
                </ul>
                {$page|raw}
            </div>
        </div>
    </div>
</div>
{/block}
{block name="script"}
<script>
    jQuery(function ($) {
        $('.media').click(function () {
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
            location.href = "{:aurl('index/member.message/list',['id'=>'__ID__'])}".replace('__ID__', id)
        })
    })
</script>
{/block}