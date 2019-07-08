<?php

namespace app\util;

use think\facade\Session;

class File
{	
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
	    foreach (glob($path.'*') as $v) 
	    {
	        $arr[] = $v; 
	        if (is_dir($v)) 
	        {
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
	public function readDir($path='../public/uploads/')
	{
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
	public function getDirSzie($path) 
	{
		$GLOBALS['count'] = 0;
		$path .= substr($path, -1) == '/' ? '' : '/';
	    foreach (glob($path.'*') as $v) 
	    {
	    	if (is_file($v)) 
	    	{
				$GLOBALS['count'] += filesize($v);
			}
	        if (is_dir($v))
	        {
	            $this->getDirSzie($v);
	        } 
	    }
	    return $GLOBALS['count'];
	}

	/**
	 * @Author:      Hzf
     * @DateTime:    2019-05-01
     * @Description: 读取文件内容
	 * @param string $path  路径
	 * @return json  $res   文件基本信息
	 */
	public function readFile($path) {
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
	 * @param  string $str  文字
	 * @return string 
	 */
	public function convertUTF8($str) 
	{
		if (!empty($str))
		{
			$fileType = mb_detect_encoding($str , mb_list_encodings(), true);
			if ( strtolower($fileType) != 'utf-8')
			{
				$str = mb_convert_encoding($str, 'utf-8', $fileType);  
			}
		}
		return $str;
	}

	public function delete($path)
	{
		if (file_exists($path)) {
			if (is_dir($path)) {
				return $this->deleteDir($path);
			} else {
				return $this->deleteFile($path);
			}
		} else {
			return json(array('code'=>0,'msg'=>'请检查路径'));
		}
	}

	/**
	 * @Author:      Hzf
     * @DateTime:    2019-05-01
     * @Description: 删除文件  
	 * @param string $path  路径
	 * @return json  $res  返回信息
	 */
	public function deleteFile($path) 
	{

		if (unlink($path))
		{
			return json(array('code'=>200,'msg'=>'删除成功'));
		}
		return json(array('code'=>0,'msg'=>'删除失败'));
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
	    foreach (glob($path.'*') as $v) 
	    {
	        if (is_dir($v))
	        {
	            $this->deleteDir($v);
	        } 
	        if (is_file($v)) 
	        {
				unlink($v);
			}
	    }
	    if (rmdir($path)) 
	    {
	    	return json(array('code'=>200, 'msg'=>'删除成功'));
	    } 
	    return json(array('code'=>0, 'msg'=>'删除失败'));
	}

	/**
	 * @Author:      Hzf
     * @DateTime:    2019-05-01
     * @Description: 文件复制  
	 * @param string $filename  文件名
	 * @param string $dirName   目录名
	 * @return json
	 */
	public function copyFile($dirName, $fileName) 
	{
		if (!file_exists($dirName)) 
		{ 
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
	public function cutFile($dirName, $fileName) 
	{
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
	public function renameFunc($path, $fileName) 
	{
		if ($this->cheackFilename($fileName)) {
			// 检测当前目录是否包含同名文件
			$newPath = dirname($path).'/'.$fileName;
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
     * @Description: 上传文件  
	 */
	public function upload($file, $savePath) 
	{
        if (!empty($file)) 
        {  
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

	/**
	 * @Author:      Hzf
     * @DateTime:    2019-05-01
     * @Description: 下载文件  
	 */
	public function download($path) 
	{
		//检查文件是否存在    
		if (!file_exists($path)) {    
			header('HTTP/1.1 404 NOT FOUND');  
		} else {  
			$fileName = basename($path);  
			$fileSize = filesize($path);
			// 告诉浏览器这是一个文件流格式的文件    
			Header ("Content-type: application/octet-stream"); 
			// 请求范围的度量单位  
			Header ("Accept-Ranges: bytes");  
			// Content-Length是指定包含于请求或响应中数据的字节长度    
			Header ("Accept-Length: {$fileSize}");  
			// 用来告诉浏览器，文件是可以当做附件被下载，下载后的文件名称为$file_name该变量的值。
			Header("Content-Disposition: attachment; filename={$fileName}");   
			Header("Content-Length: {$fileSize}");   
			readfile($path);
		}    
	}
}