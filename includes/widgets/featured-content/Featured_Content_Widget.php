<?php
/**
 * Featured_Content_Widget
 *
 * Description: Display featured content based on settings.
 *
 * @since 1.0
 */

if( ! class_exists( 'Featured_Content_Widget' ) ) {
	class Featured_Content_Widget extends WP_Widget {
		private static $instance; // Keep track of the instance

		/*
		 * Function used to create instance of class.
		 * This is used to prevent over-writing of a variable (old method), i.e. $nbtg = new NBTG();
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) )
				self::$instance = new Featured_Content_Widget;

			return self::$instance;
		}

		/*
		 * This function sets up all widget options including class name, description, width/height, and creates an instance of the widget
		 */
		function __construct() {
			$widget_options = array( 'classname' => 'ere-featured-content-widget featured-content-widget', 'description' => 'Displays featured content based on settings.' );
			$control_options = array( 'width' => 200, 'height' => 350, 'id_base' => 'featured-content-widget' );
			self::WP_Widget( 'featured-content-widget', 'Featured Content', $widget_options, $control_options );

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) ); // Enque admin scripts
			add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) ); // Enqueue CSS
			add_action( 'admin_init', array( $this, 'admin_init' ) ); // Add image sizes
		}


		/**
		 * This function sets up the form on admin pages
		 */
		function form( $instance ) {
			global $post;

			// Set up the default widget settings.
			$defaults = array(
				'title' => false,
				'content_type' => 'post',
				'num_posts' => false,
				'show_thumbnails' => false,
				'thumbnail_size' => 'large',
				'content_or_excerpt' => 'excerpt',
				'excerpt_length' => 55,
				'read_more_label' => 'Read More',
				'hide_read_more' => false,
				'post__not_in' => false,
				'post__in' => false,
				'widget_size' => 'large'
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
				<?php // Content Type (which post_type to be displayed) ?>
				<label for="<?php echo $this->get_field_id( 'content_type' ); ?>"><strong>Content Type</strong></label>
				<br />
				<select name="<?php echo $this->get_field_name( 'content_type' ); ?>" id="<?php echo $this->get_field_id( 'content_type' ); ?>" class="ere-featured-content-type">
					<option value="">Select A Content Type</option>
					<?php
						// Loop through all public registered post types
						$public_post_types = get_post_types( array( 'public' => true ), 'object' );

						if ( ! empty( $public_post_types ) ) :
					?>
						<optgroup label="Post Types" data-content-type="post_type">
							<?php foreach ( $public_post_types as $public_post_type ) : ?>
								<option value="<?php echo esc_attr( $public_post_type->name ); ?>" <?php selected( $instance['content_type'], $public_post_type->name ); ?>><?php echo $public_post_type->labels->name; ?></option>
							<?php endforeach; ?>
						</optgroup>
					<?php
						endif;
					?>

					<?php
						// Loop through all post categories
						$post_categories = get_categories();

						if ( ! empty( $post_categories ) ) :
					?>
						<optgroup label="Categories" data-content-type="category">
							<?php foreach ( $post_categories as $post_category ) : ?>
								<option value="cat-<?php echo esc_attr( $post_category->slug ); ?>" <?php selected( $instance['content_type'], 'cat-' . $post_category->slug ); ?>><?php echo ( $post_category->name ); ?></option>
							<?php endforeach; ?>
						</optgroup>
					<?php
						endif;
					?>

					<?php
						// Loop through all taxonomies/terms registered on Properties
						$ere_properties_taxonomies = get_object_taxonomies( 'ere_properties', 'objects' );

						if ( ! empty( $ere_properties_taxonomies ) ) :
							foreach ( $ere_properties_taxonomies as $ere_properties_taxonomy_name => $ere_properties_taxonomy ) :
								$ere_properties_taxonomy_terms = get_terms( $ere_properties_taxonomy_name );

								if ( ! empty( $ere_properties_taxonomy_terms ) ) :
					?>
								<optgroup label="Properties Taxonomy: <?php echo esc_attr( $ere_properties_taxonomy->label ); ?>" data-content-type="taxonomy">
									<?php foreach ( $ere_properties_taxonomy_terms as $ere_properties_taxonomy_term ) : ?>
										<option value="<?php echo esc_attr( 'tax-' . $ere_properties_taxonomy_name . '-' . $ere_properties_taxonomy_term->slug ); ?>" <?php selected( $instance['content_type'], 'tax-' . $ere_properties_taxonomy_name . '-' . $ere_properties_taxonomy_term->slug ); ?>><?php echo esc_attr( $ere_properties_taxonomy_term->name ); ?></option>
									<?php endforeach; ?>
								</optgroup>
					<?php
								endif;
							endforeach;
						endif;
					?>
				</select>
			</p>

			<p>
				<?php // Number of Posts to Display ?>
				<label for="<?php echo $this->get_field_id( 'num_posts' ); ?>"><strong>Number of Posts To Display</strong></label>
				<br />
				<?php
					// Set up data attributes with post counts for each publich post type
					$data_attrs = '';
					foreach ( $public_post_types as $public_post_type ) // Public Post Types
						$data_attrs .= 'data-count-' . $public_post_type->name . '="' . wp_count_posts( $public_post_type->name )->publish . '" ';
					foreach( $post_categories as $post_category ) // Post Categories
						$data_attrs .= 'data-count-cat-' . $post_category->slug . '="' . $post_category->count . '" ';
					foreach ( $ere_properties_taxonomies as $ere_properties_taxonomy_name => $ere_properties_taxonomy ) { // Property Taxonomies
						$ere_properties_taxonomy_terms = get_terms( $ere_properties_taxonomy_name );

						foreach ( $ere_properties_taxonomy_terms as $ere_properties_taxonomy_term )
							$data_attrs .= 'data-count-tax-' . $ere_properties_taxonomy_name . '-' . $ere_properties_taxonomy_term->slug . '="' . $ere_properties_taxonomy_term->count . '" ';
					}
				?>
				<select name="<?php echo $this->get_field_name( 'num_posts' ); ?>" id="<?php echo $this->get_field_id( 'num_posts' ); ?>" class="ere-featured-num-posts" <?php echo $data_attrs; ?>>
					<option value="">Select Number of Posts</option>
					<?php
						// If a post_type has been selected
						if ( ! empty ( $instance['content_type'] ) && strpos( $instance['content_type'], 'cat-' ) === false && strpos( $instance['content_type'], 'tax-' ) === false ) {
							// Get count of published testimonials
							$content_type_count = wp_count_posts( $instance['content_type'] )->publish;

							for( $i = 1; $i <= $content_type_count; $i++ ) :
					?>
						<option value="<?php echo esc_attr( $i ); ?>" <?php selected( $instance['num_posts'], $i ); ?>><?php echo $i; ?></option>
					<?php
							endfor;
						}

						// If a category has been selected
						if ( ! empty ( $instance['content_type'] ) && strpos( $instance['content_type'], 'cat-' ) !== false ) {
							// Get count of published in current category
							$content_type_count = get_category( get_cat_ID( str_replace( 'cat-', '', $instance['content_type'] ) ) )->count;

							for( $i = 1; $i <= $content_type_count; $i++ ) :
					?>
						<option value="<?php echo esc_attr( $i ); ?>" <?php selected( $instance['num_posts'], $i ); ?>><?php echo $i; ?></option>
					<?php
							endfor;
						}

						// If a taxonomy has been selected
						if ( ! empty ( $instance['content_type'] ) && strpos( $instance['content_type'], 'tax-' ) !== false ) {
							// Get count of published in current taxonomy ( [0] is tax prefix, [1] is taxonomy, [2] is taxonomy slug )
							$ere_term_details = explode( '-', $instance['content_type'], 3 ); // Limit 3
							$content_type_count = get_term_by( 'slug', $ere_term_details[2], $ere_term_details[1] )->count;

							for( $i = 1; $i <= $content_type_count; $i++ ) :
					?>
						<option value="<?php echo esc_attr( $i ); ?>" <?php selected( $instance['num_posts'], $i ); ?>><?php echo $i; ?></option>
					<?php
							endfor;
						}
					?>
				</select>
			</p>

			<p>
				<?php // Show Featured Images ?>
				<input id="<?php echo $this->get_field_id( 'show_thumbnails' ); ?>"  name="<?php echo $this->get_field_name( 'show_thumbnails' ); ?>" type="checkbox" <?php checked( $instance['show_thumbnails'], true ); ?> />
				<label for="<?php echo $this->get_field_id( 'show_thumbnails' ) ; ?>"><strong>Show Featured Images</strong></label>
			</p>

			<!--p>
				<?php // Featured Image Size ?>
				<label for="<?php echo $this->get_field_id( 'thumbnail_size' ) ; ?>"><strong>Featured Image Size</strong></label>
				<br />
				<select name="<?php echo $this->get_field_name( 'thumbnail_size' ); ?>" id="<?php echo $this->get_field_id( 'thumbnail_size' ); ?>">
					<optgroup label="Default">
						<option value="thumbnail" <?php selected( $instance['thumbnail_size'], 'thumbnail' ); ?>>Thumbnail</option>
						<option value="small" <?php selected( $instance['thumbnail_size'], 'small' ); ?>>Small</option>
						<option value="medium" <?php selected( $instance['thumbnail_size'], 'medium' ); ?>>Medium</option>
						<option value="large" <?php selected( $instance['thumbnail_size'], 'large' ); ?>>Large</option>
					</optgroup>
					<optgroup label="Additional">
					<?php
						global $_wp_additional_image_sizes;

						// Loop through each image size
						foreach( ( array ) $_wp_additional_image_sizes as $name => $size ) :
					?>
							<option value="<?php echo esc_attr( $name ); ?>" <?php selected( $instance['thumbnail_size'], $name ); ?>><?php echo esc_html( $name ); ?></option>
					<?php
						endforeach;
					?>
					</optgroup>
				</select>
			</p-->

			<p>
				<?php // Content or Excerpt (should the_content or the_excerpt be used) ?>
				<label for="<?php echo $this->get_field_id( 'content_or_excerpt' ); ?>"><strong>Display Content or Excerpt</strong></label>
				<br />
				<select name="<?php echo $this->get_field_name( 'content_or_excerpt' ); ?>" id="<?php echo $this->get_field_id( 'content_or_excerpt' ); ?>">
					<option value="content" <?php selected( $instance['content_or_excerpt'], 'content' ); ?>>Content</option>
					<option value="excerpt" <?php selected( $instance['content_or_excerpt'], 'excerpt' ); ?>>Excerpt</option>
				</select>
			</p>

			<p>
				<?php // Post Not In (posts to specifically exclude) ?>
				<label for="<?php echo $this->get_field_id( 'excerpt_length' ); ?>"><strong>Excerpt Length (Words)</strong></label>
				<br />
				<input id="<?php echo $this->get_field_id( 'excerpt_length' ); ?>" name="<?php echo $this->get_field_name( 'excerpt_length' ); ?>" value="<?php echo esc_attr( $instance['excerpt_length'] ); ?>" />
			</p>

			<p>
				<?php // Read More Link Label ?>
				<label for="<?php echo $this->get_field_id( 'read_more_label' ); ?>"><strong>Read More Link Label</strong></label>
				<br />
				<input id="<?php echo $this->get_field_id( 'read_more_label' ); ?>" name="<?php echo $this->get_field_name( 'read_more_label' ); ?>" value="<?php echo esc_attr( $instance['read_more_label'] ); ?>" />
			</p>

			<p>
				<?php // Hide Read More ?>
				<input id="<?php echo $this->get_field_id( 'hide_read_more' ); ?>"  name="<?php echo $this->get_field_name( 'hide_read_more' ); ?>" type="checkbox" <?php checked( $instance['hide_read_more'], true ); ?> />
				<label for="<?php echo $this->get_field_id( 'hide_read_more' ) ; ?>"><strong>Hide Read More</strong></label>
			</p>


			<p>
				<?php // Post Not In (posts to specifically exclude) ?>
				<label for="<?php echo $this->get_field_id( 'post__not_in' ); ?>"><strong>Exclude Posts</strong></label>
				<br />
				<input id="<?php echo $this->get_field_id( 'post__not_in' ); ?>" name="<?php echo $this->get_field_name( 'post__not_in' ); ?>" value="<?php echo esc_attr( $instance['post__not_in'] ); ?>" />
				<br />
				<small class="description">Comma separated list of post IDs. Will display all posts except these.</small>
			</p>

			<p>
				<?php // Post In (posts to specifically include) ?>
				<label for="<?php echo $this->get_field_id( 'post__in' ); ?>"><strong>Include Only These Posts</strong></label>
				<br />
				<input id="<?php echo $this->get_field_id( 'post__in' ); ?>" name="<?php echo $this->get_field_name( 'post__in' ); ?>" value="<?php echo esc_attr( $instance['post__in'] ); ?>" />
				<br />
				<small class="description">Comma separated list of post IDs. Will only display these posts. <span class="ere-red">Setting this will ignore "Number of Posts to Display" and "Exclude Posts". "Content Type" has to be set to the correct post type.</span></small>
			</p>

			<p>
				<?php // Widget Size (size of the widget on the front end of the site) ?>
				<label for="<?php echo $this->get_field_id( 'widget_size' ); ?>"><strong>Select A Widget Size</strong></label>
				<br />
				<select name="<?php echo $this->get_field_name( 'widget_size' ); ?>" id="<?php echo $this->get_field_id( 'widget_size' ); ?>">
					<option value="">Select A Widget Size</option>
					<option value="small" <?php selected( $instance['widget_size'], 'small' ); ?>>Small</option>
					<option value="medium" <?php selected( $instance['widget_size'], 'medium' ); ?>>Medium</option>
					<option value="large" <?php selected( $instance['widget_size'], 'large' ); ?>>Large</option>
				</select>
				<br />
				<small class="description">Change the widget displayed on the front end.</small>
			</p>
		<?php
		}

		/**
		 * This function handles updating (saving) widget options
		 */
		function update( $new_instance, $old_instance ) {
			// Sanitize all input data
			$new_instance['title'] = sanitize_text_field( $new_instance['title'] ); // Widget Title
			$new_instance['content_type'] = sanitize_text_field( $new_instance['content_type'] ); // Content Type
			$new_instance['num_posts'] = ( int ) $new_instance['num_posts'] ; // Number of Posts
			$new_instance['show_thumbnails'] = ( isset( $new_instance['show_thumbnails'] ) ) ? true : false; // Featured Images
			//$new_instance['thumbnail_size'] = ( ! empty( $new_instance['thumbnail_size'] ) ) ? sanitize_text_field( $new_instance['thumbnail_size'] ) : 'large'; // Featured Image Size (default to large)
			$new_instance['content_or_excerpt'] = sanitize_text_field( $new_instance['content_or_excerpt'] ); // Content or Excerpt
			$new_instance['excerpt_length'] = ( int ) abs( $new_instance['excerpt_length'] ) ; // Excerpt Length
			$new_instance['read_more_label'] = ( ! empty( $new_instance['read_more_label'] ) ) ? sanitize_text_field( $new_instance['read_more_label'] ) : 'Read More'; // Read More Link Label (default to Read More)
			$new_instance['hide_read_more'] = ( isset( $new_instance['hide_read_more'] ) ) ? true : false; // Hide Read More
			$new_instance['post__not_in'] = sanitize_text_field( str_replace( ' ', '', $new_instance['post__not_in'] ) ); // Exclude Posts
			$new_instance['post__in'] = sanitize_text_field( str_replace( ' ', '', $new_instance['post__in'] ) ); // Specifically Include Posts
			$new_instance['widget_size'] = ( ! empty( $new_instance['widget_size'] ) ) ? sanitize_text_field( $new_instance['widget_size'] ) : 'large'; // Widget Size (default to large)

			return $new_instance;
		}

		/*
		 * This function controls the display of the widget on the website
		 */
		function widget( $args, $instance ) {
			global $post;

			extract( $args ); // $before_widget, $after_widget, $before_title, $after_title

			if ( isset( $instance['content_type'] ) && ! empty( $instance['content_type'] ) && isset( $instance['num_posts'] ) && ( int ) $instance['num_posts'] !== 0 ) :
				// Start of widget output
				echo $before_widget;

				if ( ! empty( $instance['title'] ) )
					echo $before_title . $instance['title'] . $after_title;
				?>
					<section class="ere-featured-content featured-content-clear"></section>
				<?php

				/*
				 * Set up query arguments
				 */
				$ere_featured_content_args = array( 'ignore_sticky_posts' => true, 'posts_per_page' => $instance['num_posts'] );

				// If a post_type has been selected
				if ( strpos( $instance['content_type'], 'cat-' ) === false && strpos( $instance['content_type'], 'tax-' ) === false )
					$ere_featured_content_args['post_type'] = $instance['content_type'];

				// If a category has been selected
				if ( strpos( $instance['content_type'], 'cat-' ) !== false ) {
					$ere_featured_content_args['post_type'] = 'post';
					$ere_featured_content_args['category_name'] = str_replace( 'cat-', '', $instance['content_type'] );
				}

				// If a taxonomy has been selected
				if ( strpos( $instance['content_type'], 'tax-' ) !== false ) {
					// Get count of published in current taxonomy ( [0] is tax prefix, [1] is taxonomy, [2] is taxonomy slug )
					$ere_term_details = explode( '-', $instance['content_type'], 3 ); // Limit 3
					$content_type_count = get_term_by( 'slug', $ere_term_details[2], $ere_term_details[1] )->count;
					$ere_featured_content_args['post_type'] = 'ere_properties';
					$ere_featured_content_args['tax_query'] =array(
						'taxonomy' => $ere_term_details[1],
						'field' => 'slug',
						'terms' => $ere_term_details[2]
					);
				}

				// If a posts should be excluded (and none to be included)
				if ( ( isset( $instance['post__not_in'] ) && ! empty( $instance['post__not_in'] ) ) && ( ! isset( $instance['post__in'] ) || empty( $instance['post__in'] ) ) )
					$ere_featured_content_args['post__not_in'] = explode( ',', $instance['post__not_in'] );

				// If a posts should be included
				if ( isset( $instance['post__in'] ) && ! empty( $instance['post__in'] ) ) {
					$ere_featured_content_args['post__in'] = explode( ',', $instance['post__in'] );
					unset( $ere_featured_content_args['post__not_in'] ); // Ignore excluded posts
					unset( $ere_featured_content_args['posts_per_page'] ); // Ignore posts per page
				}

				$ere_featured_content_query = new WP_Query( $ere_featured_content_args );

				// Display featured content
				if ( $ere_featured_content_query->have_posts() ) :
					while ( $ere_featured_content_query->have_posts() ) : $ere_featured_content_query->the_post();
		?>
						<section class="<?php echo implode( ' ', get_post_class( 'featured-content-widget featured-content-widget-' . $instance['widget_size'] . ' ere-featured-content-widget ere-featured-content-widget-' . $instance['widget_size'] . ' ' . $instance['widget_size'], $post->ID ) ); ?>">
							<?php if( isset( $instance['show_thumbnails'] ) && $instance['show_thumbnails'] && has_post_thumbnail( $post->ID ) ) : // Featured Image ?>
								<section class="thumbnail post-thumbnail featured-image">
									<a href="<?php echo get_permalink( $post->ID ); ?>">
										<?php echo get_the_post_thumbnail( $post->ID, 'ere-front-page-' . $instance['widget_size'] . '-featured' ); ?>
									</a>
								</section>
							<?php endif; ?>
							<section class="content post-content <?php echo ( has_post_thumbnail( $post->ID ) ) ? 'has-post-thumbnail content-has-post-thumbnail' : false; ?>">
								<?php if ( isset( $instance['widget_size'] ) && $instance['widget_size'] === 'small' && isset( $instance['content_type'] ) && $instance['content_type'] === 'ere_properties' ) : // Small Properties ?>
									<section class="home-house-block-info">
										<?php 
											$ere_property_address_city = get_post_meta( $post->ID, 'ere_property_address_city', true );
											$ere_property_address_state = get_post_meta( $post->ID, 'ere_property_address_state', true );
											$ere_property_bedrooms = get_post_meta( $post->ID, 'ere_property_bedrooms', true );
											$ere_property_bathrooms = get_post_meta( $post->ID, 'ere_property_bathrooms', true );
											$ere_property_price = get_post_meta( $post->ID, 'ere_property_price', true );
										?>

										<?php if ( $ere_property_address_city ) : // Price ?>
											<p><?php echo ( $ere_property_address_state ) ? $ere_property_address_city . ', ' . $ere_property_address_state : $ere_property_address_city; ?></p>
										<?php endif ?>

										<?php if ( $ere_property_bedrooms || $ere_property_bathrooms ) : // Bedrooms/Bathrooms ?>
											<p><?php echo ( $ere_property_bedrooms && $ere_property_bathrooms ) ? $ere_property_bedrooms . ' Beds - ' . $ere_property_bathrooms . ' Baths' : ( ( ! $ere_property_bathrooms ) ? $ere_property_bedrooms . ' Bedrooms' : $ere_property_bathrooms . ' Bathrooms' ); ?></p>
										<?php endif ?>

										<?php if ( $ere_property_price ) : // Price ?>
											<p><?php echo $ere_property_price; ?></p>
										<?php endif ?>

										<?php if ( isset( $instance['hide_read_more'] ) && ! $instance['hide_read_more'] ) : // Read More ?>
											<a class="more read-more" href="<?php echo get_permalink( $post->ID ); ?>"><?php echo $instance['read_more_label']; ?></a>
										<?php endif; ?>
									</section>
								<?php else : ?>
									<h3><a href="<?php echo get_permalink( $post->ID ); ?>"><?php echo get_the_title( $post->ID ); ?></a></h3>

									<?php if ( isset( $instance['content_or_excerpt'] ) && $instance['content_or_excerpt'] === 'content' ) : // Post Content ?>
										<p><?php echo get_the_content( false ); ?></p>
									<?php endif; ?>

									<?php if ( isset( $instance['content_or_excerpt'] ) && $instance['content_or_excerpt'] === 'excerpt' ) : // Post Excerpt ?>
										<p><?php echo $this->get_excerpt_by_id( $post->ID, ( isset( $instance['excerpt_length'] ) && ( $instance['excerpt_length'] === 0 || ! empty( $instance['excerpt_length'] ) ) ) ? $instance['excerpt_length'] : 55 ); ?></p>
									<?php endif; ?>

									<?php if ( isset( $instance['hide_read_more'] ) && ! $instance['hide_read_more'] ) : // Read More ?>
										<a class="more read-more" href="<?php echo get_permalink( $post->ID ); ?>"><?php echo $instance['read_more_label']; ?></a>
									<?php endif; ?>
								<?php endif; ?>
							</section>
						</section>

						<?php if ( isset( $instance['widget_size'] ) && $instance['widget_size'] == 'medium' && ( $ere_featured_content_query->current_post + 1 ) % 2 === 0 ) : // If medium widget size, after every 2 posts, clear floats ?>
							<section class="ere-featured-content featured-content-clear"></section>
						<?php endif; ?>

						<?php if ( isset( $instance['widget_size'] ) && $instance['widget_size'] == 'small' && ( $ere_featured_content_query->current_post + 1 ) % 4 === 0 ) : // If medium widget size, after every 4 posts, clear floats ?>
							<section class="ere-featured-content featured-content-clear">&nbsp;</section>
						<?php endif; ?>
		<?php
					endwhile;

					wp_reset_query();
				endif;

				// End of widget output
				echo $after_widget;
			endif;
		}

		/**
		 * This function enqueues the necessary styles associated with this widget on admin.
		 */
		 function admin_enqueue_scripts( $hook ) {
			// Only on Widgets Admin Page
			if ( $hook === 'widgets.php' )
				wp_enqueue_script( 'ere-featured-content-widget-admin-js', ERE_PLUGIN_URL . 'includes/widgets/featured-content/js/featured-content-widget-admin.js', array( 'jquery' ) ); 
		 }



		/**
		 * This function enqueues the necessary styles associated with this widget.
		 */
		function wp_enqueue_scripts() {
			// Only enqueue styles if this widget is active
			if ( is_active_widget( false, false, $this->id_base, true ) )
				wp_enqueue_style( 'ere-featured-content', ERE_PLUGIN_URL . 'includes/widgets/featured-content/css/featured-content-widget.css' );
		}


		/**
		 * ------------------
		 * Internal Functions
		 * ------------------
		 */

		/**
		 * This function gets the excerpt of a specific post ID or object.
		 */
		function get_excerpt_by_id( $post, $length = 55, $tags = '', $extra = ' [...]' ) {
			// Get the post object of the passed ID
			if( is_int( $post ) )
				$post = get_post($post);
			else if( ! is_object( $post ) )
				return false;
		 
			if( has_excerpt( $post->ID ) ) {
				$the_excerpt = $post->post_excerpt;
				return apply_filters( 'the_content', $the_excerpt );
			}
			else
				$the_excerpt = $post->post_content;
		 
			$the_excerpt = strip_shortcodes( strip_tags( $the_excerpt ), $tags );
			$the_excerpt = preg_split( '/\b/', $the_excerpt, $length * 2+1 );

			array_pop( $the_excerpt );
			$the_excerpt = implode( $the_excerpt ) . $extra;
		 
			return apply_filters( 'the_content', $the_excerpt );
		}

		/*
		 * This function creates necessary image sizes used in this widget.
		 */
		function admin_init() {
			add_image_size( 'ere-front-page-small-featured', 270, 135, true );
			add_image_size( 'ere-front-page-medium-featured', 200, 300, true );
			add_image_size( 'ere-front-page-large-featured', 515, 350, true );
		}
	}

	function Featured_Content_Widget_Instance() {
		return Featured_Content_Widget::instance();
	}

	Featured_Content_Widget_Instance();
}