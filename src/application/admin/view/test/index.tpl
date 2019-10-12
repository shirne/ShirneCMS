<extend name="public:base" />

<block name="body">

    <include file="public/bread" menu="test_index" title="测试列表" />

    <div id="page-wrapper">

        <div class="row">
            <volist name="lists" id="item">
            <div class="col card" style="min-width: 150px;flex:0;">
                <div class="card-img-top" style="text-align: center"><i class="ion-md-{$item.icon|default='apps'}" style="font-size:100px;line-height: 100px;"></i></div>
                <div class="card-body">
                    <h5 class="card-title">{$item.title}</h5>
                    <a href="{:url('test/'.$item['action'])}" class="btn btn-primary">查看</a>
                </div>
            </div>
            </volist>
        </div>
    </div>
</block>