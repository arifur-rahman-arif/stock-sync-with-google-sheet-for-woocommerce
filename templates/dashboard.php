<?php settings_errors();?>


<div class="wrap">
    <form action="options.php" method="POST">
        <?php settings_fields('wsmgs_general_setting')?>
        <?php do_settings_sections('wsmgs-page')?>
        <?php submit_button('Save Settings', 'primary');?>
    </form>
    <br>


    <?php if (get_option('tabName') && get_option('sheetURL')) {?>

    <h3>
        Save the google sheet and give this bot
        <span class="bot_mail">
            <code>wsmgs-plugin@wsmgs-plugin-338313.iam.gserviceaccount.com</code>
        </span> ID editor
        access in your google sheet.
        <br>
        <br>
        <i>Note:</i> You have to give this bot ID editor access or your won't be in sync with your sheet.
    </h3>

    <span>
        <b>
            Copy the code & paste it into your google sheet App Script
        </b>
    </span>

    <br>

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
            if (!organizedData[columns[i - 1]]) {
                organizedData[columns[i - 1]] = editedValue[tempIndex];
            }

            if (!organizedData.id) {
                organizedData[columns[0]] = aToBValues[0];
            }

            if (!organizedData.type) {
                organizedData[columns[1]] = aToBValues[1];
            }

            tempIndex++;
        }

        data.push(organizedData);
    });

    return data;
}

// Update the product on wordpress when there is a new change in sheet
function updateProduct(args) {
    if (!Array.isArray(args.data) || args.data.length < 1) {
        SpreadsheetApp.getActiveSpreadsheet().toast("Data is not valid to send to WordPress");
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
        payload: JSON.stringify(data),
    };

    try {
        let url = "<?php echo site_url() ?>/wp-json/wsmgs/v1/update-product";
        let result = UrlFetchApp.fetch(url, options);
        let response = JSON.parse(result.getContentText());

        SpreadsheetApp.getActiveSpreadsheet().toast("Task started");

    } catch (error) {
        Logger.log(error);
    }
}
</code>
</pre>
        </div>
    </div>

    <?php }?>