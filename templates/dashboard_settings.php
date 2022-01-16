<?php

$sheetUrl = get_option('sheetUrl') ? sanitize_text_field(get_option('sheetUrl')) : null;
$tabName = get_option('tabName') ? sanitize_text_field(get_option('tabName')) : null;

?>

<tr>
    <td>
        <strong style="font-size: 15px;">
            <label for="sheetUrl">Google Sheet :</label>
        </strong>
    </td>
    <td>
        <input style='width: 50%' type="text" name="sheetUrl" placeholder="Enter the google sheet url here."
            value="<?php echo esc_attr($sheetUrl) ?>" />
    </td>
</tr>
<tr>
    <td>
        <strong style="font-size: 15px;">
            <label for="tabName">Tab Name :</label>
        </strong>
    </td>
    <td>
        <input style='width: 30%' type="text" name="tabName" placeholder="Enter the google sheet url here."
            value="<?php echo esc_attr($tabName) ?>" />
    </td>
</tr>