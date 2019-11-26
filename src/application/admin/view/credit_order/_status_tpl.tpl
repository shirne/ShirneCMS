<script type="text/html" id="orderStatus">
    <div class="row" style="margin:0 20%;">
        <div class="text-muted status_ext status_ext_0"> 
            确定取消此订单? <br />取消后订单无法再进行支付!!!
        </div>
        <div class="text-muted status_ext status_ext_3"> 
            确定客户已签收? <br />此操作最好由客户在会员中心自行处理
        </div>
        <div class="text-muted status_ext status_ext_4"> 
            确定完成订单? <br />此操作将忽略评价流程直接结算订单
        </div>
        <div class="col-12 form-group status_ext paytype_row">
            <div class="input-group">
                <div ><span class="input-group-text">付款方式</span> </div>
                <div class="col w-50 text-center" >
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                    <label class="btn btn-outline-primary active"> <input type="radio" name="pay_type" value="balance" autocomplete="off" checked> 从余额扣款</label>
                    <label class="btn btn-outline-primary"> <input type="radio" name="pay_type" value="offline" autocomplete="off" > 线下已支付</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 form-group status_ext express_row">
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">快递公司</span></div>
                <select class="form-control express-code">
                    <option value="">无需快递</option>
                    <foreach name="expresscodes" item="exp" key="k">
                        <option value="{$k}">{$exp}</option>
                    </foreach>
                </select>
            </div>
        </div> 
        <div class="col-12 form-group status_ext express_row express_no">
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">快递单号</span></div>
                <input type="text" class="form-control express-no" placeholder="如需物流，请填写单号"/>
            </div>
        </div> 
    </div>
</script>
<script type="text/javascript">
jQuery(function($){
    var statusTitles=['订单取消','订单支付','订单发货','订单确认','订单完成'];
    var actions=['setcancel','setpayed','setdelivery','setreceive','setcomplete'];
    var tpl=$('#orderStatus').text();
    $('.btn-status').click(function() {
        var id=$(this).data('id');
        var status=$(this).data('status');
        var express=($(this).data('express')+'').split('/');
        var action = actions[status<0?0:status];
        if(!action){
            dialog.error('操作错误')
        }
        var dlg=new Dialog({
            onshown:function(body){
                var express_code=body.find('.express-code');
                express_code.change(function(){
                    if($(this).val()){
                        body.find('.express_no').show();
                    }else{
                        body.find('.express_no').hide();
                    }
                }).val(express[0]||'');
                body.find('.express-no').val(express[1]||'');
                body.find('.status_ext').hide();
                if(status == 1){
                    body.find('.paytype_row').show();
                }else if(status==2){
                    body.find('.express_row').show();
                }else if(status < 0){
                    body.find('.status_ext_0').show();
                }else if(status == 3){
                    body.find('.status_ext_3').show();
                }else if(status == 4){
                    body.find('.status_ext_4').show();
                }
                
            },
            onsure:function(body){
                var data={
                        id:id,
                        status:status
                    };
                if(data.status==1){
                    data['pay_type']=body.find('[name=pay_type]:checked').val();
                }else if(data.status==2){
                    data['express_code']=body.find('select.express-code').val();
                    data['express_no']=body.find('.express-no').val();
                }
                
                $.ajax({
                    url:"{:url('credit_order/[action]')}".replace('[action]',action),
                    type:'POST',
                    data:data,
                    dataType:'JSON',
                    success:function(json){
                        dlg.hide();
                        if(json.code==1){
                            dialog.success(json.msg)
                        }else{
                            dialog.error(json.msg)
                        }
                        setTimeout(function(){
                            location.reload();
                        },800)
                    }
                })
            }
        }).show(tpl,statusTitles[status<0?0:status]);
    });
})
</script>