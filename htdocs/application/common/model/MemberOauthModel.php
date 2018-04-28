<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirn
 * Date: 2018/4/16
 * Time: 11:00
 */

namespace app\common\model;


use think\Model;

class MemberOauthModel extends Model
{
    protected $_auto = array (
        array('create_at','time',self::MODEL_INSERT,'function'),
        array('update_at','time',self::MODEL_BOTH,'function')
    );
}