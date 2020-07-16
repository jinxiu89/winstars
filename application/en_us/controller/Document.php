<?php
/**
 * Created by PhpStorm.
 * User: guo
 * Date: 2018/4/24
 * Time: 14:08
 */
namespace app\en_us\controller;

use app\common\model\Category as CategoryModel;
use app\common\model\Document as DocumentModel;
use app\common\model\Language as LanguageModel;
use app\common\helper\Category as CategoryHelp;
use think\Request;
use app\common\model\AdSpace;

class Document extends Base
{

//    protected $beforeActionList = [
//        'cate' => ['only', 'index,details'],
//    ];

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $result = (new AdSpace())->getDataByCode('About');
        if ($result['status'] == true) {
            $this->assign('banner', $result['data']);
        }
    }

    /***
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function _initialize()
    {
        parent::_initialize();
        $data = collection((new CategoryModel())->getDataByCode($this->code))->toArray();
        $tree = CategoryHelp::toLayer($data, $name = 'child', $parent_id = 0);
        $this->language=(new LanguageModel())->getLanguageCodeOrID($this->code);
        $this->assign('tree', $tree);
    }

    public function index(){
        if(request()->isGet()){
            $result=(new DocumentModel())->getDataByLanguageId($this->language);
            $this->assign('data',$result['data']['data']);
            $this->assign('count',$result['data']['count']);
            return $this->fetch($this->template . '/document/index.html');
        }
    }

    public function category($category)
    {
        if (empty($category) || !isset($category)) {
            abort(404);
        }
        $categoryModel = new CategoryModel();
        $cate = $categoryModel->getAllCategory($this->code);
        $language_id = LanguageModel::getLanguageCodeOrID($this->code);
        $category_data = (new CategoryModel())->getCategoryByName($category, $language_id);
        $path = collection(CategoryHelp::getParents($cate, $category_data['id']))->toArray();
        $result = (new DocumentModel())->getDocumentByCategory($category);
        $this->assign("category", $category_data);
        $this->assign('path', $path);
        $this->assign('data', $result);
        return $this->fetch($this->template . '/document/category.html');
    }

    public function details($url_title)
    {
        $result=(new DocumentModel())->getDetailByUrlTitle($url_title);
        $this->assign('data',$result[0]);
        return $this->fetch($this->template . '/document/details.html');
    }
}