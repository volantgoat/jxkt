<?php
$modular_8 = $id['modular_8'];
$modular_bg = $id['modular_8_bg'];
$modular_bg_color = $modular_bg['modular_8_bg_color'] ?: '#072948';
$modular_bg_img   = $modular_bg['modular_8_bg_img']['url'] ?: '';
?>
<style>
<?php if( $modular_bg_img ){?>
.count-up-sec {background-image: url(<?php echo $modular_bg_img;?>)}
.count-up-sec .count-up-sec-overlay {opacity: 1;background-color: <?php echo $modular_bg_color;?>}
<?php }?>
<?php if( $modular_bg_color && !$modular_bg_img ){?>
.count-up-sec {background: <?php echo $modular_bg_color;?> none repeat scroll 0 0}
<?php }?>
</style>
<div class="<?php if( $id['modular_no_mobile'] ){ ?>mobile_modular_no <?php } ?>count-up-sec module-full-screen not-animated"<?php echo data_animate();?>>
	<div class="count-up-sec-overlay"></div>
	<div class="page-width">
		<div class="row">
			<?php if($modular_8['modular_8_count_1']){?>
			<div class="col-md-3 col-sm-6 col-xs-6 inner">
				<div class="counting_sl">
					<div class="countup-text">
						<h2 class="counter"><?php echo $modular_8['modular_8_count_1'];?></h2>
						<span class="border"></span>
						<h4><?php echo $modular_8['modular_8_title_1'];?></h4>
					</div>
				</div>
			</div>
			<?php }?>
			<?php if($modular_8['modular_8_count_2']){?>
			<div class="col-md-3 col-sm-6 col-xs-6 inner">
				<div class="counting_sl">
					<div class="countup-text">
						<h2 class="counter"><?php echo $modular_8['modular_8_count_2'];?></h2>
						<span class="border"></span>
						<h4><?php echo $modular_8['modular_8_title_2'];?></h4>
					</div>
				</div>
			</div>
			<?php }?>
			<?php if($modular_8['modular_8_count_3']){?>
			<div class="col-md-3 col-sm-6 col-xs-6 inner">
				<div class="counting_sl">
					<div class="countup-text">
						<h2 class="counter"><?php echo $modular_8['modular_8_count_3'];?></h2>
						<span class="border"></span>
						<h4><?php echo $modular_8['modular_8_title_3'];?></h4>
					</div>
				</div>
			</div>
			<?php }?>
			<?php if($modular_8['modular_8_count_4']){?>
			<div class="col-md-3 col-sm-6 col-xs-6 inner">
				<div class="counting_sl">
					<div class="countup-text">
						<h2 class="counter"><?php echo $modular_8['modular_8_count_4'];?></h2>
						<span class="border"></span>
						<h4><?php echo $modular_8['modular_8_title_4'];?></h4>
					</div>
				</div>
			</div>
			<?php }?>
		</div>
	</div>
</div>