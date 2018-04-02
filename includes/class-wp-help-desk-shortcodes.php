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

class WP_Help_Desk_Shortcodes {

	/**
	 * Post type.
	 */
	public $post_type = 'article';

	/**
	 * Taxonomy.
	 */
	public $taxonomy = 'topic';

	/**
	 * Constructor.
	 */
	public function __construct() {

		add_shortcode( 'list-docs', array( $this, 'wp_help_desk_display_topics' ) );

		add_shortcode( 'search-docs', array( $this, 'wp_help_desk_search' ) );

	}

	/**
	 * List docs.
	 *
	 * Displays an unordered list of topics and docs links.
	 * Only the current topic will show docs, others will be hidden.
	 *
	 * @return string
	 */
	public function wp_help_desk_display_topics() {

		$page_id = get_the_ID();

		wp_reset_query();

		$topics = get_terms( $this->taxonomy );

		$html = '<div class="accordion">';

		foreach ( $topics as $topic ) {

			$args = array(
				'post_type' => $this->post_type,
				'tax_query' => array(
					array(
						'taxonomy' => $this->taxonomy,
						'field'    => 'slug',
						'terms'    => $topic->slug,
					),
				),
			);

			$loop = new WP_Query( $args );

			if ( $loop->have_posts() ) {

				$active = is_singular( $this->post_type ) && has_term( $topic->slug, $this->taxonomy, $page_id ) ? ' is-active' : '';

				$html .= '<div class="topic">';

				$html .= sprintf( '<button class="accordion-header%s" type="button" >%s</button>', $active, esc_html( $topic->name ) );

				$html .= sprintf( '<ul class="accordion-content%s">', $active );

				while ( $loop->have_posts() ) {

					$loop->the_post();

					$html .= sprintf( '<li><a href="%s">%s</a></li>', get_permalink(), get_the_title() );
				}

				$html .= '</ul></div>';

			}
		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * Search form.
	 *
	 * Displays a search form that only returns results for docs.
	 *
	 * @return void
	 */
	public function wp_help_desk_search() {
		?>
		<form class="search-form" role="search" action="<?php echo home_url( '/' ); ?>">
			<input type="search" class="search-field" name="s" placeholder="Search <?php echo $this->post_type; ?>s&hellip;">
			<input type="submit" class="search-submit" value="Search">
			<input type="hidden" name="post_type" value="<?php echo $this->post_type; ?>">
		</form>
		<?php
	}
}
