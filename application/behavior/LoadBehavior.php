<?php
 
namespace app\behavior;
 
use think\facade\Config;
use think\Facade;
use think\Loader;
 
class LoadBehavior
{
    public function run()
    {
        // 门面类facade注册
        Facade::bind(Config::get('facade.facade'));
        // 别名注册
        Loader::addClassAlias(Config::get('facade.alias'));
    }

}