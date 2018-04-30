
<div class="panel pull-left main_left">
    <div class="panel-heading"><h3>资讯中心</h3></div>
    <div class="panel-body">

        <div id="news" class="list">
            <Volist name="categories[0]" id="cate">
            <div class="panel">
                <a class="Level_1" href="{:url('Post/index',array('name'=>$cate['name']))}"> {$cate.title} </a>
            </div>
            </Volist>
        </div>


    </div>
</div>