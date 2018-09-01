<?php
/**
 * Created by PhpStorm.
 * User: wavlink
 * Date: 2018/1/2
 * Time: 14:55
 */
namespace app\en_us\controller;
use app\common\validate\OtherInformation as OtherInformationValidate;
use app\common\validate\ProductInformation as ProductInformationValidate;
use app\common\validate\UserInformation as UserInformationValidate;
use \app\common\model\Ticket as TicketModel;
class Ticket extends Base{
    public function index(){
        return $this->fetch();
    }
    public function add(){
        return $this->fetch();
    }
    public function detail($sn){
        return $sn;
    }
    public function save(){
        (new UserInformationValidate())->goCheck();
        (new ProductInformationValidate())->goCheck();
        (new OtherInformationValidate())->goCheck();
        $ticketAdd = new TicketModel();
        $data = input('post.','','htmlentities');
        if($ticketAdd->addTicket($data,$this->code)){
            return show(1,'','','','',lang('ticket_success'));
        }else{
            return show(0,'','','','',lang('ticket_error'));
        }
    }
}