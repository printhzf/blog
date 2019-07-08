<?php
namespace app\backend\controller;

use think\Controller;
use app\backend\validate\UserValidate;
use app\model\RoleModel;
use app\model\RuleModel;
use app\model\UserModel;
use think\captcha\Captcha;
use think\facade\Session;
use think\facade\Request;
use think\facade\File;
use think\db\Where;
use think\Db;

/**
 * 用户管理：
 * 1.用户列表
 * 2.用户信息CURD
 */
class User extends Controller
{
    private $userService;

    public function initialize()
    {
        $this->userService = new \app\service\UserService;
    }

    /**
     * @Author:      Hzf
     * @DateTime:    2019-05-09
     * @Description: 登录页
     */
	public function viewLogin()
    {
        // 判断是否已登录
        // $this->isRelogin();
		return $this->fetch('user_login');
	}

    /**
     * @Author:      Hzf
     * @DateTime:    2019-05-09
     * @Description: 生成验证码
     */
    public function verify()
    {
    	$config = [
             // 验证码字体大小
            'fontSize' => 30,
            // 验证码位数
            'length'   => 3,
            // 关闭验证码杂点
            'useNoise' => true,
            // // 验证码图片高度
            'imageH'   => 80,
            // // 验证码图片宽度
            'imageW'   => 180,
            // 验证码过期时间（s）
            'expire'   => 1800,
            // 验证码字体，不设置随机获取
        ];
        return $this->userService->verify($config);
    }

    /**
     * @Author:      Hzf
     * @DateTime:    2019-05-09
     * @Description: 验证用户登录
     */
    public function checkLogin() {
        if(Request::isAjax()) {
            $param    = Request::param();
            $validate = new UserValidate();
            if (!$validate->scene('login')->check($param)) {
                return showMsg(0, $validate->getError());
            } else {
                return ajaxReturn($this->userService->login($param));
            }
        }
    }

    /**
     * @Author:      Hzf
     * @DateTime:    2019-05-09
     * @Description: 退出登录
     */
    public function logout() {
        //1.清除全部session
        Session::clear();
        //2.退出登录并跳转到登录页面
        $this->redirect('login');
    }

    /**
     * @Author:      Hzf
     * @DateTime:    2019-05-09
     * @Description: 注册页
     */
    public function viewRegister(){
    	return $this->fetch('user_register');
    }

    /**
     * @Author:      Hzf
     * @DateTime:    2019-05-09
     * @Description: 添加用户
     */
    public function create(){
        if(Request::isAjax()){
            $data = Request::param();
            $validate = new UserValidate();
            if (!$validate->scene('register')->check($data)) {
                return showMsg(0, $validate->getError());
            } else {
                $user  = new UserModel();
                $data['userip']   = $_SERVER["REMOTE_ADDR"];
                $data['password'] = md5($data['password']);
                $user->data($data, true); //批量修改
                $state = $user->allowField(['username','password','email','userip'])->isUpdate(false)->save();
                if ($state == 1){
                    return json(array('code' => 200, 'msg' => '注册成功'));
                } else {
                    return json(array('code' => 0, 'msg' => '注册失败'));
                }
            }
        }
    }

    /**
     * @Author:      Hzf
     * @DateTime:    2019-05-09
     * @Description: 用户列表页
     */
    public function viewUserList() {
        $roleList = Db::name('think_auth_group')->field('id,title')->where('status',1)->select();
        $this->assign('roleList', $roleList);
        return $this->fetch('user_list');
    }

     /**
     * @Author:      Hzf
     * @DateTime:    2019-05-21
     * @Description: 获取用户列表数据(优化)
     */
    public function getUserList() {
        $map   = [];
        $data  = [];
        $limit = intval(Request::param('limit'));
        $page  = intval(Request::param('page'));
        $begin = ($page-1)*$limit;
        $condition = Request::param('condition');
        $ip  = $condition['last_login_ip'];
        $gid = $condition['group_id'];
        $status = $condition['status'];
        $username = $condition['username'];
        if (!empty($ip)) {
            $map['last_login_ip'] = ['like',"%{$ip}%"];
        }
        if (!empty($status)) {
            $map['status'] = $status;
        }
        if (!empty($username)) {
            $map['username'] = ['like',"%{$username}%"];
        }
        $userArr = UserModel::with(['roles' => function($query) use ($gid) {
            if (!empty($gid)) {
                $query->field('title')->where('id', $gid);
            } else {
                $query->field('title');
            }
        }])->where(new where($map))->all()->toArray();
        if (!empty($userArr)) {             
            foreach ($userArr as $k => $user) {
                if (!empty($user['roles'])) {
                    $title  = ['role' => $user['roles'][0]['title']];
                    $data[] = array_merge($user, $title);
                }
            }
        } else {
            $data = $userArr;
        }
        return showLayuiTableMsg(count($data), $data);
    }

