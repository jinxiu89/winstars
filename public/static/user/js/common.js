/**
 * Created by Administrator on 16-12-26.
 * 用户中心js表单操作
 */
// $("#button_update").click(function () {
//     var data = $("#form-data").serializeArray();
//     /***
//      * 修正日期：2016-09-08
//      * 修正人：袁普照
//      * 解决数组的问题
//      */
//     var postData={};
//     $(data).each(function(){
//         if(postData[this.name]){
//             if($.isArray(postData[this.name])){
//                 postData[this.name].push(this.value);
//             }else{
//                 postData[this.name]=[postData[this.name],this.value];
//             }
//         }else{
//             postData[this.name]=this.value;
//         }
//     });
//     //console.log(postData);
//     //SCOPE是在模板文件的script block中写的
//     var url=SCOPE.update_url;
//     //noinspection JSUnresolvedFunction
//     $.post(url,postData,function (result) {
//         //r如果返回结果是0 表示失败了
//         if(result.status == 0){
//             dialog.error(result.message,result.title,result.btn);
//         }
//         // 如果返回结果为1 则表示成功
//         if(result.status == 1){
//             dialog.success(result.message,result.jump_url,result.title,result.btn);
//         }
//     },'JSON');
// });
/***
 * 用户提交登录操作
 */
function login() {
    var url = SCOPE.login_url;
    $.ajax({
        type:"POST",
        dataType: "json",
        url: url,
        data: $('#form-data').serialize(),
        success:function (result) {
            if (result.status == 1){
                dialog.OK(result.message,result.jump_url);
                // dialog.success(result.message,result.jump_url,result.title,result.btn);
            }else {
                dialog.error(result.message,result.title,result.btn);
            }
        },
        error:function (result) {
            dialog.error(result.message,result.title,result.btn);
        }
    })
}

/**
 * 国家和地区联动
 */
$('#Country').change(function () {
    var country_id=$(this).children('option:selected').val();
    var data={};
    data['country_id']=country_id;
    var url=SCOPE.getZone;
    var option='<option value="0">请选择</option>';
    $.post(url,data,function(result) {
        $(result).each(function () {
            /** @namespace this.zone_id */
            option += '<option value="'+this.zone_id+'" >'+this.name+'</option>';
        });
        var select='<select class="form-control" name="zone_id">'+option+'</select>';
        $(".select_zone").html(select);
    },'JSON');
});
/***
 * 产品注册或许国家回调
 */
$('#address').change(function () {
    var  address_id=$(this).children('option:selected').val();
    var data={};
    data['address_id']=address_id;
    var url=SCOPE.getCountry;
    var option='';
    $.post(url,data,function (result) {
        console.log(result);
        option='<input type="hidden" name="country_id" value="'+result+'">';
        $(".country_id").html(option);
    },'JSON')
});

