<?php
namespace app\common\controller;

use think\facade\Request;  //导入请求静态代理
use think\facade\Session;  //导入SESSION静态代理
use think\Db;
use Redis;

class Image 
{
    public function __construct($path='') 
    {
        $this->path = $path;
    }

    // 上传图片
    public function upload() 
    {
        $file = Request::file('file');
        if (!empty($file)) {  
            $validate = [
                'size' => 1567800,
                'ext'  => 'jpg,png,gif'
            ];
            // 将文件移动到框架应用目录/public/upload/ 目录下
            $info = $file->validate($validate)->move('upload/'); 
            if($info) {
                $res = ['errno' => 0, 'data' =>'upload/'.$info->getSaveName()]; //文件的位置以及文件名
            }else{
                $res = ['errno' => 200, 'data' =>$file->getError()];
            } 
        } else {
            $res = ['errno' => 0, 'data' =>'请上传图片'];
        }
        return json($res);
    }

    // 添加水印
    public function addWater() 
    {
        // $path = Request::param('path');
    	// $path = $request->file('image');
        $image = \think\Image::open($this->path);
        if ($image) {    	
	    	$dir = dirname($this->path);
	    	$base= basename($this->path);
	    	$newDir = "{$dir}/water_/";
	    	$file= "water_{$base}";
	        // 给原图右下角添加透明度为50的水印
			$image->water($this->path, \think\Image::WATER_SOUTHEAST, 50)->save($newDir.$file);
        }
    }

    // 图片裁剪
    public function crop($info) 
    {
        $image = \think\Image::open($info['path']);
    	if ($image) { 
	    	$dir  = dirname($info['path']);
	    	$base = basename($info['path']);
	    	$newDir = "{$dir}/crop/";
	    	$file = "crop_{$base}";
	        // 将图片裁剪为300x300并保存为crop.png
			$image->crop($info['width'], $info['height'])->save($newDir.$file);
		}
    }
    
    // 删除无效图片
    public function delete($path)
    {
        if (file_exists($this->path)) {         
            if (unlink($this->path)) {
                $res = ['code' => 200, 'msg'=> '删除成功'];
            } else {
                $res = ['code' => 0, 'msg'=> '删除失败'];
            }
        } else {
            $res = ['code' => 0, 'msg'=> '目录不存在']
        }
        return json($res);
    }
}
