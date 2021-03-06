import { Toast } from "bootstrap";

var $ = jQuery.noConflict();

export const copyToClipboard = (text) => {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text);
    } else {
        // Create a "hidden" input
        let input = document.createElement("input");

        // Assign it the value of the specified element
        input.setAttribute("value", text);

        // Append it to the body
        document.body.appendChild(input);

        // Highlight its content
        input.select();

        // Copy the highlighted text
        document.execCommand("copy");

        // Remove it from the body
        document.body.removeChild(input);
    }

    showAlert({
        message: "Text copied",
        type: "alert_success",
    });
};

export const showAlert = (args) => {
    const { message, type } = args;

    if (!message || !type) return;

    insertAlertToUi();

    const element = $(".wsmgs_alert");

    let alertTypes = {
        alert_success: "Success &#128077;",
        alert_warning: "Warning &#9888;&#65039;",
        alert_error: "Error &#128683;",
    };

    for (const key in alertTypes) {
        if (Object.hasOwnProperty.call(alertTypes, key)) {
            element.find(".toast-header").removeClass(key);
        }
    }

    element.find(".toast-header").addClass(type);
    element.find(".message").text(message);

    element.find(".message_type").html(alertTypes[type]);

    let toast = new Toast(element);

    toast.show();
};

const insertAlertToUi = () => {
    $("#wpwrap").append(`
        <div class="position-fixed top-0 end-0 me-4 mt-5" style="z-index: 99999; top: 130px !important; opacity: 1">
            <div class="toast hide wsmgs_alert" role="alert" aria-live="assertive" aria-atomic="true" style="background-color: #fff">
                <div class="toast-header">
                    <strong class="me-auto message_type">Bootstrap</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body message">
                    Hello, world! This is a toast message.
                </div>
            </div>
        </div>
    `);
};

// Show loader in page
export const showLoader = () => {
    if ($("#wpcontent .wsmgs_overlay").length) {
        $("#wpcontent .wsmgs_overlay").show();
    } else {
        $("#wpcontent").css({
            position: "relative",
        }).append(`
            
            <div class="wsmgs_overlay">
                <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);
    }
};

// Hide the loader from page
export const closeLoader = () => {
    if ($("#wpcontent .wsmgs_overlay").length) {
        $("#wpcontent .wsmgs_overlay").hide();
    }
};

// Check if a given url is valid or not
export const isValidHttpUrl = (string) => {
    if (!string.length) return false;

    let url;

    try {
        url = new URL(string);
    } catch (_) {
        return false;
    }

    return url.protocol === "http:" || url.protocol === "https:";
};

// Show loading button
export const showLoadingButton = (target) => {
    target.addClass("btn-loading");
    target.html(`
        <div class="spinner-border text-success" role="status">
            <span class="sr-only"></span>
        </div>
    `);
};

// Show loading button
export const closeLoadingButton = (target, text) => {
    target.removeClass("btn-loading");

    if (!text) text = "Default";

    target.html(text);
};

// Get the url hash value from current browser url
export const getURLHashValue = () => {
    let url = new URL(window.location);
    return url.hash || false;
};
