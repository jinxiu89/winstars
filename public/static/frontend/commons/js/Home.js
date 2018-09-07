$(function(){
    let judeg = true;
    let $on_off = $("div.w-home-box > div.w-home-cont > div.w-home-nav-search > a.on-off");
    let $nav_list = $('div.w-home-box > div.w-home-cont > div.w-home-nav-list');

    let $search = $("div.w-home-box > div.w-home-cont > div.w-home-nav-search > a.search");
    let $input = $('div.w-home-box > div.w-home-form-search-box');

    if( $(window).width() >= 768 && $(window).width() < 992 ){
        function slide(name, cont){
            name.slideDown(200);
            cont.slideUp(200);
        }
        $on_off.click(function(){
            slide($nav_list, $input)
        });

        $search.click(function(){
            slide($input, $nav_list)
        });
    }

    if( $(window).width() >= 992 ){
        $search.click(function(ev){
            ev.stopPropagation();
            $input.slideToggle(200);
        });
        $input.click(function(ev){
            ev.stopPropagation()
        });
        $(document).click(function(){
            $input.slideUp(200)
        })
    }

    if($(window).width() <= 767){
        let $on_off = $("div.w-home-box > div.w-home-cont > div.w-home-nav-search > a.on-off");
        let $nav_list_a = $("div.w-home-box > div.w-home-cont > div.w-home-nav-list > a,div.w-home-form-search-box");

        $nav_list_a.each(function(i){

            $on_off.click(function(){
                setTimeout(function(){
                    if(i < $nav_list_a.length-1 ){
                        $nav_list_a.eq(i).slideToggle(200)
                    }
                }, 200*i)
            });

        })
    }
});