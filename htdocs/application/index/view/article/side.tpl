
<div class="card float-left main_left">
    <div class="card-header"><h3>资讯中心</h3></div>
    <div class="card-body">

        <div id="news" class="list">
            <Volist name="categories[0]" id="cate">
            <div class="panel">
                <a class="Level_1" href="{:url('Article/index',array('name'=>$cate['name']))}"> {$cate.title} </a>
            </div>
            </Volist>
        </div>


    </div>
</div>