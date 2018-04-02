<?php
/**
 * This file contains the main plugin class.
 *
 * @package WP_Help_Desk
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	 die;
}

/**
 * Plugin class.
 */
class WP_Help_Desk {

	/**
	 * The single instance of WP_Help_Desk.
	 *
	 * @var    object
	 * @access private
	 * @since  0.1.0
	 */
	private static $_instance = null;

	/**
	 * Settings class object
	 *
	 * @var    object
	 * @access public
	 * @since  0.1.0
	 */
	public $settings = null;

	/**
	 * The version number.
	 *
	 * @var    string
	 * @access public
	 * @since  0.1.0
	 */
	public $_version;

	/**
	 * The token.
	 *
	 * @var    string
	 * @access public
	 * @since  0.1.0
	 */
	public $_token;

	/**
	 * The main plugin file.
	 *
	 * @var    string
	 * @access public
	 * @since  0.1.0
	 */
	public $file;

	/**
	 * The main plugin directory.
	 *
	 * @var    string
	 * @access public
	 * @since  0.1.0
	 */
	public $dir;

	/**
	 * The plugin assets directory.
	 *
	 * @var    string
	 * @access public
	 * @since  0.1.0
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 *
	 * @var    string
	 * @access public
	 * @since  0.1.0
	 */
	public $assets_url;

	/**
	 * Suffix for Javascripts.
	 *
	 * @var    string
	 * @access public
	 * @since  0.1.0
	 */
	public $script_suffix;

	/**
	 * Post type.
	 *
	 * @var    string
	 * @access public
	 * @since  0.1.0
	 */
	public $post_type = 'article';

	/**
	 * Constructor function.
	 *
	 * @access  public
	 * @since   0.1.0
	 * @return  void
	 */
	public function __construct( $file = '', $version = '0.1.0' ) {
		$this->_version = $version;
		$this->_token = 'wp_help_desk';

		// Load plugin environment variables.
		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		register_activation_hook( $this->file, array( $this, 'install' ) );

		// Enable support for Markdown Editor plugin.
		add_post_type_support( $this->post_type, 'wpcom-markdown' );

		// Load frontend JS & CSS.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );

		// Load admin JS & CSS.
		// add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10, 1 );
		// add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ), 10, 1 );

		// Filter custom post type content.
		add_filter( 'the_content', array( $this, 'filter_content' ) );

		// Load shortcodes and widgets.
		$this->shortcodes = new WP_Help_Desk_Shortcodes();

		// Load API for generic admin functions.
		if ( is_admin() ) {
			$this->admin = new WP_Help_Desk_Admin_API();
		}

		// Handle localization.
		$this->load_plugin_textdomain();
		add_action( 'init', array( $this, 'load_localization' ), 0 );

	}

	/**
	 * Wrapper function to register a new post type.
	 *
	 * @param  string $post_type   Post type name.
	 * @param  string $plural      Post type item plural name.
	 * @param  string $single      Post type item single name.
	 * @param  string $description Description of post type.
	 * @param  array  $options     Post type options.
	 * @return object              Post type class object
	 */
	public function register_post_type( $post_type = '', $plural = '', $single = '', $description = '', $options = array() ) {

		if ( ! $post_type || ! $plural || ! $single ) return;

		$post_type = new WP_Help_Desk_Post_Type( $post_type, $plural, $single, $description, $options );

		return $post_type;
	}

	/**
	 * Wrapper function to register a new taxonomy.
	 *
	 * @param  string $taxonomy      Taxonomy name.
	 * @param  string $plural        Taxonomy single name.
	 * @param  string $single        Taxonomy plural name.
	 * @param  array  $post_types    Post types to which this taxonomy applies.
	 * @param  array  $taxonomy_args Post type options.
	 * @return object                Taxonomy class object
	 */
	public function register_taxonomy( $taxonomy = '', $plural = '', $single = '', $post_types = array(), $taxonomy_args = array() ) {

		if ( ! $taxonomy || ! $plural || ! $single ) return;

		$taxonomy = new WP_Help_Desk_Taxonomy( $taxonomy, $plural, $single, $post_types, $taxonomy_args );

		return $taxonomy;
	}

