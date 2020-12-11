<?php
/**
 * @Author: 大胡子
 * @Email:  dahuzi@xintheme.com
 * @Link:   www.dahuzi.me
 * @Date:   2020-04-27 23:57:28
 * @Last Modified by:   dahuzi
 * @Last Modified time: 2020-04-29 15:56:48
 */
?>
		<section class="site-footer bottom footer-3">
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