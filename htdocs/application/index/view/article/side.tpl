
<div class="card main_left">
    <div class="card-header"><h5>资讯中心</h5></div>
    <div class="card-body">

        <div id="news-cate" class="list-group">
            <Volist name="categories[0]" id="cate">
                <a class="list-group-item Level_1" href="{:url('Article/index',array('name'=>$cate['name']))}"> {$cate.title} </a>
            </Volist>
        </div>

    </div>
</div>