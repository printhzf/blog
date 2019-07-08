<?php
namespace app\index\Controller;

use app\frontend\controller\Base;
use app\common\model\Reply as ReplyModel;
use app\common\model\User;
use think\facade\Request; //导入请求静态代理
use think\facade\Session; //导入Session静态代理
use think\Db;

class Reply extends Base {

	// 评论列表
	public static function index() {

		$replyListParent   = ReplyModel::alias('r')->join('user u', 'r.user_id = u.id')
							->field('r.*,u.username')->where('reply_pid', 0)->order('gmt_create desc')->select();
		$replyListChildren = ReplyModel::alias('r')->join('user u','r.user_id = u.id')
							->field('r.*,u.username')->where('reply_pid', '<>', 0)->order('gmt_create desc')->select();
		$replyListParent   = $replyListParent->toArray();
		$replyListChildren = $replyListChildren->toArray();
		foreach ($replyListParent as $k => $parent) {
			foreach ($replyListChildren as $k2 => $children) {
				if ($children['reply_pid'] == $parent['id']) {
					$replyListParent[$k]['children'][] = $children;
				}
			}
		}
		return $replyListParent;
	}

	// 发表评论
	public function saveReply() {
		$rData = Request::param('replyData/a');
		$nData = Request::param('notifiedData/a');	
		$data  = [
			'user_id'  => $rData[0],
			'topic_id' => $rData[1],
			'content'  => $rData[2],
			'reply_pid'=> $rData[3],  	
		];
		$replies = new ReplyModel;
		$state = $replies->allowField(true)->isUpdate(false)->save($data);
		if ($state == 1) {
			$reply = ReplyModel::get($replies->id); 
			// 新增评论通知
			$data = [
				'user_id'  => $nData[0],
				'user_name'=> $nData[1],
				'topic_id' => $nData[2],
				'topic_title'  => $nData[3],
				'reply_content'=> $nData[4],  	
				'reply_id' => $replies->id,  	
			];
			$reply->notice()->save($data); 

			// 更新帖子评论数
			$reply_count = $reply->topic->reply_count + 1;
			$reply->topic->save(['reply_count'=>$reply_count]);

			// 更新用户未读通知数
			$user = User::find($nData[0]);
			$notification_count = $user->notification_count + 1;
			$$user->save(['notification_count'=>$notification_count]);

			return json(array('code' => 200, 'msg' =>'发表成功'));
		} else {
			return json(array('code' => 0, 'msg' =>'发表失败'));
		}
		
	}

	// 是否为作者 
    public static function isAuthorOf($replyId, $userId) {
        $map['id'] = $replyId;
        $map['user_id'] = $userId;
        $res = ReplyModel::where($map)->find();
        if(is_null($res)){
        	return false;
        }else{
        	return true;
        }
    }

	// 删除评论 
	public function deleteReply() {
		$replyId = Request::post('replyId');
		$userId  = Request::post('userId');
		if(self::isAuthorOf($replyId, $userId)){		
			ReplyModel::destory($replyId);
			// 删除该评论下的评论
			ReplyModel::destory(['reply_pid',$replyId]);
			return json(array('code' => 200, 'msg' =>'删除成功'));
		}else{
			return json(array('code' => 0, 'msg' =>'删除失败'));
		}
	}

	// 消息推送
	public function noticed() {

	}
}