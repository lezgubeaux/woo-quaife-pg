<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://framework.tech
 * @since             1.0.0
 * @package           Quaife_Pg
 *
 * @wordpress-plugin
 * Plugin Name:       Woo Quaife Payments
 * Plugin URI:        https://woo-quaife-pg.cm
 * Description:       Quaife Payment Gateway for WooCommerce
 * Version:           1.2.0
 * Author:            Vladimir Eric
 * Author URI:        https://framework.tech
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-quaife-pg
 * Domain Path:       /languages * 
 *
 * WC requires at least: 5.5.1
 * WC tested up to: 5.5.1
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

// Make sure WooCommerce is active
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
	return;
}

/**
 * Current plugin version.
 */
define('QUAIFE_PG_VERSION', '1.2.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-quaife-pg-activator.php
 */
function activate_quaife_pg()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-quaife-pg-activator.php';
	Quaife_Pg_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-quaife-pg-deactivator.php
 */
function deactivate_quaife_pg()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-quaife-pg-deactivator.php';
	Quaife_Pg_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_quaife_pg');
register_deactivation_hook(__FILE__, 'deactivate_quaife_pg');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-quaife-pg.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_quaife_pg()
{

	$plugin = new Quaife_Pg();
	$plugin->run();
}
run_quaife_pg();
