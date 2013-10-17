<?php
/**
 * Single Agent Template
 * Description: Used to display a single Easy Real Estate Agent if a template in the current theme does not already exist.
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
					<section id="post-<?php the_ID(); ?>" <?php post_class( 'single-post post-content page-content blog-post-content' ); ?>>
						<h1 class="title"><?php the_title(); ?></h1>

						<?php the_content(); ?>

						<?php edit_post_link( 'Edit Agent' ); // Allow logged in users to edit ?>
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


					<footer class="blog-post-footer agent-post-footer <?php echo ( has_post_thumbnail() ) ? 'has-post-thumbnail' : 'no-post-thumbnail'; ?>">
						<section class="author-thumb">
							<?php if ( has_post_thumbnail() ) the_post_thumbnail( 'thumbnail' ); ?>
						</section>
						<section class="author-info">
							<p><span class="author-name"><?php the_title(); ?></span></p>

							<section class="agent-info">
								<?php if ( $ere_agent_position = get_post_meta( $post->ID, 'ere_agent_position', true ) ) : // Position ?>
									<p class="agent-position"><?php echo $ere_agent_position; ?></p>
								<?php endif; ?>
								<section class="agent-social-media">
									<?php if ( $ere_agent_linked_in = get_post_meta( $post->ID, 'ere_agent_linked_in', true ) ) : // Linked In ?>
										<a href="<?php echo $ere_agent_linked_in; ?>" class="icon-linkedin agent-linked-in" target="_blank" rel="me"></a>
									<?php endif; ?>
									<?php if ( $ere_agent_facebook = get_post_meta( $post->ID, 'ere_agent_facebook', true ) ) : // Facebook ?>
										<a href="<?php echo $ere_agent_facebook; ?>" class="icon-facebook agent-facebook" target="_blank" rel="me"></a>
									<?php endif; ?>
									<?php if ( $ere_agent_twitter = get_post_meta( $post->ID, 'ere_agent_twitter', true ) ) : // Twitter ?>
										<a href="<?php echo $ere_agent_twitter; ?>" class="icon-twitter agent-twitter" target="_blank" rel="me"></a>
									<?php endif; ?>
								</section>
							</section>

						</section>
					</footer>
				</section>
	<?php

				sds_single_post_navigation();
			endwhile;
		endif;
	?>
</section>

<?php get_sidebar(); ?>

<?php get_footer(); ?>