{extend name="public:base"/}

{block name="body"}
{include file="public/bread" menu="shop_postage_index" title="运费设置"/}

<div id="page-wrapper">
    <div class="page-header">{if !empty($model['id'])}编辑{else}添加{/if}运费模板</div>
    <div class="page-content">
        <form method="post">
            <div class="row">
                <div class="col-12 col-lg-6">
                    <div class="card mt-3">
                        <div class="card-header">基本设置</div>
                        <div class="card-body">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">模板名称</span>
                                    </div>
                                    <input type="text" name="title" class="form-control" v-model="postage.title"
                                        placeholder="输入名称">
                                </div>
                            </div>

                            <div class="form-group col form-row">
                                <label for="is_default">计算方式</label>
                                <div class="col">
                                    <div class="btn-group btn-group-toggle">
                                        <label :class="'btn btn-outline-secondary '+(postage.calc_type==0?'active':'')">
                                            <input type="radio" name="calc_type" :value="0" v-model="postage.calc_type"
                                                autocomplete="off"> 按重量计算
                                        </label>
                                        <label :class="'btn btn-outline-secondary '+(postage.calc_type==1?'active':'')">
                                            <input type="radio" name="calc_type" :value="1" v-model="postage.calc_type"
                                                autocomplete="off"> 按件计算
                                        </label>
                                        <label :class="'btn btn-outline-secondary '+(postage.calc_type==2?'active':'')">
                                            <input type="radio" name="calc_type" :value="2" v-model="postage.calc_type"
                                                autocomplete="off"> 按体积计算
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col form-row">
                                <label for="is_default">是否默认</label>
                                <div class="col">
                                    <div class="btn-group btn-group-toggle">
                                        <label :class="'btn btn-outline-primary '+(postage.is_default==1?'active':'')">
                                            <input type="radio" name="is_default" :value="1"
                                                v-model="postage.is_default" autocomplete="off"> 是
                                        </label>
                                        <label
                                            :class="'btn btn-outline-secondary '+(postage.is_default==0?'active':'')">
                                            <input type="radio" name="is_default" :value="0"
                                                v-model="postage.is_default" autocomplete="off"> 否
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="card mt-3">
                        <div class="card-header">限制地区</div>
                        <div class="card-body">
                            <div class="form-group col form-row">
                                <label for="is_default">限制类型</label>
                                <div class="col">
                                    <div class="btn-group btn-group-toggle">
                                        <label :class="'btn btn-outline-primary '+(postage.area_type==1?'active':'')">
                                            <input type="radio" name="area_type" :value="1" v-model="postage.area_type"
                                                autocomplete="off"> 仅配送地区
                                        </label>
                                        <label :class="'btn btn-outline-secondary '+(postage.area_type==0?'active':'')">
                                            <input type="radio" name="area_type" :value="0" v-model="postage.area_type"
                                                autocomplete="off"> 不配送地区
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="text-muted">此处如果设置了区域，则使用该模板的商品在不配送地区将不可购买</div>
                            <div class="form-group col form-row">
                                <div class="col">
                                    <div class="areas">
                                        <span v-for="a in postage.specials" class="badge badge-secondary mr-1">
                                            <input type="hidden" name="specials[]" :value="a" />
                                            {{a}}
                                        </span>
                                    </div>
                                    <a href="javascript:" class="setareas" @click="setGlobalAreas">设置</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">地区设置</div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>可选快递</th>
                            <th>配送区域</th>
                            <th>首重({{unit}})</th>
                            <th>首费</th>
                            <th>续重({{unit}})</th>
                            <th>续费</th>
                            <th>封顶</th>
                            <th>免运费额度</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(area,idx) in areas" :key="idx">
                            <td width="200">
                                <input type="hidden" :name="'areas['+area.id+'][id]'" v-model="area.id">
                                <div class="expresses">
                                    <span v-for="a in area.expresses" class="badge badge-secondary mr-1">
                                        <input type="hidden" :name="'areas['+area.id+'][expresses][]'" :value="a" />
                                        {{expresses[a]}}
                                    </span>
                                </div>
                                <a href="javascript:" class="setexpress" :data-id="idx" @click="setExpress">设置</a>
                            </td>
                            <td width="400">
                                <div class="areas">
                                    <span v-for="a in area.areas" class="badge badge-secondary mr-1">
                                        <input type="hidden" :name="'areas['+area.id+'][areas][]'" :value="a" />
                                        {{a}}
                                    </span>
                                </div>
                                <a href="javascript:" class="setareas" :data-id="idx" @click="setAreas">设置</a>
                            </td>
                            <td width="100">
                                <input type="text" class="form-control" :name="'areas['+area.id+'][first]'"
                                    v-model="area.first">
                            </td>
                            <td width="100">
                                <input type="text" class="form-control" :name="'areas['+area.id+'][first_fee]'"
                                    v-model="area.first_fee">
                            </td>
                            <td width="100">
                                <input type="text" class="form-control" :name="'areas['+area.id+'][extend]'"
                                    v-model="area.extend">
                            </td>
                            <td width="100">
                                <input type="text" class="form-control" :name="'areas['+area.id+'][extend_fee]'"
                                    v-model="area.extend_fee">
                            </td>
                            <td width="100">
                                <input type="text" class="form-control" :name="'areas['+area.id+'][ceiling]'"
                                    v-model="area.ceiling">
                            </td>
                            <td width="120">
                                <input type="text" class="form-control" :name="'areas['+area.id+'][free_limit]'"
                                    v-model="area.free_limit">
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="card-footer">
                    <div class="row">
                        <div class="text-center" style="width: 120px;">
                            <a href="javascript:" class="btn btn-outline-primary addarea" @click="addarea">添加设置</a>
                        </div>
                        <div class="col">
                            <div class="text-muted">1. 配送区域不设置默认不限制区域，设置了区域则仅在设置的区域内可用。如果匹配不到可用区域，使用本模板的产品将不可购买<br />2.
                                不填写(或0)封顶和免运费额度则运费根据货品一直累加<br />3.
                                计算运费时优先匹配设置的地区，若有匹配到地区则忽略未匹配到地区的。若有多个地区同时满足，则显示选项给用户选择</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group mt-3">
                <input type="hidden" name="id" v-model="postage.id" />
                <button type="submit" class="btn btn-primary">提交</button>
            </div>
        </form>
    </div>
