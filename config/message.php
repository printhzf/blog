<?php
return [
	'code' => [
		'ERROR'   => 0,
		'SUCCESS' => 200,
		// 通用类错误1
		'ERROR_TIMEOUT'  => 1001,
		// 用户类错误2
		'ERROR_LOGIN'    => 2001,
		'ERROR_NO_USER'  => 2002,
		'ERROR_PASSWORD' => 2003,
		'ERROR_BAN_USER' => 2004,
	],
	'info' => [
		0    => '操作失败',
		200  => '操作成功',
		1001 => '连接超时',
		2001 => '用户登录校验失败',
		2002 => '用户不存在',
		2003 => '用户密码错误',
		2004 => '用户状态不对',
	]
];