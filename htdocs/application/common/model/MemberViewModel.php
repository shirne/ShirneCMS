<?php
/**
 * 会员视图
 * User: shirn
 * Date: 2018/4/15
 * Time: 18:46
 */

namespace app\common\model;

use think\Model\ViewModel;

class MemberViewModel extends ViewModel
{
    public $viewFields = array(
        'member'=>array('*','_type'=>'LEFT'),
        'member_level'=>array('level_name','short_name','is_default','level_price','sort','commission_layer','commission_percent','_on'=>'member.level_id=member_level.level_id')
    );
}