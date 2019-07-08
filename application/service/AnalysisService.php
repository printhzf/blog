<?php

namespace app\service;
use think\Db;

class AnalysisService
{
	public $illegal = ['information_schema','mysql','performance_schema','sys'];

	public function __construct($param=[])
	{
		if (!is_array($param)) return;	
		$illegal = isset($param['illegal']) ? $param['illegal'] :  $this->illegal;
		$this->illegal = implode(',', array_map('change_to_quotes', $illegal));
	}

	public function getCount($database='', $table='')
	{
		$sql = "SELECT TABLE_SCHEMA FROM information_schema.TABLES WHERE TABLE_SCHEMA NOT IN ({$this->illegal}) GROUP BY TABLE_SCHEMA "; 
		$res = Db::query($sql);
		return $res;
	}

	public function getDatabase()	
	{
		$sql = "SELECT TABLE_SCHEMA FROM information_schema.TABLES WHERE TABLE_SCHEMA NOT IN ({$this->illegal}) GROUP BY TABLE_SCHEMA "; 
		$res = Db::query($sql);
		return $res;
	}

	public function getTable($database = '')	
	{
		if (!empty($database))
		{
			$sql = "SELECT TABLE_SCHEMA,TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA NOT IN ({$this->illegal}) AND TABLE_SCHEMA ='{$database}'"; 
		} else {
			$sql = "SELECT TABLE_SCHEMA,TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA NOT IN ({$this->illegal}) GROUP BY TABLE_SCHEMA,TABLE_NAME"; 
		}
		$res = Db::query($sql);
		return $res;
	}

	public function getColumn($database = '', $table = '')	
	{
		if (!empty($database) && !empty($table))
		{
			$sql = "SELECT TABLE_SCHEMA, TABLE_NAME, COLUMN_NAME, DATA_TYPE, COLUMN_COMMENT FROM information_schema.COLUMNS  WHERE  TABLE_SCHEMA NOT IN ({$this->illegal}) AND table_schema = '{$database}' AND table_name = '{$table}'";
		} else {
			$sql = "SELECT TABLE_SCHEMA, TABLE_NAME, COLUMN_NAME, DATA_TYPE, COLUMN_COMMENT FROM information_schema.COLUMNS  WHERE  TABLE_SCHEMA NOT IN ({$this->illegal})";
		}
		$res = Db::query($sql);
		return $res;
	}

	public function getSizeInfo()
	{
		$totalSize = 0;
		$res = $this->getSize();
		foreach ($res as $k => $v) 
		{
			$res[$k]['data_size']  = convertSize($v['data_size']);
			$res[$k]['index_size'] = convertSize($v['index_size']);
			$res[$k]['size'] = convertSize($v['size']);
			$totalSize += $v['size'];
		}
		$totalSize = convertSize($totalSize);
		$sizeInfo = [
			'data' => $res,
			'totalSize' => $totalSize,
		];
		return $sizeInfo;
	}

	public function getSize($database = '', $table = '')
	{
		if (!empty($database) && !empty($table)) {
			// 指定表的磁盘大小
			$$sql = "SELECT TABLE_SCHEMA,TABLE_NAME, SUM(DATA_LENGTH) AS data_size, SUM(INDEX_LENGTH), SUM(DATA_LENGTH)+SUM(INDEX_LENGTH) AS size AS index_size FROM information_schema.TABLES WHERE TABLE_SCHEMA = '{$database}' AND TABLE_NAME = '{$table}';";
		} else if (!empty($database) && empty($table)) {
			// 指定库的磁盘大小
			$sql = "SELECT TABLE_SCHEMA, SUM(DATA_LENGTH) AS data_size, SUM(INDEX_LENGTH) AS index_size, SUM(DATA_LENGTH)+SUM(INDEX_LENGTH) AS size FROM information_schema.TABLES WHERE TABLE_SCHEMA = '{$database}';";
		} else {
			// 所有数据库的磁盘大小
			$sql = "SELECT TABLE_SCHEMA, SUM(DATA_LENGTH) AS data_size, SUM(INDEX_LENGTH) AS index_size, SUM(DATA_LENGTH)+SUM(INDEX_LENGTH) AS size FROM information_schema.TABLES GROUP BY TABLE_SCHEMA;";
		}
		$size = Db::query($sql);
		return $size;
	}


}