const notlar = document.getElementById('notlar');
const resizer = document.getElementById('resizer');
const icerik = document.getElementById('icerik');
const govde = document.getElementById('govde');
const not_baslik = document.getElementById('not_baslik');
const icerik_icerik = document.getElementById('icerik_icerik');
const baslik_degistir_btn = document.getElementById('baslik_degistir_btn');
const container_nonav = document.getElementById('container_nonav');
const container_nonav_overflow = document.getElementById('container_nonav_overflow');
const allTools = document.getElementsByClassName('tools');
const navbar_altbar = document.getElementById('navbar_altbar');
const navbar_ustbar = document.getElementById('navbar_ustbar');
const scroll_sagbuton = document.getElementById('scroll_sagbuton');
const scroll_solbuton = document.getElementById('scroll_solbuton');
const ustscroll_sagbuton = document.getElementById('ustscroll_sagbuton');
const ustscroll_solbuton = document.getElementById('ustscroll_solbuton');
const ustbar_bas = document.getElementById('ustbar_bas');
const ustbar_son = document.getElementById('ustbar_son');
const altbar_bas = document.getElementById('altbar_bas');
const altbar_son = document.getElementById('altbar_son');
const hesap_container = document.getElementById('hesap_container');
const menu_sifredegis = document.getElementById('menu_sifredegis');
const menu_notolustur = document.getElementById('menu_notolustur');
const menu_altnotolustur = document.getElementById('menu_altnotolustur');
const sifredegis_hesap_btn = document.getElementById('sifredegis_hesap_btn');
const menuac_altyeninot = document.getElementById('menuac_altyeninot');
const menuac_yeninot = document.getElementById('menuac_yeninot');

//forms
const sifredegisForm = document.getElementById('sifredegisForm');
const notolusturForm = document.getElementById('notolusturForm');
const altnotolusturForm = document.getElementById('altnotolusturForm');

let kaydirmawidth = 0;
let notbaslik = "";

document.addEventListener('DOMContentLoaded', function () {

    //__________________ BOYUT AYARLA _________________

    const minGovdeWidth = parseInt(getComputedStyle(container_nonav).minWidth);
    Boyut_Ayarla(minGovdeWidth);
    window.addEventListener('resize', function() {
        Boyut_Ayarla(minGovdeWidth);
    });

    //___________________ RESIZER ______________________

    const minNotlarWidth = 100; // Minimum notlar genişliği
    const maxNotlarWidth = 395; // Maksimum notlar genişliği
    let isResizing = false;
    let lastDownX = 0;
    let notlarWidth = notlar.offsetWidth; // İlk başta notlar bölümünün genişliği

    resizer.addEventListener('mousedown', function (e) {
        isResizing = true;
        lastDownX = e.clientX;
    });

    document.addEventListener('mousemove', function (e) {
        if (!isResizing) return;

        const offsetX = e.clientX - lastDownX;

        let newNotlarWidth = notlarWidth + offsetX;

        // Minimum ve maksimum genişlik sınırları kontrol ediliyor
        if (newNotlarWidth < minNotlarWidth) {
            newNotlarWidth = minNotlarWidth;
        } else if (newNotlarWidth > maxNotlarWidth) {
            newNotlarWidth = maxNotlarWidth;
        }

        // İçerik ve notlar genişlikleri güncelleniyor
        notlar.style.width = newNotlarWidth + 'px';
        icerik.style.width = (govde.offsetWidth - notlar.offsetWidth - resizer.offsetWidth) + 'px';
    });

    document.addEventListener('mouseup', function () {
        isResizing = false;
        // Yeniden boyutlandırıldıktan sonra notlar ve icerik genişliklerini güncelle
        notlarWidth = notlar.offsetWidth;
    });


    //____________________________ NAVBAR SCROLL _______________________  

    scroll_sagbuton.addEventListener('click', function () {
        navbar_altbar.scrollBy({
            left: kaydirmawidth,
            behavior: "smooth"
        });
    });
    scroll_solbuton.addEventListener('click', function () {
        navbar_altbar.scrollBy({
            left: -kaydirmawidth,
            behavior: "smooth"
        });
    });

    ustscroll_sagbuton.addEventListener('click', function () {
        navbar_ustbar.scrollBy({
            left: kaydirmawidth,
            behavior: "smooth"
        });
    });
    ustscroll_solbuton.addEventListener('click', function () {
        navbar_ustbar.scrollBy({
            left: -kaydirmawidth,
            behavior: "smooth"
        });
    });


    //____________________________ MENU _______________________   

    // for (let i = 0; i < menukapat_btn.length; i++) {
    //     menukapat_btn[i].addEventListener('click', function () {
    //         hesap_container.style.display = 'none'; // hesapMenu'yu gizle
    //     });
    // }

    sifredegis_hesap_btn.addEventListener('click', function () {
        hesap_container.style.display = 'block';
        menu_sifredegis.style.display = 'flex';
    });

    menuac_yeninot.addEventListener('click', function () {
        hesap_container.style.display = 'block';
        menu_notolustur.style.display = 'flex';
    });

    menuac_altyeninot.addEventListener('click', function () {
        hesap_container.style.display = 'block';
        menu_altnotolustur.style.display = 'flex';
    });


    //_____________________BASLIK DEGISTIRMEK_______________________
    notbaslik = not_baslik.innerText;
    let notbaslik_degis = false;

    baslik_degistir_btn.addEventListener("click", function() {
        notbaslik_degis = Not_baslik_degis(true);
        let range = document.createRange();
        let selection = window.getSelection();
        range.selectNodeContents(not_baslik);
        selection.removeAllRanges();
        selection.addRange(range);
    });

    document.addEventListener("click", function(event) {
        if (notbaslik_degis && event.target !== not_baslik && event.target !== baslik_degistir_btn) {
            notbaslik_degis = Not_baslik_degis(false);
        }
    });

    not_baslik.addEventListener('keydown', function (event) {
        let text = this.textContent;

        // Eğer basılan tuş "Enter" ise veya metnin uzunluğu 20'den fazlaysa ve basılan tuş bir karakter değilse varsayılan davranışını engelle
        if (text.length >= 20 && event.key.length === 1) {
            event.preventDefault();
        }
        if (event.key === 'Enter'){
            event.preventDefault();
            notbaslik_degis = Not_baslik_degis(false);
        }
    });
});

