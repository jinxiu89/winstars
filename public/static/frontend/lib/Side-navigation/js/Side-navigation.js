$(document).ready(function() {
    // 一级导航默认展开显示
    var $show_ul = $(".list > ul > li:nth-child(2) > ul");
    var $show_li = $(".list > ul > li:nth-child(2) > ul > li:nth-child(1) > ul");
    // 二级导航默认展开显示
    var $show_ul_a = $(".list > ul > li:nth-child(2) > a");
    var $show_li_a = $(".list > ul > li:nth-child(2) > ul > li:nth-child(1) > a");
    $show_ul.css({
        display: "block"
    });
    $show_li.css({
        display: "block"
    });
    if( $show_ul.css("display") == "block" || $show_li.css("display") == "block" ){
        $show_ul_a.addClass("icon-icon_next_arrow");
        $show_li_a.addClass("icon-icon_next_arrow")
    }


    $('.list ul li a').click(function(){
        if($(this).siblings('ul').css('display')=='none'){
            $(this).parent('li').siblings('li').removeClass('icon-icon_next_arrow');
            $(this).addClass('icon-icon_next_arrow');
            $(this).siblings('ul').slideDown(100).children('li');
            if($(this).parents('li').siblings('li').children('ul').css('display')=='block'){
                $(this).parents('li').siblings('li').children('ul').parent('li').children('a').removeClass('icon-icon_next_arrow');
                $(this).parents('li').siblings('li').children('ul').slideUp(100);
            }
        }else{
            //控制自身变成+号
            $(this).removeClass('icon-icon_next_arrow');
            //控制自身菜单下子菜单隐藏
            $(this).siblings('ul').slideUp(100);
            //控制自身子菜单变成+号
            $(this).siblings('ul').children('li').children('ul').parent('li').children('a').addClass('icon-icon_next_arrow');
            //控制自身菜单下子菜单隐藏
            $(this).siblings('ul').children('li').children('ul').slideUp(100);

            //控制同级菜单只保持一个是展开的（-号显示）
            $(this).siblings('ul').children('li').children('a').removeClass('icon-icon_next_arrow');
        }
    })
});