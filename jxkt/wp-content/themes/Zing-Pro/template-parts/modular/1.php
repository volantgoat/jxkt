<?php
$modular_title = $id['modular_title'] ?: '模块标题';
$modular_subtitle = $id['modular_subtitle'] ?: '自定义文本描述，通常为英文别名';
$modular_url = $id['modular_url'];?>
<div class="<?php if( $id['modular_no_mobile'] ){ ?>mobile_modular_no <?php } ?>module-full-screen module-full-1">
	<div class="module-inner not-animated"<?php echo data_animate();?>>
		<div class="page-width">

			<div class="module-full-screen-title">
				<h2><?php echo $modular_title; ?></h2>
				<div class="module-title-content">
					<i class="mark-left"></i>
					<h3><?php echo $modular_subtitle;?></h3>
					<i class="mark-right"></i>
				</div>
			</div>

			<div class="qhd-module">
				<div class="column">
					<div class="module-default module">
						<div class="entry-list-time-hl-col entry-list-time-hl">
						<ul class="column marg-per5 clearfix">
							<?php
							$cat_or_post = $id['modular_cat_or_post'] ?: 'cat';
							if( $cat_or_post == 'cat' ){

					            $category = array();
					            if( is_array( $id['modular_category'] ) ){
						            foreach ( $id['modular_category'] as $value) {
						                if( $value ) $category[] = $value;
						            }
					        	}

								//query_posts( 'cat='.implode($category, ',').'&posts_per_page=6,&ignore_sticky_posts=1' );
								$args = array(
									'no_found_rows' => true,
									'ignore_sticky_posts' => 1,
									'posts_per_page' => 6,
									'cat' => implode($category, ',')
								);
								$cat_posts = dahuzi_query( $args );
								while( $cat_posts->have_posts() ): $cat_posts->the_post();
								get_template_part('template-parts/loop/1');
								endwhile; wp_reset_query();

							}else{

					            $posts = array();
					            if( is_array( $id['modular_posts_id'] ) ){
						            foreach ( $id['modular_posts_id'] as $value) {

						        	$posts = get_posts("post_type=any&include=".$value.""); if($posts) : foreach( $posts as $post ) : setup_postdata( $post );
									get_template_part('template-parts/loop/1');
									endforeach; endif; wp_reset_query();

						            }
					        	}

							}?>
							<?php if( $modular_url ){?>
							<div class="module-full-screen-more">
								<a href="<?php echo $modular_url; ?>">查看更多</a>
							</div>
							<?php }?>
						</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>