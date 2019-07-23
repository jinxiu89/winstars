var TabBtn = $("div.tab-bar .tab-bar-title i"); // 产品标题 右侧按钮
var Nav = $("div.tab-bar ul"); // 导航列表盒子
var NavBtn = $("div.tab-bar ul li"); // 导航列表
var ContentDiv = $("div.content > div"); // 概述，参数
var result = true;

NavBtn.eq(0).addClass("bgColor"); // 默认给第一个导航按钮添加背景
ContentDiv.eq(0).show(); // 默认让第一个详情内容显示，概述
NavBtn.click(function(){
    var index = $(this).index();
    $(this).addClass("bgColor").siblings().removeClass("bgColor");
    ContentDiv.eq(index).show().siblings().hide()
});

TabBtn.click(function(){
    var This = $(this);
    if (result)
    {
        slide(This, 180, false)
    }
    else
    {
        slide(This, 0, true)
    }
    Nav.slideToggle()
});
function slide(This, num, res){
    This.css({
        "transform": "rotate("+num+"deg)"
    });
    result = res;
}
