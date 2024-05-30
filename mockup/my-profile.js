//confirmation box show
let deleteAccount = document.querySelector('#deleteAccountID');
let confirmBox = document.querySelector('.warning');
let noButton = document.querySelector('.no-btn');
let yesButton = document.querySelector('.yes-btn');
let okButton = document.querySelector('#okBtnID');

deleteAccount.onclick = () => {
  confirmBox.classList.add('open');
}
//if no
noButton.onclick = () => {
  confirmBox.classList.remove('open');
}
//if yes open check container
yesButton.onclick = () => {
  window.location.href = "sign-up.html";
}
//

//go to edit profile
let editProfileButton = document.querySelector('#editProfileID');
let homepage = document.querySelector('#back');

editProfileButton.onclick = () => {
  window.location.href = "edit-profile.html";
}

homepage.onclick = () => {
  window.location.href = "cp-homepage.html";
}
//

//display data from edit-profile
const params = new URLSearchParams(window.location.search);
const firstName = params.get('firstName');
const lastName = params.get('lastName');
const phoneNum = params.get('phoneNum');
const email = params.get('email');

const outputElement = document.getElementById('displayFirstName');
outputElement.textContent = firstName;

const outputElement2 = document.getElementById('displayLastName');
outputElement2.textContent = lastName;

const outputElement3 = document.getElementById('displayPhoneNum');
outputElement3.textContent = phoneNum;

const outputElement4 = document.getElementById('displayEmail');
outputElement4.textContent = email;
//

//remove client old info when edit in edit-profile
const displayInfos = document.querySelectorAll('.display-info');

displayInfos.forEach(displayInfo => {
  const displaySpan = displayInfo.querySelector('span');
  const nameLabel = displayInfo.querySelector('.name');

    if (displaySpan.textContent.trim() != "") {
      nameLabel.remove();
    }
});
//

//display photo
const imgData = localStorage.getItem('uploadedImage');
  if (imgData) {
    document.getElementById('uploadedImageID').src = imgData;
  }
//


