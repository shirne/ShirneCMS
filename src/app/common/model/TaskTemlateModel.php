<?php


namespace app\common\model;


use app\common\core\BaseModel;

class TaskTemlateModel extends BaseModel
{
    protected $name = 'task_template';
    protected $autoWriteTimestamp = true;
    protected $type = ['content'=>'array'];
    
}