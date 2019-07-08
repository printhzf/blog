<?php

return [
	'facade' => [
		// 代理类 => 实际类
		app\facade\Sc::class => app\util\Sc::class
	],
	'alias'  => [
		'Sc' => app\facade\Sc::class
	]
];