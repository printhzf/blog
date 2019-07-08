<?php 
namespace app\model;

use think\Model;

class MemoModel extends Model 
{

	protected $pk = 'id';
	protected $table = 'memo';
	protected $createTime = 'gmt_create';
    protected $updateTime = 'gmt_modified';

	// 忽略数据表不存在的字段
	protected $field = true;
	// 自动写入时间戳
    protected $autoWriteTimestamp = true;
    // 时间字段取出后的默认时间格式
    protected $dateFormat = 'Y-m-d H:i:s';

    // 类型获取器
    public function getTypeAttr($value) {
        $type = ['1'=>'其他'];
        return $type[$value];
    }

    // 内容修改器
    public function setContentAttr($value) {
        return htmlspecialchars($value);
    }

    // 日期修改器
    public function setDeadlineAttr($value) {
        return strtotime($value);
    }

	// 日期获取器
    public function getDeadlineAttr($value) {
        return date('Y-m-d H:i:s', $value);
    }


// CREATE TABLE `smartz`.`memo` ( `id` INT UNSIGNED NOT NULL AUTO_INCREMENT , `deadline` INT(10) UNSIGNED NOT NULL COMMENT '提醒时间' , `content` VARCHAR(500) NOT NULL COMMENT '内容' , `is_top` TINYINT UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否置顶' , `status` TINYINT UNSIGNED NOT NULL DEFAULT '1' COMMENT '状态（1：等待提醒；0：已提醒）' , `gmt_create` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间' , `gmt_modified` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间' , `uid` INT UNSIGNED NOT NULL COMMENT '用户ID' , PRIMARY KEY (`id`)) ENGINE = MyISAM;
}