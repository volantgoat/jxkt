<?php
$modular_title = $id['modular_title'] ?: '模块标题';
$modular_subtitle = $id['modular_subtitle'] ?: '自定义文本描述，通常为英文别名';
$post_img_height = $id['post_img_height'] ?: '500';
$post_img_width = $id['post_img_width'] ?: '500';?>
<div class="<?php if( $id['modular_no_mobile'] ){ ?>mobile_modular_no <?php } ?>module-full-screen module-full-screen-hl">
	<div class="module-inner not-animated"<?php echo data_animate();?>>
		<div class="page-width news-tab">
			<div class="module-full-screen-title">
				<h2><?php echo $modular_title; ?></h2>
				<div class="module-title-content">
					<i class="mark-left"></i>
					<h3><?php echo $modular_subtitle;?></h3>
					<i class="mark-right"></i>
					<div class="tab-category">
					<?php
					if(is_array( $categories = $id['modular_category'] )){
					$count_cat = count($categories);
					$i = 1;
					foreach ($categories as $cat=>$catid ){?>
					<a href="javascript:;" <?php if( $i == '1' ){ echo 'class="two_sel"'; }?> id="productsolutiont<?php echo $i;?>" onMouseOver="producthoverlia<?php echo $count_cat;?>('productsolutiont', 'productsolutiondiv', <?php echo $i;?>, <?php echo $count_cat;?>,'two_sel')"><?php echo get_cat_name( $catid );?></a>
					<?php $i++; } }?>
					</div>
				</div>
			</div>
			<?php
			if(is_array( $categories = $id['modular_category'] )){
			$s = 1;
			foreach ($categories as $cat=>$catid ){?>
			<div class="module-full-screen-content<?php if( $s != '1' ){ echo ' divhidden'; }?>" id="productsolutiondiv<?php echo $s;?>">
				<div class="scrollable carousel product-scrollable product-set clearfix">
					<ul id="product-modular" class="clearfix">
					<?php
						$args = array(
							'no_found_rows' => true,
							'ignore_sticky_posts' => 1,
							'posts_per_page' => 8,
							'cat' => $catid
						);
						$cat_posts = dahuzi_query( $args );
						while( $cat_posts->have_posts() ): $cat_posts->the_post();
						include TEMPLATEPATH.'/template-parts/loop/4.php';
						endwhile; wp_reset_query();?>
					</ul>
				</div>
				<div class="module-full-screen-more">
					<a href="<?php echo get_category_link( $catid ); ?>">查看更多</a>
				</div>
			</div>
			<?php $s++; } }?>
		</div>
	</div>
</div>
<script type="text/javascript">
	//js tab功能   
	function g(o) {
		return document.getElementById(o);
	}
	function producthoverlia<?php echo $count_cat;?>(t_n, t_n2, n, k, className) {
		for(var i = 1; i <= k; i++) {
			g(t_n2 + i).className = 'divhidden';
			g(t_n + i).className = '';
			$("#" + t_n2 + i).find(".anim").removeClass("anim-show");
		}
		g(t_n2 + n).className = '';
		g(t_n + n).className = className;
		setTimeout(function() {
			$("#" + t_n2 + n).find(".anim").addClass("anim-show");
		}, 6)
	}
</script>