<?php 
namespace app\model;

use think\Model;
use think\facade\Cache;

class TopicModel extends Model 
{

	protected $pk = 'id';
	protected $table = 'topic';
	protected $createTime = 'gmt_create';
  protected $updateTime = 'gmt_modified';

  public function __construct()
  {
    parent::__construct();
    $this->redis = Cache::store('redis');
  }

	protected static function init()
  {
    // 文章创建成功后将数据保存到Redis文章列表
    self::event('after_insert', function ($event) {
      $this->redis->lPush('topic:list', $event);
    });
      
    // 观看次数更新成功后将数据保存到Redis文章列表
   	self::event('ater_update', function ($event) {
      // 热门文章有序集合
      $this->redis->zAdd("topic:hot", $event->view_count, $event->id);
   	});
  }

	// 文章状态获取器
	public function getStatusAttr($value)
	{
		$status = ['1'=>'显示', '0'=>'隐藏'];
		return $status[$value];
	}

	public function getViewCountAttr($value)
	{
		$status[$value] = self::changeNumberType($value);
		return $status[$value];
	}

	public function getReplyCountAttr($value)
	{
		$status[$value] = self::changeNumberType($value);
		return $status[$value];
	}

  public static function changeNumberType($num)
  {	
  	$arr = [
  		'万' => '10000',
  		'千' => '1000',
  		'百' => '100',
  	];
  	foreach ($arr as $k => $v) {
  		if ($a = floor($num/$v) != 0) {
  			$num = "{$a}{$k}+";
  		} 
  	}
  	return $num;
  }
  	
  // 关联用户表（一对一）
	public function user(){
		return $this->belongsTo('UserModel');
	}

	// 关联回复表（一对多）
	public function reply(){
		return $this->hasMany('ReplyModel');
	}
}