<?php
$modular_title = $id['modular_title'] ?: '模块标题';
$modular_3_img = $id['modular_3_img']['url'];
$modular_3_describe = $id['modular_3_describe'];
$modular_url = $id['modular_url'];?>
<div class="<?php if( $id['modular_no_mobile'] ){ ?>mobile_modular_no <?php } ?>module-full-screen module-full-3">
	<div class="module-inner not-animated"<?php echo data_animate();?>>
		<div class="page-width">
				<div class="qhd-content">
					<div class="column">
						<?php if($id['modular_3_type']=='1'){?>
						<div class="col-2-1">
							<p style="text-align: center;">
								<img src="<?php echo $modular_3_img;?>" alt="<?php echo $modular_title;?>"/>
							</p>
						</div>
						<div class="col-2-1 last">
							<p class="title">
								<span style="font-size:22px;color: #333;"><strong><?php echo $modular_title;?></strong></span>
							</p>
							<div class="about_describe">
								<?php echo $modular_3_describe;?>
							</div>
							<?php if($modular_url){?>
							<div class="module-full-screen-more" style="text-align: left;">
								<a href="<?php echo $modular_url;?>" title=" 查看详情">查看详情</a>
							</div>
							<?php }?>
						</div>
						<?php }else{?>
						<div class="col-2-1">
							<p class="title">
								<span style="font-size:22px;color: #333;"><strong><?php echo $modular_title;?></strong></span>
							</p>
							<div class="about_describe">
								<?php echo $modular_3_describe;?>
							</div>
							<?php if($modular_url){?>
							<div class="module-full-screen-more" style="text-align: left;">
								<a href="<?php echo $modular_url;?>" title=" 查看详情">查看详情</a>
							</div>
							<?php }?>
						</div>
						<div class="col-2-1 last">
							<p style="text-align: center;">
								<img src="<?php echo $modular_3_img;?>" alt="<?php echo $modular_title;?>"/>
							</p>
						</div>
						<?php }?>
					</div>
				</div>
		</div>
	</div>
</div>