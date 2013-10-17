<?php get_header(); ?>

<section class="breadcrumb">
	<?php if ( function_exists( 'yoast_breadcrumb' ) )
		yoast_breadcrumb( '<span id="archive-breadcrumbs" class="breadcrumbs">', '</span>' ); ?>
</section>

<section class="inner-content">
	<section class="inner-block cf">
		<header class="archive-block-header home-block-header archive-title cf">
			<h1 title="<?php single_term_title( 'Archive: ' ); ?>" class="page-title">
				<?php single_tag_title( 'Archive: ' ); ?>
			</h1>
		</header>
		<?php if ( have_posts() ) : ?>
				<?php
					while ( have_posts() ) : the_post();
						global $wp_query, $post;
				?>
					<section id="post-<?php the_ID(); ?>"<?php post_class( ( has_post_thumbnail() ) ? 'post post-block has-post-thumbnail news-block' : 'post post-block no-post-thumbnail news-block' ); ?>>
						<?php if ( has_post_thumbnail() ) : // Featured Image ?>
							<section class="news-thumb">
								<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="more read-more">
									<?php the_post_thumbnail( 'mre-200x300' ); ?>
								</a>
							</section>
						<?php endif; ?>
						<section class="news-block-info">
							<?php if ( $post->post_type === 'post' ) : // Single Posts ?>
								<p class="home-block-date"><?php the_time( 'M j, Y' ); ?></p>
							<?php elseif ( $post->post_type === 'ere_properties' && $ere_property_price = get_post_meta( $post->ID, 'ere_property_price', true ) ) : // Properties ?>
								<p class="home-block-date"><?php echo $ere_property_price; ?></p>
							<?php endif; ?>
							<h3 class="block-news-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
							<?php the_excerpt(); ?>
							
							<?php if ( $post->post_type === 'ere_properties' ) : // Properties ?>
								<p><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="more read-more">View Property</a></p>
							<?php else : ?>
								<p><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="more read-more">Read More</a></p>
							<?php endif; ?>
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
