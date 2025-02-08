document.addEventListener("DOMContentLoaded", function () {
    const upcInput = document.getElementById("upcInput");
    const itemDetails = document.getElementById("itemDetails");
    const upcError = document.getElementById("upcError");
    const itemName = document.getElementById("itemName");
    const itemCategory = document.getElementById("itemCategory");
    const serialNumbers = document.getElementById("serialNumbers");
    const submitButton = document.getElementById("submitItem");
    const submitMessage = document.getElementById("submitMessage");
    const apiLimitInfo = document.createElement("div");
    apiLimitInfo.classList.add("mt-3", "text-muted");
    upcInput.parentNode.appendChild(apiLimitInfo);

    upcInput.addEventListener("keypress", function (event) {
        if (event.key === "Enter") {
            event.preventDefault();
            fetchItemDetails(upcInput.value.trim());
        }
    });

    submitButton.addEventListener("click", function () {
        submitItem();
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
                    if (!data.category) {
                        itemCategory.classList.remove("d-none");
                    }
                    itemDetails.classList.remove("d-none");
                    updateApiLimitInfo(data.api_limits);
                } else {
                    showError(data.error || "UPC not found.");
                    updateApiLimitInfo(data.api_limits);
                }
            })
            .catch(() => showError("Error fetching UPC data."));
    }

    function submitItem() {
        const serials = serialNumbers.value.split("\n").map(s => s.trim()).filter(s => s !== "");
        if (serials.length === 0) {
            showError("Please enter at least one serial number.");
            return;
        }

        const itemData = {
            name: itemName.value.trim(),
            category: itemCategory.value.trim(),
            serialNumbers: serials,
            upc: upcInput.value.trim()
        };

        if (!itemData.category) {
            showError("Please select a category before submitting.");
            return;
        }

        fetch(`../ajax/add_item.php`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(itemData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                submitMessage.classList.remove("d-none");
                submitMessage.textContent = "Items added successfully!";
            } else {
                showError(data.error || "Failed to add items.");
            }
        })
        .catch(() => showError("Error submitting items."));
    }

    function updateApiLimitInfo(apiLimits) {
        if (apiLimits) {
            apiLimitInfo.innerHTML = `Remaining Lookups: ${apiLimits.lookups_remaining ?? 'N/A'} | Reset Time: ${apiLimits.reset_time ? new Date(apiLimits.reset_time * 1000).toLocaleString() : 'N/A'}`;
            if (apiLimits.lookups_remaining !== null && apiLimits.lookups_remaining <= 5) {
                apiLimitInfo.classList.add("text-danger");
            } else {
                apiLimitInfo.classList.remove("text-danger");
            }
        }
    }

    function showError(message) {
        upcError.textContent = message;
        upcError.classList.remove("d-none");
        itemDetails.classList.add("d-none");
    }
});
