
	<?php
	$foot_type = xintheme('foot_type') ?: '1';
	get_template_part( 'template-parts/footer/footer', $foot_type);?>

	</div>

	<div class="consultation">
		<ul>
			<?php
			$weixin_img = xintheme_img('consultation_weixin_img','');
			if( $weixin_img ) : ?>
			<li>
				<a href="javascript:;">
					<img class="ico" src="<?php bloginfo('template_directory'); ?>/static/images/icon-weixin.svg" alt="微信" title="微信">
					<span class="ewm animated flipInX">
						<img src="<?php echo $weixin_img;?>">
						<em><?php echo xintheme('consultation_weixin_txt');?></em>
					</span>
				</a>
			</li>
			<?php endif; ?>

			<?php
			$consultation_qq = xintheme('consultation_qq');
			if( $consultation_qq ) : ?>
			<li>
				<a href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $consultation_qq;?>&site=qq&menu=yes" target="_blank" rel="nofollow" title="联系我们">
					<img class="ico" src="<?php bloginfo('template_directory'); ?>/static/images/icon-qq.svg" alt="客服" title="客服">
				</a>
			</li>
			<?php endif; ?>

			<?php
			$consultation_weibo_url = xintheme('consultation_weibo_url');
			if( $consultation_weibo_url ) : ?>
			<li>
				<a href="<?php echo $consultation_weibo_url;?>" target="_blank" rel="nofollow" title="官方微博">
					<img class="ico" src="<?php bloginfo('template_directory'); ?>/static/images/icon-weibo.svg" alt="官方微博" title="官方微博">
				</a>
			</li>
			<?php endif; ?>

			<?php
			$consultation_email = xintheme('consultation_email_url');
			if( $consultation_email ) : ?>
			<li>
				<a rel="nofollow" target="_blank" href="<?php echo $consultation_email;?>">
					<img class="ico" src="<?php bloginfo('template_directory'); ?>/static/images/icon-yx.svg" alt="邮箱" title="邮箱">
				</a>
			</li>
			<?php endif; ?>

			<?php
			$consultation_tel = xintheme('consultation_tel');
			if( $consultation_tel ) : ?>
			<li class="dri_pho">
				<a href="javascript:;">
					<img class="ico" src="<?php bloginfo('template_directory'); ?>/static/images/icon-dh.svg" alt="联系电话" title="联系电话">
					<span class="dh animated flipInX"><?php echo $consultation_tel;?></span>
				</a>
			</li>
			<?php endif; ?>

			<li id="thetop">
				<a href="javascript:;" class="fixed-gotop gotop">
					<img src="<?php bloginfo('template_directory'); ?>/static/images/icon-gotop-fixed.gif" alt="返回顶部" title="返回顶部">
				</a>
			</li>
		</ul>
	</div>
<?php if( xintheme('mobile_footer_nav') ){?>
<ul class="mobi-bar">
	<li class="mobi-phone"><a href="tel:<?php echo xintheme('consultation_tel');?>" rel="nofollow"><i>联系电话</i></a></li>
	<li class="mobi-chat"><a href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo xintheme('consultation_qq');?>&site=qq&menu=yes" target="_blank" rel="nofollow"><i>在线咨询</i></a></li>
	<li class="mobi-email"><a href="<?php echo xintheme('consultation_email_url');?>" rel="nofollow"><i>E-mail</i></a></li>
	<li class="mobi-map"><a href="<?php echo xintheme('baiduditu');?>"><i>百度地图</i></a></li>
</ul>
<?php }else{?>
<style>#wrapper {margin-bottom: 0}</style>
<?php }?>
<?php if( xintheme('mobile_foot_menu_sw') ){ get_template_part( 'template-parts/mobile-foot-menu' );}?>
<?php wp_footer(); ?>

</body>
</html>