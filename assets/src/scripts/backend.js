import "../styles/backend.scss";

import "./modules/utils/authButton";
import "./modules/backend/dashboard";

var $ = jQuery.noConflict();

import { Toast } from "bootstrap";

let toast = new Toast($("#liveToast"));

toast.show();
