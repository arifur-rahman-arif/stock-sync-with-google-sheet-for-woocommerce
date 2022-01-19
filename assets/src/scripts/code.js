function atEdit(e) {
    let data = collectData(e);
    updateProduct({
        data,
    });
}

// Collect the data from sheet and organize those based on their column position.
function collectData(e) {
    let sheet = e.range.getSheet();

    let currentTab = sheet.getName();

    if (currentTab !== "Copy of WooCommerce Stock Management with Google Sheet") return "INVALID_SHEET";

    let column = e.range.getColumn();

    if (column > 8) return "INVALID_COLUMN";

    let range = sheet.getActiveRange();

    let editedValues = range.getValues()[0];

    // The start index of the user column selection in the row
    let startIndex = column;
    // The end index of user current selection in the row
    let endIndex = column + (editedValues.length - 1);

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
            organizedData[columns[i - 1]] = editedValues[tempIndex];
        }
        tempIndex++;
    }

    return organizedData;
}

// Update the product on wordpress when there is a new change in sheet
function updateProduct(args) {
    if (typeof args.data !== "object") {
        SpreadsheetApp.getActiveSpreadsheet().toast("Data is not valid to send to WordPress");
        return "INVALID_DATA";
    }

    //Request body
    let data = {
        token: "alsdfjoq23drlkncaoiohsdjf",
        reqData: args.data,
    };

    // Request options
    let options = {
        method: "POST",
        contentType: "application/json",
        payload: JSON.stringify(data),
    };

    try {
        let url = "https://7e9d-118-179-170-193.ngrok.io/wp-json/wsmgs/v1/update-product";
        let result = UrlFetchApp.fetch(url, options);
        let response = JSON.parse(result.getContentText());

        SpreadsheetApp.getActiveSpreadsheet().toast("Task started");

        Logger.log(response);
    } catch (error) {
        Logger.log(error);
    }
}
