<?php

/**
 * @Author: 大胡子
 * @Email:  dahuzi@xintheme.com
 * @Link:   www.dahuzi.me
 * @Date:   2020-04-29 15:56:41
 * @Last Modified by:   dahuzi
 * @Last Modified time: 2020-04-29 16:23:42
 */
?>
<header class="top header-v4 desktops-section default-top">
<div class="top-main">
	<div class="page-width clearfix">
		<div class="logo">
			<a href="<?php bloginfo( 'url' ); ?>">
				<img src="<?php echo xintheme_img('logo','');?>" alt="<?php bloginfo('name'); ?>">
			</a>
		</div>
		<div class="top-main-content">
			<nav class="nav">
			<div class="main-nav clearfix">
				<ul class="sf-menu">
					<?php if(function_exists('wp_nav_menu')) wp_nav_menu(array('container' => false, 'items_wrap' => '%3$s', 'theme_location' => 'main')); ?>
				</ul>
				<?php if( xintheme('search_header') ){?>
				<button id="toggle-search" class="header-button"><i class="la la-search"></i></button></li>
				<form id="search-form" action="<?php echo esc_url(home_url('/')); ?>">
					<fieldset>
						<input type="text" name="s" placeholder="请输入关键词进行搜索" />
						<input type="submit" value="搜索一下" />
					</fieldset>	
				</form>
				<?php }?>
			</div>
			</nav>
		</div>
	</div>
</div>
</header>