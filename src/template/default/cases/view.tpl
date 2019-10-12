<extend name="public:base"/>

<block name="body">
    <div class="main">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item icon"><a href="/">首页</a></li>
                <volist name="categotyTree" id="cate">
                    <li class="breadcrumb-item"><a href="{:url("index/article/index",['name'=>$cate['name']])}">{$cate['title']}</a></li>
                </volist>
                <li class="breadcrumb-item active"><a href="{:url('index/article/index')}">{$article.title}</a></li>
            </ol>
        </div>

        <div class="container">
            <div class="card main_right news_list">
                <div class="card-body articlebody">
                    <h1>{$article.title}</h1>
                    <div class="info text-muted text-center">
                        分类:<a href="{:url('index/article/index',array('name'=>$category['name']))}">{$category.title}</a>
                        &nbsp;&nbsp;
                        发表时间:{$article.create_time|showdate}
                    </div>
                    <div class="container-fluid">
                        {$article.content|raw}
                    </div>
                </div>
            </div>
        </div>
    </div>
</block>