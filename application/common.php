<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**
 * @Author:      Hzf
 * @DateTime:    2019-04-23
 * @Description: 输出变量
 */
function show($var, $isDump = false) {
	$func = $isDump === false ? 'print_r' : 'var_dump';
	echo '<hr>';
	if (empty($var)) 
	{
		echo 'null';
	} else {		
		if (is_array($var)||is_object($var)) 
		{
			echo "<pre>";
			$func($var);
		} else {
			echo $var;
		}
	}
}

/**
 * @Author:      Hzf
 * @DateTime:    2019-06-01
 * @Description: 生成一个32位不重复的随机数
 */
function getToken()
{
	return md5(uniqid(mt_rand(), true));
}

/**
 * @Author:      Hzf
 * @DateTime:    2019-06-01
 * @Description: 过滤空格
 */
function filterBlank($str) 
{
	return preg_replace("/\s/", "", $str);  
}

//--------------------------- 转换数据格式 ---------------------------//

/**
 * @Author:      Hzf
 * @DateTime:    2019-05-22
 * @Description: 返回数组【状态码、数据提示以及数据】
 * @param string  $code  状态码
 * @param array   $data  数据
 * @return array  $result   
 */
function ajaxReturn($code, $data=[]) 
{
	$result = ['code'=>$code, 'message'=> getMessage($code)];
	$result = (!empty($data)) ? $result['data'] = $data : $result;
	return json($result);
}

/**
 * @Author:      Hzf
 * @DateTime:    2019-05-22
 * @Description: 获取状态码信息
 * @param string  $code  响应码
 * @return string 提示
 */
function getMessage($code) 
{
	$info = config('message.info');
	return (array_key_exists($code, $info) ? $info[$code] : '操作失败');
}

/**
 * @Author:      Hzf
 * @DateTime:    2019-04-26
 * @Description: 返回json格式的数据以及提示
 * @param string  $code     状态码
 * @param string  $message  需要过滤的字符串
 * @param array   $data 	数据
 * @return json
 */
function showMsg($code=0, $message='', $data=[]) 
{
    $result = ['code' => $code, 'message' => $message];
    $result = (!empty($data)) ? $result['data'] = $data : $result;
    return json($result);
}

/**
 * @Author:      Hzf
 * @DateTime:    2019-05-05
 * @Description: 格式化LAYUI表格信息
 * @param string $code   状态码
 * @param string $msg    状态信息
 * @param int    $count  数据条数
 * @param array  $data   具体数据
 * @return json  $res    返回信息
 */
function showLayuiTableMsg($count=0, $data=[], $code=0, $msg='数据为空') 
{
	$res = [
		'code' => $code,
		'msg'  => $msg,
		'count'=> $count,
		'data' => $data
	];
	return json($res);
}

/**
 * @Author:       Hzf
 * @DateTime:     2019-05-05
 * @Description:  文字两边添加单引号
 * @param  string  $str 字符串
 * @return string  $str 格式化字符串
 */
function change_to_quotes($str) 
{
	return sprintf("'%s'", $str);
}


/**
 * @Author:       Hzf
 * @DateTime:     2019-05-05
 * @Description:  截取一部分文字并拼接...
 * @param string  $str 字符串
 * @param int  $size 长度
 */
function convertText($str, $length = 40) 
{
    if (mb_strlen($str) > $length) {
        $str  = mb_substr($str, 0, $length);
        $str .='...';
    }
    return $str;
}

/**
 * @Author:       Hzf
 * @DateTime:     2019-05-05
 * @Description:  转换时间格式
 * @param  int     $time 时间戳
 * @return string  格式化时间
 */
function convertTime($time) 
{
	$time = time() - $time; 
	$arr = [
	    YEAR_SECOND  => '年', 
	    MONTH_SECOND => '个月',
	    WEEK_SECOND  => '星期',
	    DAY_SECOND   => '天', 
	    HOUR_SECOND  => '小时', 
	    MINUTE_SECOND=> '分钟',
	    SECOND       => '秒',
	]; 
	foreach ($arr as $k => $v) 
	{ 
	    if (0 != $u = floor($time/(int)$k)) 
	    { 
	        $str = "{$u}{$v}前"; 
	        break;
	    } 
	} 
	return $str;
}

/**
 * @Author:       Hzf
 * @DateTime:     2019-05-05
 * @Description:  转换数据大小格式
 * @param  int  $size 数据大小
 * @return string     格式化大小
 */
function convertSize($size) 
{
	$i = 0;
	$arr = ['B', 'KB', 'MB', 'GB', 'TB', 'EB'];
	while($size >= 1024)
	{
		$size /= 1024;
		$i++;
	}
	return sprintf('%.2f',round($size,2)).$arr[$i];
}

//--------------------------- 检查格式是否符合要求 ---------------------------//

/**
 * @Author:      Hzf
 * @DateTime:    2019-04-25
 * @Description: 检测字符串是否为日期格式
 * @param string  $str  需要检测的字符串
 * @return boolean $res （true：合法；false：非法）
 */
function isDate($str) 
{
	if (strtotime($str) === false) 
	{
		return false;
	} 
	return true;
}

