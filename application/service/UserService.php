<?php

namespace app\service;

use app\model\UserModel;
use app\index\validate\UserValidate;
use think\facade\Session;
use think\facade\Request;
use app\facade\Sc;

class UserService
{
	/**
     * @Author:      Hzf
     * @DateTime:    2019-05-09
     * @Description: 生成验证码
     */
    public function verify($config)
    {
        $captcha = new \think\captcha\Captcha($config);
        return $captcha->entry();
    }

	public function login($param) 
	{
        $verify   = $param['verify'];
        $username = $param['username'];
        $password = $param['password'];
        $captcha = new Captcha();
        if (!$captcha->check($verify)) {
            return ERROR_CAPTCHA;
        } 
		$user = UserModel::where('username', $username)->find();
		if (!$user) {
			return ERROR_NO_USER;
		}
		if ($user->status != 1) {
			return ERROR_BAN_USER;
		}
		if (!$this->loginVerify($user, $password)) {
			return ERROR_PASSWORD;
		}
		return SUCCESS;
	}

	public function loginVerify($user, $password) 
	{
		if (md5($password) == $user->password) {
            // 存储用户信息
            // $this->initLogin($user);
			Sc::setUserInfo($user);
            // 修改用户登录信息
            $user->last_login_time = time();
            $user->last_login_ip   = $_SERVER["REMOTE_ADDR"];
            if ($user->save()) {
				return true;
            }
		}
		return false;
	}

    public function initLogin($user) 
    {
        $data = [
            'userId'   => $user->id,
            'userName' => $user->username,
            'userImg'  => $user->headimg,
        ];
        // 将用户信息存储Session
        Session::set('userInfo', $data);
    }

	public function getUserList($limit, $page, $condition) {
        $map      = [];
        $data     = [];
        $begin    = ($page-1)*$limit;
        $ip       = $condition['last_login_ip'];
        $gid      = $condition['group_id'];
        $status   = $condition['status'];
        $username = $condition['username'];
        if (!empty($ip)) {
            $map['last_login_ip'] = ['like',"%{$ip}%"];
        }
        if (!empty($status)) {
            $map['status'] = $status;
        }
        if (!empty($username)) {
            $map['username'] = ['like',"%{$username}%"];
        }
        $userArr = UserModel::with(['roles' => function($query) use ($gid) {
            if (!empty($gid)) {
                $query->field('title')->where('id', $gid);
            } else {
                $query->field('title');
            }
        }])->where(new where($map))->limit($begin, $limit)->all()->toArray();
        if (!empty($userArr)) {             
            foreach ($userArr as $k => $user) {
                if (!empty($user['roles'])) {
                    $title  = ['role' => $user['roles'][0]['title']];
                    $data[] = array_merge($user, $title);
                }
            }
        } else {
            $data = $userArr;
        }
        return showLayuiTableMsg(count($data), $data);
    }
}