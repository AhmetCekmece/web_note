const wait_container = document.getElementById('wait_container');

function SendForm(_FormID, _operation) {
    var form = document.getElementById(_FormID);
    var formData = new FormData(form);

    // wait_container.style.display = "block";

    var xhr = new XMLHttpRequest();
    xhr.open("POST", '../backend/response_ajax.php?operation=' + _operation);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {

            // setTimeout(function() {



                // wait_container.style.display = "none";

                if (xhr.status === 200) {
                    var responseData = "";
                    try {
                        responseData = JSON.parse(xhr.responseText);
                        if(responseData.test1){
                            console.log("Test1: " + responseData.test1);
                        }
                        if(responseData.test2){
                            console.log("Test2: " + responseData.test2);
                        }
    
                        if (responseData.error) {
                            console.log("ERROR: " + responseData.error);
                            footer.innerHTML = ("<span style='color:red; font-weight:bold;'>ERROR: </span>" + responseData.error);
                        } else if (responseData.success) {
                            console.log("SUCCESS: " + responseData.success);
                            form.reset();  
                            footer.innerHTML = ("<span style='color:yellowgreen; font-weight:bold;'>SUCCESS: </span>" + responseData.success);
                            Response_Islem(_operation, responseData);
                        }
                    } catch (error) {
                        console.log("BIGHATA: " + xhr.responseText)
                    }
                } else {
                    console.error('HATA: ' + xhr.status);
                }



            // }, 500);

        }
    };
    xhr.send(formData);
}


//------------------------------------------------------------------------------

function Notu_Kaydet_Post(){
    notu_kaydet_btn.disabled = true; 
    icerik_icerik.addEventListener('input', Icerik_degistimi);
    icerik_icerik.setAttribute("icerikdegisti", false);
    window.removeEventListener("beforeunload", CikisEngelle);

    let form = document.createElement('form');
    form.setAttribute('method', 'post');
    form.setAttribute('id', 'notkaydetForm');

    let input1 = document.createElement('input');
    input1.setAttribute('type', 'hidden');
    input1.setAttribute('name', 'icerik');
    input1.setAttribute('value', document.getElementById('icerik_icerik').innerHTML);  
    form.appendChild(input1);

    document.body.appendChild(form);
    SendForm(form.id,'not_kaydet');
    form.remove();
}

function Notu_Sil_Post(){
    let form = document.createElement('form');
    form.setAttribute('method', 'post');
    form.setAttribute('id', 'notsilForm');

    document.body.appendChild(form);
    SendForm(form.id,'not_sil');
    form.remove();
}

function Baslik_Kaydet_Post(){  
    let form = document.createElement('form');
    form.setAttribute('method', 'post');
    form.setAttribute('id', 'baslikkaydetForm');

    let input1 = document.createElement('input');
    input1.setAttribute('type', 'hidden');
    input1.setAttribute('name', 'baslik');
    input1.setAttribute('value', document.getElementById('not_baslik').innerText);  
    form.appendChild(input1);

    document.body.appendChild(form);
    SendForm(form.id,'baslik_kaydet');
    form.remove();
}

function Not_Olustur_Post(){
    if(!notu_kaydet_btn.disabled){
        Notu_Kaydet_Post();
    }

    create_not_ismi.value = create_not_ismi.value.trim();
    if(create_not_ismi.value == ""){
        create_not_ismi.value = "isimsiz";
    }

    SendForm(notolusturForm.id,'not_olustur');    
}

function Altnot_Olustur_Post(){
    if(!notu_kaydet_btn.disabled){
        Notu_Kaydet_Post();
    }

    create_altnot_ismi.value = create_altnot_ismi.value.trim();
    if(create_altnot_ismi.value == ""){
        create_altnot_ismi.value = "isimsiz";
    }

    SendForm(altnotolusturForm.id,'altnot_olustur');
}

function Activenot_Sec_Post(_not_uindex){
    clearTimeout(timeout); // Activenot_Sec_Post tetiklendiğinde zamanlayıcıyı iptal et

    if(!notu_kaydet_btn.disabled){
        Notu_Kaydet_Post();
    }

    let form = document.createElement('form');
    form.setAttribute('method', 'post');
    form.setAttribute('id', 'activenotsecForm');

    let input1 = document.createElement('input');
    input1.setAttribute('type', 'hidden');
    input1.setAttribute('name', 'not_uindex');
    input1.setAttribute('value', _not_uindex);  
    form.appendChild(input1);

    document.body.appendChild(form);
    SendForm(form.id,'activenot_sec');
    form.remove();
}

function Altnot_Gizle_Post(_not_uindex, _istek_tipi){
    let form = document.createElement('form');
    form.setAttribute('method', 'post');
    form.setAttribute('id', 'altnotgizleForm');

    let input1 = document.createElement('input');
    input1.setAttribute('type', 'hidden');
    input1.setAttribute('name', 'not_uindex');
    input1.setAttribute('value', _not_uindex);  
    form.appendChild(input1);

    let input2 = document.createElement('input');
    input2.setAttribute('type', 'hidden');
    input2.setAttribute('name', 'istek_tipi');
    input2.setAttribute('value', _istek_tipi);  
    form.appendChild(input2);

    document.body.appendChild(form);
    SendForm(form.id,'altnot_gizle');
    form.remove();
}

function Not_Tasi_Post(_tasimaTuru, _draggedButton, _dropTarget){   //tasimaTuru = Altina_Tasi - Yanina_Tasi - Ustune_Tasi
        
    var form = document.createElement('form');
    form.setAttribute('method', 'post');
    form.setAttribute('id', 'nottasiForm');

    var input1 = document.createElement('input');
    input1.setAttribute('type', 'hidden');
    input1.setAttribute('name', _tasimaTuru);
    input1.setAttribute('value', 'true');
    form.appendChild(input1);

    var input2 = document.createElement('input');
    input2.setAttribute('type', 'hidden');
    input2.setAttribute('name', 'tasidigim_not');
    input2.setAttribute('value', _draggedButton.getAttribute('not_uindex'));
    form.appendChild(input2);

    var input3 = document.createElement('input');
    input3.setAttribute('type', 'hidden');
    input3.setAttribute('name', 'alici_not');
    input3.setAttribute('value', _dropTarget.getAttribute('not_uindex'));
    form.appendChild(input3);

    document.body.appendChild(form);
    SendForm(form.id,'not_tasi');
    form.remove();
}

function Notlarwidth_Kaydet_Post(_notlarwidth){
    var form = document.createElement('form');
    form.setAttribute('method', 'post');
    form.setAttribute('id', 'notlarwidthForm');

    var input1 = document.createElement('input');
    input1.setAttribute('type', 'hidden');
    input1.setAttribute('name', 'notlarwidth');
    input1.setAttribute('value', _notlarwidth);
    form.appendChild(input1);

    document.body.appendChild(form);
    SendForm(form.id,'notlar_width');
    form.remove();
}
