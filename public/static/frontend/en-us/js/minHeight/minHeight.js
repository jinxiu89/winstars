function max(title) {
    for (var i = 0; i < title.length; i++) {
        var h = title.eq(i).height();
        var result = Math.max(0, h);
        title.css({
            "min-height": result + "px"
        })
    }
}