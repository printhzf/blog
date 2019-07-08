<?php
namespace app\backend\controller;

use think\Controller;
use app\model\MemoModel;
use app\backend\validate\MemoValidate;
use think\facade\Request;
use think\facade\Session;
use think\db\Where;
use think\Db;
use Redis;

class Memo extends Controller
{
     /**
     * @Author:      Hzf
     * @DateTime:    2019-04-13
     * @Description: 备忘录首页
     */
    public function index() {
    	$map = [];  // 设置全局查询条件
    	// 初始条件
        $uid = Session::has('uerId') ? Session::get('uerId') : 1;
        $map['status'] = 1;
        $map['uid'] = $uid;
        // 实现搜索功能
        $range = Request::param('datetimeRange');
        if (!empty($range)) {
            $rangeArr = explode('~', $range);
            $range = strtotime($rangeArr[0]).','.strtotime($rangeArr[1]);
            $map['deadline'] = ['between',$range];
        }
        $keywords  = Request::param('keywords');
        if (!empty($keywords)) { 
            $map['content'] = ['like',"%{$keywords}%"]; 
        }
    	// thinkphp使用自定义排序
    	$exp = new \think\db\Expression('if(is_top = 1,0,1)');
    	$field = 'id,uid,deadline,content,is_top';
        // 注意SQL注入
        $memoList = MemoModel::field($field)->where(new Where($map))
                    ->order($exp)->order('gmt_modified','desc')
                    ->paginate(4);
                    // ->each(function($item, $key) use ($keywords){
                    //     if (!empty($keywords)) {
                    //         $item['content'] = preg_replace("/($keywords)/i","<span class='highlight'>{$keywords}</span>",$item['content']);
                    //     }
                    // });
    	$this->assign('memoList', $memoList);
    	return $this->fetch('memo_index');
    }

    /**
     * @Author:      Hzf
     * @DateTime:    2019-04-13
     * @Description: 备忘录编辑页
     */
    public function viewEditMemo() {
    	$mid  = Request::param('id');
    	$data = [
    		'id'  => 0,
    		'uid' => Session::get('userId'),
    		'deadline' => date('Y-m-d H:i:s'),
    		'content'  => ''
    	];
    	$modelObj = $mid != 0 ? MemoModel::get($mid) : $data;
    	$this->assign('modelObj', $modelObj);
    	return $this->fetch('memo_edit');
    }

     /**
     * @Author:      Hzf
     * @DateTime:    2019-04-13
     * @Description: 新增/编辑备忘录信息保存
     */
    public function save() {
    	if (Request::isAjax()) {
    		$param = Request::post();
    		$res = $this->validate($param, 'Memo');
            if (true !== $res) {
                return json(array('code' => 0, 'msg' =>$res));
            } else {
            	$mid  = $param['mid'];
        		$memo = $mid != 0 ? MemoModel::get($mid) : new MemoModel;	
                if ($memo->allowField(true)->save($param)) {
                    return json(array('code' => 200, 'msg' =>'保存成功！'));
                } else {
                    return json(array('code' => 0, 'msg' =>'保存失败！'));
                }
    		}
    	}	
    }

     /**
     * @Author:      Hzf
     * @DateTime:    2019-04-13
     * @Description: 删除备忘录
     */
    public function delete() {
    	$id = Request::param('id');
    	$res= MemoModel::destroy($id);
    	if ($res == 1) {
            return json(array('code' => 200, 'msg' =>'删除成功！'));
        } else {
            return json(array('code' => 0, 'msg' =>'删除失败！'));
        }
    }

     /**
     * @Author:      Hzf
     * @DateTime:    2019-04-13
     * @Description: 备忘录置顶
     */
    public function setStick() {
        $memo = MemoModel::get(Request::param('id'));
        $memo->is_top = Request::param('is_top');
        if ($memo->save()) {
            return json(array('code' => 200, 'msg' =>'设置成功！'));
        } else {
            return json(array('code' => 0, 'msg' =>'设置失败！'));
        }
    }

     /**
     * @Author:      Hzf
     * @DateTime:    2019-04-13
     * @Description: 新增闹钟
     */
    public function setClocks() {
        show(111);exit;
        // 注意防止恶意频繁访问
        // $redis = new Redis;
        // return json(array('code' => 0, 'msg' =>'系统繁忙！请勿频繁操作！'));
        // 检测当前用户当天是否有需要提醒的内容
        $uid = Session::has('userId') ? Session::get('userId') : 1;
        $field = 'id,content,deadline';
        $sql = "SELECT $field FROM `memo` WHERE uid = $uid AND status = 1 AND TO_DAYS(FROM_UNIXTIME(deadline)) = TO_DAYS(NOW())";
        $res = Db::query($sql);
        if (!empty($res)) {	
        	$clocks = [];
        	$expired= [];
        	foreach ($res as $k => $v) {
        		if ($v['deadline'] - time() >= 0) {
        			$clocks[$k] = $v;
        			$clocks[$k]['clockname'] = 'clock'.$v['id'];
        			$clocks[$k]['clocktime'] = ($v['deadline'] - time()) * 1000;
        		} else {
        			$expired[$k]['id'] = $v['id'];
        			$expired[$k]['status'] = 0;
        		}
        	}
            // 修改过期闹钟的状态
        	if (!empty($expired)) {
        		$memo = new MemoModel;
                $memo->saveAll($expired); 
        	}
        	if (!empty($clocks)) {
        		return json(['code' => 200, 'msg' => $clocks]);
        	} 
        } 
        return json(['code' => 0, 'msg' => '暂无定时任务']);
        
    }

     /**
     * @Author:      Hzf
     * @DateTime:    2019-04-13
     * @Description: 用户静置页面到第二天后自动刷新闹钟 或者 获取两天内的闹钟，当用户session过期后，重新登录并刷新闹钟
     */
    public static function updateMemoStatus() {
    	
    }

    
}
