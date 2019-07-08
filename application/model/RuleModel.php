<?php
namespace app\model;

use think\Model;

class RuleModel extends Model
{
    // 默认主键
    protected $pk = 'id';  
    // 默认数据表
    protected $table = 'think_auth_rule';

    // 关联用户模型
    public function roles()
    {
        return $this->belongsToMany('role');
    }
}