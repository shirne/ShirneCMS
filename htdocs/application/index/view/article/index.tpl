<extend name="public:base"/>

<block name="body">
    <div class="main">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item icon"><a href="/">首页</a></li>
                <volist name="categotyTree" id="cate">
                    <li class="breadcrumb-item"><a href="{:url("index/article/index",['name'=>$cate['name']])}">{$cate['title']}</a></li>
                </volist>
            </ol>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <include file="article/side"/>
                </div>
                <div class="col">
                    <div class="card main_right news_list">
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <php>$empty='<li class="empty">暂时没有内容</li>';</php>
                                <Volist name="lists" id="article" empty="$empty">
                                    <li class="media">
                                        <div class="media-body">
                                        <h5 class="mt-0 mb-1">
                                            <a href="{:url('index/article/view',array('id'=>$article['id']))}">{$article.title}</a>
                                        </h5>
                                        <div>{$article.content|cutstr=250}</div>
                                        </div>
                                    </li>
                                </Volist>
                            </ul>
                            {$page|raw}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</block>