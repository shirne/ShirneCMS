<extend name="public:base" />
<block name="body">
    <div class="container">
        <div class="page-header"><h1>系统公告</h1></div>
        <ul class="list-group">
            <php>$empty='<span class="col-12 empty">暂时没有公告</span>';</php>
        <foreach name="notices" empty="$empty" item="v">
            <li class="list-group-item">
                <div>{$v.title} <span class="float-right badge badge-secondary">{$v.create_time|showdate}</span></div>
                <div class="bg-light p-2 mt-3">
                    {$v.content|raw}
                </div>
            </li>
        </foreach>
        </ul>
        {$page|raw}
    </div>
</block>