<?php
$modular_title = $id['modular_title'] ?: '模块标题';
$modular_subtitle = $id['modular_subtitle'] ?: '自定义文本描述，通常为英文别名';
$feature_color = $id['feature_color'] ?: 'no';?>
<div class="module-full-screen <?php if( $id['modular_no_mobile'] ){ ?>mobile_modular_no <?php } ?>categories-area<?php if( $feature_color ){ echo ' feature2_color_'.$feature_color.''; }?>">
	<div class="module-full-screen-title not-animated"<?php echo data_animate();?>>
		<h2><?php echo $modular_title; ?></h2>
		<div class="module-title-content">
			<i class="mark-left"></i>
			<h3><?php echo $modular_subtitle;?></h3>
			<i class="mark-right"></i>
		</div>
	</div>
	<div class="page-width">
		<div class="row">
			<?php
			if( is_array($id['add_feature']) ){
			foreach ( $id['add_feature'] as $value ): ?>
			<div class="col-md-4 col-sm-6 col-xs-12"<?php echo data_animate();?>>
				<div class="single-item">
					<div class="icon-holder">
						<div class="icon-box">
							<div class="icon">
								<span class="<?php echo $value['feature_icon'];?>"></span>
							</div>
						</div>
					</div>
					<div class="text-holder">
						<h5><?php echo $value['feature_title'];?></h5>
						<p><?php echo $value['feature_describe'];?></p>
					</div>
				</div>
			</div>
			<?php
			endforeach;
			}?>
		</div>
	</div>
</div>