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
   hamburger.style.display = 'none';
   x.style.display = 'block';
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