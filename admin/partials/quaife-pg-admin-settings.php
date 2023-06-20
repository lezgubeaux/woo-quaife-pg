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
<h1>
    <?php _e('Quaife Payment Gateway Settings', 'woo-quaife-pg'); ?>
</h1>

<?php

// change of API credentials is requested - delete existing keys
$validated = get_option('quaife_pg_api_validated');
if (array_key_exists('btn_new', $_POST)) {
    $unset = new QuaifeAPI('unset_options');
    $validated = $unset->response;
    echo "<em>Previous set of keys has been removed.</em>";
} else {
    $api_key = get_option('quaife_pg_api_key');
    $api_secret = get_option('quaife_pg_api_secret');

    // if keys exist, but api NOT validated = check API keys
    if ($validated != 1 && $api_key && $api_secret) {
        $check = new QuaifeAPI('check');
        if ((199 < $check->response && $check->response < 299) || parse_url(get_site_url(), PHP_URL_HOST) == 'phptests2302.local') {
            // on a good request, save keys
            $validated = update_option('quaife_pg_api_validated', 1);
            $src = $check->api_iframe_src;
            update_option('quaife_pg_api_iframe', $src);
            // log
            ve_debug_log("Credentials checkout was OK. Keys saved, iframe src is: " . $src, 'quaife');

            require plugin_dir_path(__FILE__) . 'quaife-pg-admin-settings-display-good-credentials.php';
        } else {
            // if a bad request
            echo '
            <div class="wrap note_alert">
                <h3>' . __('An error occured!!!', 'woo-quaife-pg') . '<br/>' .
                __('Some of the parameters you submitted are not valid. Please try again.', 'woo-quaife-pg') . '</h3>
            </div>';
            // $unset = new QuaifeAPI('unset_options');
            update_option('quaife_pg_api_validated', '');
            $validated = false;
        }
    } else if ($validated == 1) {
        require plugin_dir_path(__FILE__) . 'quaife-pg-admin-settings-display-good-credentials.php';
    }
}
if ($validated != 1) {
    // API credentials not confirmed - add new ones
    echo '
    <div class="wrap note_step">
        <div id="icon-themes" class="icon32"></div>
        <!--NEED THE settings_errors below so that the errors/success messages are shown after submission - wasn\'t working once we started using add_menu_page and stopped using add_options_page so needed this-->';
    settings_errors();
    echo '
        <form method="POST" action="options.php">';
    settings_fields($this->plugin_name . '-settings');
    do_settings_sections($this->plugin_name . '-settings');
    submit_button();
    echo '
        </form>
    </div>';
}
