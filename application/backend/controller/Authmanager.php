<?php
namespace app\backend\controller;

use think\Controller;
use app\model\RoleModel;
use app\model\RuleModel;
use app\backend\validate\RoleValidate;
use app\backend\validate\RuleValidate;
use think\facade\Session;
use think\facade\Request;
use think\facade\File;
use think\db\Where;
use think\Db;
use auth\Auth;
/**
 * 权限管理
 */
class Authmanager extends Controller
{
    /**
     * @Author:      Hzf
     * @DateTime:    2019-05-02
     * @Description: 权限列表页
     */
    public function viewRuleList() {
        return $this->fetch('rule_list');
    }

    /**
     * @Author:      Hzf
     * @DateTime:    2019-05-02
     * @Description: 获取权限列表数据
     */
    public function getRuleList() { 
        $map  = [];
        $data = [];
        $cond  = Request::param('cond');
        $name  = $cond['name'];
        $title = $cond['title'];
        $type  = $cond['type'];
        $status  = $cond['status'];
        $condition  = $cond['condition'];
        if (!empty($name)) {
            $map['name'] = ['like',"%{$name}%"];
        }
        if (!empty($name)) {
            $map['title'] = ['like',"%{$title}%"];
        }
        if (!empty($name)) {
            $map['condition'] = ['like',"%{$condition}%"];
        }
        if (!empty($type)) {
            $map['type'] = $type;
        }
        if (!empty($type)) {
            $map['status'] = $status;
        }
        if (!empty($map)) {
            $count = RuleModel::where(new Where($map))->count();
            $data  = RuleModel::where(new Where($map))->all();
        } else {         
            $count = RuleModel::count();
            $data  = RuleModel::all();
        }
    	return showLayuiTableMsg($count, $data);
    }


     /**
     * @Author:      Hzf
     * @DateTime:    2019-05-02
     * @Description: 权限编辑页
     */
    public function viewEditRule() {
        $id = Request::param('id');
        $ruleList = RuleModel::field('id,title')->all();
        $modelObj = RuleModel::get($id);
        $this->assign('ruleList', $ruleList);
        $this->assign('modelObj', $modelObj);
        return $this->fetch('rule_edit');
    }

    /**
     * @Author:      Hzf
     * @DateTime:    2019-05-03
     * @Description: 获取编辑权限列表数据
     */
    public function getEditRuleData() {
        $checkedId= [];
        $ruleList = [];
        $id = Request::param('id');
        $ruleList = RuleModel::all()->toArray();
        $checked  = RoleModel::field('rules')->get($id);  
        if (!empty($checked['rules'])) {
            $checkedArr= explode(',', $checked['rules']);
            foreach ($checkedArr as $k => $v) {
                $checkedId[] = (int)$v;
            }
        }
        $res  = [
            'code' => 0,
            'msg'  => '获取成功',
            'data' => [
                'list'      => $ruleList,
                'checkedId' => $checkedId
            ]
        ];
        return json($res);
    }

     /**
     * @Author:      Hzf
     * @DateTime:    2019-05-02
     * @Description: 保存编辑权限
     */
    public function saveRule() {
        $param = Request::param();
        $validate = new RuleValidate();
        if (!$validate->check($param)) {
            $res = ['code'=>0, 'msg'=>$validate->getError()];
        } else {
            $param['name'] = strtolower($param['name']);
            $param['condition'] = trim($param['condition']);
            $rule = $param['id'] != 0 ? RuleModel::get($param['id']) : new RuleModel;
            if ($rule->allowField(true)->save($param)) {
                $res = ['code'=>200, 'msg'=>'保存成功！'];
            } else {
                $res = ['code'=>0, 'msg'=>'保存失败！'];
            }
        }
        return json($res);
    }
 
