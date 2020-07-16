<?php
/**
 * Created by PhpStorm.
 * User: guo
 * Date: 2017/8/23
 * Time: 10:37
 */

namespace app\wavlink\controller;

use app\common\model\Faq as FaqModel;
use app\common\model\Category as CategoryModel;
use app\wavlink\validate\Faq as FaqValidate;
use think\Request;
use think\exception\DbException;

Class Faq extends BaseAdmin
{
    public function _initialize()
    {
        parent::_initialize();
        $this->language_id = $this->current_language['id'];
        try {
            $category = (new CategoryModel())->getCategoryLevel($this->language_id);
        } catch (DbException $e) {
            $this->error($e->getMessage());
        }
        $this->assign('category', $category);
    }


    public function index()
    {
        $result = (new FaqModel())->getDataByLanguageId($this->language_id);
        return $this->fetch('', [
            'faq' => $result['data']['data'],
            'counts' => $result['data']['count'],
//            'language_id' => $language_id,
        ]);
    }

    public function faq_recycle()
    {
        $faq = FaqModel::getDataByStatus(-1, $this->language_id);
        return $this->fetch('', [
            'faq' => $faq['data'],
            'counts' => $faq['count']
        ]);
    }

    public function add()
    {
        if (request()->isGet()) {
//            $language_id = $this->MustBePositiveInteger(input('get.language_id'));
            //获取faq的服务分类
            return $this->fetch('', [
                'language_id' => $this->language_id
            ]);
        }
        if (request()->isAjax()) {
            $data = input('post.');
            $data['url_title'] = md5(uniqid());
            $validate = new FaqValidate();
            if ($validate->scene('add')->check($data)) {
                try {
                    $result = (new FaqModel())->saveData($data);
                } catch (PDOException $e) {
                    return show(false, $e->getMessage(), '', '', Request::instance()->header('referer'));
                } catch (\DbException $e) {
                    return show(false, $e->getMessage(), '', '', Request::instance()->header('referer'));
                } catch (\Exception $exception) {
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

    /**
     * 编辑操作
     * @param $url_title
     * @return array|mixed
     */
    public function edit($url_title)
    {
        if(\request()->isGet()){
            $result = (new FaqModel())->getDataByUrlTitle($url_title);
            $products = (new CategoryModel())->getDataByCategoryId($result['category_id']);
            if(!empty($result->products)){
                $product_ids = (new FaqModel())->getProductList($result->products);
                $this->assign('products_ids', $product_ids);
                $this->assign('products', $products[0]['products']);
            }
            if ($result) {
                $this->assign('result', $result);
            }
            return $this->fetch();
        }
        if(\request()->isAjax()){
            $data=input('post.');
            $validate=new FaqValidate();
            if($validate->scene('edit')->check($data)){
                try {
                    $result = (new FaqModel())->saveData($data);
                } catch (PDOException $e) {
                    return show(false, $e->getMessage(), '', '', Request::instance()->header('referer'));
                } catch (\DbException $e) {
                    return show(false, $e->getMessage(), '', '', Request::instance()->header('referer'));
                } catch (\Exception $exception) {
                    return show(false, $e->getMessage(), '', '', Request::instance()->header('referer'));
                }
                if ($result) {
                    return show($result['status'], $result['message'], '', '', Request::instance()->header('referer'), $result['message']);
                }
            }else{
                return show(false, 'failed', '', '', '', $validate->getError());
            }
        }
    }

    /**
     * 保存操作
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function save()
    {
        if (request()->isAjax()) {
            $data = input('post.');
            if (!empty($data['id'])) {
                return $this->update($data);
            }
            $res = (new FaqModel())->add($data);
            if ($res) {
                return show(1, '', '', '', '', '添加成功');
            } else {
                return show(1, '', '', '', '', '添加失败');
            }
        }
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