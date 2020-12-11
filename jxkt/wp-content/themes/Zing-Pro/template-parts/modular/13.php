<?php
$modular_title = $id['modular_title'] ?: '模块标题';
$modular_subtitle = $id['modular_subtitle'] ?: '自定义文本描述，通常为英文别名';?>

<div class="<?php if( $id['modular_no_mobile'] ){ ?>mobile_modular_no <?php } ?>module-full-screen module-full-1">
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
					<a href="javascript:;" <?php if( $i == '1' ){ echo 'class="two_sel"'; }?> id="solutiont<?php echo $i;?>" onMouseOver="hoverlia<?php echo $count_cat;?>('solutiont', 'solutiondiv', <?php echo $i;?>, <?php echo $count_cat;?>,'two_sel')"><?php echo get_cat_name( $catid );?></a>
					<?php $i++; } }?>
					</div>
				</div>
			</div>

			<?php
			if(is_array( $categories = $id['modular_category'] )){
			$s = 1;
			foreach ($categories as $cat=>$catid ){?>
			<div class="qhd-module<?php if( $s != '1' ){ echo ' divhidden'; }?>" id="solutiondiv<?php echo $s;?>">
				<div class="column">
					<div class="module-default module">
						<div class="entry-list-time-hl-col entry-list-time-hl">
						<ul class="column marg-per5 clearfix">
							<?php
								$args = array(
									'no_found_rows' => true,
									'ignore_sticky_posts' => 1,
									'posts_per_page' => 6,
									'cat' => $catid
								);
								$cat_posts = dahuzi_query( $args );
								while( $cat_posts->have_posts() ): $cat_posts->the_post();
								get_template_part('template-parts/loop/1');
								endwhile; wp_reset_query();?>
							<div class="module-full-screen-more">
								<a href="<?php echo get_category_link( $catid ); ?>">查看更多</a>
							</div>
						</ul>
						</div>
					</div>
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
	function hoverlia<?php echo $count_cat;?>(t_n, t_n2, n, k, className) {
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
