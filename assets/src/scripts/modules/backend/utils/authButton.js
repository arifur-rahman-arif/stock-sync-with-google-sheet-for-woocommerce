var $ = jQuery.noConflict();

$(document).ready(function () {
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
            $(this.btn).after(
                `<a href="#" class="page-title-action wsmgs_authenticate">Authenticate</a>`
            );
        }

        // Authenticate user google sheet by sending request to backend
        authenticateGoogleSheet(e) {
            alert("hello");
        }
    }

    new AuthButton();
});
