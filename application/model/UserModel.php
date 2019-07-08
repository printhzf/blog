<?php
namespace app\model;

use think\Model;

class UserModel extends Model
{
    // 设置当前模型的数据库连接
    protected $connection = [
        // 数据库类型
        'type'     => 'mysql',
        // 服务器地址
        'hostname' => '127.0.0.1',
        // 数据库名
        'database' => 'forum',
        // 数据库用户名
        'username' => 'root',
        // 数据库密码
        'password' => '',
        // 数据库编码默认采用utf8
        'charset'  => 'utf8',
        // 数据库表前缀
        'prefix'   => '',
        // 自动写入时间戳字段
        'auto_timestamp'  => true,
        // 时间字段取出后的默认时间格式（关闭使用false）
        'datetime_format' => 'Y-m-d H:i:s', 
        // 数据库调试模式
        'debug'    => false,
    ];

    // 默认主键
    protected $pk = 'id';  
    // 默认数据表
    protected $table = 'user';  

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    // 时间字段取出后的默认时间格式
    protected $dateFormat = 'Y-m-d H:i:s';
    
    // 定义时间戳字段名:默认为create_time和create_time,如果一致可省略
    // 如果想关闭某个时间戳字段,将值设置为false即可:$create_time = false
    // 创建时间字段
    protected $createTime = 'gmt_create';
    // 更新时间字段
    protected $updateTime = 'gmt_modified';
    
    // 开启自动设置
    // protected $auto = [];
    // 仅在新增有效
    protected $insert = ['status'=>1,'gender'=>2];
    // 仅在更新有效
    // protected $updtae = [];

    // 用户状态获取器
    // public function getStatusAttr($value)
    // {
    //     $status = ['1'=>'启用', '0'=>'禁用'];
    //     return $status[$value];
    // }

    // 用户性别获取器
    public function getGenderAttr($value)
    {
        $gender = ['2'=>'保密', '1'=>'男', '0'=>'女'];
        return $gender[$value];
    }

    // 用户密码修改器
    public function setPasswordAttr($value)
    {
        return md5($value);
    }

    // 用户IP修改器
    protected function setUseripAttr()
    {
        return ip2long($_SERVER["REMOTE_ADDR"]);
    }

    // 关联收藏表
    public function collect(){
        return $this->hasMany('collectModel','user_id');
    }

    // 关联帖子表
    public function topic(){
        return $this->hasMany('TopicModel','user_id');
    }

    // 关联回复表
    public function reply(){
        return $this->hasMany('ReplyModel','user_id');
    }

    // 关联备忘录模型
    public function memo()
    {
        return $this->hasMany('MemoModel', 'uid');
    }
    
    // 关联交接班模型
    public function swap()
    {
        return $this->hasMany('SwapModel', 'uid');
    }

    // 关联角色模型
    public function roles()
    {
        return $this->belongsToMany('RoleModel', '\\app\\model\\AccessModel', 'group_id', 'uid');
    }

}
