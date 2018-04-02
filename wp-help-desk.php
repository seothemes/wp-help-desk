<?php
/**
 * Plugin Name: WP Help Desk
 * Version: 0.1.0
 * Plugin URI: https://seothemes.com/
 * Description: Documentation and knowledge base WordPress plugin.
 * Author: SEO Themes
 * Author URI: https://seothemes.com/
 * Requires at least: 4.7
 * Tested up to: 4.9.4
 *
 * Text Domain: wp-help-desk
 * Domain Path: /lang/
 *
 * @package WP_Help_Desk
 * @author  SEO Themes
 * @since   0.1.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	 die;
}

// Load plugin libraries.
require_once( 'includes/lib/class-wp-help-desk-admin-api.php' );
require_once( 'includes/lib/class-wp-help-desk-post-type.php' );
require_once( 'includes/lib/class-wp-help-desk-taxonomy.php' );

// Load plugin class files.
require_once( 'includes/class-wp-help-desk.php' );
require_once( 'includes/class-wp-help-desk-settings.php' );
require_once( 'includes/class-wp-help-desk-shortcodes.php' );
require_once( 'includes/class-wp-help-desk-widgets.php' );

// Register custom post type.
WP_Help_Desk()->register_post_type(
	'article',
	__( 'Articles', 'wp-help-desk' ),
	__( 'Article', 'wp-help-desk' )
);

// Register custom taxonomy.
WP_Help_Desk()->register_taxonomy(
	'topic',
	__( 'Topics', 'wp-help-desk' ),
	__( 'Topic', 'wp-help-desk' ),
	'article'
);

add_filter( 'article_labels', 'wp_help_desk_filter_cpt_label' );
/**
 * Modify post type settings.
 *
 * @param  array $args Custom post type args.
 * @return array
 */
function wp_help_desk_filter_cpt_label( $args ) {
	$args['menu_name'] = 'Help Desk';
	return $args;
}

add_filter( 'article_register_args', 'wp_help_desk_filter_cpt' );
/**
 * Modify post type settings.
 *
 * @param  array $args Custom post type args.
 * @return array
 */
function wp_help_desk_filter_cpt( $args ) {
	$args['menu_icon'] = 'dashicons-sos';
	$args['supports']  = array( 'title', 'editor', 'genesis-cpt-archives-settings' );
	return $args;
}

/**
 * Returns the main instance of WP_Help_Desk to
 * prevent the need to use globals.
 *
 * @since  0.1.0
 * @return object WP_Help_Desk
 */
function WP_Help_Desk() {
	$instance = WP_Help_Desk::instance( __FILE__, '0.1.0' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = WP_Help_Desk_Settings::instance( $instance );
	}

	return $instance;
}

WP_Help_Desk();
