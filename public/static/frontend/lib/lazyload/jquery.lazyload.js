//图片延时加载
//闭包，局部作用域
//延时加载插件——jq

(function ($) {    //自我执行的匿名函数  $ 接收jQuery对象

    function LazyLoad(el, opts) {
        this.init(el, opts);
    }

    LazyLoad.DEFAULTS = {
        dataAttr: 'src'
    };

    LazyLoad.prototype.init = function (el, opts) {    //初始化
        this.$el = $(el);
        this.opts = $.extend({}, LazyLoad.DEFAULTS, opts);   //this.a = $.extend({},b,c)  a 继承 b c 的属性和方法，保存在 {} 中，当 b c 属性冲突时，以最后的 c 为准
        this.$win = $(window);

        this.bindEvent();
        this.load();
    };

    LazyLoad.prototype.bindEvent = function () {
        var self = this,
            timer = null;
            this.fn = function () {
            if (timer) return;

            timer = setTimeout(function () {
                self.load();
                timer = null;
            }, 250)
        };
        this.$win.on('scroll resize', this.fn)    //window绑定事件scroll、resize
    };

    LazyLoad.prototype.load = function () {    //加载图片
        var self = this,
            $el = this.$el;
        $el.each(function () {    //遍历所有没有加载过的图片
            if (!this.loaded) {
                if (inVisibleArea(this)) {
                    appear(this)
                }
            }
        });

        if (isAllLoaded()) {
            self.destructor();
        }

        //图片全部加载完毕
        function isAllLoaded() {
            return $el.length === 0
        }

        //位置检测
        function inVisibleArea(elem) {
            var $win = self.$win;
            //滚动高度         +  可视区域高度    >= 图片到文档顶部的高度     结果为true 图片已经进入了可视区域，或者曾经进入过可视区域
            return $win.scrollTop() + $win.height() >= $(elem).offset().top;
        }

        //显示图片
        function appear(elem) {
            elem.src = $(elem).data(self.opts.dataAttr);   //获取img上的data-src属性值，，并赋值给src
            elem.loaded = true;

            var tmp = $.grep($el, function (elem) {    //去除已经加载过的图片，，避免每次遍历时都要遍历所有的图片
                return !elem.loaded;
            });
            $el = $(tmp)
        }
    };

    LazyLoad.prototype.destructor = function () {  //当所有图片加载完成  销毁绑定的事件  优化性能
        this.$win.off('scroll resize', this.fn)
    };

    //业务代码完成

    //插件完成形式
    // 1、$.functionName  ==>  $.extend             不需要绑定DOM，直接调用         $.lazyload()
    // 2、$().functionName  ==>  $.fn.extend        用户自己绑定需要引用插件的DOM    $('.lazy').lazyload()

    $.fn.extend({
        lazyload: function (opts) {      //lazyload  外部调用插件名
            new LazyLoad(this, opts);
            return this;
        }
    })

})(jQuery);    //传递jQuery对象









