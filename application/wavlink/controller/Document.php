<?php
/**
 * Created by PhpStorm.
 * User: guo
 * Date: 2017/8/23
 * Time: 10:37
 */

namespace app\wavlink\controller;

use app\lib\exception\ParameterException;
use app\wavlink\validate\DocumentDownload as DocumentDownloadValidate;
use app\wavlink\validate\UrlTitleMustBeOnly;
use think\exception\DbException;
use think\exception\PDOException;
use think\Request;
use app\common\model\Document as DocumentModel;
use app\common\model\DocumentDownload as DocumentDownloadModel;
use app\wavlink\validate\Document as DocumentValidate;
use app\common\model\Category as CategoryModel;

Class Document extends BaseAdmin
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
        try {
            $category = (new CategoryModel())->getCategoryLevel($this->language_id);
        } catch (DbException $e) {
            return "hello." . $e->getMessage();
        }
        $this->assign('category', $category);
    }

    public function index()
    {
        $language_id = $this->MustBePositiveInteger(input('get.language_id'));
        $document = (new DocumentModel())->getDataByLanguageId($language_id);
        return $this->fetch('', [
            'document' => $document['data']['data'],
            'counts' => $document['data']['count'],
        ]);
    }

    //添加文档页面开发
    public function add()
    {
        if (request()->isGet()) {
            return $this->fetch();
        }
        if (request()->isAjax()) {
            $data = input('post.');
            $data['url_title'] = md5(uniqid());
            $validate = new DocumentValidate();
            if ($validate->scene('add')->check($data)) {
                try {
                    $result = (new DocumentModel())->saveData($data);
                } catch (PDOException $e) {
                    return show(false, $e->getMessage(), '', '', Request::instance()->header('referer'));
                } catch (DbException $e) {
                    return show(false, $e->getMessage(), '', '', Request::instance()->header('referer'));
                }
                if ($result) {
                    return show($result['status'], $result['message'], '', '', Request::instance()->header('referer'), $result['message']);
                }
            } else {
                return show(false, 'failed', '', '', '', $validate->getError());
            }
        }
    }

    //文档新增保存操作
    public function save()
    {
        if (request()->isAjax()) {
            $data = Request::instance()->post();
            try {
                (new DocumentValidate())->goCheck();
            } catch (ParameterException $e) {
            }
            try {
                (new UrlTitleMustBeOnly())->goCheck();
            } catch (ParameterException $e) {
            }
            if (!empty($data['id'])) {
                return $this->update($data);
            }
            $res = (new DocumentModel())->add($data);
            if ($res) {
                return show(1, '', '', '', '', '添加成功');
            } else {
                return show(1, '', '', '', '', '添加失败');
            }
        }

    }

    //文档编辑页面
    public function edit($url_title)
    {
        if (\request()->isGet()) {
            $result = (new DocumentModel())->getDataByUrlTitle($url_title);
            if ($result['status'] == false) {
                $this->error($result['message']);
            }
            $products = (new CategoryModel())->getDataByCategoryId($result['data']['category_id']);
            if (!empty($result['data']->products)) {
                $product_ids = (new DocumentModel())->getProductList($result['data']->products);
                $this->assign('products_ids', $product_ids);
                $this->assign('products', $products[0]['products']);
            }
            if ($result['status'] == true) {
                $this->assign('result', $result['data']);
            }
            return $this->fetch('', []);
        }
        if (\request()->isAjax()) {
            $data = input('post.');
            $validate = new DocumentValidate();
            if ($validate->scene('edit')->check($data)) {
                try {
                    $result = (new DocumentModel())->saveData($data);
                } catch (PDOException $e) {
                    return show(false, $e->getMessage(), '', '', Request::instance()->header('referer'));
                } catch (DbException $e) {
                    return show(false, $e->getMessage(), '', '', Request::instance()->header('referer'));
                }
                if ($result) {
                    return show($result['status'], $result['message'], '', '', Request::instance()->header('referer'), $result['message']);
                }
            } else {
                return show(false, 'failed', '', '', '', $validate->getError());
            }
        }
    }

    /***
     * @param $document_id
     * @return mixed|void
     * 添加 多对多 下载关系
     */
    public function add_download($document_id)
    {
        if (\request()->isGet()) {
            $this->assign('language_id', input('get.language_id'));
            $this->assign('document_id', $document_id);
            return $this->fetch();
        }
        if (\request()->isPost()) {
            $data = input('post.');
            $validate = new DocumentDownloadValidate();
            if ($validate->scene('add')->check($data)) {
                $result = (new DocumentModel())->saveDownload($data);
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
            try {
                $data = (new DocumentDownloadModel())->get($id);
            } catch (DbException $e) {
                $this->error($e->getMessage());
            }
            $this->assign('language_id', $language_id);
            $this->assign('data', $data);
            return $this->fetch();
        }
        if (\request()->isPost()) {
            $data = input('post.');
            $validate = new DocumentDownloadValidate();
            if ($validate->scene('edit')->check($data)) {
                $result = (new DocumentModel())->saveDownload($data);
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
            $validate=new DocumentValidate();
            if($validate->scene('sort')->check($data)){
                $result=(new DocumentModel())->sort($data);
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

    //回收站数据列表，状态值为-1的
    public function doc_recycle()
    {
        $document = DocumentModel::getDataByStatus(-1);
        return $this->fetch('', [
            'document' => $document['data'],
            'counts' => $document['count'],
        ]);
    }

    /**
     * 排序功能开发
     * 默认 必须数据 id,type,language_id
     **type == 1 时 置底
     * type == 4 时 置顶
     * type == 3 时 上移
     * type == 2 时 下移
     */
    public function listorder()
    {
        if (request()->isAjax()) {
            $data = input('post.'); //id ,type ,language_id
            self::order(array_filter($data));
        } else {
            return show(0, '置顶失败，未知错误', 'error', 'error', '', '');
        }
    }
}