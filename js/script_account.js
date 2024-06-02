const notlar = document.getElementById('notlar');
const resizer = document.getElementById('resizer');
const icerik = document.getElementById('icerik');
const govde = document.getElementById('govde');
const footer = document.getElementById('footer');
const not_baslik = document.getElementById('not_baslik');
const icerik_icerik = document.getElementById('icerik_icerik');
const baslik_degistir_btn = document.getElementById('baslik_degistir_btn');
const notu_kaydet_btn = document.getElementById('notu_kaydet_btn');
const notu_sil_btn = document.getElementById('notu_sil_btn');
const container_nonav = document.getElementById('container_nonav');
const container_nonav_overflow = document.getElementById('container_nonav_overflow');
const allTools = document.getElementsByClassName('tools');
const navbar_altbar = document.getElementById('navbar_altbar');
const navbar_ustbar = document.getElementById('navbar_ustbar');
const ustbar_orta = document.getElementById('ustbar_orta');
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
const create_not_ismi = document.getElementById('not_ismi');
const create_altnot_ismi = document.getElementById('altnot_ismi');
const sifredegis_hesap_btn = document.getElementById('sifredegis_hesap_btn');
const menuac_altyeninot = document.getElementById('menuac_altyeninot');
const menuac_yeninot = document.getElementById('menuac_yeninot');
const notlar_ul = document.getElementById('notlar_ul');

//forms
const sifredegisForm = document.getElementById('sifredegisForm');
const notolusturForm = document.getElementById('notolusturForm');
const altnotolusturForm = document.getElementById('altnotolusturForm');

let kaydirmawidth = 0;
let timeout;

