let tarayiciHeight = 0;
let tarayiciWidth = 0;
const container = document.getElementById('container');
const navbar = document.getElementById('navbar');
const container_nonav = document.getElementById('container_nonav');
const govde = document.getElementById('govde');
const footer = document.getElementById('footer');
const notlar = document.getElementById('notlar');
const resizer = document.getElementById('resizer');
const icerik = document.getElementById('icerik');
const scroll_sagbuton = document.getElementById('scroll-sagbuton');
const scroll_solbuton = document.getElementById('scroll-solbuton');
const navbar_altbar = document.getElementById('navbar-altbar');
const altbar_bas = document.getElementById('altbar-bas');
const altbar_son = document.getElementById('altbar-son');
let kaydirmawidth = 0;
const ustscroll_sagbuton = document.getElementById('ustscroll-sagbuton');
const ustscroll_solbuton = document.getElementById('ustscroll-solbuton');
const navbar_ustbar = document.getElementById('navbar-ustbar');
const ustbar_bas = document.getElementById('ustbar-bas');
const ustbar_son = document.getElementById('ustbar-son');
const hesap_btn = document.getElementById('hesap_btn');
const giris_btn = document.getElementById('giris_btn');
const renk_btn = document.getElementById('renk_btn');
const cizim_btn = document.getElementById('cizim_btn');
const ekle_btn = document.getElementById('ekle_btn');
const allTools = document.getElementsByClassName('tools');
const tools_hesap = document.getElementById('tools_hesap');
const tools_giris = document.getElementById('tools_giris');
const tools_renk = document.getElementById('tools_renk');
const tools_cizim = document.getElementById('tools_cizim');
const tools_ekle = document.getElementById('tools_ekle');
const menukapat_btn = document.getElementsByClassName('menukapat_btn');
const sifredegis_hesap_btn = document.getElementById('sifredegis_hesap_btn');
const hesap_container = document.getElementById('hesap_container');
const not_baslik = document.getElementById('not_baslik');
const test = document.getElementById('test');
const icerik_icerik = document.getElementById('icerik-icerik');
var selection_kayit = null;
var kayit_color = null;
var kayit_bcolor = null;

document.addEventListener('DOMContentLoaded', function () {

    // Tarayıcının boyutu değiştiğinde veya sayfa yenilendiğinde
    Boyut_Ayarla();
    window.addEventListener('resize', Boyut_Ayarla);


    //____________________________ RESIZER _______________________

    let isResizing = false;
    let lastDownX = 0;
    let notlarWidth = notlar.offsetWidth; // İlk başta notlar bölümünün genişliği

    const minNotlarWidth = 100; // Minimum notlar genişliği
    const maxNotlarWidth = 395; // Maksimum notlar genişliği

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
        console.log("aaaaaaaaaaaaaaa");
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

    for (let i = 0; i < menukapat_btn.length; i++) {
        menukapat_btn[i].addEventListener('click', function () {
            hesap_container.style.display = 'none'; // hesapMenu'yu gizle
        });
    }

    sifredegis_hesap_btn.addEventListener('click', function () {
        hesap_container.style.display = 'block';
    });

    //__________________________ ICERIK __________________________

    not_baslik.addEventListener('keydown', function (event) {
        let text = this.textContent;

        // Eğer basılan tuş "Enter" ise veya metnin uzunluğu 20'den fazlaysa ve basılan tuş bir karakter değilse varsayılan davranışını engelle
        if (event.key === 'Enter' || (text.length >= 20 && event.key.length === 1)) {
            event.preventDefault();
        }
    });


});

function Boyut_Ayarla() {
    if (container_nonav.offsetWidth <= parseInt(getComputedStyle(container_nonav).minWidth)) {  //pırpır engelleyici
        test.style.overflowX = 'auto';
    }
    else {
        test.style.overflowX = 'hidden';
    }

    tarayiciHeight = document.documentElement.clientHeight;
    container.style.height = tarayiciHeight + 'px';
    govde.style.height = (container_nonav.offsetHeight - footer.offsetHeight) + 'px';
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

    // console.log(notlar.offsetWidth);                         //scrollu sayar  100
    // console.log(parseInt(getComputedStyle(notlar).width));   //scrollu saymaz  83

    // console.log(navbar_altbar.scrollWidth);   //Bir elementin içeriğinin tam boyutunu (genişlik) döndürür.
    // console.log(navbar_altbar.clientWidth);   //Bir elementin içeriğinin görülebilir genişliğini döndürür.
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


