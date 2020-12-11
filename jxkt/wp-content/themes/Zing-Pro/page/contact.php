<?php
/*
Template Name: 在线留言页面
*/
get_header();?>
<?php while( have_posts() ): the_post(); ?>
<div class="module-default">
    <div class="responsive-carousel carousel clearfix">
		<div class="carousel-item">
            <?php the_post_thumbnail(); ?>
        </div>
    </div>
</div>
<section class="main">
    <div class="page-width clearfix">
        <section class="content float-right">
            <section class="page-title page-title-inner clearfix">
                <div class="breadcrumbs">
                    <span>当前位置：</span>
                    <?php if (function_exists('get_breadcrumbs')){get_breadcrumbs(); } ?>
                </div>
            </section>
			<div class="module-default">
				<div class="module-inner">
						<div class="article-detail">
							<div class="article-title">
								<h1><?php the_title_attribute(); ?></h1>
							</div>
							<div class="article-content-wrapper">
								<div class="article-content">
									<div class="qhd-content" id="wzzt">
										<?php the_content(); ?>
									</div>
								</div>
								<?php endwhile; ?>

								<?php get_template_part( 'template-parts/dahuzi_contact');?>

							</div>
						</div>
				</div>
			</div>
        </section>
		<?php get_sidebar();?>
    </div>
</section>
<?php get_footer(); ?>