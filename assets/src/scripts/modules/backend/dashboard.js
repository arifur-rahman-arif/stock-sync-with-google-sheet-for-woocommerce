import { Tooltip } from "bootstrap";
import {
    closeLoadingButton,
    copyToClipboard,
    getURLHashValue,
    isValidHttpUrl,
    showAlert,
    showLoadingButton,
} from "../utils/helper-functions";

var $ = jQuery.noConflict();

$(function () {
    class Dashboard {
        constructor() {
            // Grab the elements
            this.botMail = $(".bot_id_container");
            this.botCopyBtn = $(".bot_copy_btn");
            this.scriptCopyBtn = $(".script_copy_btn");
            this.settingsInput = $(".modal_sheet_url, .modal_tab_name");
            this.getStartedBtn = $(".get_started_btn");
            this.smartWizard = $("#smartwizard");
            this.gaveEditorAccess = $("#gave_editor_access");
            this.pastedAppScript = $("#pasted_app_script");
            this.syncButton = $(".sync_button");
            this.accordion = $(".tutorial_accordion");

            // Properties of this class
            this.optionSaved = false;
            this.hasEditorAccess = false;

            this.callMethods();
            this.events();
        }

        events() {
            this.botMail.on("click", (e) => {
                copyToClipboard("wcsmgs@wc-stock-management-with-sheet.iam.gserviceaccount.com");
            });

            this.botCopyBtn.on("click", (e) => {
                copyToClipboard("wcsmgs@wc-stock-management-with-sheet.iam.gserviceaccount.com");
            });

            this.scriptCopyBtn.on("click", this.copyScriptCode.bind(this));

            this.settingsInput.on("input", this.handleNavigationButton.bind(this));
            this.getStartedBtn.on("click", this.showWizard.bind(this));

            $(".btn.sw-btn-next").on("click", this.saveOptionsValue.bind(this));
            $(".btn.sw-btn-next").on("click", this.doesBotHasEditorAccess.bind(this));

            this.gaveEditorAccess.on("change", this.checkNextButton.bind(this));
            this.pastedAppScript.on("change", this.activateStep4.bind(this));
            this.syncButton.on("click", this.exportProducts.bind(this));
            this.accordion.on("click", this.increaseHeightOfWizard.bind(this));

            $(document).on("click", ".redirection_button", this.redirectUser.bind(this));
            $(document).on("click", ".btn.sw-btn-prev", this.handleNextButtonStyle.bind(this));
        }

        callMethods() {
            // Initiate all tooltip
            let tooltips = $(".wsmgs_tooltip_element");
            tooltips.length &&
                $.each(tooltips, function (i, element) {
                    new Tooltip($(element), {
                        html: true,
                        trigger: "hover",
                    });
                });

            // Remove the hash from url if browser reloaded
            history.pushState("", document.title, window.location.pathname + window.location.search);

            // Initialize the wizard step if element is active
            this.smartWizard.length &&
                this.smartWizard.smartWizard({
                    selected: 0,
                    autoAdjustHeight: true,
                    enableURLhash: true,
                    transition: {
                        animation: "slide", // Effect on navigation, none/fade/slide-horizontal/slide-vertical/slide-swing
                        speed: "100", // Transition animation speed
                    },
                });

            this.smartWizard.length && this.smartWizard.smartWizard("stepState", [1, 2, 3], "disable");

            this.handleNavigationButton();

            this.resetAnimatedGif();
        }

        // Disable the modal navigation button if input field values are empty
        handleNavigationButton() {
            let sheetUrl = $(".modal_sheet_url").val();
            let tabName = $(".modal_tab_name").val();

            this.smartWizard.length && this.smartWizard.smartWizard("stepState", [1, 2, 3], "disable");

            this.optionSaved = false;
            this.hasEditorAccess = false;

            this.gaveEditorAccess.prop("checked", false);
            this.pastedAppScript.prop("checked", false);

            if (getURLHashValue() !== "#step-1") return false;

            if (!sheetUrl || !tabName) {
                this.setTheTooltip({ title: "Please fill up all input fields" });
                $(".btn.sw-btn-next").addClass("disabled");
            } else {
                if (!$(".btn.sw-btn-next").hasClass("wsmgs_inactive")) return;

                let tooltip = new Tooltip($(".wsmgs_inactive"), {
                    html: true,
                });
                tooltip.dispose();

                $(".btn.sw-btn-next")
                    .removeClass("wsmgs_inactive")
                    .removeClass("disabled")
                    .attr("disabled", false);
            }
        }

        // If one of required input field is empty than restrict the user to go to next page
        checkRequiredFields() {
            // If its not the first step than return false
            if (getURLHashValue() !== "#step-1") return false;

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
        doesBotHasEditorAccess(e) {
            let target = $(e.currentTarget);
            // If its not the first step than return false
            if (getURLHashValue() !== "#step-2") return false;
            if (target.hasClass("disabled") || target.hasClass("wsmgs_inactive")) return false;
            if (!this.gaveEditorAccess.prop("checked")) return false;
            if (this.hasEditorAccess) return false;

            try {
                $.ajax({
                    type: "POST",
                    url: wsmgsLocal.ajaxUrl,
                    data: {
                        action: "wsmgs_check_bot_access",
                        wpNonce: wsmgsLocal.wpNonce,
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

                        this.smartWizard.smartWizard("stepState", [2], "enable");
                        this.smartWizard.smartWizard("next");
                        this.hasEditorAccess = true;

                        this.setTheTooltip({ title: "Are you sure you have pasted Script Code correctly?" });
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

                        this.gaveEditorAccess.prop("checked", false);
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

                        this.setTheTooltip({ title: "Please give this ID editor access in your sheet" });
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
        }

        // Show the wizard modal upon clicking next button
        showWizard(e) {
            $(".container.wsmgs_welcome_container").addClass("d-none");
            $(".container.wsmgs_wizard_container").addClass("active");
            $(".btn.sw-btn-next").addClass("disabled");
        }

        // Enable or disable the next button upon changing of gave editor access checkbox
        checkNextButton(e) {
            let nextBtn = $(".btn.sw-btn-next");
            if ($(e.currentTarget).prop("checked")) {
                if ($(".wsmgs_inactive").length) {
                    let tooltip = new Tooltip($(".wsmgs_inactive"), {
                        html: true,
                    });
                    tooltip.dispose();
                }
                nextBtn.removeClass("disabled").removeClass("wsmgs_inactive");
            } else {
                nextBtn.addClass("disabled").addClass("wsmgs_inactive");

                this.setTheTooltip({ title: "Please give this ID editor access in your sheet" });
            }
        }

        // Activate step 4
        activateStep4(e) {
            let nextBtn = $(".btn.sw-btn-next");

            if ($(e.currentTarget).prop("checked")) {
                if ($(".wsmgs_inactive").length) {
                    let tooltip = new Tooltip($(".wsmgs_inactive"), {
                        html: true,
                    });
                    tooltip.dispose();
                }
                nextBtn.removeClass("disabled").removeClass("wsmgs_inactive");
                this.smartWizard.length && this.smartWizard.smartWizard("stepState", [3], "enable");
                this.smartWizard.length &&
                    this.smartWizard.find(".nav li:nth-child(4) a").addClass("full_width");
            } else {
                nextBtn.addClass("disabled").addClass("wsmgs_inactive");

                this.setTheTooltip({ title: "Are you sure you have pasted Script Code correctly?" });
                this.smartWizard.length && this.smartWizard.smartWizard("stepState", [3], "disable");
                this.smartWizard.length &&
                    this.smartWizard.find(".nav li:nth-child(4) a").removeClass("full_width");
            }
        }

        // Copy the script code to user clipboard
        copyScriptCode(e) {
            let target = $(e.currentTarget);

            try {
                $.ajax({
                    type: "POST",
                    url: wsmgsLocal.ajaxUrl,
                    data: {
                        action: "wsmgs_copy_script",
                        wpNonce: wsmgsLocal.wpNonce,
                    },

                    beforeSend: () => {
                        showLoadingButton(target);
                        target.addClass("wsmgs_inactive");
                    },

                    success: (response) => {
                        let text = response.data.scriptCode;
                        copyToClipboard(text);

                        showAlert({
                            message: response.data.message,
                            type: `alert_success`,
                        });

                        this.smartWizard.length && this.smartWizard.smartWizard("stepState", [3], "enable");
                    },

                    complete: () => {
                        closeLoadingButton(target, "Copy Script");
                        target.attr("disabled", false).removeClass("wsmgs_inactive");
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
        }

        // Set the tooltip for next step
        setTheTooltip(args) {
            const { title } = args;

            $(".btn.sw-btn-next")
                .addClass("wsmgs_inactive")
                .attr("title", title)
                .attr("data-bs-toggle", "tooltip")
                .attr("data-bs-placement", "left");

            $(".btn.sw-btn-next").length &&
                new Tooltip($(".wsmgs_inactive"), {
                    html: true,
                    trigger: "hover",
                });
        }

        // Export all WordPress products to google sheet
        exportProducts(e) {
            let target = $(e.currentTarget);

            // Send ajax request to server for user authentication
            $.ajax({
                type: "POST",
                url: wsmgsLocal.ajaxUrl,

                data: {
                    action: "wsmgs_export_product",
                    wpNonce: wsmgsLocal.wpNonce,
                },

                beforeSend: () => {
                    showLoadingButton(target);
                    $(".btn.sw-btn-next").removeClass("redirection_btn");
                    $(".btn.sw-btn-prev").addClass("disabled");
                    this.setTheTooltip({
                        title: "Please wait. Products are still exporting into your Google Sheet",
                    });
                },

                success: (response) => {
                    try {
                        showAlert({
                            message: response.data.message,
                            type: `alert_${response.data.status}`,
                        });

                        $(".step4_content_wrapper").html(`
                            <div class="congratulation_box">
                                <span class="congratulation_icon">
                                    <i class="fa-solid fa-check"></i>
                                </span>
                                <h1>Congratulations!</h1>
                                <p>Successfully Synced in your Google Sheet</p>

                                <button class="redirection_button">Done</button>

                            </div>
                        `);

                        $(".btn.sw-btn-next").remove();
                        $(".btn.sw-btn-prev").remove();
                    } catch (error) {
                        showAlert({
                            message: error,
                            type: `alert_error`,
                        });
                    }
                },

                complete: () => {
                    closeLoadingButton(target, "Sync with Google Sheet");
                    target.attr("disabled", false).removeClass("wsmgs_inactive");
                },

                error: (error) => {
                    let response = JSON.parse(error.responseText);

                    showAlert({
                        message: response.data.message,
                        type: `alert_${response.data.status}`,
                    });

                    target.attr("disabled", true).addClass("wsmgs_inactive");
                    $(".btn.sw-btn-prev").removeClass("disabled");
                },
            });
        }

        // Redirect the user to product page and
        redirectUser(e) {
            if (getURLHashValue() !== "#step-4") return false;

            let target = $(e.currentTarget);

            try {
                $.ajax({
                    type: "POST",
                    url: wsmgsLocal.ajaxUrl,
                    data: {
                        action: "wsmgs_exit_wizard_mode",
                        wpNonce: wsmgsLocal.wpNonce,
                    },

                    beforeSend: () => {
                        showLoadingButton(target);
                        target.attr("disabled", true).addClass("wsmgs_inactive");
                    },

                    success: (response) => {
                        showAlert({
                            message: response.data.message,
                            type: `alert_success`,
                        });

                        setTimeout(() => {
                            window.location.href = response.data.redirectUrl;
                        }, 1000);
                    },

                    complete: () => {
                        closeLoadingButton(target, "Done");
                        target.attr("disabled", false).removeClass("wsmgs_inactive");
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
        }

        // Handle next button style
        handleNextButtonStyle(e) {
            let target = $(e.currentTarget);

            if (target.hasClass(".sw-btn-prev")) {
                if ($(".btn.sw-btn-next").hasClass("wsmgs_inactive")) {
                    let tooltip = new Tooltip($(".wsmgs_inactive"));
                    tooltip.dispose();
                    $(".btn.sw-btn-next").removeClass("wsmgs_inactive");
                }
            }
        }

        // Increase height of of wizard upon clicking on accordion
        increaseHeightOfWizard(e) {
            let target = $(e.currentTarget);

            if (target.find("#flush-collapseOne").hasClass("show")) {
                $(".tab-content").css({
                    height: "800px",
                });
            } else {
                $(".tab-content").css({
                    height: "auto",
                });
            }
        }

        // Reset animated gif
        resetAnimatedGif() {
            this.scriptCopyBtn.on({
                mouseenter: () => {
                    $(".copy_script_animated_gif").prop("src", $(".copy_script_animated_gif").attr("src"));
                },
                mouseleave: () => {
                    $(".copy_script_animated_gif").prop("src", $(".copy_script_animated_gif").attr("src"));
                },
            });
        }
    }

    new Dashboard();
});
