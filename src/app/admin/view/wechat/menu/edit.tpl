{extend name="public:base" /}

{block name="body"}
    {include  file="public/bread" menu="wechat_index" title="公众号菜单"  /}

    <div id="page-wrapper">
        <div class="page-header">
            <div class="float-right pt-1"><a class="btn d-block btn-outline-warning btn-sm" href="{:url('wechat.menu/edit',['id'=>$model['id'],'refresh'=>1])}">刷新缓存</a> </div>
            编辑菜单
        </div>
        <div id="page-content">
            <form method="post" action="">
                <div class="form-row mb-3">
                    <div class="col menu-view-box">
                        <div class="menu-view">
                            <div class="menu-inner">
                                <div class="menubox">
                                    <div class="row"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col menu-info-box">
                        <div class="card">
                            <div class="card-header">
                                <a class="float-right delete" href="javascript:" onclick="deleteMenu()"></a>
                                <h4></h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label for="menu-name" class="col-sm-2 col-form-label">菜单名称</label>
                                    <div class="col-sm-10"><input type="text" name="menu-name" class="form-control" onchange="updateMenu(this)" value="" /><div class="text-muted nametip"></div> </div>
                                </div>
                                <div class="form-group row menu-content">
                                    <label for="content-view" class="col-2 col-form-label">菜单内容</label>
                                    <div class="col-10">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="menu-type" id="menuTypeClick" onclick="setType(this)" value="click">
                                            <label class="form-check-label" for="menuTypeClick">发送消息</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="menu-type" id="menuTypeView" onclick="setType(this)" value="view">
                                            <label class="form-check-label" for="menuTypeView">跳转网页</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="menu-type" id="menuTypeMpro" onclick="setType(this)" value="miniprogram">
                                            <label class="form-check-label" for="menuTypeMpro">跳转小程序</label>
                                        </div>
                                    </div>

                                    <div class="col-12 content-view type-click">
                                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="key-tab" data-toggle="tab" href="#key" role="tab" aria-controls="key" aria-selected="true">关键字</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="picture-tab" data-toggle="tab" href="#picture" role="tab" aria-controls="picture" aria-selected="false">图片</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="audio-tab" data-toggle="tab" href="#audio" role="tab" aria-controls="audio" aria-selected="false">音频</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="video-tab" data-toggle="tab" href="#video" role="tab" aria-controls="video" aria-selected="false">视频</a>
                                            </li>
                                        </ul>
                                        <div class="tab-content" id="myTabContent">
                                            <div class="tab-pane fade show active" id="key" role="tabpanel" aria-labelledby="key-tab">
                                                <div class="form-group row">
                                                    <label for="menu-name" class="col-2 col-form-label">关键字</label>
                                                    <div class="col-10"><input type="text" name="menu-key" class="form-control" onchange="updateMenu(this)" value="" /><div class="text-muted">模拟发送的关键字</div> </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="picture" role="tabpanel" aria-labelledby="picture-tab">...</div>
                                            <div class="tab-pane fade" id="audio" role="tabpanel" aria-labelledby="audio-tab">...</div>
                                            <div class="tab-pane fade" id="video" role="tabpanel" aria-labelledby="video-tab">...</div>
                                        </div>
                                    </div>
                                    <div class="col-12 content-view type-view">
                                        <div class="form-group row">
                                            <label for="menu-name" class="col-2 col-form-label">跳转网址</label>
                                            <div class="col-10"><input type="text" name="menu-url" class="form-control" onchange="updateMenu(this)" value="" /><div class="text-muted">点击按钮跳转的链接地址</div> </div>
                                        </div>
                                    </div>
                                    <div class="col-12 content-view type-miniprogram">
                                        <div class="form-group row">
                                            <label for="menu-name" class="col-2 col-form-label">APPID</label>
                                            <div class="col-10"><input type="text" name="menu-appid" class="form-control" onchange="updateMenu(this)" value="" /><div class="text-muted">关联的小程序的APPID</div> </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="menu-name" class="col-2 col-form-label">跳转页面</label>
                                            <div class="col-sm-10"><input type="text" name="menu-pagepath" class="form-control" onchange="updateMenu(this)" value="" /><div class="text-muted">要跳转的小程序页面</div> </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="menu-name" class="col-2 col-form-label">替代链接</label>
                                            <div class="col-10"><input type="text" name="menu-url" class="form-control" onchange="updateMenu(this)" value="" /><div class="text-muted">在不支持的设备上使用链接跳转</div> </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="menu-tip text-muted">已添加子菜单，仅可设置菜单名称。</div>
                            </div>
                            <div class="card-tip">请从左侧选择菜单编辑</div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <input type="hidden" name="id" value="{$model.id}">
                    <input type="hidden" name="menu" value="">
                    <button type="submit" class="btn btn-primary">保存并更新</button>
                </div>
            </form>
        </div>
    </div>
{/block}
{block name="script"}
    <script type="text/javascript">
        if(!JSON){
            dialog.alert('您的浏览器版本太低，不支持本页面功能，建议使用edge,chrome,firefox等现代浏览器！');
        }
        var menuStr='{$menuData|json_encode|raw}';
        var menuData=JSON.parse(menuStr);
        var curIdx=-1;
        var curSubIdx=-1;
        var curMenu=null;
        function showMenu(data) {
            var html = [];
            for (var i = 0; i < data.length; i++) {
                var menu={
                    'idx':i,
                    'name':data[i].name,
                    'submenu':createSubMenu(data[i].sub_button||[],i),
                    'prefix':data[i].sub_button&& data[i].sub_button.length>0?'<i class="ion-md-menu"></i>':''

                };
                html.push('<div class="col"><span class="menutext" onclick="editMenu({@idx})">{@prefix}{@name}</span><div class="submenu">{@submenu}</div></div>'.compile(menu));
            }
            if(data.length<3){
                html.push('<div class="col" onclick="addMenu(-1)"><i class="ion-md-add"></i></div>');
            }
            $('.menubox .row').html(html.join('\n'));
        }
        function createSubMenu(list,idx){
            var html=[];
            for (var i = 0; i < list.length; i++) {
                var menu={
                    'idx':idx,
                    'subidx':i,
                    'name':list[i].name
                };
                html.push('<div class="subitem" onclick="editMenu({@idx},{@subidx})"><span>{@name}</span></div>'.compile(menu));
            }
            if(list.length<5){
                html.push('<div class="subitem" onclick="addMenu('+idx+')"><i class="ion-md-add"></i></div>');
            }
            return html.join('\n');
        }
        function editMenu(idx, subidx) {
            curIdx=idx;
            curSubIdx=subidx;
            var menus=$('.menubox .col');
            var subMenus=$('.menubox .col .subitem');
            menus.removeClass('active').removeClass('focus').eq(curIdx).addClass('active');
            subMenus.removeClass('active');
            if(curSubIdx>-1){
                menus.eq(curIdx).find('.subitem').eq(curSubIdx).addClass('active');
                curMenu=menuData[curIdx].sub_button[curSubIdx]
            }else{
                menus.eq(curIdx).addClass('focus');
                curMenu=menuData[curIdx]
            }

            var infobox=$('.menu-info-box');
            infobox.find('.card-tip').hide();
            infobox.find('.card-body').show();
            infobox.find('.card-header').show();

            infobox.find('.card-header h4').text(curMenu.name);
            infobox.find('.card-header a').text(curSubIdx>-1?'删除子菜单':'删除菜单');
            infobox.find('[name=menu-name]').val(curMenu.name);
            if(curSubIdx>-1){
                infobox.find('.nametip').text('字数不超过8个汉字或16个字母');
                infobox.find('.menu-content').show();
                infobox.find('.menu-tip').hide();
            }else{
                infobox.find('.nametip').text('字数不超过4个汉字或8个字母');
                if(curMenu.sub_button&&curMenu.sub_button.length>0){
                    infobox.find('.menu-content').hide();
                    infobox.find('.menu-tip').show();
                }else{
                    infobox.find('.menu-content').show();
                    infobox.find('.menu-tip').hide();
                }
            }
            infobox.find('[name=menu-type]').filter('[value='+curMenu.type+']').trigger('click');
            var keys=['key','url','appid','pagepath'];
            for(var i=0;i<keys.length;i++){
                infobox.find('[name=menu-'+keys[i]+']').val(curMenu[keys[i]]===undefined?'':curMenu[keys[i]]);
            }

        }
        function deleteMenu(){
            dialog.confirm('确定删除该菜单？',function () {
                var infobox=$('.menu-info-box');
                infobox.find('.card-tip').show();
                infobox.find('.card-body').hide();
                infobox.find('.card-header').hide();
                if(curSubIdx>-1){
                    menuData[curIdx].sub_button.splice(curSubIdx,1);
                }else{
                    menuData.splice(curIdx,1);
                }
                showMenu(menuData);
                updateJson();
            })
        }
        function setType(current) {
            var infobox=$('.menu-info-box');
            infobox.find('.content-view').hide();
            infobox.find('.type-'+current.value).show();
            updateMenu(current);
        }
        function updateMenu(input){
            var infobox=$('.menu-info-box');
            var key=$(input).attr('name');
            key=key.split('-')[1];
            if (curSubIdx > -1) {
                curMenu = menuData[curIdx].sub_button[curSubIdx]
            } else {
                curMenu = menuData[curIdx]
            }
            if(key==='name') {
                var menuname = $(input).val();
                curMenu.name = menuname;
                infobox.find('.card-header h4').text(menuname);
                var menus = $('.menubox .col');
                if (curSubIdx > -1) {
                    menus.eq(curIdx).find('.subitem').eq(curSubIdx).find('span').text(menuname);
                } else {
                    menus.eq(curIdx).find('.menutext').html((curMenu.sub_button && curMenu.sub_button.length > 0 ? '<i class="ion-md-menu"></i>' : '') + menuname);
                }
            }else{
                curMenu[key]=$(input).val();
                if(key==='url' && !curMenu[key].match(/^https?:\/\//)){
                    dialog.alert('链接地址请使用附带完整域名的地址<br />如：http://www.baidu.com/');
                }
            }
            updateJson();
        }
        function addMenu(idx){
            if(idx>-1){
                if(!menuData[idx].sub_button){
                    menuData[idx].sub_button=[];
                }
                if(menuData[idx].sub_button.length>4){
                    return dialog.warning('子菜单数量最多5个');
                }
                menuData[idx].sub_button.push({
                    name:'新建菜单',
                    type:'click'
                })
            }else{
                if(menuData.length>2){
                    return dialog.warning('主菜单数量最多3个');
                }
                menuData.push({
                    name:'新建菜单',
                    type:'click'
                });
            }
            showMenu(menuData);
            if(idx>-1){
                editMenu(idx,menuData[idx].sub_button.length-1);
            }else{
                editMenu(menuData.length-1);
            }
            updateJson();
        }
        function updateJson(){
            menuStr=JSON.stringify(menuData);
            $('[name=menu]').val(menuStr);
        }
        showMenu(menuData);
        updateJson();
    </script>
{/block}