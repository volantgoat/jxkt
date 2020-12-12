<?php
/**
 * @Author: 大胡子
 * @Email:  dahuzi@xintheme.com
 * @Link:   www.dahuzi.me
 * @Date:   2020-04-27 23:57:28
 * @Last Modified by:   dahuzi
 * @Last Modified time: 2020-04-28 20:07:45
 */
?>
		<footer class="footer footer-1">
		<div class="footer-main">
			<div id="a1portalSkin_footerAreaA" class="page-width clearfix">
				<div class="module-default">
					<div class="module-inner">
						<div class="module-content">
							<div class="qhd-module">
								<div class="column">
									<div class="col-5-1">
										<div class="qhd_column_contain">
											<div class="module-default">
												<div class="module-inner">
													<div class="module-title module-title-default clearfix">
														<div class="module-title-content clearfix">
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
													</div>
													<div class="module-content">
														<div class="link link-block">
															<ul>
																<?php if(function_exists('wp_nav_menu')) wp_nav_menu(array('container' => false, 'items_wrap' => '%3$s', 'theme_location' => 'd1','depth'=> 1)); ?>
															</ul>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-5-1">
										<div class="qhd_column_contain">
											<div class="module-default">
												<div class="module-inner">
													<div class="module-title module-title-default clearfix">
														<div class="module-title-content clearfix">
															<h3>
															<?php 
																$menu=get_nav_menu_locations();
																if(isset($menu["d2"])):
																	$menu_object=wp_get_nav_menu_object($menu["d2"]); 
																	echo $menu_object->name ;
																else:
																	echo '请到后台设置菜单';
																endif;
															?>
															</h3>
														</div>
													</div>
													<div class="module-content">
														<div class="link link-block">
															<ul>
																<?php if(function_exists('wp_nav_menu')) wp_nav_menu(array('container' => false, 'items_wrap' => '%3$s', 'theme_location' => 'd2','depth'=> 1)); ?>
															</ul>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-5-1">
										<div class="qhd_column_contain">
											<div class="module-default">
												<div class="module-inner">
													<div class="module-title module-title-default clearfix">
														<div class="module-title-content clearfix">
															<h3>
															<?php 
																$menu=get_nav_menu_locations();
																if(isset($menu["d3"])):
																	$menu_object=wp_get_nav_menu_object($menu["d3"]); 
																	echo $menu_object->name ;
																else:
																	echo '请到后台设置菜单';
																endif;
															?>
															</h3>
														</div>
													</div>
													<div class="module-content">
														<div class="link link-block">
															<ul>
																<?php if(function_exists('wp_nav_menu')) wp_nav_menu(array('container' => false, 'items_wrap' => '%3$s', 'theme_location' => 'd3','depth'=> 1)); ?>
															</ul>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-5-1">
										<div class="qhd_column_contain">
											<div class="module-default">
												<div class="module-inner">
													<div class="module-title module-title-default clearfix">
														<div class="module-title-content clearfix">
															<h3>
															<?php 
																$menu=get_nav_menu_locations();
																if(isset($menu["d4"])):
																	$menu_object=wp_get_nav_menu_object($menu["d4"]); 
																	echo $menu_object->name ;
																else:
																	echo '请到后台设置菜单';
																endif;
															?>
															</h3>
														</div>
													</div>
													<div class="module-content">
														<div class="link link-block">
															<ul>
																<?php if(function_exists('wp_nav_menu')) wp_nav_menu(array('container' => false, 'items_wrap' => '%3$s', 'theme_location' => 'd4','depth'=> 1)); ?>
															</ul>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-5-1 last">
										<div class="qhd_column_contain">
											<div class="module-default">
												<div class="module-inner">
													<div class="module-content">
														<div class="qhd-content">									
														<?php if( xintheme_img('footer_qr_code','') ) { ?>
															<p style="text-align: center;">
																<img src="<?php echo xintheme_img('footer_qr_code',''); ?>" style="width: 130px; display: inline !important;padding-bottom: 5px;"/><br/>
																<?php echo xintheme('footer_qr_code_text'); ?>
															</p>
														<?php }else{ ?>
															<p style="text-align: center;">
																<img src="<?php bloginfo('template_directory'); ?>/static/images/weixin.png" style="width: 130px; display: inline !important;padding-bottom: 5px;"/><br/>
																关注微信公众号
															</p>
														<?php }?>	
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
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