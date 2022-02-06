import { closeLoader, showAlert, showLoader } from "./helper-functions";

var $ = jQuery.noConflict();

$(function () {
    class AuthButton {
        constructor() {
            // Define the properties of this class
            this.btn = $(".wrap .page-title-action")[2];

            this.events();
            this.callMethods();
        }

        events() {
            $(document).on("click", ".wsmgs_export_btn", (e) => {
                e.preventDefault();
                this.exportProducts(e);
            });
        }

        callMethods() {
            this.setButtton();
        }

        // Set the auth button after woocommerce export button
        setButtton() {
            $(this.btn).after(`<button href="#" class="page-title-action wsmgs_export_btn">Sync with sheet</button>`);
        }

        // Authenticate user google sheet by sending request to backend
        exportProducts(e) {
            // Send ajax request to server for user authentication
            $.ajax({
                type: "POST",
                url: wsmgsLocal.ajaxUrl,

                data: {
                    action: "wsmgs_export_product",
                    wpNonce: wsmgsLocal.wpNonce,
                },

                beforeSend: () => {
                    $(e.currentTarget).addClass("wsmgs_disabled");
                    $(e.currentTarget).attr("disabled", true);
                    showLoader();
                },

                success: (response) => {
                    try {
                        showAlert({
                            message: response.data.message,
                            type: `alert_${response.data.status}`,
                        });
                    } catch (error) {
                        showAlert({
                            message: error,
                            type: `alert_error`,
                        });
                    }
                },

                complete: () => {
                    $(e.currentTarget).removeClass("wsmgs_disabled");
                    $(e.currentTarget).attr("disabled", false);
                    closeLoader();
                },

                error: (error) => {
                    let response = JSON.parse(error.responseText);

                    showAlert({
                        message: response.data.message,
                        type: `alert_${response.data.status}`,
                    });

                    closeLoader();
                },
            });
        }
    }

    new AuthButton();
});