function Boyut_Ayarla(_minGovdeWidth){
    if (container_nonav.offsetWidth <= _minGovdeWidth) {  //pırpır engelleyici
        container_nonav_overflow.style.overflowX = 'auto';
    }
    else {
        container_nonav_overflow.style.overflowX = 'hidden';
    }
    icerik.style.width = (govde.offsetWidth - notlar.offsetWidth - resizer.offsetWidth) + 'px';


    if (navbar_altbar.scrollWidth != navbar_altbar.clientWidth) {
        altbar_bas.style.display = 'block';
        altbar_son.style.display = 'block';
        kaydirmawidth = navbar_altbar.clientWidth - 60;
    }
    else {
        altbar_bas.style.display = 'none';
        altbar_son.style.display = 'none';
    }

    if (navbar_ustbar.scrollWidth != navbar_ustbar.clientWidth) {
        ustbar_bas.style.display = 'block';
        ustbar_son.style.display = 'block';
    }
    else {
        ustbar_bas.style.display = 'none';
        ustbar_son.style.display = 'none';
    }
}

function Not_baslik_degis(_notbaslik_degis){
    if (_notbaslik_degis) {
        not_baslik.contentEditable = true;
        baslik_degistir_btn.disabled = true;
    }
    else{
        not_baslik.contentEditable = false;
        baslik_degistir_btn.disabled = false;
        not_baslik.textContent = not_baslik.textContent.trim();
        if (not_baslik.textContent == "") {
            not_baslik.textContent = "isimsiz";
        }
        let selection = window.getSelection();
        selection.removeAllRanges();
        
        if(notbaslik != not_baslik.textContent){
            notbaslik = not_baslik.textContent;
            Baslik_Kaydet_Post(); //BASLIK KAYIT ISLEMI
        }
    }
    return _notbaslik_degis;
}

function ToolAc(_active_tool) {
    let active_tool_obj = document.getElementById(_active_tool);
    for (let i = 0; i < allTools.length; i++) {
        allTools[i].style.display = 'none';
    }
    active_tool_obj.style.display = 'flex';
    navbar_altbar.scrollLeft = 0;
    Boyut_Ayarla();
}

function MenuKapat() {
    hesap_container.style.display = 'none';
    menu_sifredegis.style.display = 'none';
    menu_notolustur.style.display = 'none';
    menu_altnotolustur.style.display = 'none';
    sifredegisForm.reset();
    notolusturForm.reset();
    altnotolusturForm.reset();
}





// console.log(notlar.offsetWidth);                         //scrollu sayar  100
// console.log(parseInt(getComputedStyle(notlar).width));   //scrollu saymaz  83

// console.log(navbar_altbar.scrollWidth);   //Bir elementin içeriğinin tam boyutunu (genişlik) döndürür.
// console.log(navbar_altbar.clientWidth);   //Bir elementin içeriğinin görülebilir genişliğini döndürür.



