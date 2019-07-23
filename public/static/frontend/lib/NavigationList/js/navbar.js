function ShowMenu(obj) {
    var n = $(obj).parent().children("li").index(obj);
    $(obj).siblings("li").find("i").text("+");
    var Nav = obj.parentNode;
    if (!Nav.id) {
        var BName = Nav.getElementsByTagName("ol");
        var HName = Nav.getElementsByTagName("h2");
        var t = 2;
    } else {
        var BName = document.getElementById(Nav.id).getElementsByTagName("span");
        var HName = document.getElementById(Nav.id).getElementsByTagName(".header");
        var t = 1;
    }
    for (var i = 0; i < HName.length; i++) {
        HName[i].innerHTML = HName[i].innerHTML.replace("-", "+");
        HName[i].className = "";
    }
    obj.className = "h" + t;
    for (var i = 0; i < BName.length; i++) {
        if (i !== n) {
            BName[i].className = "no";
        }
    }
    if (BName[n].className === "no") {
        BName[n].className = "";
        obj.innerHTML = obj.innerHTML.replace("+", "-");
    } else {
        BName[n].className = "no";
        obj.className = "";
        obj.innerHTML = obj.innerHTML.replace("-", "+");
    }
}
