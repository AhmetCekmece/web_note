// document.addEventListener('DOMContentLoaded', function () {
//     var notlar = document.querySelectorAll('.not-duzenle');
//     var baslikInput = document.getElementById('duzenle_baslik');
//     var icerikTextarea = document.getElementById('duzenle_icerik');
//     var duzenlenotuindex = document.getElementById('duzenle_notuindex');

//     notlar.forEach(function (link) {
//         link.addEventListener('click', function (e) {
//             e.preventDefault();
//             var baslik = this.getAttribute('data-baslik');
//             var icerik = this.getAttribute('data-icerik');
//             var notuindex = this.getAttribute('data-notuindex');
//             baslikInput.value = baslik;
//             icerikTextarea.value = icerik;
//             duzenlenotuindex.value = notuindex; 
//         });
//     });
// });

document.querySelectorAll(".not-duzenle").forEach(function(element) {
    element.addEventListener("click", function(event) {
        event.preventDefault(); // Not başlığına tıklamayı engelle
        var duzenlenotuindex = document.getElementById('duzenle_notuindex');
        duzenlenotuindex.value = this.getAttribute('data-notuindex');
        document.querySelector("[name='Not_Goster']").click(); // "Not_Goster" butonuna tıklamış gibi işlem yap
    });
});