</div>
{/block}
{block name="script"}
<script type="text/javascript" src="__STATIC__/js/location.min.js"></script>
<script type="text/javascript" src="__STATIC__/vue/2.6/vue.min.js"></script>
<script type="text/javascript">
    var exphtml = '';
    var areahtml = '';
    var locobj = new Location()
    var app = new Vue({
        el: '#page-wrapper',
        data: {
            postage: {
                id: 0,
                title: '',
                is_default: 0,
                calc_type: 0,
                area_type: 0,
                specials: []
            },
            areas: [],
            area_new_index: 0,
            expresses: {}
        },
        computed: {
            unit: function () {
                var unit = '克'
                if (this.postage.calc_type == 2) {
                    unit = '方'
                } else if (this.postage.calc_type == 1) {
                    unit = '件'
                }

                return unit;
            }
        },
        mounted: function () {
            this.loadData();
        },
        methods: {
            loadData: function () {
                var self = this;

                jQuery.ajax({
                    url: '',
                    type: 'GET',
                    dataType: 'JSON',
                    success: function (json) {
                        if (json.code == 1) {
                            self.postage = json.data.model
                            self.areas = json.data.areas
                            self.expresses = json.data.express
                        }
                    }
                })
            },
            addarea: function () {
                this.areas.push({
                    id: 'new_area_' + this.area_new_index++,
                    expresses: [],
                    areas: [],
                    first: '',
                    first_fee: '',
                    extend: '',
                    extend_fee: '',
                    ceiling: '',
                    free_limit: ''
                })
            },
            setExpress: function (e) {
                var idx = $(e.target).data('id')
                var picked = this.areas[idx].expresses
                var self = this
                if (!exphtml) {
                    exphtml = '<div class="clearfix">';
                    for (var k in this.expresses) {
                        exphtml += '<div class="float-left mr-2 mb-2"> <div class="btn-group-toggle btn-group-sm" data-toggle="buttons">\n' +
                            '  <label class="btn btn-outline-secondary">\n' +
                            '    <input type="checkbox" value="' + k + '" name="expitem" autocomplete="off"> ' + this.expresses[k] + '\n' +
                            '  </label>\n' +
                            '</div></div>'
                    }
                    exphtml += '</div>';
                }
                var dlg = new Dialog({
                    onshown: function (body) {
                        if (picked && picked.length > 0) {
                            var ckboxes = $(body).find('[name=expitem]')
                            for (var i = 0; i < ckboxes.length; i++) {
                                if (picked.indexOf(ckboxes.eq(i).val()) > -1) {
                                    ckboxes.eq(i).parent('label')[0].click()
                                }
                            }
                        }
                    },
                    onsure: function (body) {
                        var ckboxes = $(body).find('[name=expitem]:checked')
                        var picked = []
                        for (var i = 0; i < ckboxes.length; i++) {
                            picked.push(ckboxes.eq(i).val())
                        }
                        Vue.set(self.areas[idx], 'expresses', picked)
                    }
                }).show(exphtml, '选择支持快递');
            },
            setAreas: function (e) {
                var idx = $(e.target).data('id')
                var picked = this.areas[idx].areas
                var self = this
                this.pickArea(picked, function (newpicked) {
                    Vue.set(self.areas[idx], 'areas', newpicked)
                })
            },
            setGlobalAreas: function (e) {
                var self = this
                var picked = self.postage.specials
                this.pickArea(picked, function (newpicked) {
                    Vue.set(self.postage, 'specials', newpicked)
                }, self.postage.area_type == 1 ? '选择不配置送区域' : '选择配送区域')
            },
            pickArea: function (picked, callback, title) {
                if (!areahtml) {
                    areahtml = '<div class="container-fluid" style="height:500px;overflow: scroll">';
                    var provinces = locobj.find(0)
                    for (var k in provinces) {
                        var cities = locobj.find('0,' + k)
                        if (cities) {
                            areahtml += '<div class="row border-bottom pt-1 pb-1"> <div class="text-center" style="width:100px"><span class="badge badge-info " style="font-size:14px;">' + provinces[k] + '</span><br /><a href="javascript:" class="btn btn-sm pt-0 pb-0 pickrow">选择全部</a><br /><a href="javascript:" class="btn btn-sm pt-0 pb-0 cancelrow">取消选择</a></div><div class="col">';
                            for (var c in cities) {
                                areahtml += '<div class="float-left mr-2 mb-2"> <div class="btn-group-toggle btn-group-sm" data-toggle="buttons">\n' +
                                    '  <label class="btn btn-outline-secondary">\n' +
                                    '    <input type="checkbox" value="' + cities[c] + '" name="areaitem" autocomplete="off"> ' + cities[c] + '\n' +
                                    '  </label>\n' +
                                    '</div></div>'
                            }
                            areahtml += '</div></div>';
                        }
                    }
                    areahtml += '</div>';
                }
                var dlg = new Dialog({
                    size: 'lg',
                    addbtn: {
                        text: '清空',
                        click: function (body) {
                            var ckboxes = $(body).find('[name=areaitem]')
                            for (var i = 0; i < ckboxes.length; i++) {
                                if (ckboxes.eq(i).prop('checked')) {
                                    ckboxes.eq(i).parent('label')[0].click()
                                }
                            }
                            return false;
                        },
                        type: 'warning'
                    },
                    onshown: function (body) {
                        if (picked && picked.length > 0) {
                            var ckboxes = $(body).find('[name=areaitem]')
                            for (var i = 0; i < ckboxes.length; i++) {
                                if (picked.indexOf(ckboxes.eq(i).val()) > -1) {
                                    ckboxes.eq(i).parent('label')[0].click()
                                }
                            }
                        }
                        $(body).find('a.pickrow').click(function (e) {
                            var prow = $(this).parents('.row')
                            var ckboxes = prow.find('[name=areaitem]')
                            for (var i = 0; i < ckboxes.length; i++) {
                                if (!ckboxes.eq(i).prop('checked')) {
                                    ckboxes.eq(i).parent('label')[0].click()
                                }
                            }
                        })
                        $(body).find('a.cancelrow').click(function (e) {
                            var prow = $(this).parents('.row')
                            var ckboxes = prow.find('[name=areaitem]')
                            for (var i = 0; i < ckboxes.length; i++) {
                                if (ckboxes.eq(i).prop('checked')) {
                                    ckboxes.eq(i).parent('label')[0].click()
                                }
                            }
                        })
                    },
                    onsure: function (body) {
                        var ckboxes = $(body).find('[name=areaitem]:checked')
                        var picked = []
                        for (var i = 0; i < ckboxes.length; i++) {
                            picked.push(ckboxes.eq(i).val())
                        }
                        callback(picked)
                    }
                }).show(areahtml, title ? title : '选择支持区域');
            }
        }
    });
</script>
{/block}