<extend name="public:base"/>

<block name="body">
    <div class="main">
        <div class="subbanner">
            <div class="inner" style="background-image:url({:getAdImage($topCategory['name'])})"></div>
        </div>

        <div class="container list-body">
            <div class="row">
            <div class="col-lg-9">
                <ul class="list-group article-list">
                    <php>$empty='<li class="list-group-item empty-box"><p class="empty">暂时没有内容</p></li>';</php>
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
            <include file="article:_left" />
            </div>
        </div>
    </div>
</block>