<?php

namespace app\common\facade;


use app\common\core\SimpleFacade;

/**
 * Class MemberFavouriteFacade
 * @package app\common\facade
 * @see \app\common\model\MemberFavouriteModel
 * @method static int isFavourite($member_id,$type,$id) 是否已加入收藏
 * @method static int addFavourite($member_id,$type,$id) 加入收藏
 */
class MemberFavouriteFacade extends SimpleFacade
{
    protected static function getFacadeClass(){
        return \app\common\model\MemberFavouriteModel::class;
    }
}