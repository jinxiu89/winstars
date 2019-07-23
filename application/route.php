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

//首页URL
Route::rule([
    //英文网站首页
    '/' => 'en_us/Index/index',
    'en_us/' => 'en_us/Index/index',
    'en_us/index' => 'en_us/Index/index',
    //产品详情页
    'en_us/product/:product' => 'en_us/Product/details',
    //产品分类列表页
    'en_us/category' => 'en_us/Category/index',
    'en_us/category/:category' => 'en_us/Category/category',
    //子分类产品列表页
    'en_us/product/:category' => 'en_us/Category/index',
    //支持列表页
    'en_us/support/' => 'en_us/Support/index',
    //驱动列表页面
    'en_us/support/driver/' => 'en_us/Driver/index',
    // 驱动详情页
    'en_us/support/driver/details/:url_title' => 'en_us/Driver/details',
    //子分类驱动列表页
    'en_us/support/driver/:category' => 'en_us/Driver/category',
    //视频列表页面
    'en_us/video/:category' => 'en_us/Video/index',
    //视频详情页
    'en_us/video/detail/:video' => 'en_us/Video/detail',
    //    视频子列表页
//    'en_us/video/:category'=>'en_us/Video/category',
    //关于我们
    'en_us/about' => 'en_us/About/index',
    'en_us/about/:about' => 'en_us/About/details',
    //固件下载
    'en_us/ticket/' => 'en_us/Ticket/add',
    'en_us/ticket/:sn' => 'en_us/Ticket/detail',
    //产品搜索路由
    'en_us/search/product' => 'en_us/Search/product',
    //驱动搜索
    'en_us/search/drivers' => 'en_us/Search/drivers',
    //事件列表页面
    'en_us/article/:url_title' => 'en_us/Article/index',
    //事件详情页
    'en_us/article/details/:article' => 'en_us/Article/details',
    //文档列表页
    'en_us/document/:url_title' => 'en_us/Document/index',
    //文档详情页
    'en_us/document/details/:document' => 'en_us/Document/details',
    //    //FAQ列表页面
    'en_us/faq/' => 'en_us/Faq/index',
    //分类faq下的faq集锦
    'en_us/faq/:url_title' => 'en_us/Faq/category',
    //faq详情页
    'en_us/faq/details/:url_title' => 'en_us/Faq/details',
    'en_us/error/:code'=>'en_us/Base/customerError'
], '', 'GET');
