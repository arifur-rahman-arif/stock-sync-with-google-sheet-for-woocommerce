import { copyToClipboard } from "../utils/helper-functions";

var $ = jQuery.noConflict();

$(function () {
    class Dashboard {
        constructor() {
            // Define the properties of this class
            this.botMail = $(".bot_mail");

            this.events();
            this.callMethods();
        }

        events() {
            this.botMail.on("click", (e) => {
                let target = $(e.currentTarget);
                let text = target.text();
                copyToClipboard(text);
            });
        }

        callMethods() {}
    }

    new Dashboard();
});
