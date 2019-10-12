<extend name="public:base" />
<block name="header">
    <style type="text/css">
        .helptext{text-align:center;padding-top:10px;}
        .helptext p{color:#999;}
    </style>
</block>
<block name="body">
    <div class="container">
        <div class="page-header"><h1>注册链接</h1></div>
        <div class="form-group text-center">
            <figure class="figure">
                <img src="{$qrurl}?v={$qrtime}" class="figure-img img-fluid rounded" alt="分享二维码">
                <figcaption class="figure-caption text-center">扫描二维码</figcaption>
            </figure>
        </div>
        <div class="form-group">
            <div class="col-xs-12">
                <textarea readonly class="form-control" id="shareurl">{$shareurl}</textarea>
            </div>
        </div>
        <div class="form-group">
            <div class="col-xs-12 helptext">
                <button class="btn btn-default clipboardbtn" data-clipboard-target="#shareurl">点击复制链接</button>
                <p>将链接发送给您推荐的会员注册使用</p>
            </div>
        </div>
    </div>
</block>
<block name="script">
    <script type="text/javascript" src="__STATIC__/js/clipboard.min.js"></script>
    <script type="text/javascript">
        jQuery(function($){
            var clipboard = new Clipboard('.clipboardbtn');
            //复制成功执行的回调，可选
            clipboard.on('success', function(e) {
                alert('复制成功')
            });

            //复制失败执行的回调，可选
            clipboard.on('error', function(e) {
                alert('复制失败，请手动选择复制')
            });
        })
    </script>
</block>