     /**
     * @Author:      Hzf
     * @DateTime:    2019-05-02
     * @Description: 删除权限
     */
    public function deleteRule() {
        $msg   = '';
        $idArr = Request::param('idArr');
        $roles = RoleModel::field('id,rules')->all()->toArray(); 
        foreach ($idArr as $id) {
            $rule = RuleModel::field('pid,name')->get($id); 
            $name = $rule->name;
            // 提示删除子级权限
            if (RuleModel::where('pid', $id)->count() > 0) {
                $msg .= "请先删除{$name}的子级权限！";
            } else {
                // 删除权限
                $res  = $rule->delete();
                // 注意修改角色拥有权限
                if (!empty($roles)) {
                    foreach ($roles as $k => $v) {
                        $rulesArr = explode(',', $v['rules']);
                        $key = array_search($id, $rulesArr);
                        if ($key !== false) {
                            array_splice($rulesArr, $key, 1);
                            $rules = implode(',', $rulesArr);
                            $role  = RoleModel::get($v['id']);
                            $role->rules = $rules;
                            $role->save();
                        }
                    }
                } 
            }
            $msg .=  $res ? "{$name}删除成功！" : "{$name}删除失败！";
            $msg .= "<br/>";
        }
        return json(['code' => 200, 'msg' => $msg]);
    }

    /**
     * @Author:      Hzf
     * @DateTime:    2019-05-02
     * @Description: 角色列表页
     */
    public function viewRoleList(){
        $id = Request::param('id');
        $modelObj = RuleModel::get($id);
        $this->assign('modelObj', $modelObj);
        return $this->fetch('role_list');
    }

     /**
     * @Author:      Hzf
     * @DateTime:    2019-05-02
     * @Description: 获取角色列表数据
     */
    public function getRoleList() { 
        $map   = [];
        $data  = [];
        // TODO:参数校验
        $limit = intval(Request::param('limit'));
        $page  = intval(Request::param('page'));
        $begin = ($page-1)*$limit;
        $cond  = Request::param('cond');
        $title = $cond['title'];
        $brief = $cond['brief'];
        if (!empty($title)) {
            $map['title'] = ['like',"%{$title}%"];
        } 
        if (!empty($brief)) {
            $map['brief'] = ['like',"%{$brief}%"];
        } 
        $data = RoleModel::where(new where($map))->limit($begin, $limit)->all()->toArray();
        $ruleList = RuleModel::limit($begin, $limit)->all()->toArray();
        if (!empty($ruleList)) {
            $ruleArr = array_column($ruleList, 'title', 'id');
            if (!empty($data) && !empty($ruleList)) {         
                foreach ($data as $k => $v) {
                    $title = '';
                    $rules = explode(',', $v['rules']);
                    foreach ($rules as $k2 => $v2) {  
                        $title .= $ruleArr[$v2];
                        $title .= '、';
                    }
                    $data[$k]['rules'] = rtrim($title,'、');
                    unset($title);
                }
            }
        }
        return showLayuiTableMsg(count($data), $data);
    }

     /**
     * @Author:      Hzf
     * @DateTime:    2019-05-02
     * @Description: 展示角色编辑页
     */
    public function viewEditRole() {
        $id = Request::param('id');
        $modelObj = RoleModel::get($id);
        $this->assign('roleId', $id);
        $this->assign('modelObj', $modelObj);
        return $this->fetch('role_edit');
    }

     /**
     * @Author:      Hzf
     * @DateTime:    2019-05-02
     * @Description: 保存编辑角色
     */
    public function saveRole() {
        $param = Request::param();
        $validate = new RoleValidate();
        if (!$validate->check($param)) {
            $res = ['code'=>0, 'msg'=>$validate->getError()];
        } else {
            $param['rules'] = implode(',', $param['rules']);
            $role = $param['id'] != 0 ? RoleModel::get($param['id']) : new RoleModel;
            if ($role->allowField(true)->save($param)) {
                $res = ['code'=>200, 'msg'=>'保存成功！'];
            } else {
                $res = ['code'=>0, 'msg'=>'保存失败！'];
            }
        }
        return json($res);
    }

     /**
     * @Author:      Hzf
     * @DateTime:    2019-05-02
     * @Description: 删除角色
     */
    public function deleteRole() {
        $msg   = '';
        $idArr = Request::param('idArr');
        foreach ($idArr as $id) {
            $role  = RoleModel::field('title')->get($id); 
            $title = $role->title;
            $res   = $role->delete();
            $msg  .= $res ? "{$title}删除成功！" : "{$title}删除失败！";
            $msg  .= "<br/>";
        }
        return json(['code' => 200, 'msg' => $msg]); 
    }

}