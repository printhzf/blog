<?php
namespace app\model;

use think\model\Pivot;

class AccessModel extends Pivot
{
    // 默认主键
    protected $pk = 'id';  
    // 默认数据表
    protected $table = 'think_auth_group_access';
    // 自动写入时间戳，中间表模型Pivot默认关闭
    // protected $autoWriteTimestamp = true; 
}