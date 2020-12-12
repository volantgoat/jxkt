<?php
//去除分类标志
if ( dahuzi('no_category') && !function_exists('no_category_base_refresh_rules') ) {
	include TEMPLATEPATH.'/public/extend/no_category.php';
}

//超过2560px的图片不剪裁
add_filter( 'big_image_size_threshold', '__return_false' );

//删除菜单多余css class
function wpjam_css_attributes_filter($classes) {
	return is_array($classes) ? array_intersect($classes, array('current-menu-item','current-post-ancestor','current-menu-ancestor','current-menu-parent','menu-item-has-children','menu-item')) : '';
}
add_filter('nav_menu_css_class',	'wpjam_css_attributes_filter', 100, 1);
add_filter('nav_menu_item_id',		'wpjam_css_attributes_filter', 100, 1);
add_filter('page_css_class', 		'wpjam_css_attributes_filter', 100, 1);

//直接去掉函数 comment_class() 和 body_class() 中输出的 "comment-author-" 和 "author-"
//避免 WordPress 登录用户名被暴露 
function xintheme_comment_body_class($content){
    $pattern = "/(.*?)([^>]*)author-([^>]*)(.*?)/i";
    $replacement = '$1$4';
    $content = preg_replace($pattern, $replacement, $content);
    return $content;
}
add_filter('comment_class', 'xintheme_comment_body_class');
add_filter('body_class', 'xintheme_comment_body_class');

//删除wordpress默认相册样式
add_filter( 'use_default_gallery_style', '__return_false' );

/* 评论作者链接新窗口打开 */
add_filter('get_comment_author_link', function () {
	$url	= get_comment_author_url();
	$author = get_comment_author();
	if ( empty( $url ) || 'http://' == $url ){
		return $author;
	}else{
		return "<a target='_blank' href='$url' rel='external nofollow' class='url'>$author</a>";
	}
});

//搜索结果排除所有页面
function search_filter_page($query) {
    if ($query->is_search && !$query->is_admin) {
        $query->set('post_type', 'post');
    }
    return $query;
}
add_filter('pre_get_posts', 'search_filter_page');

//搜索关键词为空 跳转到首页
add_filter( 'request', function ( $query_variables ) {
	if (isset($_GET['s']) && !is_admin()) {
		if (empty($_GET['s']) || ctype_space($_GET['s'])) {
			wp_redirect( home_url() );
			exit;
		}
	}
	return $query_variables;
} );

//禁止头部加载s.w.org
add_filter( 'wp_resource_hints', function ( $hints, $relation_type ) {
	if ( 'dns-prefetch' === $relation_type ) {
		return array_diff( wp_dependencies_unique_hosts(), $hints );
	}
	return $hints;
}, 10, 2 );

//移除头部emoji.js加载
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );

//给文章图片自动添加alt和title信息
add_filter('the_content', function ($content) {
	global $post;
	$pattern		= "/<a(.*?)href=('|\")(.*?).(bmp|gif|jpeg|jpg|png)('|\")(.*?)>/i";
	$replacement	= '<a$1href=$2$3.$4$5 alt="'.$post->post_title.'" title="'.$post->post_title.'"$6>';
	$content = preg_replace($pattern, $replacement, $content);
	return $content;
});

//去除加载的css和js后面的版本号
if( dahuzi('xintheme_remove_script_version') ){
	function _remove_script_version( $src ){
		$parts = explode( '?', $src );
		return $parts[0];
	}
	add_filter( 'script_loader_src', '_remove_script_version', 15, 1 );
	add_filter( 'style_loader_src', '_remove_script_version', 15, 1 );
	add_filter( 'pre_option_link_manager_enabled', '__return_true' );
}

//关闭 XML-RPC 的 pingback 端口
if( dahuzi('xintheme_pingback') ) :
add_filter( 'xmlrpc_methods', 'remove_xmlrpc_pingback_ping' );
function remove_xmlrpc_pingback_ping( $methods ) {
	unset( $methods['pingback.ping'] );
	return $methods;
}
endif;

//使用v2ex镜像avatar头像
if( dahuzi('xintheme_v2ex') ) :
	add_filter( 'get_avatar', function ($avatar) {
		return str_replace(['cn.gravatar.com/avatar', 'secure.gravatar.com/avatar', '0.gravatar.com/avatar', '1.gravatar.com/avatar', '2.gravatar.com/avatar'], 'cdn.v2ex.com/gravatar', $avatar);
	}, 10, 3 );
endif;

//去除wordpress前台顶部工具条
if( dahuzi('no_admin_bar') ) :
	show_admin_bar(false);
