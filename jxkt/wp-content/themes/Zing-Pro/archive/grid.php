<?php
$category_data = get_term_meta( $cat, '_prefix_taxonomy_options', true );
$banner_img = isset( $category_data['cat_banner_img']['url'] ) ? $category_data['cat_banner_img']['url'] : '';
$post_img_height = $category_data['post_img_height'] ?: '500';
$post_img_width = $category_data['post_img_width'] ?: '500';
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
    <div class="page-width clearfix">
        <section class="content float-right">
            <section class="page-title page-title-inner clearfix">
                <div class="breadcrumbs">
                    <span>当前位置：</span>
                    <?php if (function_exists('get_breadcrumbs')){get_breadcrumbs(); } ?></div>
            </section>
			<div class="portfolio-list product-list ieCode-del">
				<ul class="row2-svar clearfix">
				<?php while( have_posts() ): the_post(); ?> 
					<li class="col-3-1 fadeInUp not-animated"<?php echo data_animate();?>>
					<div class="product-item">
						<div class="portfolio-img">
							<a href="<?php the_permalink(); ?>">
								<img src="<?php echo post_thumbnail($post_img_width,$post_img_height); ?>">
							</a>
						</div>
						<div class="portfolio-title">
							<h2><a href="<?php the_permalink(); ?>"><?php the_title_attribute(); ?></a></h2>
						</div>
					</div>
					</li>
				<?php endwhile; ?>
				</ul>
			</div>
			<div class="pagination pagination-default">
				<?php par_pagenavi(9); ?>
			</div>
        </section>
        <?php get_sidebar();?>
    </div>
</section>
<?php get_footer(); ?>