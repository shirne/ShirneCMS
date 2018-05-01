<extend name="Public:Base" />

<block name="body">
<include file="Public/bread" menu="setting_index" section="系统" title="配置管理" />

<div id="page-wrapper">
    <div class="page-header">修改配置</div>
    <div id="page-content">
    <form method="post" action="">
        <div class="form-group">
            <label for="key">字段名</label>
            <input type="text" name="key" class="form-control" value="{$model.key}" placeholder="英文字母组成的字符，不可重复">
        </div>
        <div class="form-group">
            <label for="title">标题</label>
            <input type="text" name="title" class="form-control" value="{$model.title}" placeholder="显示名称">
        </div>
        <div class="form-group">
            <label for="value">分组</label>
            <select name="group" class="form-control" >
                <foreach name="groups" item="itm">
                    <if condition="$key==$model['group']">
                        <option value="{$key}" selected="selected">{$itm}</option>
                    <else />
                        <option value="{$key}" >{$itm}</option>
                    </if>
                </foreach>
            </select>
        </div>
        <div class="form-group">
            <label for="value">字段类型</label>
            <select name="type" class="form-control" >
                <foreach name="types" item="itm">
                    <if condition="$key==$model['type']">
                        <option value="{$key}" selected="selected">{$itm}</option>
                    <else />
                        <option value="{$key}">{$itm}</option>
                    </if>
                </foreach>
            </select>
        </div>
        <div class="form-group">
            <label for="value">字段值</label>
            <textarea name="value" class="form-control"  cols="30" rows="3" placeholder="value">{$model.value}</textarea>
        </div>
        <div class="form-group">
            <label for="description">字段描述</label>
            <input type="text" name="description" class="form-control" value="{$model.description}" placeholder="描述信息" >
        </div>
        <div class="form-group">
            <label for="description">字段数据</label>
            <textarea name="data" class="form-control" cols="30" rows="3"  placeholder="选项类字段每行一个选项" >{$model.data}</textarea>
        </div>
        <div class="form-group">
            <input type="hidden" name="id" value="{$model.id}">
            <button type="submit" class="btn btn-primary">提交</button>
        </div>
    </form>
    </div>
</div>
</block>