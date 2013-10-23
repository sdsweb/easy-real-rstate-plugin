<?php
/**
 * Agents_Widget
 *
 * Description: Display agents based on settings.
 *
 * @since 1.0
 */

if( ! class_exists( 'Agents_Widget' ) ) {
	class Agents_Widget extends WP_Widget {
		private static $instance; // Keep track of the instance

		/*
		 * Function used to create instance of class.
		 * This is used to prevent over-writing of a variable (old method), i.e. $nbtg = new NBTG();
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) )
				self::$instance = new Agents_Widget;

			return self::$instance;
		}

		/*
		 * This function sets up all widget options including class name, description, width/height, and creates an instance of the widget
		 */
		function __construct() {
			$widget_options = array( 'classname' => 'ere-agents-widget agents-widget', 'description' => 'Displays agent featured images based on settings.' );
			$control_options = array( 'width' => 200, 'height' => 350, 'id_base' => 'agents-widget' );
			self::WP_Widget( 'agents-widget', 'Agents', $widget_options, $control_options );

			add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) ); // Enqueue CSS
		}


		/**
		 * This function sets up the form on admin pages
		 */
		function form( $instance ) {
			global $post;

			// Set up the default widget settings.
			$defaults = array(
				'title' => false,
				'post__not_in' => false
			);
			$instance = wp_parse_args( (array) $instance, $defaults ); // Parse any saved arguments into defaults
		?>
		
			<p>
				<?php // Widget Title ?>
				<label for="<?php echo $this->get_field_id( 'title' ) ; ?>"><strong>Title</strong></label>
				<br />
				<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
			</p>

			<p>
				<?php // Post Not In (posts to specifically exclude) ?>
				<label for="<?php echo $this->get_field_id( 'post__not_in' ); ?>"><strong>Exclude Posts</strong></label>
				<br />
				<input id="<?php echo $this->get_field_id( 'post__not_in' ); ?>" name="<?php echo $this->get_field_name( 'post__not_in' ); ?>" value="<?php echo esc_attr( $instance['post__not_in'] ); ?>" />
				<br />
				<small class="description">Comma separated list of post IDs. Will display all posts except these.</small>
			</p>
		<?php
		}

		/**
		 * This function handles updating (saving) widget options
		 */
		function update( $new_instance, $old_instance ) {
			// Sanitize all input data
			$new_instance['title'] = sanitize_text_field( $new_instance['title'] ); // Widget Title
			$new_instance['post__not_in'] = sanitize_text_field( str_replace( ' ', '', $new_instance['post__not_in'] ) ); // Exclude Posts

			return $new_instance;
		}

		/*
		 * This function controls the display of the widget on the website
		 */
		function widget( $args, $instance ) {
			global $post;

			extract( $args ); // $before_widget, $after_widget, $before_title, $after_title

			// Start of widget output
			echo $before_widget;

			if ( ! empty( $instance['title'] ) )
				echo $before_title . $instance['title'] . $after_title;

			/*
			 * Set up query arguments
			 */
			$ere_agents_args = array( 'post_type' => 'ere_agents', 'posts_per_page' => wp_count_posts( 'ere_agents' )->publish, 'orderby' => 'name', 'order' => 'ASC' );

			// If a posts should be excluded (and none to be included)
			if ( isset( $instance['post__not_in'] ) && ! empty( $instance['post__not_in'] ) )
				$ere_agents_args['post__not_in'] = explode( ',', $instance['post__not_in'] );

			$ere_agents_query = new WP_Query( $ere_agents_args );

			// Display featured content
			if ( $ere_agents_query->have_posts() ) :
		?>
				<section class="ere-agents agents-clear"></section>
				<section class="agents-widget agents">
					<?php while ( $ere_agents_query->have_posts() ) : $ere_agents_query->next_post(); ?>
						<section class="agent">
							<a href="<?php echo get_permalink( $ere_agents_query->post->ID ); ?>">
								<?php if ( has_post_thumbnail( $ere_agents_query->post->ID ) ) : // Post Thumbnail ?>
									<?php echo get_the_post_thumbnail( $ere_agents_query->post->ID, 'thumbnail' ); ?>
								<?php else : // No post thumbnail, use mystery man from Gravatar ?>
									<img src="<?php echo ERE_PLUGIN_URL . 'includes/widgets/agents/images/avatar.png' ?>" alt="Agent">
								<?php endif; ?>
							</a>
						</section>
					<?php endwhile; ?>
				</section>
		<?php
			endif;

			// End of widget output
			echo $after_widget;
		}


		/**
		 * This function enqueues the necessary styles associated with this widget.
		 */
		function wp_enqueue_scripts() {
			// Only enqueue styles if this widget is active
			if ( is_active_widget( false, false, $this->id_base, true ) )
				wp_enqueue_style( 'ere-agents', ERE_PLUGIN_URL . 'includes/widgets/agents/css/agents-widget.css' );
		}
	}

	function Agents_Widget_Instance() {
		return Agents_Widget::instance();
	}

	Agents_Widget_Instance();
}