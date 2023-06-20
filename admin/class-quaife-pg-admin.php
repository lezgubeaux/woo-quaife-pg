<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://framework.tech
 * @since      1.0.0
 *
 * @package    Quaife_Pg
 * @subpackage Quaife_Pg/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Quaife_Pg
 * @subpackage Quaife_Pg/admin
 * @author     Vladimir Eric <Vladimir@framework.tech>
 */
class Quaife_Pg_Admin
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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/quaife-pg-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * An instance of this class should be passed to the run() function
		 * defined in Quaife_Pg_Loader
		 * The Quaife_Pg_Loader will then create hooks
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/quaife-pg-admin.js', array('jquery'), $this->version, false);
	}

	/**
	 * Add admin menu items
	 *
	 * @since    1.0.0
	 */
	public function add_admin_menu()
	{

		/**
		 * An instance of this class should be passed to the run() function
		 * defined in Quaife_Pg_Loader 
		 *
		 * The Quaife_Pg_Loader will then create hooks
		 */

		//add_menu_page( string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '', string $icon_url = '', int $position = null )
		add_menu_page(
			$this->plugin_name,
			__('Quaife Payment Gateway', 'woo-quaife-pg'),
			'administrator',
			$this->plugin_name . '-info',
			array(
				$this,
				'displayPluginAdminDashboard',
			),
			'dashicons-money-alt',
			20
		);

		//add_submenu_page( string $parent_slug, string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '', int $position = null )
		add_submenu_page(
			$this->plugin_name . '-info',
			'Settings',
			'Settings',
			'administrator',
			$this->plugin_name . '-settings',
			array(
				$this,
				'displayPluginAdminSettings',
			)
		);
	}

	/**
	 * Add admin submenu items
	 *
	 * @since    1.0.0
	 */
	public function displayPluginAdminDashboard()
	{

		/**
		 * An instance of this class should be passed to the run() function
		 * defined in Quaife_Pg_Loader
		 * The Quaife_Pg_Loader will then create hooks 
		 */

		require_once 'partials/' . $this->plugin_name . '-admin.php';
	}

	/**
	 * Add admin menu items
	 *
	 * @since    1.0.0
	 */
	public function displayPluginAdminSettings()
	{

		/**
		 * An instance of this class should be passed to the run() function
		 * defined in Quaife_Pg_Loader
		 *
		 * The Quaife_Pg_Loader will then create hooks
		 */

		require_once 'partials/' . $this->plugin_name . '-admin-settings.php';
	}

	/**
	 * Add settings form
	 *
	 * @since    1.0.0
	 */
	public function registerAndBuildFields()
	{
		/**
		 * First, we add_settings_section. This is necessary since all future settings must belong to one.
		 * Second, add_settings_field
		 * Third, register_setting
		 */
		add_settings_section(
			// ID used to identify this section and with which to register options
			'quaife_pg_general_settings',
			// Title to be displayed on the administration page
			'',
			// Callback used to render the description of the section
			array($this, 'quaife_pg_display_general_account'),
			// Page on which to add this section of options
			'quaife-pg-settings',
		);

		// API Key plugin settings field
		unset($args);
		$args = array(
			'type'      => 'input',
			'subtype'   => 'text',
			'id'    => 'quaife_pg_api_key',
			'name'      => 'quaife_pg_api_key',
			'required' => 'true',
			'get_options_list' => '',
			'value_type' => 'normal',
			'wp_data' => 'option'
		);
		add_settings_field(
			'quaife_pg_api_key',
			'API Key',
			array($this, 'quaife_pg_render_settings_field'),
			'quaife-pg-settings',
			'quaife_pg_general_settings',
			$args
		);
		register_setting(
			'quaife-pg-settings',
			'quaife_pg_api_key'
		);

		// API Secret plugin settings field
		unset($args);
		$args = array(
			'type'      => 'input',
			'subtype'   => 'password',
			'id'    => 'quaife_pg_api_secret',
			'name'      => 'quaife_pg_api_secret',
			'required' => 'true',
			'get_options_list' => '',
			'value_type' => 'normal',
			'wp_data' => 'option'
		);
		add_settings_field(
			'quaife_pg_api_secret',
			'API Secret',
			array($this, 'quaife_pg_render_settings_field'),
			'quaife-pg-settings',
			'quaife_pg_general_settings',
			$args
		);
		register_setting(
			'quaife-pg-settings',
			'quaife_pg_api_secret'
		);

		// API URL plugin settings field
		unset($args);
		$args = array(
			'type'      => 'input',
			'subtype'   => 'text',
			'id'    => 'quaife_pg_api_url',
			'name'      => 'quaife_pg_api_url',
			'required' => 'true',
			'get_options_list' => '',
			'value_type' => 'normal',
			'wp_data' => 'option'
		);
		add_settings_field(
			'quaife_pg_api_url',
			'API URL',
			array($this, 'quaife_pg_render_settings_field'),
			'quaife-pg-settings',
			'quaife_pg_general_settings',
			$args
		);
		register_setting(
			'quaife-pg-settings',
			'quaife_pg_api_url'
		);

		// API URL success plugin settings field
		unset($args);
		$args = array(
			'type'      => 'input',
			'subtype'   => 'text',
			'id'    => 'quaife_pg_api_url_success',
			'name'      => 'quaife_pg_api_url_success',
			'required' => 'true',
			'get_options_list' => '',
			'value_type' => 'normal',
			'wp_data' => 'option'
		);
		add_settings_field(
			'quaife_pg_api_url_success',
			'API URL (' . __('success', 'woo-quaife-pg') . ')',
			array($this, 'quaife_pg_render_settings_field'),
			'quaife-pg-settings',
			'quaife_pg_general_settings',
			$args
		);
		register_setting(
			'quaife-pg-settings',
			'quaife_pg_api_url_success'
		);

		// API URL cancel plugin settings field
		unset($args);
		$args = array(
			'type'      => 'input',
			'subtype'   => 'text',
			'id'    => 'quaife_pg_api_url_cancel',
			'name'      => 'quaife_pg_api_url_cancel',
			'required' => 'true',
			'get_options_list' => '',
			'value_type' => 'normal',
			'wp_data' => 'option'
		);
		add_settings_field(
			'quaife_pg_api_url_cancel',
			'API URL (' . __('cancelled', 'woo-quaife-pg') . ')',
			array($this, 'quaife_pg_render_settings_field'),
			'quaife-pg-settings',
			'quaife_pg_general_settings',
			$args
		);
		register_setting(
			'quaife-pg-settings',
			'quaife_pg_api_url_cancel'
		);

		// API URL failure plugin settings field
		unset($args);
		$args = array(
			'type'      => 'input',
			'subtype'   => 'text',
			'id'    => 'quaife_pg_api_url_failure',
			'name'      => 'quaife_pg_api_url_failure',
			'required' => 'true',
			'get_options_list' => '',
			'value_type' => 'normal',
			'wp_data' => 'option'
		);
		add_settings_field(
			'quaife_pg_api_url_failure',
			'API URL (' . __('failure', 'woo-quaife-pg') . ')',
			array($this, 'quaife_pg_render_settings_field'),
			'quaife-pg-settings',
			'quaife_pg_general_settings',
			$args
		);
		register_setting(
			'quaife-pg-settings',
			'quaife_pg_api_url_failure'
		);

		// API ?validated? plugin settings field
		unset($args);
		$args = array(
			'type'      => 'input',
			'subtype'   => 'hidden',
			'id'    => 'quaife_pg_api_validated',
			'name'      => 'quaife_pg_api_validated',
			'required' => 'true',
			'get_options_list' => '',
			'value_type' => 'normal',
			'wp_data' => 'option'
		);
		add_settings_field(
			'quaife_pg_api_validated',
			'',
			array($this, 'quaife_pg_render_settings_field'),
			'quaife-pg-settings',
			'quaife_pg_general_settings',
			$args
		);
		register_setting(
			'quaife-pg-settings',
			'quaife_pg_api_validated'
		);

		// if already marked as valid, leave it as-is
		if (!get_option('quaife_pg_api_validated')) {
			// APIs are 'not validated' by default
			update_option('quaife_pg_api_validated', false);
		}
	}

	/**
	 * Add settings form
	 *
	 * @since    1.0.0
	 */
	public function quaife_pg_display_general_account()
	{
		echo '<p>' . __('These settings apply to complete Qaife PG functionality.', 'woo-quaife-pg') . '</p>';
	}

	/**
	 * Add settings form
	 *
	 * @since    1.0.0
	 */
	public function quaife_pg_render_settings_field($args)
	{
		/* EXAMPLE INPUT
				  'type'      => 'input',
				  'subtype'   => '',
				  'id'    => $this->plugin_name.'_example_setting',
				  'name'      => $this->plugin_name.'_example_setting',
				  'required' => 'required="required"',
				  'get_option_list' => "",
					'value_type' = serialized OR normal,
		'wp_data'=>(option or post_meta),
		'post_id' =>
		*/
		if ($args['wp_data'] == 'option') {
			$wp_data_value = get_option($args['name']);
		} elseif ($args['wp_data'] == 'post_meta') {
			$wp_data_value = get_post_meta($args['post_id'], $args['name'], true);
		}

		switch ($args['type']) {

			case 'input':
				$value = ($args['value_type'] == 'serialized') ? serialize($wp_data_value) : $wp_data_value;
				if ($args['subtype'] != 'checkbox') {
					$prependStart = (isset($args['prepend_value'])) ? '<div class="input-prepend"> <span class="add-on">' . $args['prepend_value'] . '</span>' : '';
					$prependEnd = (isset($args['prepend_value'])) ? '</div>' : '';
					$step = (isset($args['step'])) ? 'step="' . $args['step'] . '"' : '';
					$min = (isset($args['min'])) ? 'min="' . $args['min'] . '"' : '';
					$max = (isset($args['max'])) ? 'max="' . $args['max'] . '"' : '';
					if (isset($args['disabled'])) {
						// hide the actual input bc if it was just a disabled input the informaiton saved in the database would be wrong - bc it would pass empty values and wipe the actual information
						echo $prependStart . '<input type="' . $args['subtype'] . '" id="' . $args['id'] . '_disabled" ' . $step . ' ' . $max . ' ' . $min . ' name="' . $args['name'] . '_disabled" size="40" disabled value="' . esc_attr($value) . '" /><input type="hidden" id="' . $args['id'] . '" ' . $step . ' ' . $max . ' ' . $min . ' name="' . $args['name'] . '" size="40" value="' . esc_attr($value) . '" />' . $prependEnd;
					} else {
						$msg = "'" . __('This field is mandatory!', 'woo-quaife-pg') . "'";
						echo $prependStart . '
							<input 
								data="default_tag" 
								type="' . $args['subtype'] . '" 
								id="' . $args['id'] . '" ' .
							($args['required'] == 'true' ? 'required' : '') . ' 
								name="' . $args['name'] . '" 
								size="40" 
								value="' . esc_attr($value) .  '" 					
								oninvalid="this.setCustomValidity(' . $msg . ')" 
								oninput="setCustomValidity(\'\')" />' .
							$prependEnd;
					}
					/*<input required="required" '.$disabled.' type="number" step="any" id="'.$this->plugin_name.'_cost2" name="'.$this->plugin_name.'_cost2" value="' . esc_attr( $cost ) . '" size="25" /><input type="hidden" id="'.$this->plugin_name.'_cost" step="any" name="'.$this->plugin_name.'_cost" value="' . esc_attr( $cost ) . '" />*/
				} else {
					$checked = ($value) ? 'checked' : '';
					echo '<input type="' . $args['subtype'] . '" id="' . $args['id'] . '" "' . ($args['required'] == 'true' ? 'required' : '') . '" name="' . $args['name'] . '" size="40" value="1" ' . $checked . ' />';
				}
				break;
			default:
				# code...
				break;
		}
	}

	/**
	 * Init Quaife Payment Gateway class
	 * 
	 * @since	1.1.0
	 */

	public function wc_quaife_gateway_init()
	{
		if (class_exists('WC_Payment_Gateway')) {

			require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-quaife-pg-gateway.php';
			require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-quaife-pg-gateway-checkout-description.php';

			return;
		}
	}

	/** 
	 * Add Quaife Payments to WOO PGs
	 * 
	 * @since 	1.1.0
	 */
	public function wc_quaife_add_to_gateways($gateways)
	{
		$gateways[] = 'WC_Gateway_Quaife_PG';
		return $gateways;
	}

	/**
	 * Adds plugin page links
	 * 
	 * @since 1.0.0
	 * @param array $links all plugin links
	 * @return array $links all plugin links + our custom links (i.e., "Settings")
	 */
	function woo_quaife_plugin_action_links($links)
	{

		$plugin_links = array(
			'<a href="' . admin_url('admin.php?page=wc-settings&tab=checkout&section=woo-quaife-pg') . '">' . __('Configure', 'woo-quaife-pg') . '</a>'
		);
		ve_debug_log("plugin links filter works!", "quaife");

		return array_merge($plugin_links, $links);
	}
}
