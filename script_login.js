const loginForm = document.getElementById("loginForm");
const signupForm = document.getElementById("signupForm");
const linfo_lbl = document.getElementById("linfo_lbl");
const cinfo_lbl = document.getElementById("cinfo_lbl");
const page_status = document.getElementById("page_status");

document.addEventListener('DOMContentLoaded', function () {
    if(page_status.textContent == "signup"){
        loginForm.style.display="none";
        signupForm.style.display="flex";
    }
});

function FormDegistir(_formdegis){
    if (_formdegis) {
        loginForm.style.display="none";
        signupForm.style.display="flex";
        linfo_lbl.textContent = "";
        cinfo_lbl.textContent = "";
        page_status.textContent = "signup";
        loginForm.reset();
    }
    else{
        loginForm.style.display="flex";
        signupForm.style.display="none";
        linfo_lbl.textContent = "";
        cinfo_lbl.textContent = "";
        page_status.textContent = "login";
        signupForm.reset();
    }
}
