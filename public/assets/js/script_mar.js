function openGroup(evt, groupType) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace("active", " ");
    }
    document.getElementById(groupType).style.display = "block";
    evt.currentTarget.className += " active";
}
function openPage(evt, groupType) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace("active", " ");
    }
    document.getElementById(groupType).style.display = "block";
    evt.currentTarget.className += " active";
}



// upload image
function uploadimg(){
    var imgcanvas = document.getElementById("canv1");
    var fileinput = document.getElementById("finput");
    var image = new SimpleImage(fileinput);
    image.drawTo(imgcanvas);
}
function uploadcover(){
    var imgcanvas2 = document.getElementById("canv2");
    var fileinput2 = document.getElementById("finput2");
    var image = new SimpleImage(fileinput2);
    image.drawTo(imgcanvas2);
}


function openCity(evt, groupType) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace("active", " ");
    }
    document.getElementById(groupType).style.display = "block";
    evt.currentTarget.className += " active";
}