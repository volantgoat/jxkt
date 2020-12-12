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

						<div class="showDetailMain clearfloat">
							<div class="showWap">
								<div class="showPic">
									<ul id="produc-slider" class="showSlider">
										<?php
											$post_meta = get_post_meta(get_the_ID(), 'extend_info', true);
											$produc_img = isset($post_meta['produc_img']) ?$post_meta['produc_img'] : '';
										?>
										<?php if($produc_img){ ?>

											<?php
												if( !empty( $produc_img ) ) :
								                $produc_img = explode( ',', $post_meta['produc_img'] );
								                foreach ( $produc_img as $id ) :
								                $produc_img_src = wp_get_attachment_image_src( $id, 'full' );
											?>
											<li>
												  <img src="<?php echo $produc_img_src[0];?>" alt="<?php the_title(); ?>"/>
											</li>
											<?php endforeach;endif ?>

										<?php }else{?>
											<li>
												  <img src="<?php echo post_thumbnail(500, 500); ?>" alt="<?php the_title(); ?>"/>
											</li>
										<?php }?>
									</ul>
								</div>
							</div>
							<div class="showDetailMainCont">
								<h1><?php the_title_attribute(); ?></h1>
								<div class="product-meta">
									<span>
										<strong>所属分类：</strong>
										<?php the_category(', ') ?>
									</span>
									<?php if( xintheme('xintheme_single_time') ){ ?>
									<span class="productline">|</span>
									<span>
										<strong>发布日期：</strong><?php echo dahuzi_post_time();?>
										<?php //echo get_the_date('Y-m-d H:i'); ?>
									</span>
									<?php }?>
									<?php if( xintheme('xintheme_single_views') ){ ?>
									<!--span>
										<strong><?php //post_views('',''); ?> 次浏览</strong>
									</span-->
									<?php }?>
								</div>
								<p>
									<?php
										$produc_abstract = isset($post_meta['produc_abstract']) ?$post_meta['produc_abstract'] : '';
										if($produc_abstract){
											echo $produc_abstract;
										}else{
											echo mb_strimwidth(strip_tags($post->post_content),0,200,'...');
										}
									?>
								</p>

								<div class="showDetailMainB clearfloat">
								<?php
								$post_add_button = isset($post_meta['add_button']) ?$post_meta['add_button'] : '';
								if($post_add_button){
									$add_button = $post_add_button;
								}else{
									$add_button = xintheme('add_button');
								}
								if(is_array($add_button)){
									foreach ( $add_button as $value ): ?>
									<?php if( $value['produc_button_type'] == 'link' ){?>
										<a href="<?php echo $value['button_url'];?>" rel="nofollow" target="_blank"<?php if( $value['button_color'] ){?> style="border: 1px solid <?php echo $value['button_color']?>;color:<?php echo $value['button_color']?>;"<?php }?>>
										<?php if( $value['button_icon'] ){?><i class="<?php echo $value['button_icon'];?>"></i><?php }?> <?php echo $value['button_title'];?>
										</a>
									<?php }elseif( $value['produc_button_type'] == 'img' ){?>
										<a id="button_img" class="button_img" href="javascript:void(0);"<?php if( $value['button_color'] ){?> style="border: 1px solid <?php echo $value['button_color']?>;color:<?php echo $value['button_color']?>;"<?php }?>><?php if( $value['button_icon'] ){?><i class="<?php echo $value['button_icon'];?>"></i><?php }?> <?php echo $value['button_title'];?></a>
										<div class="button-img-dropdown">
											<div class="tooltip-button-img-inner">
												<h3><?php echo $value['button_title'];?></h3>
												<div class="qcode"> 
													<img src="<?php echo $value['button_img']['url'];?>" alt="<?php echo $value['button_title'];?>">
												</div>
											</div>
											<div class="close-weixin">
												<span class="close-top"></span>
													<span class="close-bottom"></span>
										    </div>
										</div>
									<?php }else{?>
										<a href="<?php if( wp_is_mobile() ){?>mqqwpa://im/chat?chat_type=wpa&uin=<?php echo $value['button_qq'];?>&version=1&src_type=web&web_src=oicqzone.com<?php }else{?>http://wpa.qq.com/msgrd?v=3&uin=<?php echo $value['button_qq'];?>&site=qq&menu=yes<?php }?>" rel="nofollow" target="_blank"<?php if( $value['button_color'] ){?> style="border: 1px solid <?php echo $value['button_color']?>;color:<?php echo $value['button_color']?>;"<?php }?>>
										<?php if( $value['button_icon'] ){?><i class="<?php echo $value['button_icon'];?>"></i><?php }?> <?php echo $value['button_title'];?>
										</a>
									<?php }?>
									<?php endforeach;?>
								<?php }?>
								</div>

							</div>
						</div>
						<div class="showDetailInner">
							<h2><span>详情</span></h2>
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

								
						

								<?php if( dahuzi('post_contact') ){get_template_part( 'template-parts/dahuzi_contact');}?>

								<div class="related">
									<h2><span>相关内容</span></h2>
									<ul class="row2-svar clearfix">
									<?php
									$post_num = 4;
									$exclude_id = $post->ID;
									$posttags = get_the_tags(); $i = 0;
									if ( $posttags ) {
										$tags = '';
										foreach ( $posttags as $tag ) $tags .= $tag->term_id . ',';
										$args = array(
											'post_status' => 'publish',
											'tag__in' => explode(',', $tags),
											'post__not_in' => explode(',', $exclude_id),
											'ignore_sticky_posts' => 1,
											'orderby' => 'comment_date',
											'posts_per_page' => $post_num
										);
										query_posts($args);
										while( have_posts() ) { the_post(); ?>
										<li class="col-4-1 fadeInUp not-animated"<?php echo data_animate();?>>
										<div class="product-item">
											<div class="portfolio-img">
												<a href="<?php the_permalink(); ?>">
													<img src="<?php echo post_thumbnail(300, 300); ?>">
												</a>
											</div>
											<div class="portfolio-title">
												<h2><a href="<?php the_permalink(); ?>"><?php the_title_attribute(); ?></a></h2>
											</div>
										</div>
										</li>
										<?php
										$exclude_id .= ',' . $post->ID; $i ++;
										}
										wp_reset_query();
									}
									if ( $i < $post_num ) {
										$cats = ''; foreach ( get_the_category() as $cat ) $cats .= $cat->cat_ID . ',';
										$args = array(
											'category__in' => explode(',', $cats),
											'post__not_in' => explode(',', $exclude_id),
											'ignore_sticky_posts' => 1,
											'orderby' => 'comment_date',
											'posts_per_page' => $post_num - $i
										);
										query_posts($args);
										while( have_posts() ) {the_post(); ?>
										<li class="col-4-1 fadeInUp not-animated"<?php echo data_animate();?>>
										<div class="product-item">
											<div class="portfolio-img">
												<a href="<?php the_permalink(); ?>">
													<img src="<?php echo post_thumbnail(300, 300); ?>">
												</a>
											</div>
											<div class="portfolio-title">
												<h2><a href="<?php the_permalink(); ?>"><?php the_title_attribute(); ?></a></h2>
											</div>
										</div>
										</li>

										<?php
										$i++; }
										wp_reset_query();
									}
									if ( $i  == 0 )  echo '<li>暂无相关内容!</li>';
									?>
									</ul>
								</div>


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