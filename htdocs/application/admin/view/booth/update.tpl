<extend name="public:base" />

<block name="body">

<include file="public/bread" menu="booth_index" title="展位设置" />

<div id="page-wrapper">
    <div class="page-header">{$id>0?'编辑':'添加'}展位</div>
    <div class="page-content">
    <form method="post" class="page-form" action="">
        <div class="form-group">
            <label for="title">位置名称</label>
            <input type="text" name="title" class="form-control" value="{$model.title}" placeholder="位置名称">
        </div>
        <div class="form-group">
            <label for="flag">调用标识</label>
            <input type="text" name="flag" class="form-control" {$model['locked']?'readonly':''} value="{$model.flag}" placeholder="调用标识">
        </div>
        <div class="form-group">
            <label for="cc">状态</label>
            <label class="radio-inline">
                <input type="radio" name="status" value="1" <if condition="$model['status'] eq 1">checked="checked"</if> >显示
            </label>
            <label class="radio-inline">
                <input type="radio" name="status" value="0" <if condition="$model['status'] eq 0">checked="checked"</if>>隐藏
            </label>
        </div>
        <div class="form-group submit-btn">
            <input type="hidden" name="id" value="{$model.id}">
            <button type="submit" class="btn btn-primary">{$id>0?'保存':'添加'}</button>
        </div>
    </form>
    </div>
</div>
</block>

<block name="script">
    <script type="text/javascript">
        jQuery(function ($) {

        });
    </script>
</block>
