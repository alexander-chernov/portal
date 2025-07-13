var show;
 function look(type){
    param=document.getElementById(type);
    if(param.style.display == "none") {
        if(show) show.style.display = "none";
        param.style.display = "block";
        show = param;
    }else param.style.display = "none"
}
