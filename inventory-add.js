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
                var type = document.querySelector("select[name='type']").value;
                var prodId = document.querySelector("input[name='prodid']").value;
                var brand = document.querySelector("input[name='brand']").value;
                var model = document.querySelector("input[name='model']").value;
                var color = document.querySelector("input[name='color']").value;
                var quantity = document.querySelector("input[name='quantity']").value;
                var price = document.querySelector("input[name='price']").value;
                var specs = document.querySelector("input[name='specs']").value;
                var image = document.querySelector("input[name='image']").files[0];

                if (!type || !prodId || !brand || !model || !color || !quantity || !price || !specs || !image) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Details Incomplete!',
                        confirmButtonText: 'Go Back',
                    });
                    return; 
                }

                console.log("Form data:", {
                    type: type,
                    prodId: prodId,
                    brand: brand,
                    model: model,
                    color: color,
                    quantity: quantity,
                    price: price,
                    specs: specs,
                    image: image.name 
                });

                Swal.fire({
                    icon: 'success',
                    title: 'Successfully Added!',
                    confirmButtonText: 'Yes',
                }).then(function() {
                    window.location.href = "inventory.html";
                });
            }
        });
    });
});
