<?php
namespace app\backend\controller;

use think\Controller;
use think\facade\Request;
use think\facade\File;

/**
 * 文件管理：
 * 1.文件创建
 * 2.判断文件权限
 * 3.文件大小、创建时间、修改时间、访问时间
 * 4.文件内容查看、修改
 * 5.文件删除、重命名、复制、剪切
 * 6.文件上传、下载
 * TODO: 文件预览
 */
class Filemanager extends Controller
{

	// 图片后缀
	public $imageExt = ['gif','jpg','png','jpeg'];

	/**
	 * @Author:      Hzf
     * @DateTime:    2019-05-01
     * @Description: 文件管理页
	 */
	public function index() 
	{
		$dirName = Request::param('path') ? Request::param('path') : '../public/uploads/account';
		if ($dirName != '../public/uploads/account') {
			$backDir = dirname($dirName);
		} else {
			$backDir = '../public/uploads/account';
		}
		$this->assign('backDir', $backDir);
		$this->assign('dirname', $dirName);
		return $this->fetch('file_manager');
	}

	/**
	 * @Author:      Hzf
     * @DateTime:    2019-05-01
     * @Description: 遍历获取文件夹 
     * @param string $path  路径
	 * @return array $arr   指定目录下的所有文件夹
	 */
	public function listDir($path)
	{
	    $path .= substr($path, -1) == '/' ? '' : '/';
	    $arr = [];
	    foreach (glob($path.'*') as $v) {
	        $arr[] = $v; 
	        if (is_dir($v)) {
	            $arr['dir'][] = array_merge($arr, $this->listDir($v));
	        } 
	    }
	    return $arr;
	}

	/**
	 * @Author:      Hzf
     * @DateTime:    2019-05-01
     * @Description: 获取指定路径下的所有文件和文件夹
	 * @param string $path  路径
	 * @return array $arr   指定目录下的所有文件与文件夹
	 */
	public function readDir($path='../public/uploads/account'){
	    $arr = [];
	    $path .= substr($path, -1) == '/' ? '' : '/';
	    foreach (glob($path.'*') as $v) {
	        if (is_dir($v)) {
	            $arr['dir'][]  = $v;
	        } 
	        if (is_file($v)) {
				$arr['file'][] = $v;
			}
	    }
	    return $arr;
	}

	/**
	 * @Author:      Hzf
     * @DateTime:    2019-05-01
     * @Description: 获取文件夹大小
	 * @param string $path 路径
	 * @return $GLOBALS['count'] 文件夹大小
	 */
	public function getDirSzie($path) {
		$GLOBALS['count'] = 0;
		$path .= substr($path, -1) == '/' ? '' : '/';
	    foreach (glob($path.'*') as $v) {
	    	if (is_file($v)) {
				$GLOBALS['count'] += filesize($v);
			}
	        if(is_dir($v)){
	            $this->getDirSzie($v);
	        } 
	    }
	    return $GLOBALS['count'];
	}

	/**
	 * @Author:      Hzf
     * @DateTime:    2019-05-01
     * @Description: 遍历获取文件夹内所有文件的基本信息
	 * @param  string $path  路径
	 * @return json   $res   文件基本信息(文件大小、创建、修改、访问时间、文件是否可读、可写、可操作)
	 */
	public function getTableData() {
		$count = 0;
		$data  = [];
		$limit = Request::param('limit');
		$page  = Request::param('page') - 1;
		$path  = Request::param('path');
		$arr   = $this->readDir($path);
		if (!empty($arr['dir'])) {	
			// $dirSize = count($arr['dir']);
			foreach ($arr['dir'] as $k => $v) {
				$size = $this->getDirSzie($v);
				$data[$k] = [
					'id'   => $k+1,
					'size' => $this->transByte($size),
					'filename' => basename($v),
					'filetype' => 'dir',
					'is_readable'  => is_readable($v) ? 'yes' : 'no',
					'is_writeable' => is_writeable($v) ? 'yes' : 'no',
					'is_executable'=> is_executable($v) ? 'yes' : 'no',
					'gmt_create'   => date('Y-m-d H:i:s',filectime($v)),
					'gmt_modified' => date('Y-m-d H:i:s',filemtime($v)),
					'gmt_visited'  => date('Y-m-d H:i:s',fileatime($v)),
				];
			}
			$count = count($data);
		} 
		if (!empty($arr['file'])) {	
			foreach ($arr['file'] as $k => $v) {
				$data[$count+$k] = [
					'id'   => $count+$k+1,
					'size' => $this->transByte(filesize($v)),
					'filename' => basename($v),
					'filetype' => getExt($v),
					'is_readable'  => is_readable($v) ? 'yes' : 'no',
					'is_writeable' => is_writeable($v) ? 'yes' : 'no',
					'is_executable'=> is_executable($v) ? 'yes' : 'no',
					'gmt_create'   => date('Y-m-d H:i:s', filectime($v)),
					'gmt_modified' => date('Y-m-d H:i:s', filemtime($v)),
					'gmt_visited'  => date('Y-m-d H:i:s', fileatime($v)),
				];
			}
		}
		// clearstatcache();// 清除缓存
		return showLayuiTableMsg(count($data), $data);
	}

