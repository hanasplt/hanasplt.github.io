//header
document.addEventListener('DOMContentLoaded', function() {
 var vivobestSeller = document.querySelector('.vivo-best-seller');
 var vivonewArrivals = document.querySelector('.vivo-new-arrivals2');
 var vivoProductContainer = document.querySelector('#vivo-product-container');
 var vivoProductContainer2 = document.querySelector('#vivo-product-container2');
 var tecnobestSeller = document.querySelector('.tecno-best-seller');
 var tecnonewArrivals = document.querySelector('.tecno-new-arrivals2');
 var tecnoProductContainer = document.querySelector('#tecno-product-container');
 var tecnoProductContainer2 = document.querySelector('#tecno-product-container2');
 let dropdown = document.getElementById('dropdown');
 let vivoContainer = document.getElementById('vivo-product-container');
 let tecnoContainer = document.getElementById('tecno-product-container');

 dropdown.addEventListener('change', function() {
     let selectedOption = dropdown.value;

     if (selectedOption === 'vivo') {
         vivoContainer.style.display = 'block';
         vivoProductContainer2.style.display = 'none';
         tecnoContainer.style.display = 'none';
         tecnoProductContainer2.style.display = 'none';
     } else if (selectedOption === 'tecno') {
         vivoContainer.style.display = 'none';
         vivoProductContainer2.style.display = 'none';
         tecnoContainer.style.display = 'block';
         tecnoProductContainer2.style.display = 'none';
     } else {
         // Handle other options if needed
     }
 });
 

 vivobestSeller.addEventListener('click', function(event) {
     event.preventDefault();
     vivoProductContainer.style.display = 'none';
     vivoProductContainer2.style.display = 'block';
 });

 vivonewArrivals.addEventListener('click', function(event) {
     event.preventDefault();
     vivoProductContainer2.style.display = 'none';
     vivoProductContainer.style.display = 'block';
 });
 tecnobestSeller.addEventListener('click', function(event) {
     event.preventDefault();
     tecnoProductContainer.style.display = 'none';
     tecnoProductContainer2.style.display = 'block';
 });

 tecnonewArrivals.addEventListener('click', function(event) {
     event.preventDefault();
     tecnoProductContainer2.style.display = 'none';
     tecnoProductContainer.style.display = 'block';
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

go.onclick = () => {
   window.location.href = "my-profile.html";
}

go2.onclick = () => {
    window.location.href = "sign-in.html";
}