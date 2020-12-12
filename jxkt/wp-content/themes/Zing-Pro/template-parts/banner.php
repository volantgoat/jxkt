<?php
$banner = xintheme('banner');
if($banner){
?>
<div class="responsive-carousel carousel clearfix">
	<div id="responsive-309391">
		<?php foreach ( $banner as $value ): ?>
		<div class="carousel-item">
			<div class="carousel-img">
				<a href="<?php echo $value['banner_url'];?>" <?php if( $value['banner_blank'] ){?>target="_blank"<?php } ?> <?php if( $value['banner_nofollow'] ){?>rel="nofollow"<?php } ?> title="<?php echo $value['banner_alt'];?>">
					<?php if ( wp_is_mobile() && $value['banner_img_mobile']['url'] ){ ?>
						<img src="<?php echo $value['banner_img_mobile']['url'];?>" alt="<?php echo $value['banner_alt'];?>" />
					<?php }else { ?>
						<img src="<?php echo $value['banner_img']['url'];?>" alt="<?php echo $value['banner_alt'];?>" />
					<?php } ?>
				</a>
			</div>
		</div>
		<?php endforeach;?>
	</div>
</div>
<?php }?>