import { Toast } from "bootstrap";

var $ = jQuery.noConflict();

export const copyToClipboard = (text) => {
    navigator.clipboard.writeText(text);

    showAlert({
        message: "Text copied to clipboard",
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
        <div class="position-fixed bottom-0 start-50 translate-middle-x mb-3" style="z-index: 11">
            <div class="toast hide wsmgs_alert" role="alert" aria-live="assertive" aria-atomic="true">
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
