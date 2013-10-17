<?php
/**
 * Single Property Sidebar Template
 * Description: Used to display a sidebar for a single Easy Real Estate Property.
 *
 * Disclaimer: These templates are provided as is and are meant to provide a basic idea/layout. If you'd like to use your own custom template you may create one in your theme/child theme.
 */
?>
	<aside id="property-sidebar">
		<section class="sidebar-block">
			<?php
				// Google Maps URL parameters
				$ere_property_address_street = get_post_meta( $post->ID, 'ere_property_address_street', true );
				$ere_property_address_street_2 = get_post_meta( $post->ID, 'ere_property_address_street_2', true );
				$ere_property_address_city = get_post_meta( $post->ID, 'ere_property_address_city', true );
				$ere_property_address_state = get_post_meta( $post->ID, 'ere_property_address_state', true );
				$ere_property_address_zip_code = get_post_meta( $post->ID, 'ere_property_address_zip_code', true );
				$ere_property_address_lat_long = get_post_meta( $post->ID, 'ere_property_address_lat_long', true );
			?>
			
			<?php if ( $ere_property_address_street || ( $ere_property_address_city && $ere_property_address_state ) || $ere_property_address_zip_code || $ere_property_address_lat_long ) : // Google Map
					$ere_map_url_params = '';

					// If a latitude/longitude query is set
					if ( ! empty( $ere_property_address_lat_long ) ) {
						$ere_map_url_params .= ( ! empty( $ere_property_address_city ) ) ? urlencode( $ere_property_address_city ) . '+,' : false; // City
						$ere_map_url_params .= ( ! empty( $ere_property_address_state ) ) ? urlencode( $ere_property_address_state ) . '+' : false; // State
						$ere_map_url_params .= ( ! empty( $ere_property_address_zip_code ) ) ? urlencode( $ere_property_address_zip_code ) : false; // Zip Code
						$ere_map_url_params .= ( ! empty( $ere_property_address_street ) ) ? '(' . urlencode( $ere_property_address_street ) . ')' . '+' : false; // Street Address
						$ere_map_url_params .= ( empty( $ere_map_url_params ) ) ? $post->post_title : false; // Used to ensure marker is displayed correctly
						$ere_map_url_params .= '@' . $ere_property_address_lat_long;
					}
					else {
						$ere_map_url_params .= ( ! empty( $ere_property_address_street ) ) ? '(' . urlencode( $ere_property_address_street ) . ')+' : false; // Street Address
						$ere_map_url_params .= ( ! empty( $ere_property_address_city ) ) ? urlencode( $ere_property_address_city ) . '+,' : false; // City
						$ere_map_url_params .= ( ! empty( $ere_property_address_state ) ) ? urlencode( $ere_property_address_state ) . '+' : false; // State
						$ere_map_url_params .= ( ! empty( $ere_property_address_zip_code ) ) ? urlencode( $ere_property_address_zip_code ) : false; // Zip Code
					}
				?>

				<section class="sidebar-top">
					<iframe width="425" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps?q=<?php echo ( ! empty( $ere_map_url_params ) ) ? $ere_map_url_params : false; ?>&amp;z=12&amp;output=embed"></iframe>
				</section>
			<?php endif; ?>

			<?php
				// Property Details (top)
				$ere_property_status = get_post_meta( $post->ID, 'ere_property_status', true );
				$ere_property_bedrooms = get_post_meta( $post->ID, 'ere_property_bedrooms', true );
				$ere_property_bathrooms = get_post_meta( $post->ID, 'ere_property_bathrooms', true );
				$ere_property_price = get_post_meta( $post->ID, 'ere_property_price', true );
			?>

			<?php if ( $ere_property_status || ( $ere_property_address_city && $ere_property_address_state ) || ( $ere_property_bedrooms && $ere_property_bathrooms ) || $ere_property_price ) : // Property Details (Top) ?>
				<section class="sidebar-house-block-info">
					<?php if ( $ere_property_status ) : // Property Status ?>
						<p><?php echo ( $ere_property_status === 'sale' ) ? 'For Sale' : 'For Rent'; ?></p>
					<?php endif ?>

					<?php if ( $ere_property_address_city || $ere_property_address_state ) : // Property City/State ?>
						<p><?php echo ( $ere_property_address_city && $ere_property_address_state ) ? $ere_property_address_city . ', ' . $ere_property_address_state : ( ( ! $ere_property_address_state ) ? $ere_property_address_city : $ere_property_address_state ); ?></p>
					<?php endif ?>

					<?php if ( $ere_property_bedrooms || $ere_property_bathrooms ) : // Bedrooms/Bathrooms ?>
						<p><?php echo ( $ere_property_bedrooms && $ere_property_bathrooms ) ? $ere_property_bedrooms . ' Bedrooms - ' . $ere_property_bathrooms . ' Bathrooms' : ( ( ! $ere_property_bathrooms ) ? $ere_property_bedrooms . ' Bedrooms' : $ere_property_bathrooms . ' Bathrooms' ); ?></p>
					<?php endif ?>

					<?php if ( $ere_property_price ) : // Price ?>
						<p><?php echo $ere_property_price; ?></p>
					<?php endif ?>
				</section>
			<?php endif; ?>
			<section class="sidebar-text-block">
				<ul class="property-details">
					<?php if( $ere_property_address_street ) : // Street Address ?>
						<li><strong>Address:</strong> <?php echo $ere_property_address_street; ?></li>
					<?php endif; ?>

					<?php if( $ere_property_address_city ) : // City ?>
						<li><strong>City:</strong> <?php echo $ere_property_address_city; ?></li>
					<?php endif; ?>

					<?php if( $ere_property_address_state ) : // State ?>
						<li><strong>State:</strong> <?php echo $ere_property_address_state; ?></li>
					<?php endif; ?>

					<?php if( $ere_property_address_zip_code ) : // Zip Code ?>
						<li><strong>Zip Code:</strong> <?php echo $ere_property_address_zip_code; ?></li>
					<?php endif; ?>

					<?php if( $ere_property_mls = get_post_meta( $post->ID, 'ere_property_mls', true ) ) : // MLS # ?>
						<li><strong>MLS #:</strong> <?php echo $ere_property_mls; ?></li>
					<?php endif; ?>

					<?php if( $ere_property_square_feet = get_post_meta( $post->ID, 'ere_property_square_feet', true ) ) : // Square Feet ?>
						<li><strong>Square Feet:</strong> <?php echo $ere_property_square_feet; ?></li>
					<?php endif; ?>

					<?php if( $ere_property_bedrooms ) : // Bedrooms ?>
						<li><strong>Bedrooms:</strong> <?php echo $ere_property_bedrooms; ?></li>
					<?php endif; ?>

					<?php if( $ere_property_bathrooms ) : // Bathrooms ?>
						<li><strong>Bathrooms:</strong> <?php echo $ere_property_bathrooms; ?></li>
					<?php endif; ?>

					<?php if( $ere_property_basement = get_post_meta( $post->ID, 'ere_property_basement', true ) ) : // Basement ?>
						<li><strong>Basement:</strong> <?php echo $ere_property_basement; ?></li>
					<?php endif; ?>
				</ul>
			</section>
			
			<section class="sidebar-contact">
			</section>
			
		</section>
				
	</aside>