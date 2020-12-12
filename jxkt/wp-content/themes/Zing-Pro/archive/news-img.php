<?php
$category_data = get_term_meta( $cat, '_prefix_taxonomy_options', true );
$banner_img = isset( $category_data['cat_banner_img']['url'] ) ? $category_data['cat_banner_img']['url'] : '';
get_header();
if( $banner_img ){?>
    <div class="module-default produc-cat">
        <div class="responsive-carousel carousel clearfix">
			<div class="carousel-item">
                <img src="<?php echo $banner_img;?>">
            </div>
            <?php if( $category_data['banner_cat_desc'] ){?>
			<div class="dark-overlay"></div>
			<div class="page-width ">
				<div class="page-banner">
					<h2><?php single_term_title(); ?></h2>
					<?php echo category_description(); ?>
				</div>
			</div>
			<?php }?>
        </div>
    </div>
<?php }?>
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
							<div class="news-img entry-list entry-list-article entry-list-time-hl">
								<?php while( have_posts() ): the_post(); ?> 
									<li class="not-animated"<?php echo data_animate();?>>
									<div class="media media-lg">
										<div class="media-left">
											<a class="media-object" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
											<img alt="<?php the_title_attribute(); ?>" src="<?php echo post_thumbnail(380, 306); ?>">
											</a>
										</div>
										<div class="media-body">
											<h4 class="media-heading">
											<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
									            <?php the_title_attribute(); ?>
											</a>
											</h4>
											<p class="info">
												<span>发布日期：<?php the_time('Y-m-d') ?></span>
											</p>
											<p class="des">
												<?php echo mb_strimwidth(strip_tags($post->post_content),0,240,'...');?>
											</p>
										</div>
									</div>
									</li>
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