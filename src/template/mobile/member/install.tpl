{extend name="public:base"/}

{block name="body"}
<div class="weui-cells weui-cells_form">
    <div class="weui-cell">
        <div class="weui-cell__hd">
            <label class="weui-label">姓名</label>
        </div>
        <div class="weui-cell__bd">
            <input type="text" class="weui-input" placeholder="安装时联系人的称呼" />
        </div>
    </div>
    <div class="weui-cell">
        <div class="weui-cell__hd">
            <label class="weui-label">联系电话</label>
        </div>
        <div class="weui-cell__bd">
            <input type="text" class="weui-input" placeholder="安装时联系人的电话" />
        </div>
    </div>
    <div class="weui-cell">
        <div class="weui-cell__hd">
            <label class="weui-label">安装工程师</label>
        </div>
        <div class="weui-cell__bd">
            <input type="hidden" name="" value="" />
            <input type="text" class="weui-input" placeholder="系统根据您的安装地址自动选择就近的安装师傅" />
        </div>
    </div>
</div>

<div class="weui-cells__title">安装地址</div>
<div class="weui-cells weui-cells_checkbox">
    {volist name="addresses" id="address"}
    <label class="weui-cell weui-check__label" for="s11">
        <div class="weui-cell__hd">
            <input type="checkbox" class="weui-check" name="address_id" checked="checked">
            <i class="weui-icon-checked"></i>
        </div>
        <div class="weui-cell__bd">
            <p>{$address.username}</p>
        </div>
    </label>
    {/volist}
    <a href="{:aurl('index/member.address')}" class="weui-cell weui-cell_link">
        <div class="weui-cell__bd">添加新地址</div>
    </a>
</div>

<div class="weui-cells weui-cells_form" id="uploader">
    <div class="weui-cell">
        <div class="weui-cell__bd">
            <div class="weui-uploader">
                <div class="weui-uploader__hd">
                    <p class="weui-uploader__title">请上传门的正面图</p>
                    <div class="weui-uploader__info"></div>
                </div>
                <div class="weui-uploader__bd">
                    <ul class="weui-uploader__files" id="uploaderFiles">
                    </ul>
                    <div class="weui-uploader__input-box">
                        <input id="uploaderInput" class="weui-uploader__input" type="file" accept="image/*" multiple="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="weui-btn-area">
    <a class="weui-btn weui-btn_primary" href="javascript:" id="showTooltips">确定</a>
</div>
{/block}
{block name="script"}
<script type="text/javascript">
    var uploadCount = 0;
    weui.uploader('#uploader', {
        url: "{:aurl('index/member/uploadPicture')}",
        auto: true,
        type: 'file',
        fileVal: 'imageFile',
        compress: {
            width: 1600,
            height: 1600,
            quality: .8
        },
        onBeforeQueued: function (files) {
            // `this` 是轮询到的文件, `files` 是所有文件

            if (["image/jpg", "image/jpeg", "image/png", "image/gif"].indexOf(this.type) < 0) {
                weui.alert('请上传图片');
                return false; // 阻止文件添加
            }
            if (this.size > 10 * 1024 * 1024) {
                weui.alert('请上传不超过10M的图片');
                return false;
            }
            if (files.length > 5) { // 防止一下子选择过多文件
                weui.alert('最多只能上传5张图片，请重新选择');
                return false;
            }
            if (uploadCount + 1 > 5) {
                weui.alert('最多只能上传5张图片');
                return false;
            }

            ++uploadCount;

            // return true; // 阻止默认行为，不插入预览图的框架
        },
        onQueued: function () {
            console.log(this);

            // console.log(this.status); // 文件的状态：'ready', 'progress', 'success', 'fail'
            // console.log(this.base64); // 如果是base64上传，file.base64可以获得文件的base64

            // this.upload(); // 如果是手动上传，这里可以通过调用upload来实现；也可以用它来实现重传。
            // this.stop(); // 中断上传

            // return true; // 阻止默认行为，不显示预览图的图像
        },
        onBeforeSend: function (data, headers) {
            console.log(this, data, headers);
            // $.extend(data, { test: 1 }); // 可以扩展此对象来控制上传参数
            // $.extend(headers, { Origin: 'http://127.0.0.1' }); // 可以扩展此对象来控制上传头部

            // return false; // 阻止文件上传
        },
        onProgress: function (procent) {
            console.log(this, procent);
            // return true; // 阻止默认行为，不使用默认的进度显示
        },
        onSuccess: function (ret) {
            console.log(this, ret);
            // return true; // 阻止默认行为，不使用默认的成功态
        },
        onError: function (err) {
            console.log(this, err);
            // return true; // 阻止默认行为，不使用默认的失败态
        }
    });
</script>
{/block}