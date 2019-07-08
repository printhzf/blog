<?php
namespace app\model;

use think\Model;

class CollectModel extends Model
{

  // 默认主键
  protected $pk = 'id';  
  // 默认数据表
  protected $table = 'collect';  
  // 创建时间字段
  protected $createTime = 'gmt_create';
  // 更新时间字段
  protected $updateTime = 'gmt_modified';


}
