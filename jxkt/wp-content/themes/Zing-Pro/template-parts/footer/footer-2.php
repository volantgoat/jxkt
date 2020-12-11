<?php
/**
 * @Author: 大胡子
 * @Email:  dahuzi@xintheme.com
 * @Link:   www.dahuzi.me
 * @Date:   2020-04-27 23:57:28
 * @Last Modified by:   dahuzi
 * @Last Modified time: 2020-04-28 23:29:13
 */
?>
		<footer class="footer-area footer-2">
		<div class="page-width">
			<div class="row">
				<div class="col-lg-4 col-md-3 col-sm-12 col-xs-12">
					<div class="single-footer-widget pd-bottom">
						<div class="footer-logo">
							<a href="<?php bloginfo( 'url' ); ?>">
								<img src="<?php echo xintheme_img('footer2_logo');?>" alt="<?php bloginfo('name'); ?>">
							</a>
						</div>
						<div class="repairplus-info">
							<?php echo xintheme('footer2_describe');?>
						</div>
					</div>
				</div>
				<div class="col-lg-8 col-md-9 col-sm-12 col-xs-12">
					<div class="footer-widget">
						<div class="row">
							<div class="col-lg-3 col-md-4 col-sm-12">
								<div class="single-footer-widget">
									<div class="title">
										<h3>
										<?php 
											$menu=get_nav_menu_locations();
											if(isset($menu["d1"])):
												$menu_object=wp_get_nav_menu_object($menu["d1"]); 
												echo $menu_object->name ;
											else:
												echo '请到后台设置菜单';
											endif;
										?>
										</h3>
									</div>
									<ul class="services-list">
										<?php if(function_exists('wp_nav_menu')) wp_nav_menu(array('container' => false, 'items_wrap' => '%3$s', 'theme_location' => 'd1','depth'=> 1)); ?>
									</ul>
								</div>
							</div>
							<div class="col-lg-5 col-md-4 col-sm-12">
								<div class="single-footer-widget subscribe-form-widget">
									<div class="title">
										<h3><?php echo xintheme('footer_qr_code_title');?></h3>
									</div>
									<div class="subscribe-form">
										<img src="<?php echo xintheme_img('footer_qr_code',''); ?>">
									</div>
								</div>
							</div>
							<div class="col-lg-4 col-md-4 col-sm-12">
								<div class="single-footer-widget contact-info-widget">
									<div class="title">
										<h3>联系我们</h3>
									</div>
									<ul class="footer-contact-info">
									<?php
									$footer2_contact = xintheme('footer2_contact');
									if( $footer2_contact ){
									foreach ( $footer2_contact as $value ): ?>
										<li>
										<div class="icon-holder">
											<span class="<?php echo $value['footer2_contact_icon'];?>"></span>
										</div>
										<div class="text-holder">
											<h5><span><?php echo $value['footer2_contact_describe'];?></span></h5>
										</div>
										</li>
									<?php endforeach;?>
									<?php }?>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		</footer>
		<section class="site-footer bottom">
		<div class="page-width clearfix">
			<div class="module-default module-no-margin">
				<div class="module-inner">
					<div class="module-content">
						<div class="qhd-content" style="text-align: center;line-height: 2;">
						<?php if( xintheme('foot_link') ) : ?>
						<?php if ( is_home() && !wp_is_mobile() ) { ?>
							<ul class="footer-menu"><li style="opacity:0.4;font-size: 14px;">友情链接：</li><?php wp_list_bookmarks('title_li=&categorize=0'); ?></ul>
						<?php } ?>
						<?php endif; ?>
						<?php
							$footer_icp = xintheme('footer_icp');
							$footer_gaba = xintheme('footer_gaba');
							$footer_copyright = xintheme('footer_copyright');
							$timer_stop = xintheme('timer_stop');
						?>
						<?php if( $footer_copyright ){?><?php echo $footer_copyright;?><?php }else{?>© <?php echo date('Y'); ?>.&nbsp;All Rights Reserved.<?php } ?><?php if( $footer_icp ) : ?>&nbsp;<a rel="nofollow" target="_blank" href="http://beian.miit.gov.cn/"><?php echo $footer_icp;?></a><?php endif; ?><?php if( $footer_gaba ) : ?>&nbsp;<a rel="nofollow" target="_blank" href="<?php echo xintheme('footer_gaba_url');?>"><img class="gaba" alt="公安备案" src="<?php bloginfo('template_directory'); ?>/static/images/gaba.png"><?php echo $footer_gaba;?></a><?php endif; ?><?php if( xintheme('xintheme_link') ) : ?>&nbsp;Theme By&nbsp;<a href="http://www.xintheme.com" target="_blank">XinTheme</a><?php endif; ?><?php if( $timer_stop ) : ?>.&nbsp;页面生成时间：<?php timer_stop(1);?>秒<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		</section>