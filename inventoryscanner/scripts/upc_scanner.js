document.addEventListener("DOMContentLoaded", function () {
    const upcInput = document.getElementById("upcInput");
    const itemDetails = document.getElementById("itemDetails");
    const upcError = document.getElementById("upcError");
    const itemName = document.getElementById("itemName");
    const itemCategory = document.getElementById("itemCategory");
    
    upcInput.addEventListener("keypress", function (event) {
        if (event.key === "Enter") {
            event.preventDefault();
            fetchItemDetails(upcInput.value.trim());
        }
    });

    function fetchItemDetails(upc) {
        if (!upc) {
            showError("Please enter a UPC.");
            return;
        }
        
        upcError.classList.add("d-none");
        fetch(`../ajax/upc_lookup.php?upc=${upc}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    itemName.value = data.name || "";
                    itemCategory.value = data.category || "";
                    itemDetails.classList.remove("d-none");
                } else {
                    showError(data.error || "UPC not found.");
                }
            })
            .catch(() => showError("Error fetching UPC data."));
    }

    function showError(message) {
        upcError.textContent = message;
        upcError.classList.remove("d-none");
        itemDetails.classList.add("d-none");
    }
});
