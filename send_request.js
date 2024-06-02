function SendForm(_FormID, _operation) {
    var form = document.getElementById(_FormID);
    var formButtons = form.querySelectorAll('button, input[type="button"], input[type="submit"]');
    var formData = new FormData(form);

    // Form elemanlarını devre dışı bırak
    formButtons.forEach(function(button) {
        button.disabled = true;
    });

    var xhr = new XMLHttpRequest();
    xhr.open("POST", site_url + 'send_response.php?operation=' + _operation);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            formButtons.forEach(function(button) {
                button.disabled = false;
            });
            if (xhr.status === 200) {
                //console.log(xhr.responseText);
                var responseData = "";
                try {
                    responseData = JSON.parse(xhr.responseText);
                    if (responseData.error) {
                        console.log("ERROR: " + responseData.error);
                    } else if (responseData.success) {
                        console.log("SUCCESS: " + responseData.success);
                        form.reset();  //formun icerigini siler
                        Response_Islem(_operation);
                    }
                } catch (error) {
                    console.log("BIG ERROR: " + xhr.responseText);
                }
            } else {
                console.error('HATA: ' + xhr.status);
            }
        }
    };
    xhr.send(formData);
}

