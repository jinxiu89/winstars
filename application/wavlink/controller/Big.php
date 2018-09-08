<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 2018/6/1
 * Time: 14:44
 */
namespace app\wavlink\controller;
use app\common\helper\Search;
use app\common\helper\Excel;
class Big extends BaseAdmin
{
//    后台列表
    public function index()
    {

        $data = Search::search('Strange','','');
        return $this->fetch('', [
            'result' => $data['data'],
            'count' => $data['count']
        ]);
    }

    public function export()
    {

        $excel = new Excel();
        $table_name = "tb_strange";
        $field = ['id' => '序号', 'name' => '名','email' => '客户邮箱', 'content'=>"感兴趣的产品类别" ];
        $excel->setExcelName('data')
            ->createSheet('数据', $table_name, $field)
            ->downloadExcel();
    }
}