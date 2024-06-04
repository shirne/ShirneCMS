{extend name="public:base" /}
{block name="body"}
<div class="container">
    <div class="page-header">
        <h1>问题反馈</h1>
    </div>
    <ul class="list-group">
        {foreach $feedbacks as $key => $v}
        <li class="list-group-item">
            <div>我 <span class="badge badge-secondary">{$v.create_time|showdate}</span></div>
            <div>
                {$v.content}
            </div>
            {if $v['reply_time'] > 1}
            <div><span class="badge badge-primary">管理员回复</span>{$v.reply}</div>
            {else/}
            <span class="badge badge-danger">待回复</span>
            {/if}
        </li>
        {/foreach}
        {if $unreplyed < 1} <li class="list-group-item">
            <form action="" method="post" class="form-horizontal container-fluid">
                <div class="form-group">{$user.username}:</div>
                <div class="form-group">
                    <textarea name="content" class="form-control" id="" cols="30" rows="5"></textarea>
                </div>
                <div class="form-group"><input type="submit" class="btn btn-primary" value="提交" /></div>
            </form>
            </li>
            {/if}
    </ul>
    {$page|raw}
</div>
{/block}