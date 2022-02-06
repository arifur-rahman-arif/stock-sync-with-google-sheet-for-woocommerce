<div class="wsmgs_description">

    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
        <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
            <path
                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
        </symbol>
        <symbol id="info-fill" fill="currentColor" viewBox="0 0 16 16">
            <path
                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
        </symbol>
        <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
            <path
                d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
        </symbol>
    </svg>

    <div class="alert alert-primary d-flex align-items-center" role="alert">
        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Info:">
            <use xlink:href="#info-fill" />
        </svg>
        <div class="bot_info">
            Give this bot
            <span class="bot_mail">
                <code>wsmgs-plugin@wsmgs-plugin-338313.iam.gserviceaccount.com</code>
            </span> ID editor
            access in your Google Sheet for WooCommerce products sync.
        </div>
    </div>

    <div class="alert alert-primary d-flex align-items-center" role="alert">
        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Info:">
            <use xlink:href="#info-fill" />
        </svg>
        <div class="bot_info">
            Copy this script code & add this in your Google Sheet AppScript
            <a href="#" class="tutorial_video">Watch the tutorial video</a>
        </div>
    </div>

    <!-- Script code here -->

    <div class="wsmgs_editor_container">

        <div id="wsmgs_script_code">
            <button class="code-box-copy__btn" data-clipboard-target="#example-html" title="Copy"></button>
            <pre>
<code class="language-js">
// Run on user edit
function atEdit(e) {
    let sheet = e.range.getSheet();

    let currentTab = sheet.getName();

    if (currentTab !== "<?php echo get_option('tabName') ?>") return "INVALID_SHEET";

    let data = collectData(e);

    updateProduct({
        data,
    });
}

// Collect the data from sheet and organize those based on their column position.
function collectData(e) {
    let sheet = e.range.getSheet();

    let column = e.range.getColumn();

    if (column > 8) return "INVALID_COLUMN";

    let range = sheet.getActiveRange();

    let editedValues = range.getValues();

    if (!editedValues) return "NO_VALUES";

    let data = [];

    editedValues.forEach((editedValue, rowIndex) => {
        let row = e.range.getRow() + rowIndex;

        let aToBValues = sheet.getRange(`A${row}:B${row}`).getValues()[0];

        // The start index of the user column selection in the row
        let startIndex = column;
        // The end index of user current selection in the row
        let endIndex = column + (editedValue.length - 1);

        // The colum place
        let columns = [
            "id", // Col A
            "type", // Col B
            "sku", // Col C
            "name", // Col D
            "published", // Col E
            "stock", // Col F
            "salePrice", // Col G
            "regularPrice", // Col H
        ];

        let organizedData = {};

        let tempIndex = 0;

        for (let i = startIndex; i <= endIndex; i++) {

            // If there is no value than just simply break the loop and return;
            if(!editedValue[tempIndex]) continue;

            if (!organizedData[columns[i - 1]]) {
                organizedData[columns[i - 1]] = editedValue[tempIndex];
            }

            if (organizedData.id) {
            // Set the old ID value to that cell
            if(e.oldValue){
                sheet.getRange(`A${row}`).setValue(e.oldValue);
            }
            showAlert("ID column cannot be changed");
            data.length = 0;
            return;
            }

            if (organizedData.type) {
                // Set the old Type value to that cell
                if(e.oldValue){
                sheet.getRange(`B${row}`).setValue(e.oldValue);
                }
                showAlert("Type column cannot be changed");
                data.length = 0;
                return;
            }
            tempIndex++;
        }


        // If there are no data inside the object skip this current loop
        if(Object.keys(organizedData).length < 1){
        return;
        }

        // Insert the id & type of that product
        organizedData[columns[0]] = aToBValues[0];
        organizedData[columns[1]] = aToBValues[1];

        data.push(organizedData);
    });

    return data;
}

// Update the product on wordpress when there is a new change in sheet
function updateProduct(args) {
    if (!Array.isArray(args.data) || args.data.length < 1) {
        SpreadsheetApp.getActiveSpreadsheet().toast('Data is not valid to send to WordPress');
        return "INVALID_DATA";
    }

    //Request body
    let data = {
        token: "<?php echo get_option('wsmgsToken') ?>",
        reqData: args.data,
    };

    // Request options
    let options = {
        method: "POST",
        contentType: "application/json",
        muteHttpExceptions: true,
        payload: JSON.stringify(data),
    };

    try {

        let url = "<?php echo site_url() ?>/wp-json/wsmgs/v1/update-product";
        let result = UrlFetchApp.fetch(url, options);
        let response = JSON.parse(result.getContentText());

        SpreadsheetApp.getActiveSpreadsheet().toast(response.data.message);

    } catch (error) {
        showAlert(error.message)
        Logger.log(error.message)
    }
}

// Show a popup alert on spreadsheet
function showAlert(message){
    SpreadsheetApp.getUi().alert(message);
}
</code>
</pre>
        </div>
    </div>

</div>