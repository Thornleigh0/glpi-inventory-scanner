<?php
include ('../../../inc/includes.php');

Html::header("Inventory Scanner", $_SERVER['PHP_SELF'], "plugins", "inventoryscanner");

// Security check
Session::checkLoginUser();

// Display UI
?>
<div class="card">
    <div class="card-header"><h3>Scan New Item</h3></div>
    <div class="card-body">
        <label for="upcInput">Enter UPC:</label>
        <input type="text" id="upcInput" class="form-control" autofocus>
        <small id="upcError" class="text-danger d-none"></small>
        
        <div id="itemDetails" class="mt-3 d-none">
            <label for="itemName">Item Name:</label>
            <input type="text" id="itemName" class="form-control">

            <label for="itemCategory">Category:</label>
            <input type="text" id="itemCategory" class="form-control">

            <label for="serialNumber">Serial Number:</label>
            <input type="text" id="serialNumber" class="form-control">
        </div>
    </div>
</div>

<script src="../scripts/upc_scanner.js"></script>

<?php
Html::footer();
?>
