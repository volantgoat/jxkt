<?php
$modular_title = $id['modular_title'] ?: '模块标题';
$modular_subtitle = $id['modular_subtitle'] ?: '自定义文本描述，通常为英文别名';?>
<div class="<?php if( $id['modular_no_mobile'] ){ ?>mobile_modular_no <?php } ?>module-full-screen module-full-6">
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
								$index_modular = xintheme('index_modular');
								if(is_array($index_modular)):foreach($index_modular as $id):
									$theme_img =  $id['modular_6_logo'];
									if( ! empty( $theme_img ) ) :
									$theme_img = explode( ',', $id['modular_6_logo'] );
									foreach ( $theme_img as $id ) :
									$modular_6_logo = wp_get_attachment_image_src( $id, 'full' );
								?>
								<li>
									<img src="<?php echo $modular_6_logo[0];?>" alt="<?php echo get_the_excerpt($id);?>" class="img-thumbnail client-15">
								</li>
								<?php endforeach;endif;?>
								<?php endforeach;endif;?>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>