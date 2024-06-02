var notbaslik_liste = null;

document.addEventListener('DOMContentLoaded', function () {
    notbaslik_liste = document.getElementById("notlar").getElementsByTagName("li");

});


function Response_Islem(_operation, _responseData) {
    switch (_operation) {
        case 'not_olustur':
            MenuKapat();
            notlar_ul.innerHTML = _responseData.notlar;
            Active_Not_Goster(_responseData.not_uindex, _responseData.baslik, "");
            Notlar_boyut_ayar();
            break;

        case 'altnot_olustur':
            MenuKapat();
            notlar_ul.innerHTML = _responseData.notlar;
            Active_Not_Goster(_responseData.not_uindex, _responseData.baslik, "");
            Notlar_boyut_ayar();
            break;

        case 'baslik_kaydet':            //___________ DUZELT ____________
            for (var i = 0; i < notbaslik_liste.length; i++) {
                if (notbaslik_liste[i].getAttribute("not_uindex") === not_baslik.getAttribute("not_uindex")) {
                    notbaslik_liste[i].textContent = _responseData.baslik;
                    break; 
                }
            }
            Notlar_boyut_ayar();
            break;
        
        case 'not_sil':
            console.log(_responseData.bagli_notlar);
            notlar_ul.innerHTML = _responseData.notlar;
            Active_Not_Goster(); //bos donecek
            Notlar_boyut_ayar();
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

        case 'altnot_gizle':
            notlar_ul.innerHTML = _responseData.notlar;
            if(_responseData.baslik){  //activenot degistiyse
                Active_Not_Goster(_responseData.not_uindex, _responseData.baslik, _responseData.icerik);
            }            
            Notlar_boyut_ayar();
            break;

        case 'not_tasi':
            notlar_ul.innerHTML = _responseData.notlar;
            Notlar_boyut_ayar();
            break;
        
        case 'test':
            console.log(_responseData.test1);
            console.log(_responseData.test2);
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