<?php

namespace app\common\taglib;

use app\common\core\BaseTabLib;

/**
 * Class Article
 * @package app\common\taglib
 */
class Article extends BaseTabLib
{
    protected $tags = [
        'list' => ['attr' => 'var,category,type,ids,order,limit,cover,withimgs,recursive', 'close' => 0],
        'relation' => ['attr' => 'var,category,id,limit,withimgs', 'close' => 0],
        'prev' => ['attr' => 'var,category,id', 'close' => 0],
        'next' => ['attr' => 'var,category,id', 'close' => 0],
        'pages' => ['attr' => 'var,group,limit', 'close' => 0],
        'page' => ['attr' => 'var,name', 'close' => 0],
        'cates' => ['attr' => 'var,pid,limit', 'close' => 0],
        'cate' => ['attr' => 'var,name', 'close' => 0],
        'listwrap' => ['attr' => 'name,step,id']
    ];

    public function tagList($tag)
    {
        $var  = isset($tag['var']) ? $tag['var'] : 'article_list';

        $parseStr = '<?php ';

        $parseStr .= '$' . $var . '=\app\common\model\ArticleModel::getInstance()->tagList(' . $this->exportArg($tag) . ');';

        $parseStr .= ' ?>';
        return $parseStr;
    }
    public function tagRelation($tag)
    {
        $var  = isset($tag['var']) ? $tag['var'] : 'relations';

        $parseStr = '<?php ';

        $parseStr .= '$' . $var . '=\app\common\model\ArticleModel::getInstance()->tagRelation(' . $this->exportArg($tag) . ');';

        $parseStr .= ' ?>';
        return $parseStr;
    }
    public function tagPrev($tag)
    {
        $var  = isset($tag['var']) ? $tag['var'] : 'prev';

        $parseStr = '<?php ';

        $parseStr .= '$' . $var . '=\app\common\model\ArticleModel::getInstance()->tagPrev(' . $this->exportArg($tag) . ');';

        $parseStr .= ' ?>';
        return $parseStr;
    }
    public function tagNext($tag)
    {
        $var  = isset($tag['var']) ? $tag['var'] : 'next';
        $parseStr = '<?php ';

        $parseStr .= '$' . $var . '=\app\common\model\ArticleModel::getInstance()->tagNext(' . $this->exportArg($tag) . ');';

        $parseStr .= ' ?>';
        return $parseStr;
    }
    public function tagPages($tag)
    {
        $var  = isset($tag['var']) ? $tag['var'] : 'page_list';
        $group = isset($tag['group']) ? $this->parseArg($tag['group']) : '';

        $parseStr = '<?php ';

        $parseStr .= '$' . $var . '=\think\Db::name("page")';
        $parseStr .= '->where(\'status\',1)';
        if (!empty($group)) {
            $parseStr .= '->where(\'group\',' . $group . ')';
        }
        if (!empty($tag['limit'])) {
            $parseStr .= '->limit(' . intval($tag['limit']) . ')';
        }
        $parseStr .= '->select();';

        $parseStr .= ' ?>';
        return $parseStr;
    }
    public function tagPage($tag)
    {
        $var  = isset($tag['var']) ? $tag['var'] : 'page_model';
        $name = isset($tag['name']) ? $this->parseArg($tag['name']) : '';
        $group = isset($tag['group']) ? $this->parseArg($tag['group']) : '';

        $parseStr = '<?php ';

        $parseStr .= '$' . $var . '=\think\Db::name("page")';
        $parseStr .= '->where(\'status\',1)';
        if (!empty($name)) {
            $parseStr .= '->where(\'name\',' . $name . ')';
        }
        if (!empty($group)) {
            $parseStr .= '->where(\'group\',' . $group . ')';
        }
        $parseStr .= '->find();';

        $parseStr .= ' ?>';
        return $parseStr;
    }

    public function tagCates($tag)
    {
        $var  = isset($tag['var']) ? $tag['var'] : 'cates_list';
        $pid = isset($tag['pid']) ? $this->parseArg($tag['pid']) : -1;

        $parseStr = '<?php ';

        $parseStr .= '$' . $var . '=\think\Db::name("Category")';
        if ($pid > -1) {
            $parseStr .= "->where('pid'," . $pid . ")";
        }
        $parseStr .= '->order("sort ASC, id ASC")';
        if (!empty($tag['limit'])) {
            $parseStr .= '->limit(' . intval($tag['limit']) . ')';
        }
        $parseStr .= '->select();';

        $parseStr .= ' ?>';
        return $parseStr;
    }
    public function tagCate($tag)
    {
        $var  = isset($tag['var']) ? $tag['var'] : 'cate';
        $name = isset($tag['name']) ? $this->parseArg($tag['name']) : 0;

        $parseStr = '<?php ';

        $parseStr .= '$' . $var . '=\app\common\facade\CategoryFacade::findCategory(' . $name . ');';

        $parseStr .= ' ?>';
        return $parseStr;
    }
    public function tagListwrap($tag, $content)
    {
        $name   = $tag['name'];
        $id     = isset($tag['id']) ? $tag['id'] : 'wrapedlist';
        $step   = isset($tag['step']) ? intval($tag['step']) : 1;
        $flag     = substr($name, 0, 1);

        $parseStr = '<?php ';
        if (':' == $flag) {
            $name = $this->autoBuildVar($name);
            $parseStr .= '$_result=' . $name . ';';
            $name = '$_result';
        } else {
            $name = $this->autoBuildVar($name);
        }
        $parseStr .= '$wrapcount=count(' . $name . ');';
        $parseStr .= 'for($wrapi=0; $wrapi < $wrapcount; $wrapi+=' . $step . '):';
        $parseStr .= ' $' . $id . ' = array_slice(' . $name . ', $wrapi, ' . $step . '); ?>';
        $parseStr .= $content;
        $parseStr .= '<?php endfor; ?>';
        return $parseStr;
    }
}
