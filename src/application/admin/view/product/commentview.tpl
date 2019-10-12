<extend name="public:base" />

<block name="body">

    <include file="public/bread" menu="article_index" title="查看评论" />

    <div id="page-wrapper">
        <div class="page-header">查看评论</div>
        <div class="page-content">
            <form method="post" action="{:url('product/commentview')}">
                <div class="form-group">
                    <div>
                        <img src="{$member.avatar}" width="40" height="40" class="rounded-circle size4" /> {$member.nickname} 在 <a href="{:url('index/product/view',['id'=>$product['id']])}" target="_blank">{$product.title}</a> 的评论&nbsp;&nbsp;<span class="text-muted">{$model.create_time|showdate}</span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="bb">评论内容</label>
                    <div class="form-control">{$model.content}</div>
                </div>
                <div class="form-group actions">
                    <if condition="$model['status'] EQ 0">
                        <a href="{:url('product/commentstatus',['id'=>$model['id'],'status'=>1])}" data-confirm="确定审核此评论？" class="btn btn-success">审核</a>
                    </if>
                    <if condition="$model['status'] EQ 2">
                        <a href="{:url('product/commentstatus',['id'=>$model['id'],'status'=>2])}" data-confirm="确定显示此评论？" class="btn btn-info">显示</a>
                        <else/>
                        <a href="{:url('product/commentstatus',['id'=>$model['id'],'status'=>2])}" data-confirm="确定隐藏此评论？" class="btn btn-info">隐藏</a>
                    </if>
                    <a href="{:url('product/commentdelete',['id'=>$model['id']])}" data-confirm="确定删除此评论？" class="btn btn-danger">删除</a>
                </div>
                <div class="form-group">
                    <label for="cc">回复内容</label>
                    <textarea class="form-control" name="reply" cols="30" rows="5">{$model.reply}</textarea>
                </div>
                <div class="form-group">
                    <input type="hidden" name="id" value="{$model.id}">
                    <button type="submit" class="btn btn-primary">回复</button>
                </div>
            </form>
        </div>
    </div>
</block>
<block name="script">
    <script type="text/javascript">
        jQuery(function ($) {
            $('.actions a').click(function (e) {
                e.preventDefault();
                e.stopPropagation();
                var url=$(this).attr('href');
                dialog.confirm($(this).data('confirm'),function () {
                    $.ajax({
                        url:url,
                        dataType:'JSON',
                        success:function (json) {
                            dialog.alert(json.msg,function () {
                                if(json.code==1){
                                    location.reload();
                                }
                            });
                        }
                    })
                })
            })
        })
    </script>
</block>