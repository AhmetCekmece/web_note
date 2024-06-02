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
const notlar_ul = document.getElementById('notlar_ul');

//forms
const sifredegisForm = document.getElementById('sifredegisForm');
const notolusturForm = document.getElementById('notolusturForm');
const altnotolusturForm = document.getElementById('altnotolusturForm');

let kaydirmawidth = 0;
let notbaslik = "";
let timeout;

document.addEventListener('DOMContentLoaded', function () {

    //__________________ BOYUT AYARLA _________________

    const minGovdeWidth = parseInt(getComputedStyle(container_nonav).minWidth);
    Boyut_Ayarla(minGovdeWidth);
    window.addEventListener('resize', function() {
        Boyut_Ayarla(minGovdeWidth);
    });
    Notlar_boyut_ayar();

   

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
    
            // Minimum ve maksimum genişlik sınırları kontrol ediliyor
            if (newNotlarWidth < minNotlarWidth) {
                newNotlarWidth = minNotlarWidth;
            } else if (newNotlarWidth > maxNotlarWidth) {
                newNotlarWidth = maxNotlarWidth;
            }
    
            // İçerik ve notlar genişlikleri güncelleniyor
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

function Notlar_boyut_ayar(){
    notlar_ul.style.width = "max-content";
    if(notlar_ul.offsetWidth < notlar.offsetWidth - 10){
        notlar_ul.style.width = notlar.offsetWidth - 10 + 'px';
    }

    notlardivs = notlar_ul.children;
}

// _____________________________  NOT TASIMA __________________________

var alan = notlar_ul;
var alanDisinda = false;
var isDragging = false;
var draggedElement = null;
var draggedClone = null;
var draggedDiv_X = 0;
var draggedDiv_Y = 0;
var alanLeft = 0;
var alanRight = 0;
var alanTop = 0;
var alanBottom = 0;
var notUstCizgi = null;
var notAltCizgi = null;
var target_uindex = 0;

function dragStart(event, _not_uindex){
    timeout = setTimeout(function() { 
        if(!isDragging){
            isDragging = true;
            draggedElement = document.getElementById("notlardivs" + _not_uindex);
    
            draggedDiv_X = event.clientX - draggedElement.getBoundingClientRect().left;
            draggedDiv_Y = event.clientY - draggedElement.getBoundingClientRect().top;
            alanLeft = alan.getBoundingClientRect().left;
            alanRight = alan.getBoundingClientRect().right;
            alanTop = alan.getBoundingClientRect().top;
            alanBottom = alan.getBoundingClientRect().bottom;
        
            draggedClone = draggedElement.cloneNode(true);
            draggedClone.classList.add("dragging");
            //draggedClone.classList.remove("notbaslik_buttons");
            draggedClone.style.left = draggedElement.getBoundingClientRect().left + "px";
            draggedClone.style.top = draggedElement.getBoundingClientRect().top + "px";
            draggedClone.style.width = notlar.offsetWidth + "px";

            // while (draggedClone.firstChild) {  //clone icini temizle
            //     draggedClone.removeChild(draggedClone.firstChild);
            // }
            alan.appendChild(draggedClone); 
    
            draggedElement.classList.add("dragging_real");
        }       
    }, 150);
}

document.addEventListener("mousemove", function (e) {
    if (isDragging) {
        var droppedElements = document.elementsFromPoint(e.clientX, e.clientY);
        droppedElements.forEach(function(element) {
            if (element.classList.contains("notbaslik_buttons")) {
                // console.log("Dragged element ID:", draggedElement.getAttribute('not_uindex'));
                // console.log("Dropped element ID:", element.getAttribute('not_uindex'));

                if(target_uindex != element.getAttribute('not_uindex')){

                    if (target_uindex != 0) {
                        notUstCizgi.style.opacity = 0;
                        notAltCizgi.style.opacity = 0;
                        notUstCizgi.style.zIndex = 0;
                        notAltCizgi.style.zIndex = 0;
                    }

                    target_uindex = element.getAttribute('not_uindex');

                    for (var i = 0; i < notlardivs.length; i++) {
                        if (notlardivs[i].getAttribute('not_uindex') == target_uindex) {
                            notUstCizgi = notlardivs[i].querySelector('.not_ustcizgi');
                            notAltCizgi = notlardivs[i].querySelector('.not_altcizgi');
                            
                            if(target_uindex != draggedElement.getAttribute('not_uindex')){
                                notUstCizgi.style.opacity = 1;
                                notAltCizgi.style.opacity = 1;
                                notUstCizgi.style.zIndex  = 1;
                                notAltCizgi.style.zIndex  = 1;
                            }
                            break;
                        }
                    }
                }
            }
        });

        // notUstCizgi.addEventListener("mouseenter", function (e) {
        //     notUstCizgi.classList.add("notcizgi_hover");
        //     notAltCizgi.classList.remove("notcizgi_hover");
        // });

        var x = e.clientX - draggedDiv_X;
        var y = e.clientY - draggedDiv_Y;

        draggedClone.style.left = x + "px";
        draggedClone.style.top = y + "px";

        if (e.clientX < alanLeft || e.clientX > alanRight || e.clientY < alanTop || e.clientY > alanBottom) {
            alanDisinda = true;
            document.dispatchEvent(new Event('mouseup'));
        }
    }
});

document.addEventListener("mouseup", function (e) {
    if (isDragging) {
        isDragging = false;
        if (draggedClone && draggedClone.parentNode) {
            draggedClone.parentNode.removeChild(draggedClone);
        }

        if (target_uindex != 0) {
            notUstCizgi.style.opacity = 0;
            notAltCizgi.style.opacity = 0;
            notUstCizgi.style.zIndex = 0;
            notAltCizgi.style.zIndex = 0;
        }

        if(!alanDisinda){
            var drop_ustcizgi = null;
            var drop_altcizgi = null;
            var drop_notbaslikbtn = null;

            var droppedElements = document.elementsFromPoint(e.clientX, e.clientY);
            droppedElements.forEach(function(element) {
                if (element.classList.contains("not_ustcizgi")) {
                    drop_ustcizgi = element;
                }
                else if (element.classList.contains("not_altcizgi")) {
                    drop_altcizgi = element;
                }
                else if (element.classList.contains("notbaslik_buttons")) {
                    drop_notbaslikbtn = element;
                }
            });

            if (drop_ustcizgi && drop_ustcizgi.getAttribute('not_uindex') != draggedElement.getAttribute('not_uindex')) {
                Not_Tasi_Post("Ustune_Tasi", draggedElement, drop_ustcizgi);
            }
            else if (drop_altcizgi && drop_altcizgi.getAttribute('not_uindex') != draggedElement.getAttribute('not_uindex')) {
                Not_Tasi_Post("Yanina_Tasi", draggedElement, drop_altcizgi);
            }
            else if (drop_notbaslikbtn && drop_notbaslikbtn.getAttribute('not_uindex') != draggedElement.getAttribute('not_uindex')) {
                Not_Tasi_Post("Altina_Tasi", draggedElement, drop_notbaslikbtn);
            }        
        }
        else{
            alanDisinda = false;
        }
        draggedElement.classList.remove("dragging_real");
    }
});

function notbaslikSagtik(e, _not_uindex){  //hem secer hem tasir
    e.preventDefault();
    Activenot_Sec_Post(_not_uindex);
}



// console.log(notlar.offsetWidth);                         //scrollu sayar  100
// console.log(parseInt(getComputedStyle(notlar).width));   //scrollu saymaz  83

// console.log(navbar_altbar.scrollWidth);   //Bir elementin içeriğinin tam boyutunu (genişlik) döndürür.
// console.log(navbar_altbar.clientWidth);   //Bir elementin içeriğinin görülebilir genişliğini döndürür.

//onmouseleave = iç içe geçmiş öğelerde fare imleci bir öğeden diğerine geçtiğinde bu olay tetiklenmez.
//onmouseout   = iç içe geçmiş öğelerde fare imleci bir öğeden diğerine geçtiğinde bu olay tetiklenir.

