$(function(){
    // Home 公共 首页-头部导航定位
    var $header = $("section.header");

    $(window).scroll(function(){
        var top = $(document).scrollTop();
        if( top >= $header.outerHeight() ){
            $header.addClass("boxShow");
            $header.css({
                position:'fixed',
                left: 0,
                top: 0,
                width: 100+"%",
                'z-index': 9999
            })
        }else{
            $header.removeClass("boxShow");
            $header.css({
                position:'static'
            })
        }
    });

    /* Home 公共 首页-头部导航，搜索框 PC端 */
    var $search = $("div.w-home-box > div.w-home-cont > div.w-home-nav-search > a.search");
    var $input = $('div.w-home-box > div.w-home-form-search-box');

    if( $(window).width() >= 768 ){
        /* w-Home-nav 首页头部导航 搜索框 */
        $search.hover(function(ev){
            ev.stopPropagation();
            $input.slideDown(200);
        },function(){
            return;
        });
        $search.click(function(ev){
            ev.stopPropagation();
            $input.slideToggle(200);
        });
        $input.click(function(ev){
            ev.stopPropagation()
        });
        $(document).click(function(){
            $input.slideUp(200)
        });

        // 头部导航 按钮 划过特效
        // $nav_li.eq(0).addClass("orange_white");
        // $nav_li.hover(function(){
        //     $(this).addClass("orange_white").siblings().removeClass("orange_white")
        // });
    }
});