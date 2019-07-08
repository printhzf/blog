<?php

namespace app\service;
use think\Db;
use app\facade\File;

class DatamanagerService
{

	public function backup()
	{
		$path = '../public/sq';
		File::delete($path);
	}

	public function restore()
	{

	}

	public function optimize()
	{

	}
}