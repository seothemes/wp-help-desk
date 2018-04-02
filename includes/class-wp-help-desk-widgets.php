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
 * Widget class.
 */
class WP_Help_Desk_Widget extends WP_Widget {

	/**
	 * Post type.
	 */
	public $post_type = 'article';

	/**
	 * Taxonomy.
	 */
	public $taxonomy = 'topic';

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array(
			'classname' => 'wp_help_desk_widget',
			'description' => 'List of documentation topics',
		);
		parent::__construct( 'wp_help_desk_widget', 'Topics', $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		echo do_shortcode( '[list-docs]' );
		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options.
	 */
	public function form( $instance ) {

		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Docs', 'wp-help-desk' );
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options.
	 * @param array $old_instance The previous options.
	 */
	public function update( $new_instance, $old_instance ) {

		foreach ( $new_instance as $key => $value ) {
			$updated_instance[$key] = sanitize_text_field( $value );
		}

		return $updated_instance;
	}
}

// Register widget.
add_action( 'widgets_init', function(){
	register_widget( 'WP_Help_Desk_Widget' );
});


/**
 * Widget class.
 */
class WP_Help_Desk_Search_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array(
			'classname' => 'wp_help_desk_search_widget',
			'description' => 'Search docs',
		);
		parent::__construct( 'wp_help_desk_search_widget', 'Search Docs', $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		echo do_shortcode( '[search-docs]' );
		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options.
	 */
	public function form( $instance ) {

		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Search Docs', 'wp-help-desk' );
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options.
	 * @param array $old_instance The previous options.
	 */
	public function update( $new_instance, $old_instance ) {

		foreach ( $new_instance as $key => $value ) {
			$updated_instance[$key] = sanitize_text_field( $value );
		}

		return $updated_instance;
	}
}

// Register widget.
add_action( 'widgets_init', function(){
	register_widget( 'WP_Help_Desk_Search_Widget' );
});