endif;

//移除顶部多余信息
if( dahuzi('xintheme_wp_head') ) :
	remove_action( 'wp_head', 'feed_links', 2 ); //移除feed
	remove_action( 'wp_head', 'feed_links_extra', 3 ); //移除feed
	remove_action( 'wp_head', 'rsd_link' ); //移除离线编辑器开放接口
	remove_action( 'wp_head', 'wlwmanifest_link' );  //移除离线编辑器开放接口
	remove_action( 'wp_head', 'index_rel_link' );//去除本页唯一链接信息
	remove_action('wp_head', 'parent_post_rel_link', 10, 0 );//清除前后文信息
	remove_action('wp_head', 'start_post_rel_link', 10, 0 );//清除前后文信息
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
	remove_action( 'wp_head', 'locale_stylesheet' );
	remove_action('publish_future_post','check_and_publish_future_post',10, 1 );
	remove_action( 'wp_head', 'noindex', 1 );
	remove_action( 'wp_head', 'wp_generator' ); //移除WordPress版本
	remove_action( 'wp_head', 'rel_canonical' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
	remove_action( 'template_redirect', 'wp_shortlink_header', 11, 0 );
endif;

//禁止FEED
if( dahuzi('xintheme_feed') ) :
	function digwp_disable_feed() {
	wp_die(__('<h1>Feed已经关闭, 请访问网站<a href="'.get_bloginfo('url').'">首页</a>!</h1>'));
	}
	add_action('do_feed', 'digwp_disable_feed', 1);
	add_action('do_feed_rdf', 'digwp_disable_feed', 1);
	add_action('do_feed_rss', 'digwp_disable_feed', 1);
	add_action('do_feed_rss2', 'digwp_disable_feed', 1);
	add_action('do_feed_atom', 'digwp_disable_feed', 1);
endif;

//禁止前台加载语言包
if( dahuzi('xintheme_language') ) :
	add_filter( 'locale', 'xintheme_language' );
	function xintheme_language($locale) {
		$locale = ( is_admin() ) ? $locale : 'en_US';
		return $locale;
	}
endif;

//修改搜索结果的链接
if( dahuzi('redirect_search') ) :
	function xintheme_redirect_search() {
		if (is_search() && !empty($_GET['s'])) {
			wp_redirect(home_url("/search/").urlencode(get_query_var('s')));
			exit();
		}
	}
	add_action('template_redirect', 'xintheme_redirect_search' );
endif;

//移除后台标题后缀 - WordPress
add_filter('admin_title', 'xintheme_custom_admin_title', 10, 2);
function xintheme_custom_admin_title($admin_title, $title){
	return $title.' &lsaquo; '.get_bloginfo('name');
}

if( !defined('WPJAM_BASIC_PLUGIN_FILE') ){
	//在后台文章列表增加一列数据
	add_filter( 'manage_posts_columns', 'xintheme_customer_posts_columns' );
	function xintheme_customer_posts_columns( $columns ) {
	$columns['views'] = '浏览次数';
	return $columns;
	}
	//输出浏览次数
	add_action('manage_posts_custom_column', 'xintheme_customer_columns_value', 10, 2);
	function xintheme_customer_columns_value($column, $post_id){
	if($column=='views'){
	$count = get_post_meta($post_id, 'views', true);
	if(!$count){
	$count = 0;
	}
	echo $count;
	}
	return;
	}
}

add_action( 'admin_footer', 'dahuzi_admin_footer' );
function dahuzi_admin_footer(){

	echo "<style>
	.manage-column.column-views,.views.column-views{width:7%}
	</style>";
	if( dahuzi('dahuzi_no_admin_comments') ){
		echo "<style>#menu-comments{display:none}</style>";
	}

}

//去掉后台Wordpress LOGO
function my_edit_toolbar($wp_toolbar) {
	$wp_toolbar->remove_node('wp-logo'); 
}
add_action('admin_bar_menu', 'my_edit_toolbar', 999);


//彻底关闭WordPress生成默认尺寸的缩略图
if( dahuzi('xintheme_option_thumbnail') ) :
	add_filter('pre_option_thumbnail_size_w',	'__return_zero');
	add_filter('pre_option_thumbnail_size_h',	'__return_zero');
	add_filter('pre_option_medium_size_w',		'__return_zero');
	add_filter('pre_option_medium_size_h',		'__return_zero');
	add_filter('pre_option_large_size_w',		'__return_zero');
	add_filter('pre_option_large_size_h',		'__return_zero');
endif;

//WordPress替换登陆后跳转的后台默认首页
if( dahuzi('xintheme_article') ) :
	function my_login_redirect($redirect_to, $request){
	if( empty( $redirect_to ) || $redirect_to == 'wp-admin/' || $redirect_to == admin_url() )
	return home_url("/wp-admin/edit.php");
	else
	return $redirect_to;
	}
	add_filter("login_redirect", "my_login_redirect", 10, 3);
endif;

//彻底删除后台隐私相关设置
if( dahuzi('xintheme_privacy') ) :
add_action('admin_menu', function (){

	global $menu, $submenu;

	unset($submenu['options-general.php'][45]);

	// Bookmark hooks.
	remove_action( 'admin_page_access_denied', 'wp_link_manager_disabled_message' );

	// Privacy tools
	remove_action( 'admin_menu', '_wp_privacy_hook_requests_page' );
	// Privacy hooks
	remove_filter( 'wp_privacy_personal_data_erasure_page', 'wp_privacy_process_personal_data_erasure_page', 10, 5 );
	remove_filter( 'wp_privacy_personal_data_export_page', 'wp_privacy_process_personal_data_export_page', 10, 7 );
	remove_filter( 'wp_privacy_personal_data_export_file', 'wp_privacy_generate_personal_data_export_file', 10 );
	remove_filter( 'wp_privacy_personal_data_erased', '_wp_privacy_send_erasure_fulfillment_notification', 10 );

	// Privacy policy text changes check.
	remove_action( 'admin_init', array( 'WP_Privacy_Policy_Content', 'text_change_check' ), 100 );

	// Show a "postbox" with the text suggestions for a privacy policy.
	remove_action( 'edit_form_after_title', array( 'WP_Privacy_Policy_Content', 'notice' ) );

	// Add the suggested policy text from WordPress.
	remove_action( 'admin_init', array( 'WP_Privacy_Policy_Content', 'add_suggested_content' ), 1 );

	// Update the cached policy info when the policy page is updated.
	remove_action( 'post_updated', array( 'WP_Privacy_Policy_Content', '_policy_page_updated' ) );
},9);
endif;

//删除文章时删除图片附件
if( dahuzi('xintheme_delete_post_attachments') ) :
function xintheme_delete_post_and_attachments($post_ID) {
    global $wpdb;
    //删除特色图片
    $thumbnails = $wpdb->get_results( "SELECT * FROM $wpdb->postmeta WHERE meta_key = '_thumbnail_id' AND post_id = $post_ID" );
    foreach ( $thumbnails as $thumbnail ) {
    wp_delete_attachment( $thumbnail->meta_value, true );
    }
    //删除图片附件
    $attachments = $wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE post_parent = $post_ID AND post_type = 'attachment'" );
    foreach ( $attachments as $attachment ) {
    wp_delete_attachment( $attachment->ID, true );
    }
    $wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key = '_thumbnail_id' AND post_id = $post_ID" );
}
add_action('before_delete_post', 'xintheme_delete_post_and_attachments');
endif;

//上传图片使用日期重命名
if( dahuzi('xintheme_upload_img_rename') ) :
	function uazoh_wp_upload_filter($file){  
	$time=date("YmdHis");  
	$file['name'] = $time."".mt_rand(1,100).".".pathinfo($file['name'] , PATHINFO_EXTENSION);  
	return $file;  
	}  
	add_filter('wp_handle_upload_prefilter', 'uazoh_wp_upload_filter'); 
endif;

//禁用古腾堡编辑器
if( dahuzi('xintheme_no_gutenberg') ) :
	add_filter('use_block_editor_for_post_type', '__return_false');
endif;

// 文章外链 自动添加nofollow标签
if( dahuzi('xintheme_post_nofollow') ) :
	add_filter( 'the_content', function ( $content ) {
	    //文章自动nofollow
	    $regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>";
	    if(preg_match_all("/$regexp/siU", $content, $matches, PREG_SET_ORDER)) {
	        if( !empty($matches) ) {
	   
	            $srcUrl = get_option('siteurl');
	            for ($i=0; $i < count($matches); $i++)
	            {
	                $tag = $matches[$i][0];
	                $tag2 = $matches[$i][0];
	                $url = $matches[$i][0];
	   
	                $noFollow = '';
	                $pattern = '/target\s*=\s*"\s*_blank\s*"/';
	                preg_match($pattern, $tag2, $match, PREG_OFFSET_CAPTURE);
	                if( count($match) < 1 )
	                    $noFollow .= ' target="_blank" ';
	   
	                $pattern = '/rel\s*=\s*"\s*[n|d]ofollow\s*"/';
	                preg_match($pattern, $tag2, $match, PREG_OFFSET_CAPTURE);
	                if( count($match) < 1 )
	                    $noFollow .= ' rel="nofollow" ';
	   
	                $pos = strpos($url,$srcUrl);
	                if ($pos === false) {
	                    $tag = rtrim ($tag,'>');
	                    $tag .= $noFollow.'>';
	                    $content = str_replace($tag2,$tag,$content);
	                }
	            }
	        }
	    }
	    $content = str_replace(']]>', ']]>', $content);
	    return $content;
	});
endif;

//修复WordPress定时发布失败
if( dahuzi('xintheme_pubMissedPosts') ) :
	function pubMissedPosts() {
		if (is_front_page() || is_single()) {
			global $wpdb;
			$now=gmdate('Y-m-d H:i:00');
		
	    	$args=array(
	        	'public'                => true,
		        'exclude_from_search'   => false,
	    	    '_builtin'              => false
		    ); 
	    	$post_types = get_post_types($args,'names','and');
			$str=implode ('\',\'',$post_types);

			if ($str) {
				$sql="Select ID from $wpdb->posts WHERE post_type in ('post','page','$str') AND post_status='future' AND post_date_gmt<'$now'";
			}
			else {$sql="Select ID from $wpdb->posts WHERE post_type in ('post','page') AND post_status='future' AND post_date_gmt<'$now'";}

			$resulto = $wpdb->get_results($sql);
	 		if($resulto) {
				foreach( $resulto as $thisarr ) {
					wp_publish_post($thisarr->ID);
				}
			}
		}
	}
	add_action('wp_head', 'pubMissedPosts');
endif;

if( dahuzi('dahuzi_instantpage') ) :
	// instantpage-5.1.0  即时预加载  https://instant.page/
	function dahuzi_instantpage() {
		echo '<script src="'.get_template_directory_uri().'/static/js/instantpage-5.1.0.js" type="module" defer></script>';
	}
	add_action('wp_footer', 'dahuzi_instantpage', 999);
	add_action('admin_footer', 'dahuzi_instantpage', 999);
endif;

//评论回复 邮件通知
add_action('comment_post',function ($comment_id) {
	$comment = get_comment($comment_id);
	$parent_id = $comment->comment_parent ? $comment->comment_parent : '';
	$spam_confirmed = $comment->comment_approved;
	if (($parent_id != '') && ($spam_confirmed != 'spam')) {
		$wp_email = 'no-reply@' . preg_replace('#^www.#', '', strtolower($_SERVER['SERVER_NAME'])); //e-mail 发出点, no-reply 可改为可用的 e-mail.
		$to = trim(get_comment($parent_id)->comment_author_email);
		$subject = '您在 [' . get_option("blogname") . '] 的留言有了回复';
		$message = '
<table cellpadding="0" cellspacing="0" class="email-container" align="center" width="550" style="font-size: 15px; font-weight: normal; line-height: 22px; text-align: left; border: 1px solid rgb(177, 213, 245); width: 550px;">
<tbody><tr>
<td>
<table cellpadding="0" cellspacing="0" class="padding" width="100%" style="padding-left: 40px; padding-right: 40px; padding-top: 30px; padding-bottom: 35px;">
<tbody>
<tr class="logo">
<td align="center">
<table class="logo" style="margin-bottom: 10px;">
<tbody>
<tr>
<td>
<span style="font-size: 22px;padding: 10px 20px;margin-bottom: 5%;color: #65c5ff;border: 1px solid;box-shadow: 0 5px 20px -10px;border-radius: 2px;display: inline-block;">' . get_option("blogname") . '</span>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr class="content">
<td>
<hr style="height: 1px;border: 0;width: 100%;background: #eee;margin: 15px 0;display: inline-block;">
<p>Hi ' . trim(get_comment($parent_id)->comment_author) . '!<br>您评论在 "' . get_the_title($comment->comment_post_ID) . '":</p>
<p style="background: #eee;padding: 1em;text-indent: 2em;line-height: 30px;">' . trim(get_comment($parent_id)->comment_content) . '</p>
<p>'. $comment->comment_author .' 给您的答复:</p>
<p style="background: #eee;padding: 1em;text-indent: 2em;line-height: 30px;">' . trim($comment->comment_content) . '</p>
</td>
</tr>
<tr>
<td align="center">
<table cellpadding="12" border="0" style="font-family: Lato, \'Lucida Sans\', \'Lucida Grande\', SegoeUI, \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; font-weight: bold; line-height: 25px; color: #444444; text-align: left;">
<tbody><tr>
<td style="text-align: center;">
<a target="_blank" style="color: #fff;background: #65c5ff;box-shadow: 0 5px 20px -10px #44b0f1;border: 1px solid #44b0f1;width: 200px;font-size: 14px;padding: 10px 0;border-radius: 2px;margin: 10% 0 5%;text-align:center;display: inline-block;text-decoration: none;" href="' . htmlspecialchars(get_comment_link($parent_id)) . '">查看详情</a>
</td>
</tr>
</tbody></table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>

<table border="0" cellpadding="0" cellspacing="0" align="center" class="footer" style="max-width: 550px; font-family: Lato, \'Lucida Sans\', \'Lucida Grande\', SegoeUI, \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 15px; line-height: 22px; color: #444444; text-align: left; padding: 20px 0; font-weight: normal;">
<tbody><tr>
<td align="center" style="text-align: center; font-size: 12px; line-height: 18px; color: rgb(163, 163, 163); padding: 5px 0px;">
</td>
</tr>
<tr>
<td style="text-align: center; font-weight: normal; font-size: 12px; line-height: 18px; color: rgb(163, 163, 163); padding: 5px 0px;">
<p>Please do not reply to this message , because it is automatically sent.</p>
<p>© '.date("Y").' <a name="footer_copyright" href="' . home_url() . '" style="color: rgb(43, 136, 217); text-decoration: underline;" target="_blank">' . get_option("blogname") . '</a></p>
</td>
</tr>
</tbody>
</table>';
		$from = "From: \"" . get_option('blogname') . "\" <$wp_email>";
		$headers = "$from\nContent-Type: text/html; charset=" . get_option('blog_charset') . "\n";
		wp_mail( $to, $subject, $message, $headers );
	}
});






// 后台 文章列表  ajax删除文章
if( dahuzi('xintheme_moveposttotrash') ) :

	add_action( 'admin_footer', 'dahuzi_custom_internal_javascript' );
	function dahuzi_custom_internal_javascript(){
	
		echo "<script>
			jQuery(function($){
				$('body.post-type-post .row-actions .trash a').click(function( event ){
			 
					event.preventDefault();
			 
					var url = new URL( $(this).attr('href') ),
					    nonce = url.searchParams.get('_wpnonce'), // MUST for security checks
					    row = $(this).closest('tr'),
					    postID = url.searchParams.get('post'),
					    postTitle = row.find('.row-title').text();
			 
			 
					row.css('background-color','#ffafaf').fadeOut(300, function(){
						row.removeAttr('style').html('<td colspan=\'5\' style=\'background:#fff;border-left:1px solid #FF5722;border-left-width:4px;color:#555\'><strong>' + postTitle + '</strong> 已被移动到回收站</td>').show();
					});
			 
					$.ajax({
						method:'POST',
						url: ajaxurl,
						data: {
							'action' : 'moveposttotrash',
							'post_id' : postID,
							'_wpnonce' : nonce
						}
					});
			 
				});
			});
		</script>";
	
	}
	
	add_action('wp_ajax_moveposttotrash', function(){
		check_ajax_referer( 'trash-post_' . $_POST['post_id'] );
		wp_trash_post( $_POST['post_id'] );
		die();
	});

endif;



// 链接增加 nofollow 选项
if( dahuzi('dahuzi_links_nofollow') ) :

add_action('admin_head','dahuzi_links_nofollow');
function dahuzi_links_nofollow() {?>
<script type="text/javascript">

addLoadEvent(addNofollowTag);
function addNofollowTag() {
  tables = document.getElementsByTagName('table');
  for(i=0;i<tables.length;i++) {
    if(tables[i].getAttribute("class") == "links-table") {
      tr = tables[i].insertRow(1);
      th = document.createElement('th');
      th.setAttribute('scope','row');
      th.appendChild(document.createTextNode('Follow'));
      td = document.createElement('td');
      tr.appendChild(th);
      label = document.createElement('label');
      input = document.createElement('input');
      input.setAttribute('type','checkbox');
      input.setAttribute('id','nofollow');
      input.setAttribute('value','nofollow');
      label.appendChild(input);
      label.appendChild(document.createTextNode(' nofollow'));
      td.appendChild(label);
      tr.appendChild(td);
      input.name = 'nofollow';
      input.className = 'valinp';
      if (document.getElementById('link_rel').value.indexOf('nofollow') != -1) {
        input.setAttribute('checked','checked');
      }
      return;
    }
  }
}

</script>
<?php
}

endif;