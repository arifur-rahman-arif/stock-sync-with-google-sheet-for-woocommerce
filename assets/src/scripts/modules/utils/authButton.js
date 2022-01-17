import { showAlert } from "./helperFunctions";

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
            $(document).on("click", ".wsmgs_authenticate", (e) => {
                e.preventDefault();

                this.authenticateGoogleSheet(e);
            });
        }

        callMethods() {
            this.setButtton();
        }

        // Set the auth button after woocommerce export button
        setButtton() {
            $(this.btn).after(`<a href="#" class="page-title-action wsmgs_authenticate">Export To Sheet</a>`);
        }

        // Authenticate user google sheet by sending request to backend
        authenticateGoogleSheet(e) {
            // Send ajax request to server for user authentication
            $.ajax({
                type: "POST",
                url: wsmgsLocal.ajaxUrl,

                data: {
                    action: "wsmgs_export_product",
                    wpNonce: wsmgsLocal.wpNonce,
                },

                success: (response) => {
                    console.log(response);
                    showAlert({
                        message: "Text copied to clipboard",
                        type: "alert_success",
                    });
                },

                error: (error) => {},
            });
        }
    }

    new AuthButton();
});
