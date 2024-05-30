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

//images
document.addEventListener('DOMContentLoaded', function() {
 var next = document.querySelector('.next-btn');
 var previous = document.querySelector('.previous-btn2');
 var next2 = document.querySelector('.next-btn2');
 var previous2 = document.querySelector('.previous-btn3');
 var imgContainer = document.querySelector('.image');
 var imgContainer2 = document.querySelector('.image2');
 var imgContainer3 = document.querySelector('.image3');

 next.addEventListener('click', function(event) {
  event.preventDefault();
  imgContainer.style.display = 'none';
  imgContainer2.style.display = 'flex';
 });

 previous.addEventListener('click', function(event) {
  event.preventDefault();
  imgContainer2.style.display = 'none';
  imgContainer.style.display = 'flex';
 });

 next2.addEventListener('click', function(event) {
  event.preventDefault();
  imgContainer2.style.display = 'none';
  imgContainer3.style.display = 'flex';
 });

 previous2.addEventListener('click', function(event) {
  event.preventDefault();
  imgContainer3.style.display = 'none';
  imgContainer2.style.display = 'flex';
});
});

//go to reservation form
let preOrder = document.querySelector('#preOrderID');

preOrder.onclick = () => {
  window.location.href = "reservation-form.html";
}

let go = document.querySelector('#myProfile');
let go2 = document.querySelector('#logOut');
let back = document.querySelector('.back-btn');


go.onclick = () => {
   window.location.href = "my-profile.html";
}

go2.onclick = () => {
    window.location.href = "sign-in.html";
}

back.onclick = () => {
  window.location.href = "cp-homepage.html";
}
