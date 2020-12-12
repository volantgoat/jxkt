<div class="<?php if( $id['modular_no_mobile'] ){ ?>mobile_modular_no <?php } ?>video-section"<?php echo data_animate();?> style="background-image: url(<?php echo $id['video_bg_img']['url'];?>);">
<div class="page-width fadeInLeft animated">
	<div class="sec-title light">
		<span class="title"><?php echo $id['video_sub_title'];?></span>
		<h2><?php echo $id['video_title'];?></h2>
	</div>
	<div class="video-link">
		<a data-fancybox data-type="iframe" href="<?php echo $id['video_url'];?>" class="link" data-fancybox="gallery" data-caption="">
			<span class="icon fa fa-play"></span>观看视频
		</a>
	</div>
</div>
</div>