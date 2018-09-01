<?php
/**
 * Created by PhpStorm.
 * User: guo
 * Date: 2018/1/4
 * Time: 14:27
 */
namespace app\wavlink\controller;
use app\common\helper\Excel;
use \app\common\model\Ticket as TicketModel;

class Ticket extends BaseAdmin
{
    //获取未处理的固件信息,status = -1
    public function index(){
        $language_id = $this->MustBePositiveInteger(input('get.language_id'));
        $ticket = TicketModel::getDataByStatus(-1,$language_id);
        foreach ($ticket['data'] as $k => $v){
            $v['desc'] = cut_str($v['description'],20,0);
        }
        return $this->fetch('',[
            'ticket' => $ticket['data'],
            'count' => $ticket['count'],
            'language_id' => $language_id,
        ]);
    }
    //获取已经处理的固件信息
    public function index_off(){
        $language_id = $this->MustBePositiveInteger(input('get.language_id'));
        $ticket = TicketModel::getDataByStatus(1,'');
        return $this->fetch('',[
            'ticket' => $ticket['data'],
            'count'  => $ticket['count'],
            'language_id'=>$language_id
        ]);
    }
    //查看客户提交的详细信息
    public function look($id){
        $id = $this->MustBePositiveInteger($id);
        $ticket = TicketModel::get($id);
        return $this->fetch('',[
            'ticket' => $ticket
        ]);
    }
    //回复内容
    public function reply($id){
        $id = $this->MustBePositiveInteger($id);
        $ticket = TicketModel::get($id);
        if($ticket['status'] !== -1){
           $this->error('已经回复了');
        }else{
            return $this->fetch('',[
                'ticket'  => $ticket,
            ]);
        }

    }
    public function reply_look($id){
        $id = $this->MustBePositiveInteger($id);
        $ticket = TicketModel::get($id);
        return $this->fetch('',[
            'ticket' => $ticket,
        ]);
    }
    //导出表格
    public function export(){
        $data = input('get.');
        $excel = new Excel();
        $table_name = "ticket";
        if ($data['status'] == -1){
            //如果没有选择哪种语言，就全部导出表格
            if (empty($data['language_id'])){
                $map = ['status' => -1];
                $language_name = '全部';
            }else{
                //如果选择了哪种语言，就把该语言的表格给导出来
                $language_name = getLanguage($data['language_id']);
                $map = [
                    'status' => -1,
                    'language_id' => $data['language_id']
                ];
            }
            $field =['id'=>'序号','first_name'=>'称呼','email'=>'客户邮箱','model'=>'产品型号','sn'=>'产品SN','description'=>'问题描述'];
            $excel->setExcelName($language_name.'下载未回复固件服务中心')
                ->createSheet($language_name.'未回复',$table_name,$field,$map)
                ->downloadExcel();
        }
        if ($data['status'] == 1){
            $map = ['status' => 1];
            $field2 =['id'=>'序号','first_name'=>'称呼','email'=>'客户邮箱','model'=>'产品型号','sn'=>'产品SN','description'=>'问题描述','content'=>'回复内容'];
            $excel->setExcelName('下载已回复固件服务中心')
                ->createSheet('已回复',$table_name,$field2,$map)
                ->downloadExcel();
        }
    }

    //发送邮件
    public function send(){
        $data = input('post.');
        $to   = $data['email'];
        $res = sendMail($to,$data['last_name'],$data['subject'],$data['content']);
        if ($res){
            model("Ticket")->where('id',$data['id'])->update(['status'=>1,'subject'=>$data['subject'],'content'=>$data['content']]);
            return show(1,'success','','','','发送成功');
        }else{
            return show(0,'error','','','','发送失败');
        }
    }
}