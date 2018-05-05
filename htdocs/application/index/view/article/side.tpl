
<div class="card main_left">
    <php>
        if(empty($categotyTree)){ $sidetitle='资讯中心'; }
        else { $sidetitle=$categotyTree[0]['title'];}
    </php>
    <div class="card-header"><h5>{$sidetitle}</h5></div>
    <div class="card-body">

        <div id="news-cate" class="list-group">
            <php>
                if(empty($category)){ $catelist=$categories[0]; }
                else { $catelist=$categories[$category['pid']];}
            </php>
            <Volist name="catelist" id="cate">
                <a class="list-group-item Level_1" href="{:url('Article/index',array('name'=>$cate['name']))}">{$cate.title}</a>
            </Volist>
        </div>
    </div>
</div>