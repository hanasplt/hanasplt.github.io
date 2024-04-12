document.getElementById("upcoming").addEventListener("click", function() {
 document.getElementById("past-con").style.display = "none";
 document.getElementById("reserve2").style.display = "none";
});

document.getElementById("past").addEventListener("click", function() {
 document.getElementById("upcoming-con").style.display = "none";
 document.getElementById("past-con").style.display = "block";
 document.getElementById("reserve1").style.display = "none";
 document.getElementById("reserve2").style.display = "block";
});

document.getElementById("upcoming-2").addEventListener("click", function() {
 document.getElementById("past-con").style.display = "none";
 document.getElementById("upcoming-con").style.display = "block";
 document.getElementById("reserve1").style.display = "block";
 document.getElementById("reserve2").style.display = "none";
});

//confirmation box show
let cancelreserve = document.querySelector('#cancel');
let cancelreserve2 = document.querySelector('#cancel2');
let confirmBox = document.querySelector('.warning');
let check = document.querySelector('.check');
let noButton = document.querySelector('#noBtnID');
let yesButton = document.querySelector('#yesBtnID');
let okButton = document.querySelector('#okBtnID');

cancelreserve.onclick = () => {
confirmBox.classList.add('open');
}
cancelreserve2.onclick = () => {
 confirmBox.classList.add('open');
}

//if no
noButton.onclick = () => {
confirmBox.classList.remove('open');
}
//if yes open check container
yesButton.onclick = () => {
confirmBox.classList.remove('open');
check.classList.add('open');
}
//if ok
okButton.onclick = () => {
check.classList.remove('open');
}

//


let back= document.querySelector('.back');

back.onclick = () => {
window.location.href = "cp-homepage.html"; 
}
