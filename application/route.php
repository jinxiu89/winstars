<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;


/**
 * 用户后台路由
 * 如果在控制器是用url生成路由，用后面的
 */
Route::rule([
    //登录页面
    'customer/login' => 'customer/Login/index',
    //用户后台首页
    'customer/index' => 'customer/Index/index',
    //用户注销页面
    'customer/logout'=>'customer/logout/index',
    //用户注册页面
    'customer/register' => 'customer/Register/index'
]);

/***
 * 网站默认是英文模块
 * 英文路由,第一个参数是路由地址,第二个参数是模块/控制器/方法。
 * 规则是以数组形式
 */
//首页URL
Route::rule([
    //英文网站首页
    '/'=>'en_us/Index/index',
    'en_us/'=>'en_us/Index/index',
    'en_us/index'=>'en_us/Index/index',
    //产品详情页
    'en_us/product/:product'=>'en_us/Product/details',
    //产品分类列表页
    'en_us/category/:category'=>'en_us/Category/index',
    //子分类产品列表页
    'en_us/product/:category'=>'en_us/Category/index',
    //驱动列表页面
    'en_us/drivers/'=>'en_us/Drivers/index',
    // 驱动详情页
    'en_us/drivers/details/:drivers'=>'en_us/Drivers/details',
    //子分类驱动列表页
    'en_us/drivers/:category'=>'en_us/Drivers/category',
    //视频列表页面
    'en_us/video/:category'=>'en_us/Video/index',
    //视频详情页
    'en_us/video/detail/:video'=>'en_us/Video/detail',
    //    视频子列表页
//    'en_us/video/:category'=>'en_us/Video/category',
    //关于我们
    'en_us/about/:about'=>'en_us/About/index',
    //固件下载
    'en_us/ticket/'=>'en_us/Ticket/add',
    'en_us/ticket/:sn'=>'en_us/Ticket/detail',
    //产品搜索路由
    'en_us/search/product'=>'en_us/Search/product',
    //驱动搜索
    'en_us/search/drivers'=>'en_us/Search/drivers',
    //事件列表页面
    'en_us/article/:url_title' =>'en_us/Article/index',
    //事件详情页
    'en_us/article/details/:article'=>'en_us/Article/details',
    //文档列表页
    'en_us/document/:url_title'=>'en_us/Document/index',
    //文档详情页
    'en_us/document/details/:document' => 'en_us/Document/details',
    //    //FAQ列表页面
    'en_us/faq/'  => 'en_us/Faq/index',
    //分类faq下的faq集锦
    'en_us/faq/:url_title' => 'en_us/Faq/category',
    //faq详情页
    'en_us/faq/details/:url_title'=>'en_us/Faq/details'
],'','GET');


/**
 * 中文网站路由规则，写法和英文网站一样，
 */
Route::rule([
    //中文首页
    'zh_cn/index'=>'zh_cn/Index/index',
    //产品详情页
    'zh_cn/product/:product'=>'zh_cn/Product/details',
     //产品分类列表页
    'zh_cn/category/:category'=>'zh_cn/Category/index',
    //子分类产品列表页
    'zh_cn/product/:category'=>'zh_cn/Category/index',
    //驱动列表页面
    'zh_cn/drivers/'=>'zh_cn/Drivers/index',
    // 驱动详情页
    'zh_cn/drivers/details/:drivers'=>'zh_cn/Drivers/details',
    //子分类驱动列表页
    'zh_cn/drivers/:category'=>'zh_cn/Drivers/category',
    //视频列表页面
    'zh_cn/video/:category'=>'zh_cn/Video/index',
    //视频详情页
    'zh_cn/video/detail/:video'=>'zh_cn/Video/detail',
//    //    视频子列表页
//    'zh_cn/video/:category'=>'zh_cn/Video/category',
    //关于我们
    'zh_cn/about/:about'=>'zh_cn/About/index',
    //固件下载
    'zh_cn/ticket/'=>'zh_cn/Ticket/add',
    'zh_cn/ticket/:sn'=>'zh_cn/Ticket/detail',
    //产品搜索路由
    'zh_cn/search/product'=>'zh_cn/Search/product',
    //驱动搜索
    'zh_cn/search/drivers'=>'zh_cn/Search/drivers',
    //事件列表页面
    'zh_cn/article/:url_title' =>'zh_cn/Article/index',
    //事件详情页
    'zh_cn/article/details/:article'=>'zh_cn/Article/details',
    //文档列表页
    'zh_cn/document/:url_title'=>'zh_cn/Document/index',
    //文档详情页
    'zh_cn/document/details/:document' => 'zh_cn/Document/details',
//    //FAQ列表页面
    'zh_cn/faq/'  => 'zh_cn/Faq/index',
    //分类faq下的faq集锦
    'zh_cn/faq/:url_title' => 'zh_cn/Faq/category',
    //faq详情页
    'zh_cn/faq/details/:url_title'=>'zh_cn/Faq/details'
],'','GET');
////winstars 数据采集 用完即焚
//Route::post('api/winstars','api/winstars/add');