	/**
	 * @Author:      Hzf
     * @DateTime:    2019-05-01
     * @Description: 获取文件内容
	 * @param string $path  路径
	 * @return json  $res   文件基本信息
	 */
	public function getFileContent() {
		$path = Request::param('path');
		$type = filetype($path);
		$content = file_get_contents($path);
		if (mb_strlen($content) > 0) {
			$content = iconv("GB2312", "UTF-8", $content);  
			$newContent = highlight_string($content, true);
			return json(array('code'=>200, 'msg'=>$newContent));
		} else {
			return json(array('code'=>0, 'msg'=>'文件内容为空'));
		}
	}

	/**
	 * @Author:      Hzf
     * @DateTime:    2019-05-01
     * @Description: 文字格式转换 
	 * @param  string $data  文字
	 * @return string 
	 */
	public static function convert_utf8($data) {
		if( !empty($data) ){
			$fileType = mb_detect_encoding($data , mb_list_encodings(), true);
			if( strtolower($fileType) != 'utf-8'){
				$data = mb_convert_encoding($data, 'utf-8', $fileType);  
			}
		}
		return $data;
	}

	/**
	 * @Author:      Hzf
     * @DateTime:    2019-05-01
     * @Description: 字节单位转换  
	 * @param string $size  字节大小
	 * @return string 
	 */
	public static function transByte($size) {
		$arr = ['Byte', 'KB', 'MB', 'GB', 'TB', 'EB'];
		$i = 0;
		while ($size>=1024) {
			$size /= 1024;
			$i++; 
		}
		return round($size,2).$arr[$i];
	}

