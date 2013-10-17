<?php
/**
 * Single Property Template
 * Description: Used to display a single Easy Real Estate Property if a template in the current theme does not already exist.
 *
 * Disclaimer: These templates are provided as is and are meant to provide a basic idea/layout. If you'd like to use your own custom template you may create one in your theme/child theme.
 */
?>

<?php get_header(); ?>

<section class="blog-content">
	<?php
		if ( have_posts() ) :
			while ( have_posts() ) : the_post();
	?>
				<section class="blog-post">
					<?php
						$ere_property_soliloquy = get_post_meta( $post->ID, 'ere_property_soliloquy', true ); // Soliloquy Slider shortcode
					?>

					<?php if ( $ere_property_soliloquy || has_post_thumbnail() ) : // Soliloquy Slider or Featured Image ?>
						<header class="blog-post-header">
							<?php
								// Soliloquy Slider
								if ( $ere_property_soliloquy )
									echo do_shortcode( $ere_property_soliloquy );
								// Featured Image
								else
									the_post_thumbnail( 'ere-post-page-featured' );
							?>
						</header>
					<?php endif; ?>

					<?php
						// Video or Highlight
						$ere_property_video = get_post_meta( $post->ID, 'ere_property_video', true );
						$ere_property_highlight = get_post_meta( $post->ID, 'ere_property_highlight', true );
					?>

					<?php if ( $ere_property_video || $ere_property_highlight ) : ?>
						<section class="blog-video-block">
							<?php if ( $ere_property_video ) : // Video ?>
								<section class="video">
									<?php echo wp_oembed_get( $ere_property_video, array( 'width' => 420, 'height' => 315 ) ); ?>
								</section>
							<?php endif; ?>
							
							<?php if ( $ere_property_highlight ) : // Highlight ?>
								<section class="video-info <?php echo ( $ere_property_video ) ? 'has-video' : 'no-video'; ?>">
									<p><?php echo $ere_property_highlight; ?></p>
								</section>
							<?php endif; ?>
						</section>
					<?php endif; ?>

					<section id="post-<?php the_ID(); ?>" <?php post_class( 'single-post post-content page-content blog-post-content' ); ?>>
						<?php the_content(); ?>

						<?php edit_post_link( 'Edit Property' ); // Allow logged in users to edit ?>
					</section>

					<section class="clear">&nbsp;</section>

					<?php
						global $multipage; // Used to determine if the current post has multiple pages

						if ( $multipage ) :
					?>
						<section class="single-post-navigation single-post-pagination wp-link-pages">
							<?php wp_link_pages(); ?>
						</section>
					<?php
						endif;
					?>
				</section>
	<?php

				sds_single_post_navigation();
			endwhile;
		endif;
	?>
</section>

<?php
	// Load custom sidebar template
	load_template( ERE_PLUGIN_DIR . 'templates/sidebar-ere_properties.php' );

	/**
	 * Example: the conditional below will load the sidebar provided with the Easy Real Estate Plugin if the template does not exist within the current theme.
	 *
	 * if ( ! locate_template( 'sidebar-ere_properties.php' ) )
	 *		load_template( ERE_PLUGIN_DIR . 'templates/sidebar-ere_properties.php' );
	 * else
	 *		get_sidebar( 'ere_properties' );
	 */
?>

<?php get_footer(); ?>