<?php

//载入JS\CSS
if ( ! function_exists( 'xintheme_scripts_method' ) ) {
    function xintheme_scripts_method() {
        
        //载入css
		wp_enqueue_style( 'style', get_bloginfo( 'stylesheet_url' ), array(), wp_get_theme()->get( 'Version' ) );
		if( xintheme('data_animate') ){
			wp_enqueue_style( 'animate', get_template_directory_uri().'/static/css/animate.min.css', array(), wp_get_theme()->get( 'Version' ) );
		}
      	wp_enqueue_style( 'iconfont', get_template_directory_uri().'/static/font/iconfont.css', array(), wp_get_theme()->get( 'Version' ) );
      	wp_enqueue_style( 'line-awesome', get_template_directory_uri().'/static/line-awesome/css/line-awesome.min.css', array(), '' );
      	wp_enqueue_style( 'font-awesome', 'https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css', array(), '4.7.1', 'all' );
      	wp_enqueue_style( 'theme-color', get_template_directory_uri().'/static/css/theme-color.css', array(), wp_get_theme()->get( 'Version' ) );

        //载入js

        //禁止加载默认JQ
        if ( !is_admin() ) { // 后台不禁止
	        wp_deregister_script( 'jquery' ); // 取消原有的 jquery 定义
	        wp_deregister_script( 'l10n' );
        }
        wp_enqueue_script('jquery', get_template_directory_uri() . '/static/js/jquery-1.7.2.min.js', array(),false);
		wp_enqueue_script('script', get_template_directory_uri() . '/static/js/script.min.js', array('jquery'),false, true);
		wp_enqueue_script('xintheme', get_template_directory_uri() . '/static/js/xintheme.js', array(),false, true);
		wp_enqueue_script('theia-sticky-sidebar', get_template_directory_uri() . '/static/js/theia-sticky-sidebar.js', array(),false, true);

		//fancybox
		wp_enqueue_style('fancybox', 'https://cdn.staticfile.org/fancybox/3.5.7/jquery.fancybox.min.css');
		wp_enqueue_script('fancybox3', 'https://cdn.staticfile.org/fancybox/3.5.7/jquery.fancybox.min.js', ['jquery'], '', true);

		if( xintheme('notice_code_switch') ){
			$head_notice = 'true';
		}else{
			$head_notice = 'false';
		}
        wp_localize_script('script', 'dahuzi', [
            'ajaxurl' => admin_url('admin-ajax.php'),
        ]);
		if( xintheme('data_animate') ){
			wp_enqueue_script('animate', get_template_directory_uri() . '/static/js/animate.min.js', array(),false);
			wp_localize_script('animate', 'xintheme', ['data_animate' => 'true','head_notice' => $head_notice]);
		}else{
			wp_localize_script('xintheme', 'xintheme', ['data_animate' => 'false','head_notice' => $head_notice]);
		}
		
		if (is_singular() && comments_open() && get_option('thread_comments')){
			wp_enqueue_script( 'comment-reply' );
		}
		wp_enqueue_script('carousel', get_template_directory_uri() . '/static/js/owl.carousel.min.js', array(),false, true);
		
     }
}
add_action('wp_enqueue_scripts', 'xintheme_scripts_method');

//加载动画 css
function xintheme_color_css() {
	if( xintheme('data_animate') ){
		echo '<style>.not-animated {opacity:0}</style>';
	}
	if( xintheme('xintheme_post_indent') && is_single() ){
		echo '<style>#wzzt p {text-indent: 2em}</style>';
	}
}
add_action('wp_head', 'xintheme_color_css');


//判断是否开启模块加载动画
function data_animate(){
	if( xintheme('data_animate') ){
		return ' data-animate="fadeInUp" data-delay="200"';
	}
}

//移除WPjam插件的某些选项
if( defined('WPJAM_BASIC_PLUGIN_FILE') ){

	add_filter('wpjam_extends_setting', function($wpjam_setting){

		unset($wpjam_setting['fields']['related-posts.php']);//移除相关文章
		unset($wpjam_setting['fields']['wpjam-postviews.php']);//移除文章浏览量
		unset($wpjam_setting['fields']['mobile-theme.php']);//移除手机端主题选项
		unset($wpjam_setting['fields']['wpjam-rewrite.php']);//移除Rewrite 优化

		return $wpjam_setting;
	}, 99);
	
	$wpjam_extends	= get_option('wpjam-extends');
	if($wpjam_extends){
		$wpjam_extends_updated	= false;
		if(!empty($wpjam_extends['related-posts.php'])){
			unset($wpjam_extends['related-posts.php']);
			$wpjam_extends_updated	= true;
		}

		if(!empty($wpjam_extends['wpjam-postviews.php'])){
			unset($wpjam_extends['wpjam-postviews.php']);
			$wpjam_extends_updated	= true;
		}

		if(!empty($wpjam_extends['mobile-theme.php'])){
			unset($wpjam_extends['mobile-theme.php']);
			$wpjam_extends_updated	= true;
		}

		if($wpjam_extends_updated){
			update_option('wpjam-extends', $wpjam_extends);
		}
	}

}


# 清除wp所有自带的customize选项
# ------------------------------------------------------------------------------
function remove_default_settings_customize( $wp_customize ) {
    $wp_customize->remove_section( 'title_tagline');
    $wp_customize->remove_section( 'colors');
    $wp_customize->remove_section( 'header_image');
    $wp_customize->remove_section( 'background_image');
    //$wp_customize->remove_panel( 'nav_menus');
    $wp_customize->remove_section( 'static_front_page');
    $wp_customize->remove_section( 'custom_css');
    //$wp_customize->remove_panel( 'widgets' );
}
add_action( 'customize_register', 'remove_default_settings_customize',50 );
//后台禁止加载谷歌字体
function wp_style_del_web( $src, $handle ) {
    if( strpos(strtolower($src),'fonts.googleapis.com') ){
        $src='';
    }
    return $src;
}
add_filter( 'style_loader_src', 'wp_style_del_web', 2, 2 );
//js处理
function wp_script_del_web( $src, $handle ) {
    $src_low = strtolower($src);
    if( strpos($src_low,'maps.googleapis.com') ){
        return  str_replace('maps.googleapis.com','ditu.google.cn',$src_low);  //google地图
    }
    if( strpos($src_low,'ajax.googleapis.com') ){
        return  '';        //无法访问直接去除
    }
    if( strpos($src_low,'twitter.com') || strpos($src_low,'facebook.com')  || strpos($src_low,'youtube.com') ){
        return '';        //无法访问直接去除
    }
    return $src;
}
add_filter( 'script_loader_src', 'wp_script_del_web', 2, 2 );

function Bing_editor_buttons($buttons){
	$buttons[] = 'fontselect';
	$buttons[] = 'fontsizeselect';
	$buttons[] = 'backcolor';
	$buttons[] = 'underline';
	$buttons[] = 'hr';
	$buttons[] = 'sub';
	$buttons[] = 'sup';
	$buttons[] = 'cut';
	$buttons[] = 'copy';
	$buttons[] = 'paste';
	$buttons[] = 'cleanup';
	$buttons[] = 'wp_page';
	$buttons[] = 'newdocument';
	return $buttons;
}
add_filter("mce_buttons_3", "Bing_editor_buttons");


add_action( 'do_faviconico', function() {
	//Check for icon with no default value
	if ( $icon = get_site_icon_url( 32 ) ) {
		//Show the icon
		wp_redirect( $icon );
	} else {
		//Show nothing
		header( 'Content-Type: image/vnd.microsoft.icon' );
	}
	exit;
} );

