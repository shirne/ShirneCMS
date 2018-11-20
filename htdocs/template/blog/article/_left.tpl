<div class="col-lg-3 sidecolumn">
    <php>
        $catelist=$categories[$topCategory['id']];
    </php>
    <div class="card">
        <div class="card-header">
            {$topCategory.title}
        </div>
        <div class="card-body">
            <div class="list-group">
                <Volist name="catelist" id="c">
                    <a class="list-group-item {$c['id']==$category['id']?'active':''}" href="{:url('index/article/index',['name'=>$c['name']])}">{$c.title}</a>
                </Volist>
            </div>
        </div>
    </div>
</div>