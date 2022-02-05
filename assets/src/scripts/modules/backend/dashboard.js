import { copyToClipboard, showAlert } from "../utils/helper-functions";

var $ = jQuery.noConflict();

$(function () {
    class Dashboard {
        constructor() {
            // Define the properties of this class
            this.botMail = $(".bot_mail");
            this.modal1 = $(".modal_1.inactive");

            this.events();
            this.callMethods();
        }

        events() {
            this.botMail.on("click", (e) => {
                let target = $(e.currentTarget);
                let text = target.text();
                copyToClipboard(text);
            });

            this.modal1.on("click", this.checkRequiredFields);

            $(".modal_sheet_url, .modal_tab_name").on("input", this.handleNavigationButton);
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
                $(".modal_1").attr("data-bs-toggle", "");
                return;
            } else {
                $(".modal_1").removeClass("inactive");
                $(".modal_1").attr("data-bs-toggle", "modal");
            }
        }

        // If one of required input field is empty than restrict the user to go to next page
        checkRequiredFields(e) {
            e.preventDefault();

            let sheetUrl = $(".modal_sheet_url").val();
            let tabName = $(".modal_tab_name").val();

            // If sheet url or tab name value dont exits than show warning
            if (!sheetUrl || !tabName) {
                showAlert({
                    message: "Please fill up all the required fields",
                    type: "alert_warning",
                });

                return;
            }
        }
    }

    new Dashboard();
});
