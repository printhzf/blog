<?php
namespace app\service;

use app\model\TopicModel;
use think\facade\Cache;

/**
 * 帖子行为类
 */
class TopicService
{
	public function __construct()
	{
		$this->redis = Cache::store('redis');
	}

	public function view() 
	{

	}

	public function count($map=[])
	{
		return TopicModel::where($map)->count();
	}

	public function readList($map=[], $page)
	{
		$limit = 6;
		$begin = $limit * ($page - 1);
		if (empty($map)) {			
			if ($this->redis->has('topic:list')) {
				// 从集合中拿出数据
				$lists = $redis->lRange('lists', $begin, $page * $limit);
			}
		}
		$list = TopicModel::with(['user'=>['username']])
				->where($map)
				->order('gmt_create','desc')
				->limit($begin, $limit)
				->paginate(6); 
		return $list;
	}

	public function readDetail($map=[], $field = '*')
	{
		return TopicModel::get(function($query) use ($map, $field) {
			$query->where($map)->field($field);
		});
	}

	public function edit($param)
	{
		$topic = new TopicModel;
		return $topic->allowField(true)->save($param);
	}

	public function delete($id, $map=[])
	{
		if (!empty($map))
		{
			return TopicModel::destroy(function($query) use ($map) {
				$query->where($map);
			});
		} else {
			return TopicModel::destroy($id);
		}
	}

}