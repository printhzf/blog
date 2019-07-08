<?php
namespace app\index\validate;

use think\Validate;

class MemoValidate extends Validate
{

    // 验证规则
    protected $rule = [
        'uid|用户ID'    => 'require|integer',
        // 'type|类型'     => 'require|integer',
        'content|内容'  => 'require',
        'deadline|提醒时间' => 'require',
    ];

    // 提示信息
    protected $message = [
        'uid.require'     => '用户ID必须',
        'uid.integer'     => '用户ID必须为整形',
        // 'type.require'    => '类型必须',
        // 'type.integer'     => '类型必须为整形',
        'content.require' => '内容必须',
        'deadline.require'=> '提醒时间必须',
        // 'deadline.dateFormat:y-m-d h:i:s'=> '提醒时间不符合格式',
    ];

}