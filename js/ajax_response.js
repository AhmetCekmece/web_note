var notlardivs = notlar_ul.children;

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

        case 'baslik_kaydet':            
            for (var i = 0; i < notlardivs.length; i++) { 
                if(notlardivs[i].getAttribute("not_uindex") == not_baslik.getAttribute("not_uindex")){
                    notlardivs[i].getElementsByClassName('notbaslik_btns')[0].textContent = _responseData.baslik;
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
        
        case 'activenot_sec':        
            notlar_ul.innerHTML = _responseData.notlar;
            Active_Not_Goster(_responseData.not_uindex, _responseData.baslik, _responseData.icerik);
            GuestControl();
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
        
        default:
            break;
    }
} 

function Active_Not_Goster(_not_uindex = "", _baslik = "", _icerik = ""){
    not_baslik.setAttribute("not_uindex", _not_uindex);
    not_baslik.innerText = _baslik;
    icerik_icerik.innerHTML = _icerik;

    Active_Not_Kontrol();
}

function Active_Not_Kontrol(){
    if(not_baslik.getAttribute("not_uindex") > 0){
        menuac_altyeninot.disabled = false;
        baslik_degistir_btn.disabled = false;
        notu_sil_btn.disabled = false;
        icerik_icerik.contentEditable = true;
    }
    else {
        menuac_altyeninot.disabled = true;
        baslik_degistir_btn.disabled = true;
        notu_kaydet_btn.disabled = true;
        notu_sil_btn.disabled = true;
        icerik_icerik.contentEditable = false;
    }
}

icerik_icerik.addEventListener('input', Icerik_degistimi);
function Icerik_degistimi(){
    if (icerik_icerik.getAttribute("icerikdegisti") == "false" && hesap_btn.textContent !== "guest") {
        console.log('İlk yazışma gerçekleşti!');
        icerik_icerik.setAttribute("icerikdegisti", true);
        icerik_icerik.removeEventListener('input', Icerik_degistimi);
        window.addEventListener("beforeunload", CikisEngelle);

        notu_kaydet_btn.disabled = false;  
    }
}

const CikisEngelle = (event) => {
    event.preventDefault();
    event.returnValue = true;  
};