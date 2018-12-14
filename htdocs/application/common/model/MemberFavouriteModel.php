<?php

namespace app\common\model;


use think\Db;

class MemberFavouriteModel extends BaseModel
{
    protected $autoWriteTimestamp = true;

    public function addFavourite($member_id,$type,$id){
        $exist=Db::name('memberFavourite')
            ->where('member_id',$member_id)
            ->where('fav_type',$type)
            ->where('fav_id',$id)
            ->find();

        $data=[
            'member_id'=>$member_id,
            'fav_type'=>$type,
            'fav_id'=>$id
        ];
        switch ($type){
            case 'product':
                $product=Db::name('product')->where('id',$id)->find();
                if(empty($product) || $product['status'] != 1){
                    $this->error='要收藏的产品不存在';
                    return false;
                }
                $data['fav_image']=$product['image'];
                $data['fav_title']=$product['title'];
                break;
            case 'article':
                $article=Db::name('article')->where('id',$id)->find();
                if(empty($article) || $article['status'] != 1){
                    $this->error='要收藏的文章不存在';
                    return false;
                }
                $data['fav_image']=$article['cover'];
                $data['fav_title']=$article['title'];
                break;
            default:
                $this->error='收藏失败';
                return false;
        }
        if(empty($exist)){
            self::create($data);
        }else{
            self::update($data);
        }
        return true;
    }
}