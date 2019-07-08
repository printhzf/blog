<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

/**
 * 路由前缀backend
 */
Route::group('backend', function (){

	// 后台首页
	Route::get('index', 'Index/index');
	// 内容管理
	
	Route::get('datam', 'Tool/viewDataManager');
	// 常用工具
		// 规范文档
			// 规范文档首页
			Route::get('doc', 'Tool/viewDoc');
		// UI素材
			// UI素材首页
			Route::get('ui', 'Tool/viewUi');
		// Memo
			// 备忘录页
			Route::get('memo/[:datetimeRange]/[:keywords]$', 'Memo/index');
			// 初始化闹钟
			Route::get('clock', 'Memo/setclocks');
			// 闹钟置顶
			Route::get('setstick/:id/:is_top', 'Memo/setstick');
			// 备忘录编辑页
			Route::get('editmemo/[:id]$', 'Memo/vieweditmemo');
			// 保存备忘录编辑
			Route::post('savememo/[:data]$', 'Memo/save');
			// 删除备忘录
			Route::post('delmemo/[:id]$', 'Memo/delete');
		// 数据分析
			Route::get('analysis', 'Tool/viewAnalysis');

	// User
	// 初始化验证码
	Route::get('verify', 'User/verify');
	// 获取新token
	Route::get('token', 'Index/gettoken');
	// 登录页
	Route::get('login', 'User/viewlogin');
	// 用户列表页
	Route::get('user', 'User/viewuserlist');
	// 用户编辑页
	Route::get('edituser/:id$', 'User/viewedituser');
	// 用户中心页
	Route::get('userinfo/:id$', 'User/viewuserinfo');
	// 读取用户列表数据
	Route::get('readuser/[:page]/[:limit]/[:condition]$', 'User/getuserlist');
	// 登出
	Route::get('logout', 'User/logout');
	// 删除用户
	Route::get('deluser/[:idArr]', 'User/deleteuser');
	// 保存用户编辑
	Route::post('saveuser/[:data]', 'User/saveUser'); // 表单serialize提交时，须设置一个可选参数data
	// 用户登录校验
	Route::post('checklogin/[:data]', 'User/checklogin');

	

	// Authmanager
	// 权限列表页
	Route::get('rule', 'Authmanager/viewrulelist');
	// 角色列表页
	Route::get('role/[:id]', 'Authmanager/viewrolelist');
	// 读取权限列表数据
	Route::get('readrule/[:data]', 'Authmanager/getrulelist');
	// 读取角色列表数据
	Route::get('readrole/[:data]', 'Authmanager/getrolelist');
})->prefix('backend/');

/**
 * 路由前缀frontend
 */
Route::group('frontend', function (){

    // Route::group(['middleware' => 'auth'], function (){
    //     Route::get('/user', function (){});
    //     Route::get('/user/profile', function (){});
    // });
    // 网站首页
	// Route::get('index/[:cate_id]/[:keywords]$', 'Index/index');
    // Topic
    // 文章列表页
    Route::get('topic/:id', 'Topic/veiwTopicDetail');
    // 读取文章列表数据
    Route::get('topiclist/:id', 'Topic/read');
})->prefix('frontend/');

return [

];