	/**
	 * Load frontend CSS.
	 *
	 * @access  public
	 * @since   0.1.0
	 * @return void
	 */
	public function enqueue_styles() {
		wp_register_style( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'css/frontend.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-frontend' );
	}

	/**
	 * Load frontend Javascript.
	 *
	 * @access  public
	 * @since   0.1.0
	 * @return  void
	 */
	public function enqueue_scripts() {
		wp_register_script( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'js/frontend' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version, true );
		wp_enqueue_script( $this->_token . '-frontend' );
	}

	/**
	 * Load admin CSS.
	 *
	 * @access  public
	 * @since   0.1.0
	 * @return  void
	 */
	public function admin_enqueue_styles( $hook = '' ) {
		wp_register_style( $this->_token . '-admin', esc_url( $this->assets_url ) . 'css/admin.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-admin' );
	}

	/**
	 * Load admin Javascript.
	 *
	 * @access  public
	 * @since   0.1.0
	 * @return  void
	 */
	public function admin_enqueue_scripts( $hook = '' ) {
		wp_register_script( $this->_token . '-admin', esc_url( $this->assets_url ) . 'js/admin' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
		wp_enqueue_script( $this->_token . '-admin' );
	}

	/**
	 * Load plugin localization.
	 *
	 * @access  public
	 * @since   0.1.0
	 * @return  void
	 */
	public function load_localization() {
		load_plugin_textdomain( 'wp-help-desk', false, dirname( plugin_basename( $this->file ) ) . '/languages/' );
	}

	/**
	 * Load plugin textdomain
	 *
	 * @access  public
	 * @since   0.1.0
	 * @return  void
	 */
	public function load_plugin_textdomain() {
		$domain = 'wp-help-desk';

		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . 		$locale . '.mo' );
		load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->file ) ) . '/languages/' );
	}

	/**
	 * Filter the content headings.
	 *
	 * @param string $content Post content.
	 * @return string
	 */
	function filter_content( $content ) {

		if ( ! is_singular( $this->post_type ) ) {
			return $content;
		}

		// Search content for H2 headings.
		$headings = preg_match_all( '/<h2>(.*?)<\/h2>/', $content, $matches, PREG_SET_ORDER );

		// Generate links for each heading.
		$links = array();

		foreach ( $matches as $match ) {
			$links[] = sprintf( '<li><a href="#%s" title="Skip to %2$s"><span class="screen-reader-text">Skip to </span>%s</a></li>', sanitize_title_with_dashes( $match[1] ), $match[1] );
		}

		$label = apply_filters( 'wp_help_desk_toc_label', __( 'Table of contents', 'wp-help-desk' ) );

		// Prepend links to content.
		$content = sprintf( '<nav role="navigation" class="table-of-contents"><strong>%s</strong><ol>%s</ol></nav>%s', $label, implode( array_unique( $links ) ), $content );

		// Pattern that we want to match.
		$pattern = '/<h2>(.*?)<\/h2>/';

		// Add id values to H2 headings.
		$content = preg_replace_callback( $pattern, function( $matches ) {
			$title = $matches[1];
			$slug = sanitize_title_with_dashes( $title );
			return '<h2 id="' . $slug . '">' . $title . '</h2>';
		}, $content );

		return $content;
	}

	/**
	 * Main WP_Help_Desk Instance
	 *
	 * Ensures only one instance of WP_Help_Desk is loaded or can be loaded.
	 *
	 * @since 0.1.0
	 * @static
	 * @see WP_Help_Desk()
	 * @return Main WP_Help_Desk instance
	 */
	public static function instance( $file = '', $version = '0.1.0' ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 0.1.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 0.1.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	}

	/**
	 * Installation. Runs on activation.
	 *
	 * @access  public
	 * @since   0.1.0
	 * @return  void
	 */
	public function install() {
		$this->_log_version_number();
	}

	/**
	 * Log the plugin version number.
	 *
	 * @access  public
	 * @since   0.1.0
	 * @return  void
	 */
	private function _log_version_number() {
		update_option( $this->_token . '_version', $this->_version );
	}

}
