//导航js
$(document).ready(function(){

    //导航menu菜单交互效果
   var menu = $("#icon-menus"),
       menu_box = $("#menu-box"),
       menu_pane = $("#menu-box .menu-pane"),
       menu_pane_close = $("#menu-box .menu-pane-close");
    menu.click(function(){
        menu_box.fadeIn();
        menu_pane.animate({
            width: '70%'
        }, 500)
    });
    menu_pane_close.click(function(){
        menu_pane.animate({
            width: '0'
        }, 500);
        menu_box.fadeOut()
    });

    //footer交互效果
    $(".about-item h3").click(function () {
        $(this).siblings("ul").stop(true, false).slideToggle();
        $(this).children("span").toggleClass("icon-close").toggleClass("icon-increase");
        $(this).parent("li").siblings("li").children("ul").stop(true, false).slideUp();
        $(this).parent("li").siblings("li").find("span").addClass("icon-increase").removeClass("icon-close");
    })
});