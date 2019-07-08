<?php
namespace app\model;

use think\Model;

class ReplyModel extends Model
{

	// 默认主键
	protected $pk = 'id';  
	// 默认数据表
	protected $table = 'reply';  

	// 创建时间字段
	protected $createTime = 'gmt_create';
	// 更新时间字段
	protected $updateTime = 'gmt_modified';

	// 设置返回数据集的对象名
	protected $resultSetType = 'collection';

	// 关联用户表（一对一）
	public function user(){
		return $this->belongsTo('User');
	}

	// 关联帖子表（一对一）
	public function topic(){
		return $this->belongsTo('Topic','topic_id');
	}

	// 关联回复通知表（一对一）
	public function notice(){
		return $this->belongsTo('Notice','reply_id');
	}

}