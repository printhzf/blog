<?php
namespace app\model;

use think\Model;

class NoticeModel extends Model
{

	// 默认主键
	protected $pk = 'id';  
	// 默认数据表
	protected $table = 'notice';  
	// 创建时间字段
  	protected $createTime = 'gmt_create';
  	// 更新时间字段
    protected $updateTime = 'gmt_modified';

	// 关联用户表（一对一）
	public function user(){
		return $this->belongsTo('User');
	}

}