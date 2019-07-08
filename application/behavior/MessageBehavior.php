<?php

namespace app\behavior;

use think\facade\Config;

class MessageBehavior
{
	public function run() 
	{
		$codes = Config::get('message.code');
		foreach ($codes as $k => $v) {
			define($k, $v);
		}
	}	
}