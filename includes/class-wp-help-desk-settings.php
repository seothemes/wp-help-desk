<?php
/**
 * This file contains the plugin settings.
 *
 * @package WP_Help_Desk
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	 die;
}

/**
 * Plugin settings class.
 */
class WP_Help_Desk_Settings {

	/**
	 * The single instance of WP_Help_Desk_Settings.
	 *
	 * @var    object
	 * @access private
	 * @since  0.1.0
	 */
	private static $_instance = null;

	/**
	 * The main plugin object.
	 *
	 * @var    object
	 * @access public
	 * @since  0.1.0
	 */
	public $parent = null;

	/**
	 * Prefix for plugin settings.
	 *
	 * @var    string
	 * @access public
	 * @since  0.1.0
	 */
	public $base = '';

	/**
	 * Available settings for plugin.
	 *
	 * @var    array
	 * @access public
	 * @since  0.1.0
	 */
	public $settings = array();

	/**
	 * Constructor.
	 *
	 * @param object $parent Parent class.
	 */
	public function __construct( $parent ) {
		$this->parent = $parent;

		$this->base = 'wp_help_desk_';

		// Initialize settings.
		add_action( 'init', array( $this, 'init_settings' ), 11 );

		// Register plugin settings.
		add_action( 'admin_init' , array( $this, 'register_settings' ) );

		// Add settings page to menu.
		// add_action( 'admin_menu' , array( $this, 'add_menu_item' ) );

		// Add settings link to plugins page.
		add_filter( 'plugin_action_links_' . plugin_basename( $this->parent->file ) , array( $this, 'add_settings_link' ) );
	}

	/**
	 * Initialize settings.
	 *
	 * @return void
	 */
	public function init_settings() {
		$this->settings = $this->settings_fields();
	}

	/**
	 * Add settings page to admin menu.
	 *
	 * @return void
	 */
	public function add_menu_item() {
		$page = add_submenu_page(
			'edit.php?post_type=article',
			__( 'Settings', 'wp-help-desk' ),
			__( 'Settings', 'wp-help-desk' ),
			'manage_options',
			$this->parent->_token . '_settings',
			array( $this, 'settings_page' )
		);
		add_action( 'admin_print_styles-' . $page, array( $this, 'settings_assets' ) );
	}

	/**
	 * Load settings JS & CSS.
	 *
	 * @return void
	 */
	public function settings_assets() {

		// We're including the farbtastic script & styles here because they're needed for the colour picker. If you're not including a colour .picker field then you can leave these calls out as well as the farbtastic dependency for the wpt-admin-js script below.
		wp_enqueue_style( 'farbtastic' );
		wp_enqueue_script( 'farbtastic' );

		// We're including the WP media scripts here because they're needed for the image upload field If you're not including an image upload .then you can leave this function call out.
		wp_enqueue_media();
		wp_register_script( $this->parent->_token . '-settings-js', $this->parent->assets_url . 'js/settings' . $this->parent->script_suffix . '.js', array( 'farbtastic', 'jquery' ), '0.1.0' );
		wp_enqueue_script( $this->parent->_token . '-settings-js' );
	}

