<?php

namespace app\backend\controller;

use think\Controller;
use app\Service\DatamanagerService;
use app\Service\AnalysisService;

/** 
 * 数据分析控制器
 * 1.查看整体情况指标卡、个表情况、表间关系、数据可视化展示
 * 2.任意筛选多个字段生成自定义图表
 * 3.慢查询
 * 4.数据备份与还原
 */
class Tool extends Controller
{
	
	public function index()
    {
        return $this->fetch();
    }

    public function viewDataManager()
    {
        $datam = new DatamanagerService();
        $datam->backup();
        return $this->fetch('dms');
        // 数据备份
        // 数据还原
        // 数据优化
    }

    public function viewAnalysis()
    {
		$analysis = new AnalysisService();
    	// 整体指标（数据库个数、表个数、所占磁盘容量）
    	$sizeInfo = $analysis->getSizeInfo();
    	$database = $analysis->getDatabase();
    	$table    = $analysis->getTable();
    	$column   = $analysis->getColumn();
        // 性能指标（QPS、TPS、key Buffer命中率、InnoDB Buffer命中率、Query Cache命中率、Table Cache状态量、Thread Cache命中率、锁定状态、复制延时量、Tmp Table 状况、Binlog Cache 使用状况、Innodb_log_waits 量、open file and table）

    	return $this->fetch('analysis');
    }

    public function viewDoc()
    {
        return $this->fetch('doc');
    }

    public function viewUi()
    {
        return $this->fetch('ui');
    }
}
