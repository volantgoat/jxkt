<?php

//激活小工具
if( function_exists('register_sidebar') ) {
	register_sidebar(array(
		'name' => '全站侧栏',
		'id'            => 'widget_right',
		'before_widget' => '<div class="widget %2$s">', 
		'after_widget' => '</div>', 
		'before_title' => '<div class="widget__title block-heading block-heading--line"><h3 class="widget__title-text">', 
		'after_title' => '</h3></div>' 
	));
	/*
	register_sidebar(array(
		'name'          => '首页侧栏',
		'id'            => 'widget_sidebar',
		'before_widget' => '<div class="widget %2$s">', 
		'after_widget' => '</div>', 
		'before_title' => '<div class="widget__title block-heading block-heading--line"><h3 class="widget__title-text">', 
		'after_title' => '</h3></div>' 
	));
	*/

	register_sidebar(array(
		'name'          => '产品分类 侧栏',
		'id'            => 'widget_product',
		'before_widget' => '<div class="widget %2$s">', 
		'after_widget' => '</div>', 
		'before_title' => '<div class="widget__title block-heading block-heading--line"><h3 class="widget__title-text">', 
		'after_title' => '</h3></div>' 
	));

	register_sidebar(array(
		'name'          => '新闻资讯分类 侧栏',
		'id'            => 'widget_news',
		'before_widget' => '<div class="widget %2$s">', 
		'after_widget' => '</div>', 
		'before_title' => '<div class="widget__title block-heading block-heading--line"><h3 class="widget__title-text">', 
		'after_title' => '</h3></div>' 
	));

	register_sidebar(array(
		'name'          => '产品文章页 侧栏',
		'id'            => 'widget_produc',
		'before_widget' => '<div class="widget %2$s">', 
		'after_widget' => '</div>', 
		'before_title' => '<div class="widget__title block-heading block-heading--line"><h3 class="widget__title-text">', 
		'after_title' => '</h3></div>' 
	));

	register_sidebar(array(
		'name'          => '文章页侧栏',
		'id'            => 'widget_post',
		'before_widget' => '<div class="widget %2$s">', 
		'after_widget' => '</div>', 
		'before_title' => '<div class="widget__title block-heading block-heading--line"><h3 class="widget__title-text">', 
		'after_title' => '</h3></div>' 
	));
	
	register_sidebar(array(
		'name'          => '页面侧栏',
		'id'            => 'widget_page',
		'before_widget' => '<div class="widget %2$s">', 
		'after_widget' => '</div>', 
		'before_title' => '<div class="widget__title block-heading block-heading--line"><h3 class="widget__title-text">', 
		'after_title' => '</h3></div>' 
	));
	
	register_sidebar(array(
		'name'          => '其它页面',
		'id'            => 'widget_other',
		'before_widget' => '<div class="widget %2$s">', 
		'after_widget' => '</div>', 
		'before_title' => '<div class="widget__title block-heading block-heading--line"><h3 class="widget__title-text">', 
		'after_title' => '</h3></div>' 
	));

}
include_once get_template_directory() .'/template-parts/widgets/index.php';

//去除自带小工具
function unregister_widgets() {
   unregister_widget("WP_Widget_Pages");//页面
   unregister_widget("WP_Widget_Calendar");//文章日程表
   unregister_widget("WP_Widget_Archives");//文章归档
   unregister_widget("WP_Widget_Meta");//登入/登出，管理，Feed 和 WordPress 链接
   unregister_widget("WP_Widget_Search");//搜索
   unregister_widget("WP_Widget_Categories");//分类目录
   unregister_widget("WP_Widget_Recent_Posts");//近期文章
   unregister_widget("WP_Widget_Recent_Comments");//近期评论
   unregister_widget("WP_Widget_RSS");//RSS订阅
   unregister_widget("WP_Widget_Links");//链接
   unregister_widget("WP_Widget_Text");//文本
   unregister_widget("WP_Widget_Tag_Cloud");//标签云
   //unregister_widget("WP_Nav_Menu_Widget");//自定义菜单
   unregister_widget("WP_Widget_Media_Audio");//音频
   //unregister_widget("WP_Widget_Media_Image");//图片
   unregister_widget("WP_Widget_Media_Video");//视频
   unregister_widget("WP_Widget_Media_Gallery");//画廊
}
add_action("widgets_init", "unregister_widgets");

//小工具显示分类ID
add_action('optionsframework_after','show_category', 100);
function show_category() {
    global $wpdb;
    $request = "SELECT $wpdb->terms.term_id, name FROM $wpdb->terms ";
    $request .= " LEFT JOIN $wpdb->term_taxonomy ON $wpdb->term_taxonomy.term_id = $wpdb->terms.term_id ";
    $request .= " WHERE $wpdb->term_taxonomy.taxonomy = 'category' ";
    $request .= " ORDER BY term_id asc";
    $categorys = $wpdb->get_results($request);
    echo '<div class="uk-panel uk-panel-box" style="margin-bottom: 20px;"><h3 style="margin-top: 0; margin-bottom: 15px; font-size: 18px; line-height: 24px; font-weight: 400; text-transform: none; color: #666;">可能会用到的分类ID</h3>';
    echo "<ul>";
    foreach ($categorys as $category) { 
        echo  '<li style="margin-right: 10px;float:left;">'.$category->name."（<code>".$category->term_id.'</code>）</li>';
    }
    echo "</ul></div>";
}