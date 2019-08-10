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

class Document extends Base
{

    protected $beforeActionList = [
        'cate' => ['only', 'index,details'],
    ];

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
            $this->assign('data',$result);
            return $this->fetch($this->template . '/Document/index.html');

        }
    }


    public function details($document=''){
        if(!isset($document) || empty($document)){
            abort(404);
        }
        $result=DocumentModel::getDetailsByUrlTitle($document,$this->code);
        //该文档的分类
        $docCate = ServiceCategoryModel::get(['id' => $result['category_id']]);
        if(!empty($result)){
            return $this->fetch('',[
                'result'=>$result,
                'docCate' => $docCate
            ]);
        }else{
            abort(404);
        }
    }
}