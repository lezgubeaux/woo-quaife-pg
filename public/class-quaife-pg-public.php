<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://framework.tech
 * @since      1.0.0
 *
 * @package    Quaife_Pg
 * @subpackage Quaife_Pg/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Quaife_Pg
 * @subpackage Quaife_Pg/public
 * @author     Vladimir Eric <Vladimir@framework.tech>
 */
class Quaife_Pg_Public
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Quaife_Pg_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Quaife_Pg_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/quaife-pg-public.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Quaife_Pg_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Quaife_Pg_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/quaife-pg-public.js', array('jquery'), $this->version, false);
	}

	/** 
	 * insert an iframe to payment method item on the checkout page
	 */
	public function insert_iframe($content)
	{
		/**
		 * iframe of PP 
		 * (js source: #quaife_payment_iframe -> target: .payment_box.payment_method_woo-quaife-pg)
		 */
		if (is_page('checkout') || is_checkout()) {

			// get saved url (grabbed from api check)
			$iframe_src = get_option('quaife_pg_api_iframe');

			if (!$iframe_src === false) {
				$content .= '
		<div id="quaife_payment_iframe">
            <iframe src="' . $iframe_src . '" />
        </div>';
			} else {
				// dummy example, remove on release
				$content .= '
        <div id="quaife_payment_iframe">
            <iframe class="quaife_pg_iframe" src="https://paymentpage.quaife.net/payment-page/ddqsy1oq2gywxqzxxj07ipbppd31afxycj620bmv/info" />
        </div>';
			}
		}

		return $content;
	}
}
