<?php

namespace app\util;

use think\facade\Session;

class Sc
{	
	// 用户登录的session key
	CONST LOGIN_MARK_SESSION_KEY = 'LOGIN_MARK_SESSION';
	// 用户信息
	CONST USER_INFO_SESSION = 'USER_INFO_SESSION';

	public function setLogin($value) 
	{
		Session::set(self::LOGIN_MARK_SESSION_KEY, $value);
	}

	public function getLogin()
	{
		Session::get(self::LOGIN_MARK_SESSION_KEY);
	}

	public function setUserInfo($value)
	{
		Session::set(self::USER_INFO_SESSION, $value);
	}

	public function getUserInfo()
	{
		Session::set(self::USER_INFO_SESSION);
	}

	public function clear()
	{
		Session::del(self::LOGIN_MARK_SESSION_KEY);
		Session::del(self::USER_INFO_SESSION);
	}

}