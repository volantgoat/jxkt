<?php
$category_data = get_term_meta( $cat, '_prefix_taxonomy_options', true );
get_header();?>
<section class="main">
<div class="page-width clearfix news-mb">
	<section class="content float-right">
	<section class="page-title page-title-inner clearfix">
	<div class="breadcrumbs">
		<span>当前位置：</span><?php if (function_exists('get_breadcrumbs')){get_breadcrumbs(); } ?>
	</div>
	</section>
	<div id="a1portalSkin_mainArea" class="content-wrapper">
		<div class="module-default">
			<div class="module-inner">
				<div class="module-content">
					<div class="entry-list entry-list-article entry-list-time-hl">
						<?php while( have_posts() ): the_post(); ?> 
						<div class="entry-item not-animated"<?php echo data_animate();?>>
							<div class="time">
								<p class="time-day">
									<?php the_time('d') ?>
								</p>
								<p class="time-date">
									<?php the_time('Y-m') ?>
								</p>
							</div>
							<div class="entry-title">
								<h2><a href="<?php the_permalink(); ?>"><?php the_title_attribute(); ?></a></h2>
							</div>
							<div class="entry-summary">
								<div class="qhd-content">
									<p><?php echo mb_strimwidth(strip_tags($post->post_content),0,250,'...');?></p>
								</div>
							</div>
						</div>
						<?php endwhile; ?>
					</div>
					<div class="pagination pagination-default">
						<?php par_pagenavi(9); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	</section>
	<?php get_sidebar();?>
</div>
</section>
<?php get_footer();?>