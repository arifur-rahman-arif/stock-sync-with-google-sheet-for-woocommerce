<!-- Modal #2 -->
<!-- <div class="modal fade" id="modal2" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2" tabindex="-1">
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
                            <code>wcsmgs@wc-stock-management-with-sheet.iam.gserviceaccount.com</code>
                        </span>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button class="wsmgs_btn modal_back_btn" type="button" data-bs-target="#modal1"
                    data-bs-dismiss="modal">Back</button>
                <button class="wsmgs_btn modal_next_btn modal_2" type="button" data-bs-target="#modal3"
                    data-bs-dismiss="modal">Next
                </button>
            </div>
        </div>
    </div>
</div> -->


<!-- Modal #3 -->
<!-- <div class="modal fade" id="modal3" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button class="wsmgs_btn modal_back_btn" type="button" data-bs-target="#modal2"
                    data-bs-dismiss="modal">Back</button>
                <button class="wsmgs_btn modal_next_btn modal_3" type="button" data-bs-dismiss="modal">Done</button>
            </div>
        </div>
    </div>
</div>



<p class="submit">
    <a class="wsmgs_configure" data-bs-toggle="modal" href="#modal2" role="button" type="button">Configure Plugin</a>
</p> -->

<div class="container wsmgs_welcome_container">

    <h2 class="text-center">Welcome to</h2>
    <h1 class="text-center"><?php echo WSMGS_PlUGIN_NAME ?></h1>
    <a class="wsmgs_button video_link" href="#">Watch video tutorial</a>

    <div class="get_started_container">
        <p>Press the button and follow the steps to sync your product with Google Sheet</p>
        <button class="wsmgs_button get_started_btn">Get started</button>
    </div>
</div>

<div class="container wsmgs_wizard_container">

    <div id="smartwizard">
        <ul class="nav">
            <li>
                <a class="nav-link" href="#step-1">
                    Set URL
                </a>
            </li>
            <li>
                <a class="nav-link" href="#step-2">
                    Set ID
                </a>
            </li>
            <li>
                <a class="nav-link" href="#step-3">

                    Set Script Code
                </a>
            </li>
            <li>
                <a class="nav-link" href="#step-4">
                    Done
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div id="step-1" class="tab-pane" role="tabpanel">
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
            <div id="step-2" class="tab-pane" role="tabpanel">
                Step content 2
            </div>
            <div id="step-3" class="tab-pane" role="tabpanel">
                Step content 3
            </div>
            <div id="step-4" class="tab-pane" role="tabpanel">
                Step content 4
            </div>
        </div>
    </div>

</div>