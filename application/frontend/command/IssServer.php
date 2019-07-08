<?php
namespace app\index\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;

use app\common\model\Topic;

class IssServer extends Command
{

    /**
     * 配置定时器信息
     */
    protected function configure(){
        $this->setName('IssServer')->setDescription('定时任务服务端');
    }
 
    protected function execute(Input $input,Output $output){
    	$redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
    	while (true) {
    		if ($redis->llen('topicVcList') != 0) {
	    		$topicId = $redis->rpop('topicVcList');
	    		$topicVc = $redis->get("{count:topicLike:$topicId}");
				// 写入数据库
				$topic = Topic::get($topicId);
				$topic->like_count = $topic->like_count + $topicVc;
	    		// ['like_count' => $newTopicVc], ['id' => $topicId]
	    		if ($topic->save()) {
	    			$redis->set("{count:topicLike:$topicId}", 0);
	    		}
	    	} else {
    			// 输出到日志文件
	        	$output->writeln('定时任务启动成功');
		      	sleep(1);
		    }
    	}
    }

}