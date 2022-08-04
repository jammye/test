function OuvrirPopup(page, nom, option) {
    var rst = window.open(page, nom, option);

    rst.onbeforeunload = function(){
        alert('test');
    }
}