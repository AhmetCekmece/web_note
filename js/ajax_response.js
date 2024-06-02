var notbaslik_liste = null;

document.addEventListener('DOMContentLoaded', function () {
    notbaslik_liste = document.getElementById("notlar").getElementsByTagName("li");

});


function Response_Islem(_operation, _responseData) {
    switch (_operation) {
        case 'not_olustur':
            MenuKapat();
            notlar.innerHTML = _responseData.notlar;
            Active_Not_Goster(_responseData.not_uindex, _responseData.baslik, "");
            break;

        case 'altnot_olustur':
            MenuKapat();
            notlar.innerHTML = _responseData.notlar;
            Active_Not_Goster(_responseData.not_uindex, _responseData.baslik, "");
            break;

        case 'baslik_kaydet':            //___________ DUZELT ____________
            for (var i = 0; i < notbaslik_liste.length; i++) {
                if (notbaslik_liste[i].getAttribute("not_uindex") === not_baslik.getAttribute("not_uindex")) {
                    notbaslik_liste[i].textContent = _responseData.baslik;
                    break; 
                }
            }
            break;
        
        case 'not_sil':
            console.log(_responseData.bagli_notlar);
            notlar.innerHTML = _responseData.notlar;
            Active_Not_Goster(); //bos donecek
            break;
        
        case 'activenot_sec':         //___________ DUZELT ____________
            Active_Not_Goster(_responseData.not_uindex, _responseData.baslik, _responseData.icerik);

            // for (var i = 0; i < notbaslik_liste.length; i++) {
            //     if (notbaslik_liste[i].getAttribute("not_uindex") === not_baslik.getAttribute("not_uindex")) {
            //         notbaslik_liste[i].style.color = 'red';
            //     }
            //     else {
            //         notbaslik_liste[i].style.color = 'white';
            //     }
            // }
            break;

        default:
            break;
    }
} 

function Active_Not_Goster(_not_uindex = "", _baslik = "", _icerik = ""){
    not_baslik.setAttribute("not_uindex", _not_uindex);
    not_baslik.innerText = _baslik;
    icerik_icerik.innerHTML = _icerik;

    //secili notun rengini kirmizi felan yaparsin
}