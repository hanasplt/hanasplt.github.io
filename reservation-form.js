//confirmation box show
let confirmButton = document.querySelector('#confirmID');
let confirmBox = document.querySelector('.confirm-box-container');
let noButton = document.querySelector('#noBtnID');
let yesButton = document.querySelector('#yesBtnID');
let date = document.querySelector("#purchaseDateID");
let quantity = document.querySelector("#quantityID");
let reservationForm = document.getElementById("reservationForm");

reservationForm.addEventListener('submit', function(event) {
event.preventDefault();

 let quantityInput = quantity.value.trim();
 let dateInput = date.value.trim();
 let isValid = true;

 if (dateInput === "") {
  alert("Please select the appointment date.");
  isValid = false;
 }
 else if (quantityInput === "") {
  alert("Please enter a quantity.");
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
  document.getElementById("reservationForm").submit();
};
//

//multiply the price and quantity
const quantityInput = document.getElementById('quantityID');
const priceElement = document.getElementById('priceID');
const totalPaymentElement = document.getElementById('totalPayment');

const price = parseFloat(priceElement.textContent);

function updateTotalPayment() {
  const quantity = parseInt(quantityInput.value);
  if (!isNaN(quantity)) {
    totalPayment = price * quantity;
    totalPaymentElement.textContent = `Total Payment: ${totalPayment.toFixed(2)}`;
  } else {
    totalPaymentElement.textContent = `Total Payment: 0.00`;
  }
}

updateTotalPayment();

quantityInput.addEventListener('input', updateTotalPayment);
//



document.addEventListener("DOMContentLoaded", function() {
  const cancel = document.getElementById('cancelID');

  cancel.addEventListener('click', function() {
    window.location.href = "cp-homepage.html";
  });
});

