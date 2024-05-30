//confirmation box show and form validation
let confirmButton = document.querySelector('#confirmID');
let confirmBox = document.querySelector('.confirm-box-container');
let noButton = document.querySelector('#noBtnID');
let yesButton = document.querySelector('#yesBtnID');
let dateInput = document.querySelector("#appointmentDateID");
let cpUnitInput = document.querySelector("#cpUnitID");
let unitIssueInput = document.querySelector("#unitIssueID");
let appointmentForm = document.getElementById("appointmentForm");

appointmentForm.addEventListener('submit', function(event) {
  event.preventDefault();

  let cpUnit = cpUnitInput.value.trim();
  let unitIssue = unitIssueInput.value.trim();
  let date = dateInput.value.trim();
  let isValid = true;

  if (cpUnit === "") {
    alert("Please enter brand & model unit.");
    isValid = false;
  } else if (unitIssue === "") {
    alert("Please enter the issue of the unit.");
    isValid = false;
  } else if (date === "") {
    alert("Please select the appointment date.");
    isValid = false;
  }

  if (isValid) {
    confirmBox.classList.add('open');
  }
});

//if no
noButton.onclick = () => {
  confirmBox.classList.remove('open');
};

//if yes
yesButton.onclick = () => {
  document.getElementById("appointmentForm").submit();
};
//





document.addEventListener("DOMContentLoaded", function() {
  const cancel = document.getElementById('cancelID');

  cancel.addEventListener('click', function() {
    window.location.href = "services-homepage.html";
  });
});

