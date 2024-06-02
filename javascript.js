document.addEventListener('DOMContentLoaded', function() {
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
    const cizim_btn = document.getElementById('cizim_btn'); 
    const ekle_btn = document.getElementById('ekle_btn'); 
    const allTools = document.getElementsByClassName('tools');
    const tools_giris = document.getElementById('tools_giris'); 
    const tools_cizim = document.getElementById('tools_cizim'); 
    const tools_ekle = document.getElementById('tools_ekle'); 

    // Tarayıcının boyutu değiştiğinde veya sayfa yenilendiğinde
    equalizeMaxHeights();
    window.addEventListener('resize', equalizeMaxHeights);
    function equalizeMaxHeights() {
        tarayiciHeight = document.documentElement.clientHeight;
        container.style.height = tarayiciHeight + 'px';    
        //govde.style.height = (container.offsetHeight - navbar.offsetHeight - footer.offsetHeight) + 'px';
        govde.style.height = (container_nonav.offsetHeight - footer.offsetHeight) + 'px';
        icerik.style.width = (govde.offsetWidth - notlar.offsetWidth - resizer.offsetWidth) + 'px';

        if(navbar_altbar.scrollWidth != navbar_altbar.clientWidth){
            altbar_bas.style.display = 'block';
            altbar_son.style.display = 'block';
            kaydirmawidth = navbar_altbar.clientWidth - 60;
        }
        else {
            altbar_bas.style.display = 'none';
            altbar_son.style.display = 'none';
        }

        if(navbar_ustbar.scrollWidth != navbar_ustbar.clientWidth){
            ustbar_bas.style.display = 'block';
            ustbar_son.style.display = 'block';
        }
        else {
            ustbar_bas.style.display = 'none';
            ustbar_son.style.display = 'none';
        }

    }

    // console.log(notlar.offsetWidth);                         //scrollu sayar  100
    // console.log(parseInt(getComputedStyle(notlar).width));   //scrollu saymaz  83


    //____________________________ RESIZER _______________________

    let isResizing = false;
    let lastDownX = 0;
    let notlarWidth = notlar.offsetWidth; // İlk başta notlar bölümünün genişliği
    
    const minNotlarWidth = 100; // Minimum notlar genişliği
    const maxNotlarWidth = 395; // Maksimum notlar genişliği

    resizer.addEventListener('mousedown', function(e) {
        isResizing = true;
        lastDownX = e.clientX;
    });

    document.addEventListener('mousemove', function(e) {
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

    document.addEventListener('mouseup', function() {
        isResizing = false;
        // Yeniden boyutlandırıldıktan sonra notlar ve icerik genişliklerini güncelle
        notlarWidth = notlar.offsetWidth;
    });


    //____________________________ NAVBAR SCROLL _______________________           

    scroll_sagbuton.addEventListener('click', function(){
        navbar_altbar.scrollBy({
            left: kaydirmawidth,
            behavior: "smooth"
        });
    });
    scroll_solbuton.addEventListener('click', function(){
        navbar_altbar.scrollBy({
            left: -kaydirmawidth, 
            behavior: "smooth"
        });
    });

    ustscroll_sagbuton.addEventListener('click', function(){
        navbar_ustbar.scrollBy({
            left: kaydirmawidth,
            behavior: "smooth"
        });
    });
    ustscroll_solbuton.addEventListener('click', function(){
        navbar_ustbar.scrollBy({
            left: -kaydirmawidth, 
            behavior: "smooth"
        });
    });

    // console.log(navbar_altbar.scrollWidth);   //Bir elementin içeriğinin tam boyutunu (genişlik) döndürür.
    // console.log(navbar_altbar.clientWidth);   //Bir elementin içeriğinin görülebilir genişliğini döndürür.


    //____________________________ UST BUTTONS CLICK _______________________  
    

    hesap_btn.addEventListener('click', function(){

    });

    giris_btn.addEventListener('click', function(){
        ToolAc(tools_giris);
    });

    cizim_btn.addEventListener('click', function(){
        ToolAc(tools_cizim);
    });

    ekle_btn.addEventListener('click', function(){
        ToolAc(tools_ekle);
    });

    function ToolAc(_active_tool) {
        for (let i = 0; i < allTools.length; i++) {
            allTools[i].style.display = 'none';
        }
        _active_tool.style.display = 'flex';
        navbar_altbar.scrollLeft = 0;
        equalizeMaxHeights();
    }

});