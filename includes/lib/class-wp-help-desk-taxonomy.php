<?php
/**
 * This file contains the plugin custom taxonomies.
 *
 * @package WP_Help_Desk
 */

// If this file is called directly, abort..
if ( ! defined( 'WPINC' ) ) {
	 die;
}

/**
 * Plugin custom taxonomy class.
 */
class WP_Help_Desk_Taxonomy {

	/**
	 * The name for the taxonomy.
	 *
	 * @var    string
	 * @access public
	 * @since  0.1.0
	 */
	public $taxonomy;

	/**
	 * The plural name for the taxonomy terms.
	 *
	 * @var    string
	 * @access public
	 * @since  0.1.0
	 */
	public $plural;

	/**
	 * The singular name for the taxonomy terms.
	 *
	 * @var    string
	 * @access public
	 * @since  0.1.0
	 */
	public $single;

	/**
	 * The array of post types to which this taxonomy applies.
	 *
	 * @var    array
	 * @access public
	 * @since  0.1.0
	 */
	public $post_types;

	/**
	 * The array of taxonomy arguments
	 *
	 * @var    array
	 * @access public
	 * @since  0.1.0
	 */
	public $taxonomy_args;

	/**
	 * Construct.
	 *
	 * @param string $taxonomy   Taxonomy name.
	 * @param string $plural     Taxonomy plural.
	 * @param string $single     Taxonomy single.
	 * @param array  $post_types Taxonomy post types.
	 * @param array  $tax_args   Taxonomy args.
	 */
	public function __construct( $taxonomy = '', $plural = '', $single = '', $post_types = array(), $tax_args = array() ) {

		if ( ! $taxonomy || ! $plural || ! $single ) {
			return;
		}

		// Post type name and labels.
		$this->taxonomy = $taxonomy;
		$this->plural = $plural;
		$this->single = $single;
		if ( ! is_array( $post_types ) ) {
			$post_types = array( $post_types );
		}
		$this->post_types = $post_types;
		$this->taxonomy_args = $tax_args;

		// Register taxonomy.
		add_action( 'init', array( $this, 'register_taxonomy' ) );
	}

	/**
	 * Register new taxonomy.
	 *
	 * @return void
	 */
	public function register_taxonomy() {

		$labels = array(
			'name' => $this->plural,
			'singular_name'              => $this->single,
			'menu_name'                  => $this->plural,
			'all_items'                  => sprintf( __( 'All %s' , 'wp-help-desk' ), $this->plural ),
			'edit_item'                  => sprintf( __( 'Edit %s' , 'wp-help-desk' ), $this->single ),
			'view_item'                  => sprintf( __( 'View %s' , 'wp-help-desk' ), $this->single ),
			'update_item'                => sprintf( __( 'Update %s' , 'wp-help-desk' ), $this->single ),
			'add_new_item'               => sprintf( __( 'Add New %s' , 'wp-help-desk' ), $this->single ),
			'new_item_name'              => sprintf( __( 'New %s Name' , 'wp-help-desk' ), $this->single ),
			'parent_item'                => sprintf( __( 'Parent %s' , 'wp-help-desk' ), $this->single ),
			'parent_item_colon'          => sprintf( __( 'Parent %s:' , 'wp-help-desk' ), $this->single ),
			'search_items'               => sprintf( __( 'Search %s' , 'wp-help-desk' ), $this->plural ),
			'popular_items'              => sprintf( __( 'Popular %s' , 'wp-help-desk' ), $this->plural ),
			'separate_items_with_commas' => sprintf( __( 'Separate %s with commas' , 'wp-help-desk' ), $this->plural ),
			'add_or_remove_items'        => sprintf( __( 'Add or remove %s' , 'wp-help-desk' ), $this->plural ),
			'choose_from_most_used'      => sprintf( __( 'Choose from the most used %s' , 'wp-help-desk' ), $this->plural ),
			'not_found'                  => sprintf( __( 'No %s found' , 'wp-help-desk' ), $this->plural ),
		);

		$args = array(
			'label'                 => $this->plural,
			'labels'                => apply_filters( $this->taxonomy . '_labels', $labels ),
			'hierarchical'          => true,
			'public'                => true,
			'show_ui'               => true,
			'show_in_nav_menus'     => true,
			'show_tagcloud'         => true,
			'meta_box_cb'           => null,
			'show_admin_column'     => true,
			'show_in_quick_edit'    => true,
			'update_count_callback' => '',
			'show_in_rest'          => true,
			'rest_base'             => $this->taxonomy,
			'rest_controller_class' => 'WP_REST_Terms_Controller',
			'query_var'             => $this->taxonomy,
			'rewrite'               => true,
			'sort'                  => '',
		);

		$args = array_merge( $args, $this->taxonomy_args );

		register_taxonomy( $this->taxonomy, $this->post_types, apply_filters( $this->taxonomy . '_register_args', $args, $this->taxonomy, $this->post_types ) );
	}
}
