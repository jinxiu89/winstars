$(function(){
    var Header = $("section.header"); // 最顶部头部导航
    var NavHeight = Header.height(); // 最顶部头部导航高度
    var SearchBtn = $(".w-home-nav-search"); // 搜索框按钮
    var SearchBar = $(".w-home-form-search-box"); // 搜索框

    if($("html,body").width() > 767){
        $(window).scroll(function(){
            var Top = $(document).scrollTop(); // 滚动条滚动的高度

            if(Top >= NavHeight) // 判断滚动条滚动的高度（Top）是否大于或等于最顶部头部导航高度（NavHeight）
            {
                $("body").css({
                    "padding-top": Header.height()+"px"
                });
                Header.addClass("fixed boxShow");
                Header.css({
                    'left': 0,
                    'top': 0,
                    'width': 100+"%",
                    'z-index': 9999
                })
            }
            else
            {
                $("body").css({
                    "padding-top": 0
                });
                Header.removeClass("fixed")
            }
        });

        SearchBtn.click(function(e){ // 搜索框按钮点击事件，显示或隐藏
            mask.fadeIn();
            hammer_screen.slideUp();
            SearchBar.slideDown();
            SearchBar.find("input[type='text']").focus();
            return false;        });
        SearchBar.click(function(e){
            window.event? window.event.cancelBubble = true : e.stopPropagation();
        });
        $(document).click(function(){ // 通过点击DOM来关闭搜索框（SearchBar）
            mask.fadeOut();
            SearchBar.slideUp()
        })
    }
    if($("html,body").width() <= 767){
        var listBtn = $("div.w-home-nav-logo span");
        var listContainer = $("div.w-home-box > .w-home-cont > .w-home-nav-list");
        var result = true;

        listBtn.click(function(){
            if(result)
            {
                listContainer.stop(true, false).slideDown();
                result = false;
            }
            else
            {
                listContainer.stop(true, false).slideUp();
                result = true;
            }
        })
    }
});
