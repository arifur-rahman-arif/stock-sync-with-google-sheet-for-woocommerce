import { Modal, Tooltip } from "bootstrap";
import {
    closeLoadingButton,
    copyToClipboard,
    isValidHttpUrl,
    showAlert,
    showLoadingButton,
} from "../utils/helper-functions";

var $ = jQuery.noConflict();

$(function () {
    class Dashboard {
        constructor() {
            // Define the properties of this class
            this.botMail = $(".bot_mail");
            this.settingsInput = $(".modal_sheet_url, .modal_tab_name");
            this.modalNextButton = $(".modal_next_btn");
            this.modalBackButton = $(".modal_back_btn");
            this.getStartedBtn = $(".get_started_btn");
            this.smartWizard = $("#smartwizard");
            this.optionSaved = false;

            this.events();
            this.callMethods();
        }

        events() {
            this.botMail.on("click", (e) => {
                let target = $(e.currentTarget);
                let text = target.text().trim();
                copyToClipboard(text);
            });

            this.settingsInput.on("input", this.handleNavigationButton);
            // this.modalNextButton.on("click", this.showNextModal.bind(this));
            // this.modalBackButton.on("click", this.showPrevModal.bind(this));
            this.getStartedBtn.on("click", this.showWizard.bind(this));

            this.smartWizard.length &&
                this.smartWizard.smartWizard({
                    selected: 0,
                    autoAdjustHeight: true,
                    transition: {
                        animation: "slide", // Effect on navigation, none/fade/slide-horizontal/slide-vertical/slide-swing
                        speed: "100", // Transition animation speed
                    },
                });

            this.smartWizard.length &&
                this.smartWizard.smartWizard("stepState", [1, 2, 3], "disable");

            $(".btn.sw-btn-next").on("click", this.saveOptionsValue.bind(this));
        }

        callMethods() {
            this.handleNavigationButton();
        }

        // Disable the modal navigation button if input field values are empty
        handleNavigationButton() {
            let sheetUrl = $(".modal_sheet_url").val();
            let tabName = $(".modal_tab_name").val();

            if (!sheetUrl || !tabName) {
                $(".btn.sw-btn-next")
                    .addClass("wsmgs_inactive")
                    .attr("title", "Please fill up all input fields")
                    .attr("original-title", "Please fill up all input fields")
                    .attr("data-bs-toggle", "tooltip")
                    .attr("data-bs-placement", "bottom");
                let tooltip = new Tooltip($(".wsmgs_inactive"));
            } else {
                if (!$(".btn.sw-btn-next").hasClass("wsmgs_inactive")) return;

                let tooltip = Tooltip.getInstance($(".wsmgs_inactive"));
                tooltip.dispose();

                $(".btn.sw-btn-next").removeClass("wsmgs_inactive").attr("disabled", false);
            }
        }

        // Show the next modal
        showNextModal(e) {
            let target = $(e.currentTarget);

            if (target.hasClass("modal_2")) {
                this.doesBotHasEditorAccees(e);
                return;
            }

            if (target.hasClass("modal_3")) {
                $(target.parents(".modal")).modal("hide");
                this.saveOptionsValue(e);
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
            let target = $(e.currentTarget);

            try {
                $.ajax({
                    type: "POST",
                    url: wsmgsLocal.ajaxUrl,
                    data: {
                        action: "wsmgs_check_bot_access",
                        wpNonce: wsmgsLocal.wpNonce,
                    },

                    beforeSend: () => {
                        showLoadingButton(target);
                        target.attr("disabled", true);
                    },

                    success: (response) => {
                        showAlert({
                            message: response.data.message,
                            type: `alert_success`,
                        });

                        $(target.parents(".modal")).modal("hide");

                        let modal = new Modal($(target.attr("data-bs-target")));
                        modal.show();
                    },

                    complete: () => {
                        closeLoadingButton(target, "Next");
                        target.attr("disabled", false);
                    },

                    error: (error) => {
                        let response = error.responseJSON;

                        let message = response.data.message;

                        showAlert({
                            message,
                            type: `alert_error`,
                        });
                    },
                });
            } catch (error) {
                showAlert({
                    message: error,
                    type: `alert_error`,
                });
            }
        }

        // Save options to database
        saveOptionsValue(e, anchorObject, currentStepIndex, nextStepIndex, stepDirection) {
            if (!this.checkRequiredFields()) return false;

            if (this.optionSaved) return false;

            let sheetUrl = $(".modal_sheet_url").val();
            let tabName = $(".modal_tab_name").val();

            try {
                $.ajax({
                    type: "POST",
                    url: wsmgsLocal.ajaxUrl,
                    data: {
                        action: "wsmgs_save_options",
                        wpNonce: wsmgsLocal.wpNonce,
                        sheetUrl,
                        tabName,
                    },

                    beforeSend: () => {
                        showLoadingButton($(".btn.sw-btn-next"));
                        $(".btn.sw-btn-next").attr("disabled", true);
                    },

                    success: (response) => {
                        showAlert({
                            message: response.data.message,
                            type: `alert_success`,
                        });

                        this.optionSaved = true;
                        this.smartWizard.smartWizard("stepState", [1], "enable");
                        this.smartWizard.smartWizard("next");
                    },

                    complete: () => {
                        closeLoadingButton($(".btn.sw-btn-next"), "Next");
                        $(".btn.sw-btn-next").attr("disabled", false);
                    },

                    error: (error) => {
                        let response = error.responseJSON;

                        let message = response.data.message;

                        showAlert({
                            message,
                            type: `alert_error`,
                        });
                    },
                });
            } catch (error) {
                showAlert({
                    message: error.message,
                    type: `alert_error`,
                });
            }

            // return false;
        }

        // Show the wizard modal upon clicking next button
        showWizard(e) {}
    }

    new Dashboard();
});
