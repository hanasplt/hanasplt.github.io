function confirmDelete() {
    Swal.fire({
        title: 'Are you sure to delete?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Go Back'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: 'Successfully Deleted!',
                confirmButtonText: 'Go Back'
            }).then(() => {
                window.location.href = 'inventory.html';
            });
        }
    });
}
document.addEventListener("DOMContentLoaded", function() {
    var saveBtn = document.querySelector(".btn-save");

    saveBtn.addEventListener("click", function(event) {
        event.preventDefault(); 

        Swal.fire({
            title: 'Save Changes?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'Go Back',
        }).then((result) => {
            if (result.isConfirmed) {
                var color = document.querySelector("input[name='color']").value;
                var quantity = document.querySelector("input[name='quantity']").value;
                var price = document.querySelector("input[name='price']").value;
                var specs = document.querySelector("input[name='specs']").value;
                var imageInput = document.querySelector("input[name='image']");
                var image = imageInput.files[0];
                var imageName = imageInput.value.split('\\').pop();

                if (!color || !quantity || !price || !specs || !image) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Details Incomplete!',
                        confirmButtonText: 'Go Back',
                    });
                    return; 
                }

                console.log("Form data:", {
                    color: color,
                    quantity: quantity,
                    price: price,
                    specs: specs,
                    image: imageName
                });

                Swal.fire({
                    icon: 'success',
                    title: 'Successfully Edited!',
                    confirmButtonText: 'Yes',
                }).then(function() {
                    window.location.href = "inventory.html";
                });
            }
        });
    });
});
