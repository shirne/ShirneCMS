<extend name="Public:Base"/>

<block name="body">
<include file="Public/bread" menu="setting_index" section="系统" title="配置管理" />


<div id="page-wrapper">
<div class="page-header">添加配置</div>
<div id="page-content">
    <form method="post" action="{:U('setting/add')}">
        <div class="form-group">
            <label for="aa">字段名</label>
            <input type="text" name="key" class="form-control" id="aa" placeholder="key">
        </div>
        <div class="form-group">
            <label for="title">标题</label>
            <input type="text" name="title" class="form-control" placeholder="显示名称">
        </div>
        <div class="form-group">
            <label for="value">分组</label>
            <select name="group" class="form-control" >
                <foreach name="groups" item="itm">
                    <option value="{$key}" >{$itm}</option>
                </foreach>
            </select>
        </div>
        <div class="form-group">
            <label for="value">字段类型</label>
            <select name="type" class="form-control" >
                <foreach name="types" item="itm">
                    <option value="{$key}">{$itm}</option>
                </foreach>
            </select>
        </div>
        <div class="form-group">
            <label for="bb">字段值</label>
            <textarea name="value" id="bb" class="form-control"  cols="30" rows="3" placeholder="value"></textarea>
        </div>
        <div class="form-group">
            <label for="cc">字段描述</label>
            <input type="text" name="description" class="form-control" id="cc" placeholder="描述信息" >
        </div>
        <div class="form-group">
            <label for="description">字段数据</label>
            <textarea name="data" class="form-control" cols="30" rows="3"  placeholder="选项类字段每行一个选项" ></textarea>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">提交</button>
        </div>
    </form>
</div>
</div>
</block>