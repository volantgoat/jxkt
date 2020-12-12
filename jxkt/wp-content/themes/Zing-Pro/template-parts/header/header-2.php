<?php

/**
 * @Author: 大胡子
 * @Email:  dahuzi@xintheme.com
 * @Link:   www.dahuzi.me
 * @Date:   2020-04-29 15:56:37
 * @Last Modified by:   dahuzi
 * @Last Modified time: 2020-05-02 00:14:27
 */
?>
<header class="header-area header-v2">
<div class="page-width">
	<div class="row">
		<div class="logo">
			<a href="<?php bloginfo( 'url' ); ?>">
				<img src="<?php echo xintheme_img('header2_logo');?>" alt="<?php bloginfo('name'); ?>">
			</a>
		</div>
		<div class="header-contact-info">
			<ul>
				<?php
				$header2_contact = xintheme('header2_contact');
				if( $header2_contact ){
				foreach ( $header2_contact as $value ): ?>
				<li>
				<div class="iocn-holder">
					<span class="<?php echo $value['footer2_contact_icon'];?>"></span>
				</div>
				<div class="text-holder">
					<h5><?php echo $value['header2_contact_title'];?></h5>
					<h6><?php echo $value['header2_contact_describe'];?></h6>
				</div>
				</li>
				<?php endforeach;?>
				<?php }?>
			</ul>
		</div>
	</div>
</div>
</header>
<div class="mainmenu-area">
	<div class="page-width">
		<div class="row">
			<nav class="main-menu">
			<div class="navbar-collapse collapse clearfix">
				<ul class="navigation clearfix">
					<?php if(function_exists('wp_nav_menu')) wp_nav_menu(array('container' => false, 'items_wrap' => '%3$s', 'theme_location' => 'main')); ?>
				</ul>
			</div>
			</nav>
			<?php if( xintheme('search_header') ){?>
			<div class="top-search-box pull-right">
				<button><i class="fa fa-search"></i></button>
				<ul class="search-box">
					<li>
					<form action="<?php echo esc_url(home_url('/')); ?>">
						<input type="text" name="s" placeholder="输入关键词搜索..." />
						<button type="submit"><i class="fa fa-search"></i></button>
					</form>
					</li>
				</ul>
			</div>
			<?php }?>
		</div>
	</div>
</div>