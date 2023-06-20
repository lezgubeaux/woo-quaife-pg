<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://framework.tech
 * @since      1.0.0
 *
 * @package    Quaife_Pg
 * @subpackage Quaife_Pg/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Quaife_Pg
 * @subpackage Quaife_Pg/includes
 * @author     Vladimir Eric <Vladimir@framework.tech>
 */
class Quaife_Pg_i18n
{


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain()
	{

		load_plugin_textdomain(
			'woo-quaife-pg',
			false,
			dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
		);
	}
}
