<?php
/**
 * Properties Archive Template
 * Description: Used to display an archive of Easy Real Estate Properties if a template in the current theme does not already exist.
 *
 * Disclaimer: These templates are provided as is and are meant to provide a basic idea/layout. If you'd like to use your own custom template you may create one in your theme/child theme.
 */
?>

<?php get_header(); ?>

<section class="inner-content">
	<section class="inner-block">
		<?php if ( have_posts() ) : ?>
				<header class="archive-block-header home-block-header">
					<h1 class="block-title">Properties</h1>
				</header>

				<section class="clear">&nbsp;</section>

				<?php
					while ( have_posts() ) : the_post();
						global $wp_query, $post;
				?>
					<section id="post-<?php the_ID(); ?>"  <?php post_class( ( has_post_thumbnail() ) ? 'property property-block has-post-thumbnail news-block' : 'property property-block no-post-thumbnail news-block' ); ?>>
						<?php if ( has_post_thumbnail() ) : // Featured Image ?>
							<section class="news-thumb">
								<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="more read-more">
									<?php the_post_thumbnail( 'mre-front-page-featured' ); ?>
								</a>
							</section>
						<?php endif; ?>
						<section class="news-block-info">
							<?php if ( $ere_property_price = get_post_meta( $post->ID, 'ere_property_price', true ) ) : // Price ?>
								<p class="home-block-date"><?php echo $ere_property_price; ?></p>
							<?php endif; ?>
							<h3 class="block-news-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
							<?php the_excerpt(); ?>
							<p><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="more read-more">View Property</a></p>
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
