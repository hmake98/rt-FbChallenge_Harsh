var slideIndex = 1;
function plusDivs(n, no) {
    slideId = no;
    showDivs(slideIndex += n, no);
}

function showDivs(n, no) {
    var i;
    var sli = 'mySlides'+no;
    x = document.getElementsByClassName(sli);
    if (n > x.length) {
        slideIndex = 1;
    }
    if (n < 1) {
        slideIndex = x.length;
    }
    for (i = 0; i < x.length; i++) {
        x[i].style.display = 'none';
    }
    x[slideIndex-1].style.display = 'block';
}

//document.getElementById('clicked').click();

function getBack() {
    history.go(-1);
}

