<?php

namespace app\backend\controller;

use think\Controller;

/** 
 * 用户画像控制器
 * 1.自定义标签规则：统计行为次数，打标签
 * 2.自定义场景：根据不同场景
 * 3.根据标签实现用户分类
 * 4.根据分类执行相应操作（例.推送信息）
 */
class Personas extends Controller
{
    public function index()
    {
        return $this->fetch();
    }
}