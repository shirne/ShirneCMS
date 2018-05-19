<extend name="public:base" />
<block name="body">
    <div class="container">
        <div class="page-header"><h1>系统公告</h1></div>
        <ul class="list-group">
        <foreach name="notices" item="v">
            <li class="list-group-item">
                <div>{$v.title} <span class="float_rigght">{$v.create_at|showdate}</span></div>
                <div>
                    {$v.content}
                </div>
            </li>
        </foreach>
    </ul>
    {$page|raw}
    </div>
</block>