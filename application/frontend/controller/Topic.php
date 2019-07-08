<?php
namespace app\frontend\controller;

use app\service\TopicService;
use think\Controller;
use think\facade\Request;

/**
 * 帖子控制器
 */
class Topic extends Controller
{
	public function __construct()
	{
		$this->topicService = new TopicService;
	}

	/**
	 * ============================
	 * @Author:   PrintHzf
	 * @Version:  1.0 
	 * @DateTime: 2019-06-04
	 * @Description: 文章列表页
	 * ============================
	 */
	public function viewTopicList()
	{
		$list = $this->topicService->readList($map);
		$this->assign('list', $list);
		return $this->fetch('topic_list');
	}

    /**
     * ============================
     * @Author:   PrintHzf
     * @Version:  1.0 
     * @DateTime: 2019-06-04
     * @Description: 文章内容详情页
     * ============================
     */
	public function veiwTopicDetail()
	{
		return $this->fetch('topic_detail');
	}

	/**
	 * ============================
	 * @Author:   PrintHzf
	 * @Version:  1.0 
	 * @DateTime: 2019-06-04
	 * @Description: 文章编辑页
	 * ============================
	 */
	public function viewEdit()
	{
		return $this->fetch('topic_edit');
	}

	/**
	 * ============================
	 * @Author:   PrintHzf
	 * @Version:  1.0 
	 * @DateTime: 2019-06-04
	 * @Description: 统计文章数
	 * ============================
	 */
	public function count()
	{
		$map = [];
		$id  = Request::param('id'); 
		if (!empty($id))
		{
			$map[] = ['id','=', $id]; 
		}
		return $this->topicService->count($map);		
	}

	/**
	 * ============================
	 * @Author:   PrintHzf
	 * @Version:  1.0 
	 * @DateTime: 2019-06-04
	 * @Description: 获取热点文章
	 * ============================
	 */
	public function getHotList()
	{
		
	}

}