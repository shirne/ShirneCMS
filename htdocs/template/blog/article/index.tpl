<extend name="public:base"/>

<block name="body">
    <div class="main">
        <div class="subbanner">
            <div class="inner" style="background-image:url({:getAdImage('news')})"></div>
        </div>

        <div class="container list-body">
            <div class="row">
            <div class="col-md-9">
                <ul class="list-group article-list">
                    <php>$empty='<li class="list-group-item"><p class="empty">暂时没有内容</p></li>';</php>
                    <Volist name="lists" id="art" empty="$empty">
                        <li class="list-group-item">
                            <if condition="!empty($art['cover'])">
                                <a class="list-img" style="background-image:url({$art.cover})">
                                    <img class="card-img-top" src="{$art.cover}" alt="Card image cap">
                                </a>
                            </if>
                            <div class="art-view">
                                <h3><a href="{:url('index/article/view',['id'=>$art['id']])}">{$art.title}</a></h3>
                                <div class="desc">
                                    {$art.description}
                                </div>
                                <div class="text-muted">
                                    <span><i class="ion-md-time"></i> {$art.create_time|showdate}</span>
                                </div>
                            </div>
                        </li>
                    </Volist>
                </ul>
                {$page|raw}
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