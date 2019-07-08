<?php
namespace app\backend\validate;

use think\Validate;

class RoleValidate extends Validate
{

    // 验证规则
    protected $rule = [
        'title|角色名称' => 'require',
        'brief|描述'     => 'require',
        // '__token__' => 'token',
    ];

    // 提示信息
    protected $message = [
        'title.require' => '角色名称必须',
        'brief.require' => '描述必须',
    ];

}