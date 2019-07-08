<?php
namespace app\frontend\controller;

use app\frontend\controller\Base;
use app\frontend\controller\Topic;
use app\model\TopicModel;
use app\model\CategorieModel;
use app\model\ReplyModel;
use think\facade\Request; //导入请求静态代理
use think\facade\Session; //导入Session静态代理
use think\facade\Cache;
use think\Db;
// 公共模板
// 业务逻辑
// 安全


/****************
 *  PrintHzf项目
 * 一、意义
 *  1.记录收获
 *  2.共享知识
 *  3.交流看法
 * 二、功能模块
 *  1.帖子模块
 *  2.分类模块
 *  2.评论模块
 *  3.用户模块
 *  4.后台管理模块
 */
class Index extends Base
{

    public function initialize()
    {
        $this->redis = Cache::store('redis');
        $this->topic = new Topic;
    }

	// 网站首页
    public function index() {

        $map = [];  // 设置全局查询条件
        $topicsList = []; // 文章列表

        // 显示状态必须为1
        $map[] = ['title_status', '=', 1];   
        
        // 匹配文章分类
        $categoryId = Request::param('category_id'); // 获取到URL中的分类ID
        if (!empty($categoryId))
        {
            $map[] = ['category_id' , '=', $categoryId]; 
        }

        // 匹配搜索关键字
        $keywords = Request::param('keywords');
        if (!empty($keywords))
        {
            $map[] = ['title' , 'like','%'.$keywords.'%']; 
        }

        //文章列表分页显示,分页仅显示6条
        $topicsList = TopicModel::alias('t')->join('user u','t.user_id = u.id')
                        ->field('t.*,u.username')
                        ->where($map)->order('gmt_create','desc')->paginate(6); 
        $category = CategorieModel::field('name')->get($categoryId);
        $this->assign('categoryName', $category['name']);

        // 关键字高亮替换
        if (!empty($keywords)) 
        {
            $topicsList = $topicsList->toArray()['data'];
            foreach ($topicsList as $k => $v) 
            {
                $topicsList[$k]['title'] = preg_replace("/($keywords)/i","<span class='highlight'>{$keywords}</span>", $v['title']);
            }
        }

        if (!empty($topicsList))
        {
            // 页码列表
            $page = $topicsList->render();
            $this->assign('page', $page);
        }

        // 文章列表
        $this->assign('topicsList', $topicsList);
   
    	// 导航栏文章分类
        $cateList = CategorieModel::all();
        $this->assign('cateList', $cateList);

        // 帖子总数
        $topicsCount = $this->topic->count();
        $this->assign('topicsNum', $topicsCount);
        
        // 热门帖子
        $hotList = TopicModel::where('is_hot','1')->field('id,title')->order('view_count','desc')->limit(12)->select(); 
        $this->assign('hotList',$hotList);

        // 评论总数
        $repliesCount = ReplyModel::count(); 
        $this->assign('repliesNum', $repliesCount);
        
        // TODO:在线用户数

        // 渲染首页模板
        return $this->fetch();
    }

}
