<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta content="width=device-width, initial-scale=1.0, user-scalable=no" name="viewport">
<?php if( xintheme_img('favicon','') ) { ?>
<link rel="shortcut icon" href="<?php echo xintheme_img('favicon','');?>"/>
<?php }else{ ?>
<link rel="shortcut icon" href="<?php bloginfo('template_url');?>/static/images/favicon.png"/>
<?php }?>
<title><?php echo dahuzi_seo_title(); ?></title>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<div id="wrapper" class="home-page">
		<?php if( xintheme('notice_code_switch') ) :?>
		<div id="hellobar" class="<?php if( xintheme('notice_no_mobile') ){ echo 'mobile_modular_no'; }?><?php if( xintheme('notice_color') ){ echo ' notice_color_'.xintheme('notice_color').''; }?>">

			<div class="hellobar_inner">
				<div class="page-width">
					<div class="hellobar_inner_wrap">
						<p class="animate">
							<i class="cs-icon la la-bullhorn"></i> <?php echo xintheme('notice_code');?>
						</p>
						<?php if( xintheme('notice_code_close') ) :?>
							<i onclick="closeNotice()" class="la la-times"></i>
						<?php endif;?>
					</div>
				</div>
			</div>
		</div>
		<?php endif;?>
		<?php
		$foot_type = xintheme('header_type') ?: '1';
		get_template_part( 'template-parts/header/header', $foot_type);?>

		<div class="touch-top mobile-section clearfix">
			<div class="touch-top-wrapper clearfix">
				<div class="touch-logo">
					<a href="<?php bloginfo( 'url' ); ?>">
						<?php if ( xintheme_img('logo_mobile','') ){ ?>
							<img src="<?php echo xintheme_img('logo_mobile','');?>" alt="<?php bloginfo('name'); ?>">
						<?php } ?>
					</a>
				</div>
				<div class="touch-navigation">
					<div class="touch-toggle">
						<ul>
							<li class="touch-toggle-item-last"><a href="javascript:;" class="drawer-menu" data-drawer="drawer-section-menu"><span></span><i class="touch-icon-menu"></i></a></li>
						</ul>
					</div>
				</div>
			</div>
			<div class="touch-toggle-content touch-top-home">
				<div class="drawer-section drawer-section-menu">
					<div class="touch-menu">
						<ul>
							<?php if(function_exists('wp_nav_menu')) wp_nav_menu(array('container' => false, 'items_wrap' => '%3$s', 'theme_location' => 'main')); ?>
						</ul>
					</div>
					<?php if( xintheme('search_header') ){?>
					<form id="mobile-search-form" action="<?php echo esc_url(home_url('/')); ?>">
						<fieldset>
							<input type="text" name="s" placeholder="请输入关键词进行搜索" />
							<input type="submit" value="搜索一下" />
						</fieldset>	
					</form>
					<?php }?>
				</div>
			</div>
		</div>