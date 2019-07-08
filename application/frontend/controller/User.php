<?php
namespace app\index\controller;

use app\common\controller\Base; //导入公共控制器
use app\common\model\Topic;
use app\common\model\User as UserModel; //导入User模型并取别名
use app\common\validate\User as UserValidate; //导入User验证器并取别名

use think\facade\Request;  //导入请求静态代理
use think\facade\Session;  //导入SESSION静态代理
use think\Db;
use Redis;



class User extends Base {

    public function __construct() {
        parent::__construct();
        $this->redis = new Redis();
        $this->redis->connect('127.0.0.1', 6379);
        // $this->redis->auth('password');
    }

    public static function test() {
        $data = UserModel::get(1);
        $redis->set("user:{$data->id}",serialize($data));
        var_dump(unserialize($redis->get("user:{$data->id}")));
    }
	
    // 登录页
	public function viewLogin() {
        $this->isRelogin();
		return $this->fetch('login');
	}

	// 验证码校验
	public static function checkCaptcha() {
        $captcha = Request::param('verify');
        if (captcha_check($captcha)) {
            //验证码正确
            return true;
        }
    }

    // 获取用户数据
    public function getUserData($userId)
    {
        // 缓存存在直接返回缓存
        if ($data = $this->redis->get("user:{$user_id}")) {
            return $data;
        }
        // 如果抢占失败再读取一次缓存
        if (!$this->redis->setnx('lock', 1)) {
            sleep(1);
            $data = $this->redis->get("user:{$user_id}");
        } else {
            $data = Db::name('user')->where('id', $user_id)->find();
            // 缓存数据
            $this->redis->set('user:{$user_id}', $data);
            // 释放锁
            $this->redis->delete('lock');
        }
        return $data;
    }

    // 验证用户登录
	public function checkLogin() {
        if (Request::isAjax()) {
            if (self::checkCaptcha()) {
                $res  = [];
                $data = Request::param();
                $validate = new UserValidate();
                if (!$validate->scene('login')->check($data)) {
                    $res = ['code'=>0,'msg'=>$validate->getError()];
                } else {
                    $map['username'] = $data['username'];
                    $map['password'] = md5($data['password']);
                    $user = UserModel::get(function($query) use ($map) {
                        $query->field('id,username,status,head_img,notification_count')->where($map);
                    });
                    if (!empty($user)) {
                        if ($user->status != '启用') {
                            $res = ['code'=>0,'msg'=>'当前用户已禁用！'];
                        } else {
                            // $redis = new Redis();
                            // $redis->connect('127.0.0.1', 6379);
                            // // 单点登录
                            // if (!$redis->get("user:{$user->id}")) {
                                // 更新用户登录时间与IP
                                $user->last_login_time = time();
                                $user->last_login_ip   = ip2long($_SERVER["REMOTE_ADDR"]);
                                $user->save();

                                // 保存登录用户信息                            
                                Session::set('userId', $user->id);
                                Session::set('userName', $user->username);
                                Session::set('headImg', $user->head_img);
                                Session::set('userNc', $user->notification_count);  // 未读消息个数
                                
                                $collectsArr = $user->collect()->column('topic_id');
                                if (!empty($collectsArr)) {
                                    $userCollects = implode(',', $collectsArr);
                                    Session::set('userCollects', $userCollects);  // 用户收藏列表
                                }
                                // $redis->set("user:{$user->id}", serialize($user));
                                $res = ['code'=>200,'msg'=>'登录成功！'];
                            // } else {
                            //     $res = ['code'=>0,'msg'=>'您已登录！'];
                            // }

                        }
                    } else {
                        $res = ['code'=>0,'msg'=>'用户名或密码错误！'];
                    }
                }
            } else {
                $res = ['code'=>0,'msg'=>'验证码错误！'];
            }
            return json($res);
        }
	}

    // 退出登录
    public function logout() {
        // 1.删除redis的user
        // $userId= Session::get('userId');
        // $redis = new Redis();
        // $redis->connect('127.0.0.1', 6379);
        // $redis->del("user:{$userId}");
        // 2.清除全部session
        Session::clear();
        // 3.退出登录并跳转到登录页面
        $this->redirect('index/index/index');
    }

    // 注册页
    public function viewRegister() {
    	return $this->fetch('register');
    }

