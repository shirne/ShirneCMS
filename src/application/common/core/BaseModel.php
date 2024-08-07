<?php

namespace app\common\core;

use think\Db;
use think\facade\Log;
use think\Model;

/**
 * 数据模型基类
 * Class BaseModel
 * @package app\common\model
 */
class BaseModel extends Model
{
    protected function getRelationAttribute($name, &$item)
    {
        try {
            return parent::getRelationAttribute($name, $item);
        } catch (\InvalidArgumentException $e) {
            $traces = static::getTempFile($e->getTrace());
            Log::notice($e->getMessage() . ' ' . $traces['file'] . ':' . $traces['line']);
            return null;
        } catch (\Exception $e) {
            $traces = static::getTempFile($e->getTrace());
            Log::notice($e->getMessage() . ' ' . $traces['file'] . ':' . $traces['line']);
            return null;
        }
    }
    protected static function getTempFile($traces)
    {
        foreach ($traces as $trace) {
            if (isset($trace['file']) && strpos($trace['file'], '\\temp\\') > 0) {
                return $trace;
            }
        }
        return $traces[0];
    }

    protected $errno;
    protected function setError($error, $errno = -1)
    {
        $this->error = $error;
        $this->errno = $errno;
    }
    public function getErrNo()
    {
        return $this->errno;
    }

    protected static $instances = [];

    /**
     * @return static
     */
    public static function getInstance()
    {
        $class = get_called_class();
        if (!isset(static::$instances[$class])) {
            static::$instances[$class] = new static();
        }
        return static::$instances[$class];
    }

    protected function triggerStatus($item, $status, $newData = [])
    {
        return true;
    }

    /**
     * @param $status
     * @return array
     */
    protected function beforeStatus($status)
    {
        if (is_array($status)) {
            $data = $status;
        } else {
            $data['status'] = $status;
        }
        return $data;
    }

    /**
     * 用于更新需要触发状态改变的表
     * 框架model自带的触发器没有旧数据，无法进行比对
     * @param $toStatus int|array
     * @param $where string|array|int
     * @throws Exception
     */
    public function updateStatus($toStatus, $where = null)
    {
        $data = $this->beforeStatus($toStatus);
        if (empty($where)) {
            if ($this->isExists()) {
                $odata = $this->getOrigin();
                $updated = Db::name($this->name)->where($this->getWhere())->update($data);
                if ($updated && $odata['status'] != $data['status']) {
                    $this->triggerStatus($odata, $data['status'], $data);
                }
            } else {
                throw new \Exception('Update status with No data exists');
            }
        } else {
            $lists = Db::name($this->name)->where($where)->select();
            $updated = Db::name($this->name)->where($where)->update($data);
            if ($updated) {
                foreach ($lists as $item) {
                    if ($item['status'] != $data['status']) {
                        $this->triggerStatus($item, $data['status'], $data);
                    }
                }
            }
        }
        return $updated;
    }
}
