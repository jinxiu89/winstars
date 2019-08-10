<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/7/8
 * Time: 13:33
 */

namespace app\wavlink\controller;

use app\common\model\Category as CategoryModel;
use app\wavlink\validate\DriverDownload;
use think\exception\DbException;
use app\wavlink\validate\Driver as DriverValidate;
use app\wavlink\validate\DriverDownload as DriverDownloadValidate;
use app\common\model\DriverDownload as DriverDownloadModel;
use app\common\model\Driver as DriverModel;
use think\Request;

class Driver extends BaseAdmin
{
    protected $beforeActionList = [
        'getCategoryLevel',
    ];

    public function _initialize()
    {
        parent::_initialize();
        $this->language_id = $this->MustBePositiveInteger(input('get.language_id'));
    }

    public function getCategoryLevel()
    {
        $category = (new CategoryModel())->getCategoryLevel($this->language_id);
        $this->assign('category', $category);
    }

    public function getProductList()
    {
        $category = input('post.');
        $result = (new CategoryModel())->getDataByCategoryId($category['category_id']);
        return toJSON(['status' => true, 'message' => 'ok', 'data' => $result[0]['products']]);
    }

    public function index()
    {
        $result = (new DriverModel())->getDataByLanguageId($this->language_id);
        if ($result['status'] == 1) {
            $this->assign('data', $result['data']['data']);
            $this->assign('count', $result['data']['count']);
        }
        return $this->fetch();
    }

    public function add()
    {
        if (\request()->isGet()) {
            return $this->fetch();
        }
        if (\request()->isPost()) {
            $data = input('post.');
            $data['url_title'] = md5(uniqid());
            $validate = new DriverValidate();
            if ($validate->scene('add')->check($data)) {
                $result = (new DriverModel())->saveData($data);
                if ($result) {
                    return show($result['status'], $result['message'], '', '', Request::instance()->header('referer'), $result['message']);
                }
            } else {
                return show(false, 'failed', '', '', '', $validate->getError());
            }
        }
    }

    public function edit($url_title)
    {
        if (\request()->isGet()) {
            $result = (new DriverModel())->getDataByUrlTitle($url_title);
            $products = (new CategoryModel())->getDataByCategoryId($result['category_id']);
            if(!empty($result->products)){
                $product_ids = (new DriverModel())->getProductList($result->products);
                $this->assign('products_ids', $product_ids);
                $this->assign('products', $products[0]['products']);
            }
            if ($result) {
                $this->assign('result', $result);
            }
            return $this->fetch();
        }
        if (\request()->isPost()) {
            $data = input('post.');
            $validate = new DriverValidate();
            if ($validate->scene('edit')->check($data)) {
                $result = (new DriverModel())->saveData($data);
                if ($result) {
                    return show($result['status'], $result['message'], '', '', Request::instance()->header('referer'), $result['message']);
                }
            } else {
                return show(false, 'failed', '', '', '', $validate->getError());
            }
        }
    }

    public function add_download($driver_id)
    {
        if (\request()->isGet()) {
            $this->assign('language_id', input('get.language_id'));
            $this->assign('driver_id', $driver_id);
            return $this->fetch();
        }
        if (\request()->isPost()) {
            $data = input('post.');
            $validate = new DriverDownloadValidate();
            if ($validate->scene('add')->check($data)) {
                $result = (new DriverModel())->saveDownload($data);
                if ($result) {
                    return show($result['status'], $result['message'], '', '', Request::instance()->header('referer'), $result['message']);
                }
                return show(false, 'failed', '', '', '', '未知错误！');
            } else {
                return show(false, 'failed', '', '', '', $validate->getError());
            }
        }
    }

    public function edit_download($id, $language_id)
    {
        if (\request()->isGet()) {
            $data = (new DriverDownloadModel())->get($id);
            $this->assign('language_id', $language_id);
            $this->assign('data', $data);
            return $this->fetch();
        }
        if (\request()->isPost()) {
            $data = input('post.');
            $validate = new DriverDownloadValidate();
            if ($validate->scene('edit')->check($data)) {
                $result = (new DriverModel())->saveDownload($data);
                if ($result) {
                    return show($result['status'], $result['message'], '', '', Request::instance()->header('referer'), $result['message']);
                }
                return show(false, 'failed', '', '', '', '未知错误！');
            } else {
                return show(false, 'failed', '', '', '', $validate->getError());
            }
        }
    }

    public function sort(){
        if(\request()->isAjax()){
            $data=input('post.');
            $validate=new DriverValidate();
            if($validate->scene('sort')->check($data)){
                $result=(new DriverModel())->sort($data);
                if($result){
                    return show($result['status'],$result['message'],'','','',$result['message']);
                }else{
                    return show($result['status'],'未知原因的排序失败','','','','未知原因的排序失败');
                }
            }else{
                return show(false, 'failed', '', '', '', $validate->getError());
            }
        }
    }
}