    // 添加用户
    public function saveUser() {
        if (Request::isAjax()) {
            $data = Request::param();
            $validate = new UserValidate();
            if (!$validate->scene('register')->check($data)) {
                Session::set('register_email', $data['email']);
                Session::set('register_name', $data['username']);
                Session::set('register_pass', $data['password']);
                return json(array('code' => 0, 'msg' =>$validate->getError()));
            } else {
                $user  = new UserModel();
                $data['user_ip'] = $_SERVER["REMOTE_ADDR"];
                $user->data($data,true); //批量修改
                $state = $user->allowField(['username','password','email','user_ip'])->isUpdate(false)->save();
                if ($state == 1){
                    return json(array('code' => 200, 'msg' => '注册成功'));
                } else {
                    return json(array('code' => 0, 'msg' => '注册失败'));
                }
            }
        }
    }

    // 个人档案 
    public function viewUserInfo() {
        
        $userId   = Request::param('user_id');
        $userInfo = UserModel::get($userId);
        $timeline = self::getTimeline($userId);
        $this->view->assign('userInfo',$userInfo);
        $this->view->assign('timeline',$timeline);
        return $this->fetch('userInfo');
    }

    // TODO：获取时间轴数据
    public static function getTimeline($userId) {
        $map['user_id'] = $userId;
        $data = Topic::all(function ($query) use ($map) {
            $query->field('id, title, title_img, brief, reply_count, gmt_create')
                  ->where($map)->order('gmt_create desc');
        });
        return $data;
    }

    // 用户编辑
    public function viewUserEdit() {
        return $this->fetch('edit');
    }

    // 未读消息
    public function viewNotification() {
        
        $map['id'] = Request::param('user_id');
        $num = Db::table('notice')->where($map)->count();
        if ($num > 0) {
            $list = Db::table('notice')->alias('n')->join('user u','n.user_id = u.id')
                ->field('n.*,u.username,u.head_img')->where($map)->order('gmt_create','desc')->select();
        
            foreach($list as $k => $v) {
                $list[$k]['gmt_create']    = convertTime($v['gmt_create']);
                $list[$k]['reply_content'] = convertText($v['reply_content']);
            }

            // 未读通知数量归零
            UserModel::where($map)->update(['notification_count'=>0]);
            Session::set('userNc',0);

            $this->view->assign('list',$list);
        }
        
        return $this->fetch('notification');
    }

    /*****
     *  结合layui的文件上传
     *  1.上传文件格式校验
     *  2.上传参数校验
     *
     */
    public function uploadImg($request) {
        $status = 0;
        $data = [];
        $file = request()->file('file'); 
        print_r($file);exit;
        if(empty($file)) {  
            $message = '请选择上传文件';  
        }  
        // 将文件移动到框架应用目录/public/upload/ 目录下
        $info = $file->validate(['size'=>15678,'ext'=>'jpg,png,gif'])
                     ->move(ROOT_PATH.'public'.DS.'upload'); 
        if($info) {
            // 成功上传 获取上传信息
            // $info->getExtension(); // 文件名的后缀
            $data['url'] = $info->getSaveName(); //文件的位置以及文件名
            $status  = 1;
            $message = '文件上传成功！';
            // $uploadFile = new Uploadfile();
            // $uploadFile->userid = $user;
            // $uploadFile->filename = $info->getFilename();
            // $uploadFile->path = $fileUrl;
            // $uploadFile->save();
        } else {
            // 上传失败获取错误信息
            $message = $file->getError();
        }
        return showMsg($status, $message, $data);
    }

    public function uploadFile(Request $request) {
        $status = 0;
        $data = [];
        $file = request()->file('file'); 
        if (empty($file)) {  
            $message = '请选择上传文件';  
            $status = 3;
        }  
        $info = $file->move(ROOT_PATH.'public'.DS.'upload'); 
        if ($info) {
            $status  = 1;
            $data['url'] = $info->getSaveName();
            $message = '文件上传成功！';
            // $uploadFile = new Uploadfile();
            // $uploadFile->userid = 1;
            // $uploadFile->filename = $file_name;
            // $uploadFile->path = $fileUrl;
            // $uploadFile->save();
        } else {
            $message = $file->getError();
        }
        
        return showMsg($status, $message, $data);
    }

}
