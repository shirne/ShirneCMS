<?php


namespace app\admin\model;


use app\common\core\BaseModel;
use think\Db;

class ManagerRoleModel extends BaseModel
{
    protected $autoWriteTimestamp = true;
    protected $type = ['global' => 'array', 'detail' => 'array', 'actions' => 'array'];

    protected static $roles;
    protected static $roles_cache_key = 'manager_role';
    public static function init()
    {
        parent::init();

        self::event('after_write', function ($role) {
            cache(self::$roles_cache_key, null);
        });
    }

    public static function getRoles($force = false)
    {
        if (empty(self::$roles) || $force) {
            self::$roles = cache(self::$roles_cache_key);
            if (empty(self::$roles) || $force) {
                $roles = static::order('type ASC')->select();
                self::$roles = array_column($roles->toArray(), NULL, 'type');
                cache(self::$roles_cache_key, self::$roles);
            }
        }
        return self::$roles;
    }

    public function hasGlobalPerm($item)
    {
        return in_array($item, $this['global']);
    }
    public function hasPerm($item)
    {
        return in_array($item, $this['detail']) || in_array($item, $this['actions']);
    }

    public function filterPermissions($global, $detail, $actions)
    {
        if (!is_array($global)) $global = explode(',', (strval($global)));
        if (!is_array($detail)) $detail = explode(',', (strval($detail)));
        if (!is_array($actions)) $actions = explode(',', (strval($actions)));
        $globalperms = $this['global'];
        $newglobal = [];
        foreach ($global as $item) {
            if (in_array($item, $globalperms)) {
                $newglobal[] = $item;
            }
        }
        $detailperms = $this['detail'];
        $newdetail = [];
        foreach ($detail as $item) {
            if (in_array($item, $detailperms)) {
                $newdetail[] = $item;
            }
        }
        $actionperms = $this['actions'];
        $newActions = [];
        foreach ($actions as $item) {
            if (in_array($item, $actionperms)) {
                $newActions[] = $item;
            }
        }

        return [implode(',', $newglobal), implode(',', $newdetail), implode(',', $newActions)];
    }
}
