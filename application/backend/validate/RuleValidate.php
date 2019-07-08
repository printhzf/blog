<?php
namespace app\backend\validate;

use think\Validate;

class RuleValidate extends Validate
{

    // 验证规则
    protected $rule = [
        'name|权限规则'  => 'require',
        'title|权限名'   => 'require',
        'type|类型'      => 'require|integer',
        'status|状态'    => 'require|integer',
        // '__token__' => 'token',
    ];

    // 提示信息
    protected $message = [
        'name.require'     => '权限规则必须',
        'title.require'    => '权限名必须',
        'type.require'     => '类型必须',
        'status.require'   => '状态必须',
        'type.integer'     => '类型必须为整型',
        'status.integer'   => '类型必须为整型',
    ];

}