<div class="col-lg-3 sidecolumn">
    {php}
        $catelist=$categories[$topCategory['id']];
    {/php}
    <div class="card">
        <div class="card-header">
            {$topCategory.title}
        </div>
        <div class="card-body">
            <div class="list-group">
                {volist name="catelist" id="c"}
                    <a class="list-group-item {$c['id']==$category['id']?'active':''}" title="{$c.title}" href="{:url('index/article/index',['name'=>$c['name']])}">{$c.title}</a>
                {/volist}
            </div>
        </div>
    </div>
</div>