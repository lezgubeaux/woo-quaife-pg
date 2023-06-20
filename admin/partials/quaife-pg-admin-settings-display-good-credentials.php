<?php

/**
 * Provide a admin-facing view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://framework.tech
 * @since      1.0.0
 *
 * @package    Quaife_Pg
 * @subpackage Quaife_Pg/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php

echo '
<div class="wrap note_ok">
    <div class="note_box">
        <h3>' . __("Your are connected to Quaife API", "woo-quaife-pg") . '</h3>
        <em>' . __("The credentials being used are", "woo-quaife-pg") . '<br />' .
    __("API Key: ", "woo-quaife-pg") . $api_key . '<br />' .
    __("API Secret: ", "woo-quaife-pg") . str_repeat("*", strlen($api_secret)) . '<br /><br />
        </em>
    </div>
</div>
<form method="post" action="' . admin_url() . 'admin.php?page=quaife-pg-settings">' .
    __('If you wish to use different API credentials, click here:', 'woo-quaife-pg') . '
    <input type="submit" name="btn_new" class="button" value="' . __('New API', 'woo-quaife-pg') . '" />
</form>';
