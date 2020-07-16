var TabTitle = $("div.tab-bar .tab-bar-title"); // 产品标题 右侧按钮
var TabBtn = $("div.tab-bar .tab-bar-title i"); // 产品标题 右侧按钮
var Nav = $("div.tab-bar ul"); // 导航列表盒子
var NavBtn = $("div.tab-bar ul li"); // 导航列表
var ContentDiv = $("div.content > div"); // 概述，参数
var result = true;

NavBtn.eq(0).addClass("currentActive"); // 默认给第一个导航按钮添加背景
ContentDiv.eq(0).show(); // 默认让第一个详情内容显示，概述
NavBtn.click(function(){
    var index = $(this).index();
    $(this).addClass("currentActive").siblings().removeClass("currentActive");
    ContentDiv.eq(index).show().siblings().hide()
});

TabTitle.click(function(){
    var This = $(this);
    if (result)
    {
        slide(TabBtn, 180, false)
    }
    else
    {
        slide(TabBtn, 0, true)
    }
    Nav.slideToggle()
});
function slide(This, num, res){
    This.css({
        "transform": "rotate("+num+"deg)"
    });
    result = res;
}
