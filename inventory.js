document.addEventListener("DOMContentLoaded", function() {
    var dropdowns = ["dropdown", "dropdown1"];

    dropdowns.forEach(function(id) {
        var dropdown = document.getElementById(id);
        
        dropdown.addEventListener("change", function() {
            console.log("Dropdown change event triggered for:", id);
            
            var selectedOption = dropdown.options[dropdown.selectedIndex];
            console.log("Selected option:", selectedOption);
            
            var selectedHref = selectedOption.getAttribute("data-href");
            console.log("Selected href:", selectedHref);
            
            if (selectedHref && selectedHref !== "#") {
                console.log("Navigating to:", selectedHref);
                window.location.href = selectedHref;
            } else {
                console.log("No valid href found");
            }
        });
    });
});
document.addEventListener("DOMContentLoaded", function() {
    var dropdowns = ["dropdown2"];

    dropdowns.forEach(function(id) {
        var dropdown = document.getElementById(id);
        
        dropdown.addEventListener("change", function() {
            console.log("Dropdown change event triggered for:", id);
            
            var selectedOption = dropdown.options[dropdown.selectedIndex];
            console.log("Selected option:", selectedOption);
            
            var selectedHref = selectedOption.getAttribute("data-href");
            console.log("Selected href:", selectedHref);
            
            if (selectedHref && selectedHref !== "#") {
                console.log("Navigating to:", selectedHref);
                window.location.href = selectedHref;
            } else {
                console.log("No valid href found");
            }
        });
    });
});