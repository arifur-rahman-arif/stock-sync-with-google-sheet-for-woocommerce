import { Modal } from "bootstrap";
import { closeLoadingButton, copyToClipboard, isValidHttpUrl, showAlert, showLoadingButton } from "../utils/helper-functions";

var $ = jQuery.noConflict();

$(function () {
    class Dashboard {
        constructor() {
            // Define the properties of this class
            this.botMail = $(".bot_mail");
            this.settingsInput = $(".modal_sheet_url, .modal_tab_name");
            this.modalNextButton = $(".modal_next_btn");
            this.modalBackButton = $(".modal_back_btn");

            this.events();
            this.callMethods();
        }

        events() {
            this.botMail.on("click", (e) => {
                let target = $(e.currentTarget);
                let text = target.text();
                copyToClipboard(text);
            });

            this.settingsInput.on("input", this.handleNavigationButton);
            this.modalNextButton.on("click", this.showNextModal.bind(this));
            this.modalBackButton.on("click", this.showPrevModal.bind(this));
        }

        callMethods() {
            this.handleNavigationButton();
        }

        // Disable the modal navigation button if input field values are empty
        handleNavigationButton() {
            let sheetUrl = $(".modal_sheet_url").val();
            let tabName = $(".modal_tab_name").val();

            if (!sheetUrl || !tabName) {
                $(".modal_1").addClass("inactive");
            } else {
                $(".modal_1").removeClass("inactive");
            }
        }

        // Show the next modal
        showNextModal(e) {
            let target = $(e.currentTarget);

            if (target.hasClass("modal_1") && !this.checkRequiredFields()) {
                return;
            }

            if (target.hasClass("modal_2") && !this.doesBotHasEditorAccees(e)) {
                return;
            }

            $(target.parents(".modal")).modal("hide");

            let modal = new Modal($(target.attr("data-bs-target")));
            modal.show();
        }

        // Go back to the previous modal
        showPrevModal(e) {
            let target = $(e.currentTarget);

            $(target.parents(".modal")).modal("hide");

            let modal = new Modal($(target.attr("data-bs-target")));
            modal.show();
        }

        // If one of required input field is empty than restrict the user to go to next page
        checkRequiredFields() {
            let sheetUrl = $(".modal_sheet_url").val();
            let tabName = $(".modal_tab_name").val();

            // If sheet url or tab name value dont exits than show warning
            if (!sheetUrl || !tabName) {
                showAlert({
                    message: "Please fill up all the required fields",
                    type: "alert_warning",
                });

                return false;
            }

            // Check if the sheet url is a valid url
            let validUrl = isValidHttpUrl(sheetUrl);

            if (!validUrl) {
                showAlert({
                    message: "Your given sheet url is not valid. Please give a valid one",
                    type: "alert_error",
                });

                return false;
            }

            return true;
        }

        // Check if user gave access to boi
        doesBotHasEditorAccees(e) {
            let hasAccess = false;

            $.ajax({
                type: "POST",
                url: wsmgsLocal.ajaxUrl,
                async: false,
                data: {
                    action: "wsmgs_check_bot_access",
                    wpNonce: wsmgsLocal.wpNonce,
                },

                beforeSend: () => {
                    showLoadingButton($(e.currentTarget));
                    $(e.currentTarget).attr("disabled", true);
                },

                success: (response) => {
                    try {
                        hasAccess = true;
                    } catch (error) {
                        showAlert({
                            message: error,
                            type: `alert_error`,
                        });
                    }
                },

                complete: () => {
                    closeLoadingButton($(e.currentTarget), "Next");
                    $(e.currentTarget).attr("disabled", false);
                },

                error: (error) => {
                    let response = JSON.parse(error.responseText);

                    showAlert({
                        message: response.data.message,
                        type: `alert_${response.data.status}`,
                    });
                },
            });

            return hasAccess;
        }
    }

    new Dashboard();
});
