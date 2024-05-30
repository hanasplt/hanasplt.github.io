//header
document.addEventListener('DOMContentLoaded', function() {
    var phonecasebestSeller = document.querySelector('.phone-case-best-seller');
    var phonecasenewArrivals = document.querySelector('.phone-case-new-arrivals2');
    var phonecaseProductContainer = document.querySelector('#phone-case-product-container');
    var phonecaseProductContainer2 = document.querySelector('#phone-case-product-container2');
    var chargerbestSeller = document.querySelector('.charger-best-seller');
    var chargernewArrivals = document.querySelector('.charger-new-arrivals2');
    var chargerProductContainer = document.querySelector('#charger-product-container');
    var chargerProductContainer2 = document.querySelector('#charger-product-container2');
    let dropdown = document.getElementById('dropdown');
    let phonecaseContainer = document.getElementById('phone-case-product-container');
    let chargerContainer = document.getElementById('charger-product-container');

    dropdown.addEventListener('change', function() {
        let selectedOption = dropdown.value;

        if (selectedOption === 'phone-case') {
            phonecaseContainer.style.display = 'block';
            phonecaseProductContainer2.style.display = 'none';
            chargerContainer.style.display = 'none';
            chargerProductContainer2.style.display = 'none';
        } else if (selectedOption === 'charger') {
            phonecaseContainer.style.display = 'none';
            phonecaseProductContainer2.style.display = 'none';
            chargerContainer.style.display = 'block';
            chargerProductContainer2.style.display = 'none';
        } else {
        }
    });
    

    phonecasebestSeller.addEventListener('click', function(event) {
        event.preventDefault();
        phonecaseProductContainer.style.display = 'none';
        phonecaseProductContainer2.style.display = 'block';
    });

    phonecasenewArrivals.addEventListener('click', function(event) {
        event.preventDefault();
        phonecaseProductContainer2.style.display = 'none';
        phonecaseProductContainer.style.display = 'block';
    });
    chargerbestSeller.addEventListener('click', function(event) {
        event.preventDefault();
        chargerProductContainer.style.display = 'none';
        chargerProductContainer2.style.display = 'block';
    });

    chargernewArrivals.addEventListener('click', function(event) {
        event.preventDefault();
        chargerProductContainer2.style.display = 'none';
        chargerProductContainer.style.display = 'block';
    });
});
//

//header
document.addEventListener('DOMContentLoaded', function() {
    let hamburger = document.querySelector('#menu-icon');
    let menu = document.querySelector('.menu');
    let account = document.querySelector('#account-btn');
    let accountContainer = document.querySelector('.account-container');
    let x = document.querySelector('.x');
   
    hamburger.onclick = () => {
     if (!accountContainer.classList.contains('open')) {
      hamburger.style.display = 'none';
      x.style.display = 'block';
      menu.classList.toggle('open');
     }
     else {
      accountContainer.classList.remove('open');
      hamburger.style.display = 'block';
      x.style.display = 'none';
      menu.classList.toggle('open');
     }
    }
   
    x.onclick = () => {
     hamburger.style.display = 'block';
     x.style.display = 'none';
     menu.classList.remove('open');
    }
   
    account.onclick = () => {
     if (!menu.classList.contains('open')) {
      accountContainer.classList.toggle('open');
     }
     else {
      menu.classList.remove('open');
      accountContainer.classList.toggle('open');
     }
    }
   });
   //

let go = document.querySelector('#myProfile');
let go2 = document.querySelector('#logOut');
let go3 = document.querySelector('#overview');

go.onclick = () => {
   window.location.href = "my-profile.html";
}

go2.onclick = () => {
    window.location.href = "sign-in.html";
}

go3.onclick = () => {
    window.location.href = "accessories-overview.html";
}
