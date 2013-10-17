<?php
/**
 * Search_Widget
 *
 * Description: Display featured content based on settings.
 *
 * @since 1.0
 */

if( ! class_exists( 'Search_Widget' ) ) {
	class Search_Widget extends WP_Widget {
		private static $instance; // Keep track of the instance

		/*
		 * Function used to create instance of class.
		 * This is used to prevent over-writing of a variable (old method), i.e. $nbtg = new NBTG();
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) )
				self::$instance = new Search_Widget;

			return self::$instance;
		}

		/*
		 * This function sets up all widget options including class name, description, width/height, and creates an instance of the widget
		 */
		function __construct() {
			$widget_options = array( 'classname' => 'ere-search-widget search-widget', 'description' => 'Displays a search widget based on settings.' );
			$control_options = array( 'width' => 200, 'height' => 350, 'id_base' => 'search-widget' );
			self::WP_Widget( 'search-widget', 'Property Search', $widget_options, $control_options );
		}


		/**
		 * This function sets up the form on admin pages
		 */
		function form( $instance ) {
			global $post;

			// Set up the default widget settings.
			$defaults = array(
				'title' => false,
				'taxonomies' => array(),
				'button_text' => 'Search'
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
				<?php // Active Taxonomies ?>
				<label for="<?php echo $this->get_field_id( 'taxonmies' ) ; ?>"><strong>Active Property Taxonmies</strong></label>
				<br />

				<?php
					// Get active taxonomies
					$ere_property_taxonomies = Easy_Real_Estate_Property_Taxonomies::get_taxonomies();

					if ( ! empty( $ere_property_taxonomies ) ) : // Property taxonomies exist
						$taxonomies = array();

						// Check to see if all taxonomies/terms are empty
						foreach ( $ere_property_taxonomies as $taxonomy => $taxonomy_data )
							$taxonomies[] = $taxonomy;

						$taxonomy_terms = get_terms( $taxonomies );
						if ( empty( $taxonomy_terms ) )  :
				?>
						<p><small>Property taxonomies exist, however there aren't any terms selected. Please select at least 1 term on your properties to allow the choice of taxonomies here.</small></p>
				<?php
						endif;

						// Loop through each taxonomy if there are terms
						if ( ! empty( $taxonomy_terms ) )
							foreach ( $ere_property_taxonomies as $taxonomy => $taxonomy_data ) :
								// Do terms exist within the current taxonomy
								$taxonomy_terms = get_terms( $taxonomy );

								if ( ! empty( $taxonomy_terms ) ) :
				?>
									<br />
									<input id="<?php echo $this->get_field_id( 'taxonomies' ) . '[' . $taxonomy . ']'; ?>"  name="<?php echo $this->get_field_name( 'taxonomies' ) . '[' . $taxonomy . ']'; ?>" type="checkbox" <?php checked( ( isset( $instance['taxonomies'][$taxonomy] ) ) ? $instance['taxonomies'][$taxonomy] : false, $ere_property_taxonomies[$taxonomy]['labels']['name']); ?> />
									<label for="<?php echo $this->get_field_id( 'taxonomies' ) . '[' . $taxonomy . ']'; ?>"><?php echo $taxonomy_data['labels']['name']; ?></label>
				<?php
								endif;
							endforeach;
					endif;
				?>

				<br />
			</p>
		
			<p>
				<?php // Button Text ?>
				<label for="<?php echo $this->get_field_id( 'button_text' ) ; ?>"><strong>Search Button Text</strong></label>
				<br />
				<input id="<?php echo $this->get_field_id( 'button_text' ); ?>" name="<?php echo $this->get_field_name( 'button_text' ); ?>" type="text" value="<?php echo esc_attr( $instance['button_text'] ); ?>" />
			</p>
		<?php
		}

		/**
		 * This function handles updating (saving) widget options
		 */
		function update( $new_instance, $old_instance ) {
			// Sanitize all input data
			$new_instance['title'] = sanitize_text_field( $new_instance['title'] ); // Widget Title

			// Taxonomies
			$ere_property_taxonomies = Easy_Real_Estate_Property_Taxonomies::get_taxonomies(); // Get active taxonomies

			foreach( $new_instance['taxonomies'] as $taxonomy => $value )
				$new_instance['taxonomies'][$taxonomy] = ( isset( $new_instance['taxonomies'][$taxonomy] ) && array_key_exists( $taxonomy, $ere_property_taxonomies ) ) ? $ere_property_taxonomies[$taxonomy]['labels']['name'] : false;

			$new_instance['button_text'] = sanitize_text_field( $new_instance['button_text'] ); // Button Text

			return $new_instance;
		}

		/*
		 * This function controls the display of the widget on the website
		 */
		function widget( $args, $instance ) {
			global $wp_query;

			extract( $args ); // $before_widget, $after_widget, $before_title, $after_title

			// Start of widget output
			echo $before_widget;

			if ( ! empty( $instance['title'] ) )
				echo $before_title . $instance['title'] . $after_title;

	?>
		<section class="search ere-search property-search ere-property-search">
			<form role="search" method="get" id="property-searchform" action="<?php echo home_url( '/' ) ?>" >
				<input type="hidden" name="s" value=""  />
	<?php
				// Selected Taxonomies
				if ( ! empty( $instance['taxonomies'] ) ) :
	?>
				<section class="property-search-widget-taxonomies">
	<?php
					foreach( $instance['taxonomies'] as $taxonomy => $taxonomy_label ) :
	?>
					<select name="<?php echo $taxonomy; ?>" class="ere-taxonomy ere-<?php echo $taxonomy; ?> <?php echo $taxonomy; ?>">
						<option value=""><?php echo $taxonomy_label; ?></option>
						<?php
							// Get current taxonomy terms
							$taxonomy_terms = get_terms( $taxonomy, array( 'orderby' => 'name', 'order' => 'desc', 'hierarchical' => false ) );

							foreach ( $taxonomy_terms as $taxonomy_term ) :
						?>
							<option value="<?php echo $taxonomy_term->slug; ?>" <?php selected( ( isset( $wp_query->query_vars[$taxonomy] ) ) ? $wp_query->query_vars[$taxonomy] : false, $taxonomy_term->slug ); ?>><?php echo $taxonomy_term->name; ?></option>
						<?php
							endforeach;
						?>
					</select>
	<?php
					endforeach;
	?>
				</section>
	<?php
				endif;
	?>
				<input type="submit" value="<?php echo esc_attr( $instance['button_text'] ); ?>" class="search-button">
			</form>
		</section>
	<?php
			// End of widget output
			echo $after_widget;
		}
	}

	function Search_Widget_Instance() {
		return Search_Widget::instance();
	}

	Search_Widget_Instance();
}