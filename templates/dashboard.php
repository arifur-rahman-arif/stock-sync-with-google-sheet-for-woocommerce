<?php settings_errors();?>
<?php
$sheetUrl = get_option('sheetUrl') ? sanitize_text_field(get_option('sheetUrl')) : null;
$tabName = get_option('tabName') ? sanitize_text_field(get_option('tabName')) : null;
$configureMode = get_option('configureMode') ? true : false;
?>


<!-- If configure mode is active show the navigation template -->
<?php if ($configureMode) {?>

<!-- Load the configure modal template -->
<?php load_template(WSMGS_BASE_PATH . 'templates/navigation-modal.php')?>

<?php } else {?>
<div class="wrap">
    <form action="options.php" class="wsmgs_setting_form" method="POST">
        <?php settings_fields('wsmgs_general_setting')?>


        <div class="wsmgs_inputs">

            <div class="wsmgs_input_container">
                <label class="input" for="sheetUrl">
                    <span class="input__label">Google Sheet URL&nbsp;
                        <i class="fa-solid fa-circle-info wsmgs_tooltip_element" data-bs-toggle="tooltip"
                            data-bs-placement="right" title="
                                    <div class='tooltip_image_container'>
                                        <p>
                                            Copy your Google Sheet URL & paste it below to get access your products into it
                                        </p>
                                        <img
                                            src='<?php echo WSMGS_BASE_URL . 'assets/public/images/tooltips/url-screenshot.png' ?>' />
                                        </div>
                                    ">
                        </i>
                    </span>
                    <input class="input__field" type="text" name="sheetUrl" value="<?php echo esc_attr($sheetUrl) ?>"
                        placeholder="Enter your google sheet URL" />
                </label>
            </div>

            <div class="wsmgs_input_container">

                <label class="input" for="tabName">
                    <span class="input__label">Tab Name&nbsp;
                        <i class="fa-solid fa-circle-info wsmgs_tooltip_element" data-bs-toggle="tooltip"
                            data-bs-placement="right" title="
                                    <div class='tooltip_image_container'>
                                        <p>
                                            Copy the Tab Name & paste it here. Thus your products will be stored in this tab
                                        </p>
                                        <img
                                            src='<?php echo WSMGS_BASE_URL . 'assets/public/images/tooltips/tab-name.png' ?>' />
                                        </div>
                                    ">
                        </i>
                    </span>
                    <input class="input__field" type="text" name="tabName" value="<?php echo esc_attr($tabName) ?>"
                        placeholder="Enter your Tab Name" />
                </label>

            </div>

        </div>


        <?php do_settings_sections('wsmgs-page')?>

        <?php submit_button('Save Settings', 'wsmgs_save_setting_btn');?>

    </form>

    <?php if ($tabName && $sheetUrl) {?>

    <!-- Load the script template -->
    <?php load_template(WSMGS_BASE_PATH . 'templates/template-script.php', false)?>

    <?php }?>


</div>
<?php }?>