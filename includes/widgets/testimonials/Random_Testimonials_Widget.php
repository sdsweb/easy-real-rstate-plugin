<?php
/**
 * Random_Testimonials_Widget
 *
 * Description: Display random testimonials based on settings.
 *
 * @since 1.0
 */

if( ! class_exists( 'Random_Testimonials_Widget' ) ) {
	class Random_Testimonials_Widget extends WP_Widget {

		private static $instance; // Keep track of the instance

		/**
		 * Function used to create instance of class.
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) )
				self::$instance = new Random_Testimonials_Widget;

			return self::$instance;
		}

		/**
		 * This function sets up all of the actions, filters, and the widget on instance
		 */
		function __construct() {
			$widget_options = array( 'description' => 'Displays random testimonials based on settings.' );
			$control_options = array( 'width' => 200, 'height' => 350, 'id_base' => 'random-testimonials-widget' );
			self::WP_Widget( 'random-testimonials-widget', 'Random Testimonials', $widget_options, $control_options );

			add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) ); // Enqueue CSS
		}

		/**
		 * This function sets up the form on admin pages
		 */
		function form( $instance ) {
			// Set up the default widget settings.
			$defaults = array( 'number' => 1, 'title' => false, 'show_thumbnails' => false );
			$instance = wp_parse_args( (array) $instance, $defaults ); // Parse any saved arguments into defaults
?>	
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ) ; ?>"><strong>Title:</strong></label>
				<br />
				<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo ( ! empty( $instance['title'] ) ) ? esc_attr( $instance['title'] ) : false; ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'number' ) ; ?>"><strong>Number of Testimonials to display:</strong></label>
				<br />
				<select id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>">
					<?php
						// Get count of published testimonials
						$testimonials_count = wp_count_posts( 'ere_testimonials' )->publish;

						for( $i = 1; $i <= $testimonials_count; $i++ ) :
					?>
						<option value="<?php echo $i; ?>" <?php selected( $instance['number'], $i ); ?>><?php echo $i; ?></option>
					<?php
						endfor;
					?>
				</select>
			</p>

			<p>
				<input id="<?php echo $this->get_field_id( 'show_thumbnails' ); ?>"  name="<?php echo $this->get_field_name( 'show_thumbnails' ); ?>" type="checkbox" <?php checked( $instance['show_thumbnails'], true ); ?> />
				<label for="<?php echo $this->get_field_id( 'show_thumbnails' ) ; ?>"><strong>Show Featured Images</strong></label>
			</p>
<?php 
		}

		/*
		 * This function handles updating (saving) widget options
		 */
		function update( $new_instance, $old_instance ) {
			// Sanitize all input data
			$new_instance['title'] = sanitize_text_field( $new_instance['title'] ); // Widget Title
			$new_instance['number'] = ( int ) $new_instance['number']; // Number of Testimonials to display
			$new_instance['show_thumbnails'] = ( isset( $new_instance['show_thumbnails'] ) ) ? true : false; // Show Featured Images
			return $new_instance;
		}

		/*
		 * This function controls the display of the widget on the website
		 */
		function widget( $args, $instance ) {
			extract( $args );
			
			if( ! empty( $instance['number'] ) ) {
				$random_testimonials = new WP_Query( 
					array(
						'posts_per_page' => (int) $instance['number'],
						'post_type' => 'ere_testimonials',
						'orderby' => 'rand'
					)
				);
				
				echo $before_widget;

				if ( ! empty( $instance['title'] ) )
					echo $before_title . $instance['title'] . $after_title;

				if( $random_testimonials->have_posts() ) :
		?>
				<section class="random-testimonials testimonials-container">
		<?php
					while( $random_testimonials->have_posts() ) : $random_testimonials->the_post();
						$credits = get_post_meta( get_the_ID(), '_sbt_testimonial_credits', true );
		?>
						<section class="testimonial-container <?php echo ( $instance['number'] >= 2 ) ? 'ere-testimonial-2-col testimonial-container-2-col testimonial-2-col' : false; ?> <?php echo ( isset( $instance['show_thumbnails'] ) && $instance['show_thumbnails'] && has_post_thumbnail() ) ? 'testimonial-has-post-thumbnail' : false; ?>">
						<?php if ( isset( $instance['show_thumbnails'] ) && $instance['show_thumbnails'] && has_post_thumbnail() ) : ?>
							<section class="testimonial-featured-image">
								<?php the_post_thumbnail( 'thumbnail' ); ?>
							</section>
						<?php endif; ?>
							<blockquote class="testimonial">
								<p class="testimonial-quote">
									<?php echo get_the_content(); ?>
								</p>
								<footer>
									<?php if( ! empty( $credits['name'] ) ) : // Name ?>
										<p class="testimonial-name">
											<?php echo $credits['name']; ?>
										</p>
									<?php endif; ?>

									<?php if( ! empty( $credits['position'] ) ) : // Position ?>
										<p class="testimonial-position">
											<?php echo $credits['position']; ?>
										</p>
									<?php endif; ?>

									<?php if( ! empty( $credits['company'] ) ) : // Company ?>
										<p class="testimonial-company">
											<?php echo $credits['company']; ?>
										</p>
									<?php endif; ?>
								</footer>
							</blockquote>
						</section>

						<?php if ( ( $random_testimonials->current_post + 1 ) % 2 === 0 ) : // After every 2 posts, clear floats ?>
							<section class="testimonials-clear">&nbsp;</section>
						<?php endif; ?>
		<?php
					endwhile;
		?>
				</section>
		<?php
				endif;
				
				wp_reset_postdata();

				echo $after_widget;
			}
		}

		/*
		* This function enqueues the necessary styles associated with this widget
		*/
		function wp_enqueue_scripts() {
			// Only enqueue styles if this widget is active
			if ( is_active_widget( false, false, $this->id_base, true ) )
				wp_enqueue_style( 'ere-random-testimonials', ERE_PLUGIN_URL . 'includes/widgets/testimonials/css/random-testimonials-widget.css' );
		}

	}


	function Random_Testimonials_Widget_Instance() {
		return Random_Testimonials_Widget::instance();
	}

	Random_Testimonials_Widget_Instance();
}