//display data from appointment form
const params = new URLSearchParams(window.location.search);
const unitIssue = params.get('unitIssue');
const cpUnit = params.get('cpUnit');
const appDate = params.get('appointmentDate');
const time = params.get('time');

const outputElement = document.getElementById('displayUnitIssue');
outputElement.textContent = unitIssue;

const outputElement2 = document.getElementById('displayCpUnit');
outputElement2.textContent = cpUnit;

const outputElement3 = document.getElementById('displayAppDate');
outputElement3.textContent = appDate;

const outputElement4 = document.getElementById('displayTime');
outputElement4.textContent = time;