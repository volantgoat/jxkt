<?php
get_header(); 
$category = get_the_category();
if($category[0]){
    $catid = $category[0]->term_id;
}
$category_data = get_term_meta( $catid, '_prefix_taxonomy_options', true );
$cat_banner_img = isset( $category_data['cat_banner_img']['url'] ) ? $category_data['cat_banner_img']['url'] : '';

$post_extend = get_post_meta( get_the_ID(), 'extend_info', true );
$no_sidebar	= isset($post_extend['no_sidebar']) ?$post_extend['no_sidebar'] : '';
$post_header_banner = xintheme('post_header_banner');
$no_sidebar_all = xintheme('post_no_sidebar_all');
$single_ad = xintheme('single_ad');?>
<?php while( have_posts() ): the_post(); ?>

<?php if( $cat_banner_img && !$post_header_banner ){?>
<div class="module-default">
    <div class="responsive-carousel carousel clearfix">
		<div class="carousel-item">
             <img src="<?php echo $cat_banner_img;?>">
        </div>
    </div>
</div>
<?php }?>

<section class="main">
    <div class="page-width clearfix">
        <section class="content float-right<?php if( $no_sidebar==true || $no_sidebar_all ) : ?> no_sidebar<?php endif;?>">
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
						<div class="entry-meta">
							<span>
								<strong>所属分类：</strong>
								<?php the_category(', ') ?>
							</span>
							<?php if( xintheme('xintheme_single_time') ){ ?>
							<span>
								<strong>发布日期：<?php echo dahuzi_post_time();?></strong>
								<?php //echo get_the_date('Y-m-d H:i'); ?>
							</span>
							<?php }?>
							<?php if( xintheme('xintheme_single_views') ){ ?>
							<span>
								<strong><?php post_views('',''); ?> 次浏览</strong>
							</span>
							<?php }?>
						</div>
						<div class="article-content-wrapper">
							<div class="article-content">
								<div class="qhd-content" id="wzzt">
									<?php if( !empty($single_ad['single_ad_top']['url']) ){?>
									<div class="single-top">
										<a href="<?php echo $single_ad['single_ad_top_url'];?>" target="_blank" rel="nofollow">
									    	<img src="<?php echo $single_ad['single_ad_top']['url'];?>" alt="<?php echo $single_ad['single_ad_top']['title'];?>">
										</a>
									</div>
									<?php }?>
									<?php the_content(); ?>
									<div class="entry-tags">
										<?php the_tags('标签：', ' · ', ''); ?>
									</div>
									<?php if( !empty($single_ad['single_ad_bottom']['url']) ){?>
									<div class="single-bottom">
										<a href="<?php echo $single_ad['single_ad_bottom_url'];?>" target="_blank" rel="nofollow">
									    	<img src="<?php echo $single_ad['single_ad_bottom']['url'];?>" alt="<?php echo $single_ad['single_ad_bottom']['title'];?>">
										</a>
									</div>
									<?php }?>
								</div>
							</div>
							<?php endwhile; ?>
							<div class="detail-bottom">
								<div class="float-right">
								<?php
									$prev_post = get_previous_post();
									if(!empty($prev_post)):?>
										<a title="<?php echo $prev_post->post_title;?>" href="<?php echo get_permalink($prev_post->ID);?>">上一篇</a>
								<?php endif;?>
								<?php
									$next_post = get_next_post();
									if(!empty($next_post)):?>
										<a title="<?php echo $next_post->post_title;?>" href="<?php echo get_permalink($next_post->ID);?>">下一篇</a>
								<?php endif;?>
								</div>
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
								<?php if( dahuzi('post_contact') ){get_template_part( 'template-parts/dahuzi_contact');}?>
								<?php get_template_part( 'template-parts/related');?>
								<?php if( xintheme('xintheme_single_comments') ){ comments_template( '', true ); } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
        </section>
		<?php if( $no_sidebar==false && $no_sidebar_all==false ) : get_sidebar(); endif;?>
    </div>
</section>
<?php get_footer(); ?>