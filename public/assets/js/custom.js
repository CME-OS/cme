$(document).ready(function () {
    var documentHeight = $( document ).height(),
        footerHeight   = $('.footer').height(),
        pageContainer  = $('.content.container-fluid'),
        height         = documentHeight - footerHeight - 100;

    pageContainer.css("height", height);
})