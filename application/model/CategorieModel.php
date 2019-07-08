<?php 
namespace app\model;

use think\Model;

class CategorieModel extends Model 
{
	
	protected $pk = 'id';
	protected $table = 'categories';
	// 创建时间字段
  	protected $createTime = 'gmt_create';
  	// 更新时间字段
    protected $updateTime = 'gmt_modified';
}