document.addEventListener('DOMContentLoaded', function () {

    //__________________ BOYUT AYARLA _________________

    const minGovdeWidth = parseInt(getComputedStyle(container_nonav).minWidth);
    Boyut_Ayarla(minGovdeWidth);
    window.addEventListener('resize', function() {
        Boyut_Ayarla(minGovdeWidth);
    });
    Notlar_boyut_ayar();
    Active_Not_Kontrol();
   

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
        if (isResizing){
            const offsetX = e.clientX - lastDownX;

            let newNotlarWidth = notlarWidth + offsetX;
    
            if (newNotlarWidth < minNotlarWidth) {
                newNotlarWidth = minNotlarWidth;
            } else if (newNotlarWidth > maxNotlarWidth) {
                newNotlarWidth = maxNotlarWidth;
            }
    
            notlar.style.width = newNotlarWidth + 'px';
            icerik.style.width = (govde.offsetWidth - notlar.offsetWidth - resizer.offsetWidth) + 'px'; 
            Notlar_boyut_ayar();                      
        }  
    });

    document.addEventListener('mouseup', function () { 
        if(isResizing){
            isResizing = false;
            notlarWidth = notlar.offsetWidth;
            Notlarwidth_Kaydet_Post(notlarWidth);    
        }           
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

    //_____________________BASLIK DEGISTIRMEK_______________________

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
        if (text.length >= 500 && event.key.length === 1) {
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
        
        Baslik_Kaydet_Post(); //BASLIK KAYIT ISLEMI
    }
    return _notbaslik_degis;
}

function ToolAc(_element, _active_tool) {
    let active_tool_obj = document.getElementById(_active_tool);
    for (let i = 0; i < allTools.length; i++) {
        allTools[i].style.display = 'none';
    }
    active_tool_obj.style.display = 'flex';
    navbar_altbar.scrollLeft = 0;
    
    if(_element){
        var childElements = ustbar_orta.children;
        for (var i = 0; i < childElements.length; i++) {       
            var childElement = childElements[i];
            if (childElement.id == _element.id) {
                childElement.classList.add("ta_active");
            } else {
                childElement.classList.remove("ta_active");
            }
        }
    }
    Boyut_Ayarla();
}

function MenuPostYolla(event , _islemadi){
    if (event.keyCode === 13) { // Enter tuşuna basıldığında formun gönderilmesini önler
        event.preventDefault();

        switch (_islemadi) {
            case "notolustur":
                Not_Olustur_Post();
                break;
            case "altnotolustur":
                Altnot_Olustur_Post();
                break;
        
            default:
                break;
        }
    }
}

function MenuAc(_menuadi){
    switch (_menuadi) {
        case "sifredegis":
            hesap_container.style.display = 'block';
            menu_sifredegis.style.display = 'flex';
            break;
        case "notolustur":
            hesap_container.style.display = 'block';
            menu_notolustur.style.display = 'flex';
            create_not_ismi.focus();
            break;
        case "altnotolustur":
            hesap_container.style.display = 'block';
            menu_altnotolustur.style.display = 'flex';
            create_altnot_ismi.focus();
            break;
    
        default:
            break;
    }
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

function Notlar_boyut_ayar(){
    notlar_ul.style.width = "max-content";
    if(notlar_ul.offsetWidth < notlar.offsetWidth - 10){
        notlar_ul.style.width = notlar.offsetWidth - 10 + 'px';
    }

    notlardivs = notlar_ul.children;
}

// _____________________________  NOT TASIMA __________________________

var isDragging = false;
var dragElement = null;
var dropElement = null;
var targetElement = null;
var targetUstCizgi = null;
var targetAltCizgi = null;

function dragStart(_dragElement){
    timeout = setTimeout(function() { 
        if(!isDragging){
            isDragging = true;
            dragElement = _dragElement.parentElement;  //notlardivs
            dragElement.classList.add("dragging");
            document.body.style.cursor = "grabbing";
        }
    }, 100);
}

function dragEnter(_targetElement){
    if(isDragging){
        targetElement = _targetElement;
        if(dragElement.getAttribute('not_uindex') != targetElement.getAttribute('not_uindex')){
            targetUstCizgi = targetElement.querySelector('.not_ustcizgi');
            targetAltCizgi = targetElement.querySelector('.not_altcizgi');
            targetUstCizgi.classList.add("dragging_cizgi");
            targetAltCizgi.classList.add("dragging_cizgi");
        }
        else{
            targetElement = null;
        }

    }
}

function dragLeave(_targetElement){
    if(isDragging){
        if(targetUstCizgi && targetAltCizgi){
            targetUstCizgi.classList.remove("dragging_cizgi");
            targetAltCizgi.classList.remove("dragging_cizgi");
        }
        targetElement = null;
        targetUstCizgi = null;
        targetAltCizgi = null;

    }
}

function dropEnter(_dropElement){
    dropElement = _dropElement;
}

function dropLeave(_dropElement){
    dropElement = null;
}

function dragStop(_iptalmi){
    if(isDragging){
        isDragging = false;
        if(dropElement && targetElement && !_iptalmi){
            if(dropElement.classList.contains("not_ustcizgi")){
                Not_Tasi_Post("Ustune_Tasi", dragElement, dropElement);
            }
            else if(dropElement.classList.contains("not_altcizgi")){
                Not_Tasi_Post("Yanina_Tasi", dragElement, dropElement);
            }
            else if(dropElement.classList.contains("notbaslik_buttons")){
                Not_Tasi_Post("Altina_Tasi", dragElement, dropElement);
            }
        }

        if(targetUstCizgi && targetAltCizgi){
            targetUstCizgi.classList.remove("dragging_cizgi");
            targetAltCizgi.classList.remove("dragging_cizgi");
        }
        dragElement.classList.remove("dragging");
        document.body.style.cursor = "auto";       
        dragElement = null;
        dropElement = null;
        targetElement = null;
        targetUstCizgi = null;
        targetAltCizgi = null;
    }
}

document.addEventListener("mouseup", function (e) {
    if(isDragging){
        dragStop(false);
    }
});


document.addEventListener('keydown', function(event) {
    if (event.ctrlKey && event.key === 's') { // Ctrl + S tuş kombinasyonu kontrolü
        event.preventDefault();
        if(not_baslik.getAttribute("not_uindex") > 0 && icerik_icerik.getAttribute("icerikdegisti") == "true"){
            Notu_Kaydet_Post();
        }
    }
});

icerik_icerik.addEventListener("keydown", function(event) {
    if (event.key === "Tab") {
        event.preventDefault(); 
        var selection = window.getSelection();
        var range = selection.getRangeAt(0);
        var tabNode = document.createTextNode("\u00A0\u00A0\u00A0\u00A0"); // 4 boşluk
        range.insertNode(tabNode);
        range.setStartAfter(tabNode);
        range.setEndAfter(tabNode);
        selection.removeAllRanges();
        selection.addRange(range);
    }
});





// console.log(notlar.offsetWidth);                         //scrollu sayar  100
// console.log(parseInt(getComputedStyle(notlar).width));   //scrollu saymaz  83

// console.log(navbar_altbar.scrollWidth);   //Bir elementin içeriğinin tam boyutunu (genişlik) döndürür.
// console.log(navbar_altbar.clientWidth);   //Bir elementin içeriğinin görülebilir genişliğini döndürür.

//onmouseleave = iç içe geçmiş öğelerde fare imleci bir öğeden diğerine geçtiğinde bu olay tetiklenmez.
//onmouseout   = iç içe geçmiş öğelerde fare imleci bir öğeden diğerine geçtiğinde bu olay tetiklenir.

