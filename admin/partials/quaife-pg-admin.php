<?php

/**
 * Provide a admin area view for the plugin
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
<h1><?php _e('Quaife Payment Gateway ', 'woo-quaife-pg'); ?></h1>

<?php

$api_key = get_option('quaife_pg_api_key');
$api_secret = get_option('quaife_pg_api_secret');
$validated = get_option('quaife_pg_api_validated');
if ($validated == 1) {
    require plugin_dir_path(__FILE__) . 'quaife-pg-admin-settings-display-good-credentials.php';
} else {
    echo '
    <div class="wrap note_alert">' .
        __('Please enter ', 'woo-quaife-pg') . '<button><a href="' . admin_url() . '/admin.php?page=quaife-pg-settings"><strong>' . __('API keys', 'woo-quaife-pg') . '</strong></a></button>' . __(' to connect to your Quaife Merchant account.', 'woo-quaife-pg') . '
    </div>
    ';
}
?>

<h2>Personalize appearence of the Quaife payment on your site:</h2>
<!-- Add logo upload, color and other styles selection...  -->