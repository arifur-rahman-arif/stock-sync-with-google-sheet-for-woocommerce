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
                    <input class="input__field" type="text" name="sheetUrl" value="<?php echo esc_attr($sheetUrl) ?>" />
                    <span class="input__label">Sheet URL</span>
                </label>
            </div>

            <div class="wsmgs_input_container">

                <label class="input" for="tabName">
                    <input class="input__field" type="text" name="tabName" value="<?php echo esc_attr($tabName) ?>" />
                    <span class="input__label">Tab Name</span>
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