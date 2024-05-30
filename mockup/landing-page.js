//hamburger menu when 1000px
let hamburger = document.querySelector('#menu-icon');
let menu = document.querySelector('.menu');
let x = document.querySelector('.x');

hamburger.onclick = () => {
 hamburger.style.display = 'none';
 x.style.display = 'block';
 menu.classList.add('open');
}

x.onclick = () => {
 hamburger.style.display = 'block';
 x.style.display = 'none';
 menu.classList.remove('open');
}
//


//cp new arrivals and best seller
document.addEventListener('DOMContentLoaded', function() {
 var cpbestSeller = document.querySelector('.cp-best-seller');
 var cpnewArrivals = document.querySelector('.cp-new-arrivals2');
 var cpProductContainer = document.querySelector('.cp-product-container');
 var cpProductContainer2 = document.querySelector('.cp-product-container2');

 cpbestSeller.addEventListener('click', function(event) {
     event.preventDefault();
     cpProductContainer.style.display = 'none';
     cpProductContainer2.style.display = 'block';
 });

 cpnewArrivals.addEventListener('click', function(event) {
     event.preventDefault();
     cpProductContainer2.style.display = 'none';
     cpProductContainer.style.display = 'block';
 });
});
//

//accessories new arrivals and best seller
document.addEventListener('DOMContentLoaded', function() {
 var accbestSeller = document.querySelector('.acc-best-seller');
 var accnewArrivals = document.querySelector('.acc-new-arrivals2');
 var accProductContainer = document.querySelector('.acc-product-container');
 var accProductContainer2 = document.querySelector('.acc-product-container2');

 accbestSeller.addEventListener('click', function(event) {
     event.preventDefault();
     accProductContainer.style.display = 'none';
     accProductContainer2.style.display = 'block';
 });

 accnewArrivals.addEventListener('click', function(event) {
     event.preventDefault();
     accProductContainer2.style.display = 'none';
     accProductContainer.style.display = 'block';
 });
});
//