	/**
	 * @Author:      Hzf
     * @DateTime:    2019-05-01
     * @Description: 校验文件名字是否包含特殊符号  
	 * @param $path string 文件名
	 * @return boolaan 
	 */
	public function cheackFilename($path) {
		$fileName = basename($path);
		// 验证文件名是否包含,;；，《》*?/|
		$pattern = '/[、,;；，《》\*\?\/\|]+/';
		if (preg_match($pattern, $fileName)) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * @Author:      Hzf
     * @DateTime:    2019-05-01
     * @Description: 创建txt文件  
	 * @param string $path 路径
	 * @return json
	 */
	public function createFile() {
		$path = Request::param('path');
		if (!$this->cheackFilename($path)) {
			return json(array('code'=>0, 'msg'=>'文件名含有非法字符'));
		} else {
			// 检测当前目录是否包含同名文件
			if (!file_exists($path)) {
				if (touch($path)) {
					return json(array('code'=>200,'msg'=>'创建成功'));
				}
			} else {
				return json(array('code'=>0,'msg'=>'当前目录已存在同名文件，请重命名后创建'));
			}
		}
	}

	/**
	 * @Author:      Hzf
     * @DateTime:    2019-05-01
     * @Description: 文件内容更新页   
	 * @param string $path  路径
	 * @return $html
	 */
	public function viweUpdateFile() {
		$path = Request::param('path');
		$ext  = substr($path, strrpos($path, ".") + 1);
		if (!in_array($ext, $this->imageExt)) {
			$content = file_get_contents($path);
			$newContent = '';
			if (mb_strlen($content) > 0) {
				$content = iconv("GB2312", "UTF-8", $content);  
				$newContent = highlight_string($content, true);
			} 
			$html = <<<EOF
	<div style="text-align:center;padding:10px;">
		<form class="layui-form layui-form-pane">
		<input name="path" type="hidden" value="{$path}">
		<div class="layui-form-item layui-form-text">
			<div class="layui-input-block">
				<textarea name="newFileContent" placeholder="请输入内容" class="layui-textarea" style="height:178px">{$newContent}</textarea>
			</div>
		</div>
		</form>
		<button class="button button-3d button-primary button-rounded" onclick="updateFile()">保存</button>
	</div>
EOF;
			return $html;
		}
	}

	/**
	 * @Author:      Hzf
     * @DateTime:    2019-05-01
     * @Description: 编辑文件内容  
	 * @param string $path  路径
	 * @return json  $res   返回信息
	 */
	public function updateFile() {
		$path = Request::param('path');
		$content = Request::param('content');
		if (file_put_contents($path, $content)) {
			return json(array('code'=>200,'msg'=>'文件内容修改成功'));
		} else {
			return json(array('code'=>0,'msg'=>'文件内容修改失败'));
		}
	}

	/**
	 * @Author:      Hzf
     * @DateTime:    2019-05-01
     * @Description: 删除文件或文件夹  
	 * @param string $path 路径
	 * @return json  $res  返回信息
	 */
	public function deleteFunc() {
		$path = Request::param('path');
		if (filetype($path) == 'dir') {
			$this->deleteDir($v);
		} else {
			$this->deleteFile($path);
		}
	}

	/**
	 * @Author:      Hzf
     * @DateTime:    2019-05-01
     * @Description: 删除文件  
	 * @param string $path  路径
	 * @return json  $res  返回信息
	 */
	public function deleteFile($path) {
		if (file_exists($path)) { 
			if (unlink($path)){
				return json(array('code'=>200,'msg'=>'删除成功'));
			}
		} else {
			return json(array('code'=>0,'msg'=>'此文件已被删除'));
		}
	}

	/**
	 * @Author:      Hzf
     * @DateTime:    2019-05-01
     * @Description: 删除文件夹 
	 * @param string $path  路径
	 * @return json  $res  返回信息 
	 */
	public function deleteDir($path) {
		$path .= substr($path, -1) == '/' ? '' : '/';
	    foreach (glob($path.'*') as $v) {
	        if(is_dir($v)){
	            $this->deleteDir($v);
	        } 
	        if (is_file($v)) {
				unlink($v);
			}
	    }
	    if (rmdir($path)) {
	    	return json(array('code'=>200, 'msg'=>'删除成功'));
	    } else {
	    	return json(array('code'=>0, 'msg'=>'此文件夹已被删除'));
	    }
	}

	/**
	 * @Author:      Hzf
     * @DateTime:    2019-05-01
     * @Description: 文件复制  
	 * @param string $filename  文件名
	 * @param string $dirName   目录名
	 * @return json
	 */
	public function copyFile() {
		$fileName = Request::param('filename');
		$dirName  = Request::param('dirname');
		if (!file_exists($dirName)) { 
			if (!file_exists($dirName.'/'.$fileName)) {
				if (copy($fileName, $dirName)) {
					return json(array('code'=>200,'msg'=>'复制成功'));
				} else {
					return json(array('code'=>0,'msg'=>'复制失败'));
				}
			} else {
				return json(array('code'=>0,'msg'=>'存在同名文件'));
			}
		} else {
			return json(array('code'=>0,'msg'=>'目标目录不存在'));
		}
	}

	/**
	 * @Author:      Hzf
     * @DateTime:    2019-05-01
     * @Description: 文件剪切  
	 * @param string $filename  文件名
	 * @param string $dirName   目录名
	 * @return json
	 */
	public function cutFile() {
		$fileName= Request::param('filename');
		$dirName = Request::param('dirname');
		if (!file_exists($dirName)) { 
			if (!file_exists($dirName.'/'.$fileName)) {
				if (rename($fileName, $dirName)) {
					return json(array('code'=>200,'msg'=>'剪切成功'));
				} else {
					return json(array('code'=>0,'msg'=>'剪切失败'));
				}
			} else {
				return json(array('code'=>0,'msg'=>'存在同名文件'));
			}
		} else {
			return json(array('code'=>0,'msg'=>'目标目录不存在'));
		}
	}

	/**
	 * @Author:      Hzf
     * @DateTime:    2019-05-01
     * @Description: 文件重命名  
	 * @param string $path  路径
	 * @param string $name  新文件名
	 * @return json  $res  返回信息
	 */
	public function renameFunc() {
		$path = Request::param('path');
		$name = Request::param('name');
		if ($this->cheackFilename($name)) {
			// 检测当前目录是否包含同名文件
			$newPath = dirname($path).'/'.$name;
			if (!file_exists($newPath)) {
				if (rename($path, $newPath)) {
					return json(array('code'=>200,'msg'=>'重命名成功'));
				}
			} else {
				return json(array('code'=>0,'msg'=>'当前目录已存在同名文件，请重命名'));
			}
		} else {
			return json(array('code'=>0,'msg'=>'输入参数含有非法字符'));
		}
	}

	/**
	 * @Author:      Hzf
     * @DateTime:    2019-05-01
     * @Description: 下载文件  
	 * @param $path string 路径
	 */
	public function downloadFile() {
		$path = Request::param('filename');
		//检查文件是否存在    
		if (!file_exists($path)) {    
			header('HTTP/1.1 404 NOT FOUND');  
		} else {  
			$fileName = basename($path);  
			$fileSize = filesize($path);
			//告诉浏览器这是一个文件流格式的文件    
			Header ("Content-type: application/octet-stream"); 
			// //请求范围的度量单位  
			Header ("Accept-Ranges: bytes");  
			// //Content-Length是指定包含于请求或响应中数据的字节长度    
			Header ("Accept-Length: {$fileSize}");  
			//用来告诉浏览器，文件是可以当做附件被下载，下载后的文件名称为$file_name该变量的值。
			Header("Content-Disposition: attachment; filename={$fileName}");   
			Header("Content-Length: {$fileSize}");   
			readfile($path);
		}    

	}

	public function viewUploadFile() {
		return $this->fetch('file_upload');
	}

	/**
	 * @Author:      Hzf
     * @DateTime:    2019-05-01
     * @Description: 上传文件  
	 */
	public function uploadFile() {

        $file = Request::instance()->file('file');
        if (!empty($file)) {  
        	$savePath = '../public/uploads/account';
	        $info = $file->move($savePath); 
	        if ($info) {
	            $code = 0;
	            $data['url'] = $info->getSaveName();
	            $message = '文件上传成功！';
	        } else {
	        	$code = 2;
	        	$data = [];
	            $message = '文件上传失败！'.$file->getError();
	        }
        	return showMsg($code, $message, $data);
        } 
    }

}	 
