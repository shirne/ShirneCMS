
<div class="form-row form-group">
    <label for="v-sign_open" class="col-3 col-md-2 text-right align-middle">开启签到</label>
    <div class="col-9 col-md-8 col-lg-6">
        <div class="btn-group btn-group-toggle" data-toggle="buttons">
            <foreach name="setting.sign_open.data" item="value" key="k">
                <if condition="$k==$setting['sign_open']['value']">
                    <label class="btn btn-outline-secondary active">
                        <input type="radio" name="v-sign_open" value="{$k}" autocomplete="off" checked> {$value}
                    </label>
                    <else />
                    <label class="btn btn-outline-secondary">
                        <input type="radio" name="v-sign_open" value="{$k}" autocomplete="off"> {$value}
                    </label>
                </if>
            </foreach>
        </div>
    </div>
</div>

<div class="form-row form-group">
    <label for="v-sup_sign_open" class="col-3 col-md-2 text-right align-middle">开启补签</label>
    <div class="col-9 col-md-8 col-lg-6">
        <div class="btn-group btn-group-toggle" data-toggle="buttons">
            <foreach name="setting.sup_sign_open.data" item="value" key="k">
                <if condition="$k==$setting['sup_sign_open']['value']">
                    <label class="btn btn-outline-secondary active">
                        <input type="radio" name="v-sup_sign_open" value="{$k}" autocomplete="off" checked> {$value}
                    </label>
                    <else />
                    <label class="btn btn-outline-secondary">
                        <input type="radio" name="v-sup_sign_open" value="{$k}" autocomplete="off"> {$value}
                    </label>
                </if>
            </foreach>
        </div>
    </div>
</div>

<div class="form-row form-group">
    <label for="v-sup_sign_rule" class="col-3 col-md-2 text-right align-middle">补签规则</label>
    <div class="col-9 col-md-8 col-lg-6">
        <div class="input-group">
            <span class="input-group-prepend"><span class="input-group-text">扣除积分</span></span>
            <input type="text" class="form-control" name="v-sup_sign_rule[credit]" value="{$setting['sup_sign_rule']['value']['credit']}" placeholder="">
            <span class="input-group-middle"><span class="input-group-text">每月次数</span></span>
            <input type="text" class="form-control" name="v-sup_sign_rule[times]" value="{$setting['sup_sign_rule']['value']['times']}" placeholder="">
        </div>
    </div>
</div>

<div class="form-row form-group">
    <label for="v-sign_cycle" class="col-3 col-md-2 text-right align-middle">签到周期</label>
    <div class="col-9 col-md-8 col-lg-6">
        <div class="btn-group btn-group-toggle" data-toggle="buttons">
            <foreach name="setting.sign_cycle.data" item="value" key="k">
                <if condition="$k==$setting['sign_cycle']['value']">
                    <label class="btn btn-outline-secondary active">
                        <input type="radio" name="v-sign_cycle" value="{$k}" autocomplete="off" checked> {$value}
                    </label>
                    <else />
                    <label class="btn btn-outline-secondary">
                        <input type="radio" name="v-sign_cycle" value="{$k}" autocomplete="off"> {$value}
                    </label>
                </if>
            </foreach>
        </div>
    </div>
</div>
<div class="form-row form-group">
    <label for="v-sign_award" class="col-3 col-md-2 text-right align-middle">普通奖励</label>
    <div class="col-9 col-md-8 col-lg-6">
        <div class="input-group">
            <span class="input-group-prepend"><span class="input-group-text">首次签到</span></span>
            <input type="text" class="form-control" name="v-sign_award[first]" value="{$setting['sign_award']['value']['first']}" placeholder="">
            <span class="input-group-middle"><span class="input-group-text">普通签到</span></span>
            <input type="text" class="form-control" name="v-sign_award[normal]" value="{$setting['sign_award']['value']['normal']}" placeholder="">
        </div>
    </div>
</div>

<div class="form-row form-group">
    <label for="v-sign_keep_award" class="col-3 col-md-2 text-right align-middle">连续奖励</label>
    <div class="col-9 col-md-8 col-lg-6 keepawdrows">
        <foreach name="setting.sign_keep_award.value" key="key" item="keep_day">
            <div class="input-group mb-2" data-key="{$key}">
                <span class="input-group-prepend"><span class="input-group-text">连续签到</span></span>
                <input type="text" class="form-control" name="v-sign_keep_award[{$key}][day]" value="{$keep_day['day']}" placeholder="">
                <span class="input-group-middle"><span class="input-group-text">天，奖励</span></span>
                <input type="text" class="form-control" name="v-sign_keep_award[{$key}][value]" value="{$keep_day['value']}" placeholder="">
                <div class="input-group-append">
                <a class="btn btn-outline-secondary delkeepbtn" href="javascript:">移除</a>
                </div>
            </div>
        </foreach>
        <a href="javascript:" class="btn btn-primary addkeepbtn">添加奖励</a>
    </div>
</div>


<div class="form-row form-group">
    <label for="v-sign_description" class="col-3 col-md-2 text-right align-middle">签到说明</label>
    <div class="col-9 col-md-8 col-lg-6">
        <textarea name="v-sign_description" class="form-control" placeholder="签到说明">{$setting['sign_description']['value']}</textarea>
    </div>
</div>
<script type="text/html" id="keepday_tpl">
    <div class="input-group mb-2" data-key="{@key}">
        <span class="input-group-prepend"><span class="input-group-text">连续签到</span></span>
        <input type="text" class="form-control" name="v-sign_keep_award[{@key}][day]" value="" placeholder="">
        <span class="input-group-middle"><span class="input-group-text">天，奖励</span></span>
        <input type="text" class="form-control" name="v-sign_keep_award[{@key}][value]" value="" placeholder="">
        <div class="input-group-append">
            <a class="btn btn-outline-secondary delkeepbtn" href="javascript:">移除</a>
        </div>
    </div>
</script>
<script type="text/javascript">
jQuery(function($){
    var maxkey=1;
    var lastgroup=$('.keepawdrows>.input-group').eq(-1)
    if(lastgroup.length>0){
        maxkey = lastgroup.data('key')+1;
    }
    var tpl = $('#keepday_tpl').html();
    $('.addkeepbtn').click(function(e){
        $(tpl.compile({key: maxkey})).insertBefore(this)
        maxkey++
    });

    $('.keepawdrows').on('click','.delkeepbtn',function(e){
        var row = $(this).parents('.input-group');
        dialog.confirm('确定删除该行规则？',function () {
            row.remove();
        })
    })
})
</script>