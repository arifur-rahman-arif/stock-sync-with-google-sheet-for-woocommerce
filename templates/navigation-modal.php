<!-- Modal #1 -->
<div class="modal fade" id="modal1" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="wsmgs_inputs">

                    <div class="wsmgs_input_container">
                        <label class="input" for="sheetUrl">
                            <input class="input__field modal_sheet_url" type="text" name="sheetUrl" value="" />
                            <span class="input__label">Sheet URL</span>
                        </label>
                    </div>

                    <div class="wsmgs_input_container">

                        <label class="input" for="tabName">
                            <input class="input__field modal_tab_name" type="text" name="tabName" value="" />
                            <span class="input__label">Tab Name</span>
                        </label>

                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button class="wsmgs_btn modal_next_btn modal_1 inactive" type="button" data-bs-target="#modal2">Next
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal #2 -->
<div class="modal fade" id="modal2" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-primary d-flex align-items-center" role="alert">

                    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Info:">
                        <use xlink:href="#info-fill" />
                    </svg>
                    <div class="bot_info">
                        Copy this bot ID by clicking on it & give editor access in your Google Sheet.
                        <span class="bot_mail d-block pt-2">
                            <code>wsmgs-plugin@wsmgs-plugin-338313.iam.gserviceaccount.com</code>
                        </span>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button class="wsmgs_btn modal_back_btn" type="button" data-bs-target="#modal1"
                    data-bs-dismiss="modal">Back</button>
                <button class="wsmgs_btn modal_next_btn modal_2" type="button" data-bs-target="#modal3"
                    data-bs-dismiss="modal">Next</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal #3 -->
<div class="modal fade" id="modal3" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>
                    If you have successfully gave editor access, try clicking this button
                </p>
                <button class="wsmgs_btn" type="button">Sync with Google Sheet</button>
            </div>
            <div class="modal-footer">
                <button class="wsmgs_btn modal_back_btn" type="button" data-bs-target="#modal2"
                    data-bs-dismiss="modal">Back</button>
                <button class="wsmgs_btn modal_next_btn modal_2" type="button" data-bs-target="#exampleModalToggle"
                    data-bs-dismiss="modal">Next</button>
            </div>
        </div>
    </div>
</div>



<a class="wsmgs_configure" data-bs-toggle="modal" href="#modal1" role="button" type="button">Configure
    Plugin</a>