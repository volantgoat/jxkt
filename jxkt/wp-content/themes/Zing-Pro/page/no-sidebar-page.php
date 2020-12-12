<?php
/*
Template Name: 单栏页面
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
        <section class="content float-right no_sidebar">
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
								<div class="detail-bottom">
								   
								    
							 	    <div class="share-links-wrap">
										<p class="text-header small">分享文章：</p>
										<ul class="share-links hoverable">
											<?php
									
												$post_image = post_thumbnail(260, 260);
												$qrcode = ''.get_bloginfo('template_directory').'/public/qrcode?data='.get_the_permalink().'';
									
												echo '<li><a class="qq-share" href="' . esc_url(get_permalink()) . '" title="分享到QQ" data-title="' . esc_attr(get_the_title()) . '" data-image="' . esc_attr($post_image) . '" data-excerpt="'. get_the_excerpt() .'"><i class="iconfont icon-QQ"></i></a></li>';
													            
												echo '<li><a class="weixin-share" href="' . $qrcode . '" title="分享到微信" data-image="' . esc_attr($post_image) . '"><i class="iconfont icon-weixin"></i></a></li>';
													            
												echo '<li><a class="weibo-share" href="' . esc_url(get_permalink()) . '" title="分享到新浪微博" data-title="' . esc_attr(get_the_title()) . '" data-image="' . esc_attr($post_image) . '" data-excerpt="'. get_the_excerpt() .'"><i class="iconfont icon-weibo"></i></a></li>';
									
											?>
										</ul>
									</div>
									
									
									
									<?php comments_template( '', true ); ?>
								</div>
							</div>
							
							
						</div>
				</div>
			</div>
        </section>
    </div>
</section>
<?php get_footer(); ?>