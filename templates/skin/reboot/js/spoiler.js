/**
 * Created by lunavod on 16.10.15.
 */
function spoiler(b){
    if(b.style.display != "block") {
        jQuery(b).show(300);
        b.style.display = "block";
        b.parentElement.getElementsByClassName("spoiler-title")[0].className = "spoiler-title spoiler-open";
    } else {
        jQuery(b).hide(300);
        b.parentElement.getElementsByClassName("spoiler-title")[0].className = "spoiler-title spoiler-close";

    }
}
console.log('now')
function spoiler_click(event){
    var event = event || window.event;
    if(event.button!=0)return;
    var target=event.target||event.srcElement;
    if(!target) return;
    while(!target.classList.contains("spoiler-title")){
        target = target.parentNode||target.parentElement;
        if(!target || target==document.body)return;
    }

    var parent = target.parentNode||target.parentElement;
    if(!parent || parent.lastElementChild == target)return true;
    var b = parent.querySelector(".spoiler-body");
    if(!b) return;
    spoiler(b);
    event.preventDefault ? event.preventDefault() : (event.returnValue=false);
    return false;
}

window.addEventListener("DOMContentLoaded", function(){
    document.body.addEventListener("click", spoiler_click);
});

var allNew = document.querySelectorAll('.spoiler-title');
console.log(allNew)
idx=0
for(idx=0;idx<allNew.length;idx++){
    allNew[idx].className="spoiler-title spoiler-close"
}

var despoil = function() {
    var allBody = document.querySelectorAll('.spoiler-body');
    idx=0
    for(idx=0;idx<allBody.length;idx++){
        allBody[idx].style.display="block"
    }
    var allNew = document.querySelectorAll('.spoiler-title');
    idx=0
    for(idx=0;idx<allNew.length;idx++){
        allNew[idx].className="spoiler-title spoiler-open"
    }
}

var spoil = function() {
    var allBody = document.querySelectorAll('.spoiler-body');
    idx=0
    for(idx=0;idx<allBody.length;idx++){
        allBody[idx].style.display="none"
    }
    var allNew = document.querySelectorAll('.spoiler-title');
    idx=0
    for(idx=0;idx<allNew.length;idx++){
        allNew[idx].className="spoiler-title spoiler-close"
    }
}
