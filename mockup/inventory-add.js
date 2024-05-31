document.addEventListener("DOMContentLoaded", function() {
    const dash = document.getElementById('dash');
    const menu = document.querySelector('.menu');
    const icon = document.getElementById('menuIcon');

    menu.style.display = 'none';
    
    function toggleMenu() {
        if (menu.style.display === 'none') {
            menu.style.display = 'block';
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');
        } else {
            menu.style.display = 'none';
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        }
    }
    
    dash.addEventListener('click', toggleMenu);
    
    icon.addEventListener('click', function(event) {
        event.stopPropagation();
        toggleMenu();
    });
})

function previewImage(event) {
    var reader = new FileReader();
    reader.onload = function() {
        var output = document.getElementById('image-preview');
        output.src = reader.result;
        output.style.display = 'block';
    }
    reader.readAsDataURL(event.target.files[0]);
}

function validateForm() {
    const fileInput = document.getElementById('image-upload');
    const filePath = fileInput.value;
    const allowedExtensions = /(\.jpg|\.jpeg)$/i;

    if (!allowedExtensions.exec(filePath)) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid file type',
            text: 'Please upload a JPEG file.',
            showConfirmButton: true
        });
        fileInput.value = '';
        return false;
    }

    const fileSize = fileInput.files[0].size;
    if (fileSize > 1000000) { // 1MB
        Swal.fire({
            icon: 'error',
            title: 'File too large',
            text: 'Please upload a file smaller than 1MB.',
            showConfirmButton: true
        });
        fileInput.value = '';
        return false;
    }

    return true;
}


var currentDate = new Date();
var options = {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit'
};
var formattedDate = currentDate.toLocaleDateString('en-US', options);
document.getElementById('date').innerHTML = formattedDate;