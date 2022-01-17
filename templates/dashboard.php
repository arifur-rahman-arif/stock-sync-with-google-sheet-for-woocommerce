<?php settings_errors();?>


<div class="wrap">
    <form action="options.php" method="POST">
        <?php settings_fields('wsmgs_general_setting')?>
        <?php do_settings_sections('wsmgs_page')?>
        <?php submit_button('Save Settings', 'primary');?>
    </form>
    <br>
    <p>
    <h3>

        Save the google sheet and give this bot
        <span class="bot_mail">
            <code>wsmgs-plugin@wsmgs-plugin-338313.iam.gserviceaccount.com</code>
        </span> ID editor
        access in your google sheet.
        <br>
        <br>
        <i>Note:</i> You have to give this bot ID editor access or your won't be in sync with your sheet.
    </h3>
    </p>
</div>