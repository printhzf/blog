<?php
namespace app\model;

use think\Model;

class RoleModel extends Model
{
    // 默认主键
    protected $pk = 'id';  
    // 默认数据表
    protected $table = 'think_auth_group';

    // 关联用户模型
    public function users()
    {
        return $this->belongsToMany('user');
    }

    // 关联角色模型
    public function rules() 
    {
    	return $this->hasMany('rule');
    }
}