/**
 * @Author:      Hzf
 * @DateTime:    2019-04-25
 * @Description: 检测字符串是否为时间戳格式
 * @param string  $str  需要检测的字符串
 * @return boolean $res （true：合法；false：非法）
 */
function isTimestamp($str) 
{
	if (ctype_digit($str) && $str <= 2147483647) 
	{
		return true;
	}
	return false;
}

/**
 * @Author:      Hzf
 * @DateTime:    2019-04-25
 * @Description: 检测字符串是否合法
 * @param string  $str  需要检测的字符串
 * @return boolean $res （true：合法；false：非法）
 */
function isIllegal($str) 
{
	$illegal = ['%0d%0a',"'",'<','>','script','document','eval','atestu','select','insert','update','alter','delete','drop','truncate'];
	foreach ($illegal as $v) 
	{
		if (stripos(strtolower($str), $v) !== false) 
		{
			return false;
		} 
	}
	return true;
}

/**
 * @Author:      Hzf
 * @DateTime:    2019-04-25
 * @Description: 检测提交的值是否含有SQL注入的字符
 * @param string  $str  需要检测的字符串
 * @return boolean $res （true：没有；false：有）
 */
function inject_check($str) 
{
	$pattern = '/^select|insert|and|or|create|update|delete|alter|count|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile/i';
	return preg_match($pattern, $str);
}

/**
 * @Author:      Hzf
 * @DateTime:    2019-04-25
 * @Description: 检测提交的值是否含有SQL注入的字符。
 *  PS：若提交数据存入数据库，记得转义输出（stripslashes、htmlspecialchars_decode）
 * @param string  $str  需要检测的字符串
 * @return string $str  过滤后的字符串
 */	
function str_check($str) {
	if (!get_magic_quotes_gpc()) 
	{ // 判断magic_quotes_gpc是否打开
		$str = addslashes($str);
	}	
	$search = ["_", "%"];
	$replace= ["\_", "\%"];
	$str = str_replace($search, $replace, $str);
	$str = nl2br($str); //回车替换
	$str = htmlspecialchars($str); // html标记转换
	return $str;
}

/**
 * @Author:      Hzf
 * @DateTime:    2019-04-25
 * @Description: 遍历方式转义输入
 *  PS：若提交数据存入数据库，记得转义输出（stripslashes、htmlspecialchars_decode）
 * @param string  $str  需要检测的字符串
 * @return string $str  过滤后的字符串
 */	
function daddslashes($str, $force = 0, $strip = false) 
{
	if (!get_magic_quotes_gpc() || $force) 
	{
		if (is_array($str)) 
		{
			foreach ($str as $k => $v) 
			{
				$str[$k] = daddslashes($v, $force);
			}
		} else {
			$str = addslashes($strip ? stripslashes($str) : $str);
		}
	} 
	return $str;
}

function dstripslashes($str) {
	if (is_array($str)) 
	{
		foreach ($str as $k => $v) 
		{
			$str[$k] = dstripslashes($v, $force);
		}
	} else {
		$str = stripslashes($str);
	}
	return $str;
}

//--------------------------- CURL ---------------------------//

/**
 * @Author:      Hzf
 * @DateTime:    2019-04-24
 * @Description: CURL模拟登陆
 * @param string $url 登陆地址
 * @param string $cookieFilePath cookie保存位置
 * @param string $postData 登陆所需信息（ACCOUNT、PASSWORD等）
 */
function curlLogin($url, $cookieFilePath, $postData) 
{
		
	// 1.创建CURL会话资源，成功则返回一个句柄
	$ch = curl_init();
	// 2.设置
	// 1）URL
	curl_setopt($ch, CURLOPT_URL, $url);
	// 2）附带返回header信息为空
	curl_setopt($ch, CURLOPT_HEADER, 0);
	// 3）post请求
	curl_setopt($ch, CURLOPT_POST, 1);		
	// 4）post数据
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
	// 5）cookie文件位置，用于保存
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFilePath);
	// 6）是否将响应结果存入变量（0：直接输出；1：存入变量）
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	// 3.执行请求，将响应结果存入$res变量中
	$res = curl_exec($ch);
	// 4.关闭CURL会话资源
	curl_close($ch);	
	return $res;
}

/**
 * @Author:      Hzf
 * @DateTime:    2019-04-24
 * @Description: CURL抓取所需指定页面内容
 * @param string  $url 登陆地址
 * @param string  $cookieFile cookie文件
 * @param boolean $isPost 是否为POST请求（默认为fasle）
 * @param string  $postData POST请求参数
 */
function curlRequest($url , $cookieFile, $isPost = false, $postData = []) 
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);	
	if ($isPost && !empty($postData)) 
	{
		curl_setopt($ch, CURLOPT_POST, 1);	
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));	
	}
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$res = curl_exec($ch);
	if (curl_errno($ch)) 
	{
		return curl_error($ch);
	}
	curl_close($ch);
	return $res;
}

/**
 * @Author:      Hzf
 * @DateTime:    2019-04-24
 * @Description: 下载文件
 */
function curlDownloadFile($url) 
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // 最长执行时间
	curl_setopt($ch, CURLOPT_TIMEOUT, 10); // 最长等待时间
	$file_content = curl_exec($ch);
	if (curl_errno($ch)) 
	{
		return curl_error($ch);
	}
	return $file_content;
}