    /**
     * @Author:      Hzf
     * @DateTime:    2019-05-15
     * @Description: 管理中心页
     */
    public function viewUserInfo(){
        $id = Request::param('id');
        $data = [
            'id'  => 0,
            'username' => '',
            'password' => '',
            'email'    => ''
        ];
        $user = UserModel::alias('u')
                ->join('think_auth_group_access a','u.id = a.uid')
                ->join('think_auth_group g','a.group_id = g.id')
                ->field('u.*,g.id as gid,g.title as role')
                ->get($id);
        $modelObj = $id != 0 ? $user : $data;
        $roleList = Db::name('think_auth_group')->field('id,title')->where('status',1)->select();
        $this->assign('modelObj', $modelObj);
        $this->assign('roleList', $roleList);
        return $this->fetch('user_info');
    }

    /**
     * @Author:      Hzf
     * @DateTime:    2019-05-09
     * @Description: 用户编辑页
     */
    public function viewEditUser(){
        $id = Request::param('id');
        $data = [
            'id'  => 0,
            'gid' => 1,
            'username' => '',
            'password' => '',
            'email'    => ''
        ];
        $user = UserModel::alias('u')
                ->join('think_auth_group_access a','u.id = a.uid')
                ->join('think_auth_group g','a.group_id = g.id')
                ->field('u.*,g.id as gid,g.title as role')
                ->get($id);
        $modelObj = $id != 0 ? $user : $data;
        $roleList = Db::name('think_auth_group')->field('id,title')->where('status',1)->select();
        $this->assign('modelObj', $modelObj);
        $this->assign('roleList', $roleList);
        return $this->fetch('user_edit');
    }

    /**
     * @Author:      Hzf
     * @DateTime:    2019-05-09
     * @Description: 保存用户编辑
     */
	public function saveUser() {
        if (Request::isPost()) {
            $param = Request::post();
            $res = ['code' => 0, 'msg' =>'保存失败！'];
            $validate = new UserValidate;
            if ($param['id'] != 0) {
                $check = $validate->scene('edit')->check($param);
                if (true !== $check) {
                    $res = ['code' => 0, 'msg' =>$validate->getError()];
                } else {
                    $user = UserModel::get($param['id']);  
                    if ($param['password'] != $user->password) {
                        $param['password'] = md5($param['password']);
                    }
                    if ($user->allowField(true)->save($param)) {
                        // 删除旧数据
                        $user->roles()->detach();
                        if ($user->roles()->attach($param['group_id'])) {
                            $res = ['code' => 200, 'msg' =>'保存成功！'];
                        }
                    }
                }
            } else {
                $check = $validate->scene('create')->check($param);
                if (true !== $check) {
                    $res = ['code' => 0, 'msg' =>$validate->getError()];
                } else {
                    unset($param['id']);
                    $model = new UserModel;
                    $param['password'] = md5($param['password']);
                    if ($model->allowField(true)->save($param)) {
                        $user = UserModel::get($model->id);
                        if ($user->roles()->attach($param['group_id'])) {
                            $res = ['code' => 200, 'msg' =>'保存成功！'];
                        }
                    }
                }
            }
            return json($res);
        }   
	}

    /**
     * @Author:      Hzf
     * @DateTime:    2019-05-09
     * @Description: 删除用户
     */
	public function deleteUser() {
		$idArr = Request::param('idArr');
        if (!empty($idArr)) {
            $msg = '';
            foreach ($idArr as $id) {             
                $user = UserModel::get($id);
                $username = $user->username;
                $user->roles()->detach();
                $res = $user->delete();
                $msg .=  $res ? "{$username}删除成功！" : "{$username}删除失败！";
                $msg .= "<br/>";
            }
            return  json(array('code' => 200, 'msg' =>$msg));
        }
	}

}