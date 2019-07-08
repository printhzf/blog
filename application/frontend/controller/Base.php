<?php
namespace app\frontend\controller;

use app\model\TopicModel;
use app\model\ReplyModel;
use app\model\CategorieModel;

use think\Facade\Session;
use think\facade\Request;
use think\Controller;
use think\auth\Auth;

/**
* 该控制器继承自Controller.php
* 用户控制器应继承自这个基础公共控制器
* 用户可以将一些公共操作放在这个公共控制器
*/
class Base extends Controller
{

	protected $exculde = [
		'index/user/login',
		'index/user/register',
	];

	/**
	 * 初始化方法
	 * 1.在所有方法之前调用
	 * 2.常用来创建常量,公共方法等
	 */
	protected function initialize()
	{
		// 权限校验
		// $this->checkAuth();
	}

	protected function checkAuth()
	{
		$module = Request::module();
        $controller = Request::controller();
        $action = Request::action();
        $rule = strtolower($module.'/'.$controller.'/'.$action);
        $auth = new Auth();
        $no_check = [
        	'index/index/index',
        ];
        if (!in_array($rule, $no_check)) {
        	if (!$auth->check($rule, Session::get('userId'))) {
           		$this->error('你没有权限访问');
        	}
        }

	}

	// 是否登录
	protected function isLogin()
	{
		if (!Session::has('userId')) {
			return $this->error('您还没有登录！', 'index/user/viewLogin');
		}
	}

	// 是否重复登录
	protected function isRelogin()
	{
		if (Session::has('userId')) {
			return $this->error('您已经登录！');
		}
	}
}