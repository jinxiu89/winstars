<?php

namespace app\en_us\controller;

use app\common\helper\Category as CategoryHelp;
use app\common\model\Category as CategoryModel;
use app\common\model\Language as LanguageModel;
use app\common\model\AdSpace;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;
use think\Request;

class  Category extends Base
{

    protected $navbar;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $result = (new AdSpace())->getDataByCode('About');
        if ($result['status'] == true) {
            $this->assign('banner', $result['data']);
        }

    }

    public function _initialize()
    {
        parent::_initialize();
        try {
            $data = collection((new CategoryModel())->getDataByCode($this->code))->toArray();
        } catch (DataNotFoundException $e) {
        } catch (ModelNotFoundException $e) {
        } catch (DbException $e) {
            $this->error($e->getMessage() . $this->dbError);
        }
        $tree = CategoryHelp::toLayer($data, $name = 'child', $parent_id = 0);
        $this->assign('tree', $tree);
    }

    /***
     * winstars 产品列表封面页
     * @return mixed
     */
    public function index()
    {
        return $this->fetch(
            $this->template . '/category/index.html');
    }

    /***
     * 根据分类来获取产品数据
     * 20190403
     * 邱锦
     * @param string $category
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function category($category = "")
    {
        if (empty($category)) {
            abort(404);
        }
        try{
            $categoryModel = new CategoryModel();
            $cate = $categoryModel->getAllCategory($this->code);
            $language_id = LanguageModel::getLanguageCodeOrID($this->code);
            $category_data = (new CategoryModel())->getCategoryByName($category, $language_id);
            $path = collection(CategoryHelp::getParents($cate, $category_data['id']))->toArray();
            $data = CategoryModel::getCategoryWithProduct($category_data['id']);//
            $page = $data->render();
            return $this->fetch($this->template . '/category/list.html', ["category" => $category_data, "result" => $data, "page" => $page, "path" => $path, "count" => count($data)]);
        }catch (\Exception $exception){
            abort(404);
        }
    }

}