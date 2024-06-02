function dragStart(event) {   //tasima basladiginda butonun id sini text olarak tasir.
    event.dataTransfer.setData("text", event.target.id);
}

function dragEnd(event) {     //tasima durdugunda islemi durdurur.
    event.preventDefault();
}

function dragOver(event) {    //uzerine geldiginde ne yapsin
    event.preventDefault();
}

function dragOverGizli(event) {    //uzerine geldiginde ne yapsin
    event.preventDefault();
    var targetButton = event.target;
    targetButton.style.height = '30px';
}

function dragLeaveGizli(event) {         //uzerinden ayrildiginda ne yapsin
    event.preventDefault();
    var targetButton = event.target;
    targetButton.style.height = '8px';
}

function drop(event) {
    event.preventDefault();  //tasidigim butonun pozisyon degistirmesini engeller
    var data = event.dataTransfer.getData("text");
    var draggedButton = document.getElementById(data);   //tasidigim buton  
    var dropTarget = event.target;                       //alici buton

    if (dropTarget.classList.contains('notGizliButon') && dropTarget.id !== draggedButton.id && dropTarget.value !== draggedButton.value ) {
        dropTarget.style.height = '5px';
        Not_Tasi_Post("Yanina_Tasi", draggedButton, dropTarget);
    }
    else if(dropTarget.classList.contains('notButon') && dropTarget.id !== draggedButton.id && dropTarget.value !== draggedButton.value ){
        dropTarget.style.height = '5px';
        Not_Tasi_Post("Altina_Tasi", draggedButton, dropTarget);
    }
    else if(dropTarget.classList.contains('notGizliButon')){
        dropTarget.style.height = '5px';
    }
}

function Not_Tasi_Post(_tasimaTuru, _draggedButton, _dropTarget){   //tasimaTuru = altnota - yanina
    // Gizli bir form oluşturma
    var form = document.createElement('form');
    form.setAttribute('method', 'post');
    form.setAttribute('action', 'account.php'); 

    // Gizli input alanları oluşturma ve form içine yerleştirme
    var input1 = document.createElement('input');
    input1.setAttribute('type', 'hidden');
    input1.setAttribute('name', _tasimaTuru);
    input1.setAttribute('value', 'true');
    form.appendChild(input1);

    var input2 = document.createElement('input');
    input2.setAttribute('type', 'hidden');
    input2.setAttribute('name', 'tasidigim_not');
    input2.setAttribute('value', _draggedButton.value);
    form.appendChild(input2);

    var input3 = document.createElement('input');
    input3.setAttribute('type', 'hidden');
    input3.setAttribute('name', 'alici_not');
    input3.setAttribute('value', _dropTarget.value);
    form.appendChild(input3);

    // Formu sayfaya ekleyip otomatik olarak gönderme
    document.body.appendChild(form);
    setTimeout(function() {
        form.submit();
    }, 50);
}