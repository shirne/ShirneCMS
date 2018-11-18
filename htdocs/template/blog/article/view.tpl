<extend name="public:base"/>

<block name="body">
    <div class="main">
        <div class="container view-body">
            <div class="row">
                <div class="col-md-9">
                    <div class="article-body">
                        <h1 class="article-title">{$article.title}</h1>
                        <div class="article-info text-muted text-center">
                            <a href="{:url('index/article/index',array('name'=>$category['name']))}">{$category.title}</a>
                            &nbsp;&nbsp;
                            <i class="ion-md-calendar"></i>&nbsp;{$article.create_time|showdate}
                        </div>
                        <div class="article-content">
                            <div>
                            {$article.content|raw}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 sidecolumn">
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
            </div>
        </div>
    </div>
</block>