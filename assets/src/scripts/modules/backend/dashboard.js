import { copyToClipboard } from "../utils/helperFunctions";

var $ = jQuery.noConflict();

$(document).ready(function () {
    class Dashboard {
        constructor() {
            // Define the properties of this class
            this.botMail = $(".bot_mail");

            this.events();
            this.callMethods();
        }

        events() {
            $(document).on("click", ".wsmgs_authenticate", (e) => {
                e.preventDefault();
                this.authenticateGoogleSheet(e);
            });

            this.botMail.click((e) => {
                let target = $(e.currentTarget);
                let text = target.text();
                copyToClipboard(text);
            });
        }

        callMethods() {}
    }

    new Dashboard();
});
