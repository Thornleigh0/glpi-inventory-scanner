<?php
include ('../../../inc/includes.php');

Session::checkLoginUser();

// Ensure user has at least READ rights
if (!Session::haveRight("plugin_inventoryscanner", READ)) {
    Html::displayRightError();
}

Html::header("Inventory Scanner", $_SERVER['PHP_SELF'], "plugins", "inventoryscanner");
?>
<div class="card">
    <div class="card-header"><h3>Scan New Item</h3></div>
    <div class="card-body">
        <label for="upcInput">Enter UPC:</label>
        <input type="text" id="upcInput" class="form-control" autofocus>
        <small id="upcError" class="text-danger d-none"></small>
        <div id="apiLimitInfo" class="text-muted mt-2"></div>
        
        <div id="itemDetails" class="mt-3 d-none">
            <label for="itemName">Item Name:</label>
            <input type="text" id="itemName" class="form-control">

            <label for="itemCategory">Category:</label>
            <select id="itemCategory" class="form-control">
                <option value="">-- Select Category --</option>
                <option value="computer">Computer</option>
                <option value="monitor">Monitor</option>
                <option value="printer">Printer</option>
                <option value="network">Network Equipment</option>
                <option value="peripheral">Peripheral</option>
                <option value="phone">Phone</option>
                <option value="software">Software</option>
            </select>
            
            <label for="serialNumbers">Enter Serial Numbers (One per line):</label>
            <textarea id="serialNumbers" class="form-control" rows="4" placeholder="Enter each serial number on a new line"></textarea>
            
            <button id="submitItem" class="btn btn-primary mt-3">Submit</button>
            <small id="submitMessage" class="text-success d-none">Items added successfully!</small>
        </div>
    </div>
</div>

<script src="../scripts/upc_scanner.js"></script>

<?php
Html::footer();
?>