	/**
	 * Add settings link to plugin list table.
	 *
	 * @param  array $links Existing links.
	 * @return array 		Modified links
	 */
	public function add_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=' . $this->parent->_token . '_settings">' . __( 'Settings', 'wp-help-desk' ) . '</a>';
		array_push( $links, $settings_link );
		return $links;
	}

	/**
	 * Build settings fields.
	 *
	 * @return array Fields to be displayed on settings page
	 */
	private function settings_fields() {

		$settings['standard'] = array(
			'title'					=> __( 'Standard', 'wp-help-desk' ),
			'description'			=> __( 'These are fairly standard form input fields.', 'wp-help-desk' ),
			'fields'				=> array(
				array(
					'id' 			=> 'text_field',
					'label'			=> __( 'Some Text' , 'wp-help-desk' ),
					'description'	=> __( 'This is a standard text field.', 'wp-help-desk' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'Placeholder text', 'wp-help-desk' ),
				),
				array(
					'id' 			=> 'password_field',
					'label'			=> __( 'A Password' , 'wp-help-desk' ),
					'description'	=> __( 'This is a standard password field.', 'wp-help-desk' ),
					'type'			=> 'password',
					'default'		=> '',
					'placeholder'	=> __( 'Placeholder text', 'wp-help-desk' ),
				),
				array(
					'id' 			=> 'secret_text_field',
					'label'			=> __( 'Some Secret Text' , 'wp-help-desk' ),
					'description'	=> __( 'This is a secret text field - any data saved here will not be displayed after the page has reloaded, but it will be saved.', 'wp-help-desk' ),
					'type'			=> 'text_secret',
					'default'		=> '',
					'placeholder'	=> __( 'Placeholder text', 'wp-help-desk' ),
				),
				array(
					'id' 			=> 'text_block',
					'label'			=> __( 'A Text Block' , 'wp-help-desk' ),
					'description'	=> __( 'This is a standard text area.', 'wp-help-desk' ),
					'type'			=> 'textarea',
					'default'		=> '',
					'placeholder'	=> __( 'Placeholder text for this textarea', 'wp-help-desk' ),
				),
				array(
					'id' 			=> 'single_checkbox',
					'label'			=> __( 'An Option', 'wp-help-desk' ),
					'description'	=> __( 'A standard checkbox - if you save this option as checked then it will store the option as \'on\', otherwise it will be an empty string.', 'wp-help-desk' ),
					'type'			=> 'checkbox',
					'default'		=> '',
				),
				array(
					'id' 			=> 'select_box',
					'label'			=> __( 'A Select Box', 'wp-help-desk' ),
					'description'	=> __( 'A standard select box.', 'wp-help-desk' ),
					'type'			=> 'select',
					'options'		=> array(
						'drupal' => 'Drupal',
						'joomla' => 'Joomla',
						'wordpress' => 'WordPress',
					),
					'default'		=> 'wordpress',
				),
				array(
					'id' 			=> 'radio_buttons',
					'label'			=> __( 'Some Options', 'wp-help-desk' ),
					'description'	=> __( 'A standard set of radio buttons.', 'wp-help-desk' ),
					'type'			=> 'radio',
					'options'		=> array(
						'superman' => 'Superman',
						'batman' => 'Batman',
						'ironman' => 'Iron Man',
					),
					'default'		=> 'batman',
				),
				array(
					'id' 			=> 'multiple_checkboxes',
					'label'			=> __( 'Some Items', 'wp-help-desk' ),
					'description'	=> __( 'You can select multiple items and they will be stored as an array.', 'wp-help-desk' ),
					'type'			=> 'checkbox_multi',
					'options'		=> array(
						'square' => 'Square',
						'circle' => 'Circle',
						'rectangle' => 'Rectangle',
						'triangle' => 'Triangle',
					),
					'default'		=> array( 'circle', 'triangle' ),
				),
			),
		);

		$settings['extra'] = array(
			'title'					=> __( 'Extra', 'wp-help-desk' ),
			'description'			=> __( 'These are some extra input fields that maybe aren\'t as common as the others.', 'wp-help-desk' ),
			'fields'				=> array(
				array(
					'id' 			=> 'number_field',
					'label'			=> __( 'A Number' , 'wp-help-desk' ),
					'description'	=> __( 'This is a standard number field - if this field contains anything other than numbers then the form will not be submitted.', 'wp-help-desk' ),
					'type'			=> 'number',
					'default'		=> '',
					'placeholder'	=> __( '42', 'wp-help-desk' ),
				),
				array(
					'id' 			=> 'colour_picker',
					'label'			=> __( 'Pick a colour', 'wp-help-desk' ),
					'description'	=> __( 'This uses WordPress\' built-in colour picker - the option is stored as the colour\'s hex code.', 'wp-help-desk' ),
					'type'			=> 'color',
					'default'		=> '#21759B',
				),
				array(
					'id' 			=> 'an_image',
					'label'			=> __( 'An Image' , 'wp-help-desk' ),
					'description'	=> __( 'This will upload an image to your media library and store the attachment ID in the option field. Once you have uploaded an imge the thumbnail will display above these buttons.', 'wp-help-desk' ),
					'type'			=> 'image',
					'default'		=> '',
					'placeholder'	=> '',
				),
				array(
					'id' 			=> 'multi_select_box',
					'label'			=> __( 'A Multi-Select Box', 'wp-help-desk' ),
					'description'	=> __( 'A standard multi-select box - the saved data is stored as an array.', 'wp-help-desk' ),
					'type'			=> 'select_multi',
					'options'		=> array(
						'linux' => 'Linux',
						'mac' => 'Mac',
						'windows' => 'Windows',
					),
					'default'		=> array( 'linux' ),
				),
			),
		);

		$settings = apply_filters( $this->parent->_token . '_settings_fields', $settings );

		return $settings;
	}

	/**
	 * Register plugin settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		if ( is_array( $this->settings ) ) {

			// Check posted/selected tab.
			$current_section = '';
			if ( isset( $_POST['tab'] ) && $_POST['tab'] ) {
				$current_section = $_POST['tab'];
			} else {
				if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
					$current_section = $_GET['tab'];
				}
			}

			foreach ( $this->settings as $section => $data ) {

				if ( $current_section && $current_section != $section ) {
					continue;
				}

				// Add section to page.
				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), $this->parent->_token . '_settings' );

				foreach ( $data['fields'] as $field ) {

					// Validation callback for field.
					$validation = '';
					if ( isset( $field['callback'] ) ) {
						$validation = $field['callback'];
					}

					// Register field.
					$option_name = $this->base . $field['id'];
					register_setting( $this->parent->_token . '_settings', $option_name, $validation );

					// Add field to page.
					add_settings_field( $field['id'], $field['label'], array( $this->parent->admin, 'display_field' ), $this->parent->_token . '_settings', $section, array( 'field' => $field, 'prefix' => $this->base ) );
				}

				if ( ! $current_section ) {
					break;
				}
			}
		} // End if().
	}

	/**
	 * Settings section.
	 *
	 * @param  array $section Array of settings.
	 * @return void
	 */
	public function settings_section( $section ) {
		$html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo $html;
	}

	/**
	 * Load settings page content.
	 *
	 * @return void
	 */
	public function settings_page() {

		// Build page HTML.
		$html = '<div class="wrap" id="' . $this->parent->_token . '_settings">' . "\n";
		$html .= '<h2>' . __( 'WP Help Desk' , 'wp-help-desk' ) . '</h2>' . "\n";

		$tab = '';
		if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
			$tab .= $_GET['tab'];
		}

		// Show page tabs.
		if ( is_array( $this->settings ) && 1 < count( $this->settings ) ) {

			$html .= '<h2 class="nav-tab-wrapper">' . "\n";

			$c = 0;
			foreach ( $this->settings as $section => $data ) {

				// Set tab class.
				$class = 'nav-tab';
				if ( ! isset( $_GET['tab'] ) ) {
					if ( 0 == $c ) {
						$class .= ' nav-tab-active';
					}
				} else {
					if ( isset( $_GET['tab'] ) && $section == $_GET['tab'] ) {
						$class .= ' nav-tab-active';
					}
				}

				// Set tab link.
				$tab_link = add_query_arg( array(
					'tab' => $section,
				) );
				if ( isset( $_GET['settings-updated'] ) ) {
					$tab_link = remove_query_arg( 'settings-updated', $tab_link );
				}

				// Output tab.
				$html .= '<a href="' . $tab_link . '" class="' . esc_attr( $class ) . '">' . esc_html( $data['title'] ) . '</a>' . "\n";

				++$c;
			}

			$html .= '</h2>' . "\n";
		}

		$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";

			// Get settings fields.
			ob_start();
			settings_fields( $this->parent->_token . '_settings' );
			do_settings_sections( $this->parent->_token . '_settings' );
			$html .= ob_get_clean();

			$html .= '<p class="submit">' . "\n";
					$html .= '<input type="hidden" name="tab" value="' . esc_attr( $tab ) . '" />' . "\n";
					$html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Settings' , 'wp-help-desk' ) ) . '" />' . "\n";
				$html .= '</p>' . "\n";
			$html .= '</form>' . "\n";
		$html .= '</div>' . "\n";

		echo $html;
	}

	/**
	 * Main WP_Help_Desk_Settings Instance
	 *
	 * Ensures only one instance of WP_Help_Desk_Settings is loaded or can be loaded.
	 *
	 * @since  0.1.0
	 * @static
	 * @see    WP_Help_Desk().
	 * @param  object $parent Parent class.
	 * @return Main WP_Help_Desk_Settings instance
	 */
	public static function instance( $parent ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $parent );
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 0.1.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 0.1.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	}

}
