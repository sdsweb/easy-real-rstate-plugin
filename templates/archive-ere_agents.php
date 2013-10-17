<?php
/**
 * Agents Archive Template
 * Description: Used to display an archive of Easy Real Estate Agents if a template in the current theme does not already exist.
 *
 * Disclaimer: These templates are provided as is and are meant to provide a basic idea/layout. If you'd like to use your own custom template you may create one in your theme/child theme.
 */
?>

<?php get_header(); ?>

<section class="inner-content">
	<section class="inner-block blog-archive">
		<?php if ( have_posts() ) : ?>
				<header class="home-block-header">
					<h1 class="block-title">Agents</h1>
				</header>

				<section class="clear">&nbsp;</section>

				<?php
					while ( have_posts() ) : the_post();
						global $wp_query;
				?>
					<section id="post-<?php the_ID(); ?>" <?php post_class( ( has_post_thumbnail() ) ? 'agent agent-block has-post-thumbnail news-block' : 'agent agent-block no-post-thumbnail news-block' ); ?>>
						<?php if ( has_post_thumbnail() ) : // Featured Image ?>
							<section class="news-thumb agent-thumb">
								<section class="author-thumb">
									<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="more read-more">
										<?php the_post_thumbnail( 'thumbnail' ); ?>
									</a>
								</section>
							</section>
						<?php endif; ?>
						<section class="news-block-info">
							<h3 class="block-news-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
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
							<p><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="more read-more">View Agent Bio</a></p>
						</section>
					</section>
				<?php
					endwhile;

					sds_post_navigation();
			else : 
				sds_no_posts();
			endif;
		?>
	</section>

	<?php get_sidebar(); ?>
</section>

<?php get_footer(); ?>
