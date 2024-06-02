
function SendForm(_FormID, _operation) {
    var form = document.getElementById(_FormID);
    var formData = new FormData(form);

    var xhr = new XMLHttpRequest();
    xhr.open("POST",'../backend/response_ajax.php?operation=' + _operation);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                var responseData = "";
                try {
                    responseData = JSON.parse(xhr.responseText);
                    if (responseData.error) {
                        console.log("ERROR: " + responseData.error);
                    } else if (responseData.success) {
                        console.log("SUCCESS: " + responseData.success);
                        form.reset();  //formun icerigini siler
                        Response_Islem(_operation, responseData);
                    }
                } catch (error) {
                    console.log("BIGHATA: " + xhr.responseText)
                }
            } else {
                console.error('HATA: ' + xhr.status);
            }
        }
    };
    xhr.send(formData);
}


//------------------------------------------------------------------------------

function Notu_Kaydet_Post(){
    let form = document.createElement('form');
    form.setAttribute('method', 'post');
    form.setAttribute('id', 'notkaydetForm');

    let input1 = document.createElement('input');
    input1.setAttribute('type', 'hidden');
    input1.setAttribute('name', 'icerik');
    input1.setAttribute('value', document.getElementById('icerik_icerik').innerHTML);  //burada birseyler yapman gerek icerikte <div> yazisi varsa bozuluyor*(bozulmuyomus)
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
    // let formButtons = notolusturForm.querySelectorAll('button, input[type="button"], input[type="submit"]');
    // formButtons.forEach(function(button) {
    //     button.disabled = true;
    // });

    SendForm(notolusturForm.id,'not_olustur');

    // formButtons.forEach(function(button) {
    //     button.disabled = false;
    // });    
}

function Altnot_Olustur_Post(){
    SendForm(altnotolusturForm.id,'altnot_olustur');
}

function Activenot_Sec_Post(_not_uindex){
    let form = document.createElement('form');
    form.setAttribute('method', 'post');
    form.setAttribute('id', 'activenotsecForm');

    let input1 = document.createElement('input');
    input1.setAttribute('type', 'hidden');
    input1.setAttribute('name', 'not_uindex');
    input1.setAttribute('value', _not_uindex);  //burada birseyler yapman gerek icerikte <div> yazisi varsa bozuluyor*(bozulmuyomus)
    form.appendChild(input1);

    document.body.appendChild(form);
    SendForm(form.id,'activenot_sec');
    form.remove();

}