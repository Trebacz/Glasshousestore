var UdinraSlideAutoIndex = 0;
var UdinraSlideAutoIndexx = 0;
UdinraSlideAutoFunc();
UdinraSlideAutoFuncc();

function UdinraSlideAutoFunc() {
    var i;
    var x = document.getElementsByClassName("UdinraFirstSlide");
    for (i = 0; i < x.length; i++) {
       x[i].style.display = "none";  
    }
    UdinraSlideAutoIndex++;
    if (UdinraSlideAutoIndex > x.length) {UdinraSlideAutoIndex = 1}    
    x[UdinraSlideAutoIndex-1].style.display = "block";  
    setTimeout(UdinraSlideAutoFunc, 5000); 
}
function UdinraSlideAutoFuncc() {
    var j;
    var y = document.getElementsByClassName("UdinraSecondSlide");
    for (j = 0; j < y.length; j++) {
       y[j].style.display = "none";  
    }
    UdinraSlideAutoIndexx++;
    if (UdinraSlideAutoIndexx > y.length) {UdinraSlideAutoIndexx = 1}    
    y[UdinraSlideAutoIndexx-1].style.display = "block";  
    setTimeout(UdinraSlideAutoFuncc, 5000); 
}
