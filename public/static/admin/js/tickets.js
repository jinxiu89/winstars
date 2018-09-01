/**
 * Created by guo on 2018/1/4.
 */
function ticket_reply(title,url,w,h) {
    layer_show(title,url ,w, h);
}
function send_email(url) {
    $.ajax({
        url: url,
        type: "post",
        dataType: "json",
        data: $("form").serialize(),//提交表单数据
        success: function (result) {
            if (result.status == 1) {
                layer.msg(result.data, {icon: 1, time:2000});
                window.parent.location.reload();
                parent.layer.time(5000).closeAll('iframe');
            } else {
                layer.msg(result.data, {icon: 5, time: 1500});
            }
        }
    })
}