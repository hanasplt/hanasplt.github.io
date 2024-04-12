//back to my profile page
let backButton = document.querySelector('#backButtonID');

backButton.onclick = () => {
 window.location.href = "my-profile.html";
}
//

//upload image
let profilePic = document.querySelector('#profilePic');
let inputFile = document.querySelector('#inputFile');

inputFile.onchange = function(){
 profilePic.src = URL.createObjectURL(inputFile.files[0]);
}
//

//display image in edit-profile
document.getElementById('inputFile').addEventListener('change', function(event) {
 const file = event.target.files[0];
 const reader = new FileReader();
 reader.onload = function(e) {
  const imgData = e.target.result;
  localStorage.setItem('uploadedImage', imgData);
 }
 reader.readAsDataURL(file);
});
//

//display photo
const imgData = localStorage.getItem('uploadedImage');
  if (imgData) {
    document.getElementById('profilePic').src = imgData;
  }
//

//form validation
let saveChanges = document.querySelector("#saveID");
let form = document.getElementById("editProfile");
let savedChangesBox = document.querySelector('.saved-changes');
let okButton = document.querySelector('#okBtnID');

form.addEventListener('submit', function(event) {
 event.preventDefault();

 let firstName = document.querySelector("#firstNameID").value.trim();
 let lastName = document.querySelector("#lastNameID").value.trim();
 let phoneNumber = document.querySelector("#phoneNumberID").value.trim();
 let email = document.querySelector("#emailID").value.trim();

 let isValid = true;

 //for first name validation
 if (firstName === "") {
  alert("Please enter a first name.");
  isValid = false;
 }
 
 //for last name validation
 if (lastName === "") {
  alert("Please enter a last name.");
  isValid = false;
 }

 //for phone number validation
 if (phoneNumber === "") {
  alert("Please enter a phone number.");
  isValid = false;
 } else if (!phoneNumber.startsWith('0')) {
  alert("Phone number must start with '0'.");
  isValid = false;
 } else if (phoneNumber.length !== 11) {
  alert("Phone number must have a length of 11 digits.");
  isValid = false;
 }

 //for email validation
 if (email === "") {
  alert("Please enter an email address.");
  isValid = false;
 } else if (!isValidEmail(email)) {
  alert("Please enter a valid email address.");
  isValid = false;
 }

 if (isValid) {
  savedChangesBox.classList.add('open');
 }
});

//function to validate email format
function isValidEmail(email) {
 let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
 return emailPattern.test(email);
}
//

//if ok and the form inputs are correct 
okButton.onclick = () => {
 const imgData = localStorage.getItem('uploadedImage');
 if (imgData) {
  document.getElementById("editProfile").submit();
 }
};
//

//change pass button
let changePassButton = document.querySelector('#changePassButton');

changePassButton.onclick = () => {
 window.location.href = "change-password.html";
}
//
