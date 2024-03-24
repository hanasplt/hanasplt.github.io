/* log out*/
function handleLogout(){
    window.location.href = "index.html";
}

    document.getElementById('logoutButton').onclick = handleLogout;

/*date*/
var currentDate = new Date();
var options = {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit'
};
var formattedDate = currentDate.toLocaleDateString('en-US', options);
document.getElementById('date').innerHTML = formattedDate;


/*menu*/
document.addEventListener("DOMContentLoaded", function() {
    const dash = document.getElementById('dash');
    const menu = document.querySelector('.menu');
    const icon = document.getElementById('menuIcon');
    
    // Toggle menu visibility
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
    
    // Add event listener to toggle menu when clicked
    dash.addEventListener('click', toggleMenu);
    
    // Add event listener to the icon to close the menu
    icon.addEventListener('click', function(event) {
        event.stopPropagation(); // Prevents the click event from propagating to the 'dash' div
        toggleMenu();
    });
});

