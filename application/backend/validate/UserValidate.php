<?php
namespace app\index\validate;

use think\Validate;

class UserValidate extends Validate
{
    // 应用场景
    protected $scene = [
        'register' => ['username', 'password', 'resure'],
        'login'    => ['username'=>['require','token'], 'password'],
        'create'   => ['username', 'password'],
        'edit'     => ['username'=>'require', 'password']
    ];

    // 验证规则
    protected $rule = [
        'username|用户名'  => 'require|unique:user|token',
        'password|密码'    => 'require',
        'group_id|角色ID'  => 'require|integer',
        'status|状态'      => 'require|integer',
    ];

    // 提示信息
    protected $message = [
        'username.require' => '用户名必须',
        'password.require' => '密码必须',
        'group_id.require' => '角色ID必须',
        'group_id.integer' => '角色ID必须为整型',
        'status.require'   => '状态必须',
        'status.integer'   => '状态必须为整型',
    ];

}