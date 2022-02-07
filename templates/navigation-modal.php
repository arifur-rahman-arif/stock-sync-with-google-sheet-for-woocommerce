<?php
$sheetUrl = get_option('sheetUrl') ? sanitize_text_field(get_option('sheetUrl')) : null;
$tabName = get_option('tabName') ? sanitize_text_field(get_option('tabName')) : null;
$disabled = get_option('configureMode') ? 'disabled' : null;
?>

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
</div>


<!-- Modal #3 -->
<div class="modal fade" id="modal3" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Load the script template -->
                <?php load_template(WSMGS_BASE_PATH . 'templates/template-script.php', false)?>
            </div>
            <div class="modal-footer">
                <button class="wsmgs_btn modal_back_btn" type="button" data-bs-target="#modal2"
                    data-bs-dismiss="modal">Back</button>
                <button class="wsmgs_btn modal_next_btn modal_3" type="button" data-bs-dismiss="modal">Done</button>
            </div>
        </div>
    </div>
</div>



<?php if ($disabled && $tabName && $sheetUrl) {?>

<p class="submit">
    <a class="wsmgs_configure" data-bs-toggle="modal" href="#modal2" role="button" type="button">Configure Plugin</a>
</p>

<?php }?>