//display data from reservation form
const params = new URLSearchParams(window.location.search);
const unitColor = params.get('unitColor');
const purDate = params.get('purchaseDate');
const quan = params.get('quantity');
const totalPay = params.get('totalPayment');

const outputElement = document.getElementById('displayUnitColor');
outputElement.textContent = unitColor;

const outputElement2 = document.getElementById('displayPurchaseDate');
outputElement2.textContent = purDate;

const outputElement3 = document.getElementById('displayQuantity');
outputElement3.textContent = quan;

const outputElement4 = document.getElementById('displayTotalPayment');
outputElement4.textContent = totalPay;
//

let okay = document.querySelector('.okay-btn');

okay.onclick = () => {
 window.location.href = "cp-homepage.html";
}