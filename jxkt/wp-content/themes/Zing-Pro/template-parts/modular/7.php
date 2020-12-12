<?php
$modular_title = $id['modular_title'] ?: '模块标题';
$modular_subtitle = $id['modular_subtitle'] ?: '自定义文本描述，通常为英文别名';
$feature_color = $id['feature_color'] ?: 'no';?>
<div class="<?php if( $id['modular_no_mobile'] ){ ?>mobile_modular_no <?php } ?>module-full-screen icon-boxes index_modular_7 icon-box--four<?php if( $feature_color ){ echo ' feature_color_'.$feature_color.''; }?>">
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
			<div class="col-lg-4 col-md-6 not-animated"<?php echo data_animate();?>>
				<div class="icon-box-four d-flex">
					<div class="box-icon">
						<span class="icon-rounded-sm"><i class="<?php echo $value['feature_icon'];?>"></i></span>
					</div>
					<div class="box-content">
						<h6><?php echo $value['feature_title'];?></h6>
						<p>
							<?php echo $value['feature_describe'];?>
						</p>
					</div>
				</div>
			</div>
			<?php
			endforeach;
			}?>
		</div>
	</div>
</div>