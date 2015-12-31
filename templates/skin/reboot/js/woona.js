/**
     * Created by lunavod on 16.10.15.
     */
var woona = function(){
    document.body.insertBefore(img, document.body.firstChild);
    img.style.display = "block";
    setTimeout(function() {
        img.style.display = "none";
        img.parentNode.removeChild(img);
    }, 500);
}
img = document.getElementsByClassName("woona")[0]
var a = Math.random()*10000;
var b = Math.round(a);
if (b<30){
    woona();
} else {
    img.style.display = "none"
    img.parentNode.removeChild(img);
}
