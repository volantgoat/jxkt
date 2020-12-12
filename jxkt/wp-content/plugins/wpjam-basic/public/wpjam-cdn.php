<?php
class WPJAM_CDN{
	use WPJAM_Setting_Trait;

	private static $cdns		= [];
	private static $cdn_name	= '';
	private static $cdn_host	= '';
	private static $local_host	= '';

	private function __construct(){
		$this->init('wpjam-cdn');
	}

	public static function register($name, $args=[]){
		self::$cdns[$name]	= $args;
	}

	public static function unregister($name){
		unset(self::$cdns[$name]);
	}

	public static function load(){
		$instance	= self::get_instance();
		$cdn_name	= $instance->get_setting('cdn_name'); 

		if(empty($cdn_name) || empty(self::$cdns[$cdn_name])){
			return false;
		}

		$local	= $instance->get_setting('local');

		self::$cdn_name		= $cdn_name;
		self::$cdn_host		= untrailingslashit($instance->get_setting('host') ?: site_url());
		self::$local_host	= untrailingslashit($local ? set_url_scheme($local): site_url());

		// 兼容代码，以后可以去掉
		define('CDN_NAME',		self::$cdn_name);
		define('CDN_HOST',		self::$cdn_host);
		define('LOCAL_HOST',	self::$local_host);

		$cdn_file	= self::$cdns[$cdn_name]['file'] ?? '';

		if($cdn_file && file_exists($cdn_file)){
			include($cdn_file);
		}

		return $cdn_name;
	}

	public static function host_replace($html, $to_cdn=true){
		$local_hosts	= self::get_instance()->get_setting('locals') ?: [];

		if($to_cdn){
			$local_hosts[]	= str_replace('https://', 'http://', self::$local_host);
			$local_hosts[]	= str_replace('http://', 'https://', self::$local_host);

			if(strpos(self::$cdn_host, 'http://') === 0){
				$local_hosts[]	= str_replace('http://', 'https://', self::$cdn_host);
			}
		}else{
			if(strpos(self::$local_host, 'https://') !== false){
				$local_hosts[]	= str_replace('https://', 'http://', self::$local_host);
			}else{
				$local_hosts[]	= str_replace('http://', 'https://', self::$local_host);
			}
		}

		$local_hosts	= apply_filters('wpjam_cdn_local_hosts', $local_hosts);
		$local_hosts	= array_map('untrailingslashit', array_unique($local_hosts));

		if($to_cdn){
			return str_replace($local_hosts, self::$cdn_host, $html);
		}else{
			return str_replace($local_hosts, self::$local_host, $html);
		}
	}

	public static function html_replace($html){
		$dirs	= self::get_instance()->get_setting('dirs') ?: [];
		$exts	= self::get_instance()->get_setting('exts') ?: [];

		if($exts){
			$html	= self::host_replace($html, false);

			if($dirs && !is_array($dirs)){
				$dirs	= explode('|', $dirs);
			}

			if(!is_array($exts)){
				$exts	= explode('|', $exts);
			}

			$dirs	= array_unique(array_filter(array_map('trim', $dirs)));
			$exts	= array_unique(array_filter(array_map('trim', $exts)));

			if(is_login()){
				$exts	= array_diff($exts, ['js','css']);
			}

			$exts	= implode('|', $exts);
			$dirs	= implode('|', $dirs);

			if($dirs){
				$dirs	= str_replace(['-','/'],['\-','\/'], $dirs);
				$regex	= '/'.str_replace('/','\/',self::$local_host).'\/(('.$dirs.')\/[^\s\?\\\'\"\;\>\<]{1,}.('.$exts.'))([\"\\\'\)\s\]\?]{1})/';
				$html	= preg_replace($regex, self::$cdn_host.'/$1$4', $html);
			}else{
				$regex	= '/'.str_replace('/','\/',self::$local_host).'\/([^\s\?\\\'\"\;\>\<]{1,}.('.$exts.'))([\"\\\'\)\s\]\?]{1})/';
				$html	= preg_replace($regex, self::$cdn_host.'/$1$3', $html);
			}
		}

		return $html;
	}

	public static function content_images($content, $max_width=null){
		if(doing_filter('get_the_excerpt') || false === strpos($content, '<img')){
			return $content;
		}

		if(is_null($max_width)){
			$max_width	= $GLOBALS['content_width'] ?? 0;
			$max_width	= apply_filters('wpjam_content_image_width', $max_width);
			$max_width	= intval($max_width); 
		}

		if($max_width){
			if(has_filter('the_content', 'wp_filter_content_tags')){
				add_filter('wp_img_tag_add_srcset_and_sizes_attr', '__return_false');
				remove_filter('the_content', 'wp_filter_content_tags');
			}else{
				remove_filter('the_content', 'wp_make_content_images_responsive');
			}
		}

		$content	= self::host_replace($content, false);

		if(!preg_match_all('/<img.*?src=[\'"](.*?)[\'"].*?>/i', $content, $matches)){
			return $content;
		}

		$ratio	= 2;
		$search	= $replace = [];

		foreach ($matches[0] as $i => $img_tag){
		 	$img_url	= $matches[1][$i];

		 	if(empty($img_url)){
		 		continue;
		 	}
		 
		 	if(self::is_remote_image($img_url)){
		 		$new_img_url	= apply_filters('wpjam_content_remote_image', $img_url);

		 		if($img_url == $new_img_url){
		 			continue;
		 		}else{
		 			$img_url = $new_img_url;
		 		}
			}

			$size	= ['width'=>0,	'height'=>0,	'content'=>true];

			if(preg_match_all('/(width|height)=[\'"]([0-9]+)[\'"]/i', $img_tag, $hw_matches)){
				$hw_arr	= array_flip($hw_matches[1]);
				$size	= array_merge($size, array_combine($hw_matches[1], $hw_matches[2]));
			}

			$width		= $size['width'];

			$img_serach	= $img_replace	= [];

			if($max_width){
				if($size['width'] >= $max_width){
					if($size['height']){
						$size['height']	= intval(($max_width / $size['width']) * $size['height']);

						$img_serach[]	= $hw_matches[0][$hw_arr['height']];
						$img_replace[]	= 'height="'.$size['height'].'"';
					}

					$size['width']	= $max_width;

					$img_serach[]	= $hw_matches[0][$hw_arr['width']];
					$img_replace[]	= 'width="'.$size['width'].'"';
				}elseif($size['width'] == 0){
					if($size['height'] == 0){
						$size['width']	= $max_width;
					}
				}
			}

			$img_serach[]	= $img_url;

			if(strpos($img_tag, 'size-full ') && (empty($max_width) || $max_width*$ratio >= $width)){
				$img_replace[]	= wpjam_get_thumbnail($img_url, ['content'=>true]);
			}else{
				$size			= wpjam_parse_size($size, $ratio);
				$img_replace[]	= wpjam_get_thumbnail($img_url, $size);
			}

			if(function_exists('wp_lazy_loading_enabled')){
				$add_loading_attr	= wp_lazy_loading_enabled('img', current_filter());

				if($add_loading_attr && false === strpos($img_tag, ' loading=')) {
					$img_serach[]	= '<img';
					$img_replace[]	= '<img loading="lazy"';
				}
			}

			$search[]	= $img_tag;
			$replace[]	= str_replace($img_serach, $img_replace, $img_tag);
		}

		if(!$search){
			return $content;
		}

		return str_replace($search, $replace, $content);
	}

	public static function is_remote_image($img_url){
		$status	= strpos(self::host_replace($img_url), self::$cdn_host) === false;

		return apply_filters('wpjam_is_remote_image', $status, $img_url);
	}

	public static function fetch_remote_images($content){
		if(get_current_screen()->base != 'post'){
			return $content;
		}

		global $post;

		if(use_block_editor_for_post_type($post)){
			return $content;
		}

		if(!preg_match_all('/<img.*?src=\\\\[\'"](.*?)\\\\[\'"].*?>/i', $content, $matches)){
			return $content;
		}

		$update		= false;
		$search		= $replace	= [];
		$img_urls	= array_unique($matches[1]);
		$img_tags	= $matches[0];
		$exceptions	= self::get_instance()->get_setting('exceptions');

		foreach($img_urls as $i => $img_url){
			if(empty($img_url)){
				continue;
			}

			if(!self::is_remote_image($img_url)){
				continue;
			}

			if($exceptions){
				$exceptions	= explode("\n", $exceptions);
				foreach ($exceptions as $exception) {
					if(trim($exception) && strpos($img_url, trim($exception)) !== false ){
						continue;
					}
				}
			}

			if(preg_match('/[^\/?]+\.(jpe?g|jpe|gif|png)\b/i', $img_url, $img_match)){
				$file_name	= md5($img_url).'.'.$img_match[1];
			}elseif(preg_match('/data-type=\\\\[\'"](jpe?g|jpe|gif|png)\\\\[\'"]/i', $img_tags[$i], $type_match)){
				$file_name	= md5($img_url).'.'.$type_match[1];
			}else{
				continue;
			}

			$file	= wpjam_download_image($img_url, $file_name);

			if(!is_wp_error($file)){
				$search[]	= $img_url;
				$replace[]	= $file['url'];
				$update		= true;
			}
		}

		if($update){
			if(is_multisite()){
				setcookie('wp-saving-post', $_POST['post_ID'].'-saved', time()+DAY_IN_SECONDS, ADMIN_COOKIE_PATH, false, is_ssl());
			}

			$content	= str_replace($search, $replace, $content);
		}

		return $content;
	}

	public static function filter_intermediate_image_sizes_advanced($sizes){
		if(isset($sizes['full'])){
			return ['full'=>$sizes['full']];
		}else{
			return [];
		}
	}

	public static function filter_image_size_names_choose($sizes){
		$new_sizes	= ['full'=>$sizes['full']];

		unset($sizes['full']);

		foreach(['large', 'medium', 'thumbnail'] as $key){
			if(get_option($key.'_size_w') || get_option($key.'_size_h')){
				$new_sizes[$key]	= $sizes[$key];
			}

			unset($sizes[$key]);
		}

		return $sizes ? array_merge($sizes, $new_sizes) : $new_sizes;
	}

	public static function filter_upload_dir($uploads){
		$uploads['url']		= self::host_replace($uploads['url']);
		$uploads['baseurl']	= self::host_replace($uploads['baseurl']);
		return $uploads;
	}

	public static function filter_image_downsize($out, $id, $size){
		if(!wp_attachment_is_image($id)){
			return $out;
		}

		$ratio		= 2;
		$meta		= wp_get_attachment_metadata($id);
		$img_url	= wp_get_attachment_url($id);
		$size		= wpjam_parse_size($size, $ratio);

		if($size['crop']){
			$width	= min($size['width'],  $meta['width']);
			$height	= min($size['height'],  $meta['height']);
		}else{
			list($width, $height)	= wp_constrain_dimensions($meta['width'], $meta['height'], $size['width'], $size['height']);
		}

		if($width < $meta['width'] || $height <  $meta['height']){
			$img_url	= wpjam_get_thumbnail($img_url, compact('width', 'height'));
			return [$img_url, intval($width/$ratio), intval($height/$ratio), true];
		}else{
			$img_url	= wpjam_get_thumbnail($img_url);
			return [$img_url, $width, $height, false];
		}
	}

	public static function filter_wp_resource_hints($urls, $relation_type){
		return $relation_type == 'dns-prefetch' ? $urls+[CDN_HOST] : $urls;
	}

	public static function filter_option_value($value){
		foreach (['exts', 'dirs'] as $k) {
			$v	= $value[$k] ?? [];

			if($v){
				if(!is_array($v)){
					$v	= explode('|', $v);
				}

				$v = array_unique(array_filter(array_map('trim', $v)));
			}

			$value[$k]	= $v;
		};

		return $value;
	}

	public static function get_option_setting(){
		$detail = '
		<p>阿里云 OSS 用户：请点击这里注册和申请<a href="http://wpjam.com/go/aliyun/" target="_blank">阿里云</a>可获得代金券，阿里云OSS<strong><a href="https://blog.wpjam.com/m/aliyun-oss-cdn/" target="_blank">详细使用指南</a></strong>。</p>
		<p>腾讯云 COS 用户：请点击这里注册和申请<a href="http://wpjam.com/go/qcloud/" target="_blank">腾讯云</a>可获得优惠券，腾讯云COS<strong><a href="https://blog.wpjam.com/m/qcloud-cos-cdn/" target="_blank">详细使用指南</a></strong>。</p>';

		$cdn_options	= array_merge([''=>' '], wp_list_pluck(self::$cdns, 'title'));

		$cdn_fields		= [
			'cdn_name'	=> ['title'=>'云存储',	'type'=>'select',	'options'=>$cdn_options,	'class'=>'show-if-key'],
			'host'		=> ['title'=>'CDN域名',	'type'=>'url',		'description'=>'设置为在CDN云存储绑定的域名。'],
			'guide'		=> ['title'=>'使用说明',	'type'=>'view',		'value'=>$detail],
		];

		$local_fields	= [
			'exts'		=> ['title'=>'扩展名',	'type'=>'mu-text',	'value'=>['png','jpg','gif','ico'],		'class'=>'',	'description'=>'设置要缓存静态文件的扩展名。'],
			'dirs'		=> ['title'=>'目录',		'type'=>'mu-text',	'value'=>['wp-content','wp-includes'],	'class'=>'',	'description'=>'设置要缓存静态文件所在的目录。'],
			'local'		=> ['title'=>'本地域名',	'type'=>'url',		'value'=>home_url(),	'description'=>'将该域名填入<strong>云存储的镜像源</strong>。'],
			'locals'	=> ['title'=>'额外域名',	'type'=>'mu-text',	'item_type'=>'url'],
		];

		$remote_options	= [
			0			=>'关闭远程图片镜像到云存储。',
			1			=>'自动将远程图片镜像到云存储。',
			'download'	=>'将远程图片下载服务器再镜像到云存储。'
		];

		if(is_multisite() || !$GLOBALS['wp_rewrite']->using_mod_rewrite_permalinks() || !extension_loaded('gd')){
			unset($remote_options[1]);
		}

		$remote_fields	= [
			'remote'		=> ['title'=>'远程图片',	'type'=>'select',	'options'=>$remote_options],
			'exceptions'	=> ['title'=>'例外',		'type'=>'textarea',	'class'=>'regular-text','description'=>'如果远程图片的链接中包含以上字符串或域名，就不会被保存并镜像到云存储。']
		];

		$image_fields	= [
			'webp'		=> ['title'=>'WebP格式',	'type'=>'checkbox',	'description'=>'将图片转换成WebP格式，仅支持阿里云OSS和腾讯云COS。'],
			'interlace'	=> ['title'=>'渐进显示',	'type'=>'checkbox',	'description'=>'是否JPEG格式图片渐进显示。'],
			'quality'	=> ['title'=>'图片质量',	'type'=>'number',	'class'=>'all-options',	'description'=>'<br />1-100之间图片质量。','mim'=>0,'max'=>100]
		];

		$watermark_options = [
			'SouthEast'	=> '右下角',
			'SouthWest'	=> '左下角',
			'NorthEast'	=> '右上角',
			'NorthWest'	=> '左上角',
			'Center'	=> '正中间',
			'West'		=> '左中间',
			'East'		=> '右中间',
			'North'		=> '上中间',
			'South'		=> '下中间',
		];

		$watermark_fields = [
			'watermark'	=> ['title'=>'水印图片',	'type'=>'image',	'description'=>'请使用 CDN 域名下的图片'],
			'disslove'	=> ['title'=>'透明度',	'type'=>'number',	'class'=>'all-options',	'description'=>'<br />取值范围1-100，缺省值为100（不透明）','min'=>0,'max'=>100],
			'gravity'	=> ['title'=>'水印位置',	'type'=>'select',	'options'=>$watermark_options],
			'dx'		=> ['title'=>'横轴边距',	'type'=>'number',	'class'=>'all-options',	'description'=>'<br />单位:像素(px)，缺省值为10'],
			'dy'		=> ['title'=>'纵轴边距',	'type'=>'number',	'class'=>'all-options',	'description'=>'<br />单位:像素(px)，缺省值为10'],
		];

		if(is_network_admin()){
			unset($local_fields['local']);
			unset($watermark_fields['watermark']);
		}

		$remote_summary	= '
		*自动将远程图片镜像到云存储需要你的博客支持固定链接和服务器支持GD库（不支持gif图片）。
		*将远程图片下载服务器再镜像到云存储，会在你保存文章的时候自动执行。';

		return [
			'cdn'		=> ['title'=>'CDN设置',	'fields'=>$cdn_fields],
			'local'		=> ['title'=>'本地设置',	'fields'=>$local_fields],
			'image'		=> ['title'=>'图片设置',	'fields'=>$image_fields,	'show_if'=>['key'=>'cdn_name', 'compare'=>'IN', 'value'=>['aliyun_oss',	'qcloud_cos', 'qiniu']]],
			'watermark'	=> ['title'=>'水印设置',	'fields'=>$watermark_fields,'show_if'=>['key'=>'cdn_name', 'compare'=>'IN', 'value'=>['aliyun_oss',	'qcloud_cos', 'qiniu']]],
			'remote'	=> ['title'=>'远程图片',	'fields'=>$remote_fields,	'show_if'=>['key'=>'cdn_name', 'compare'=>'!=', 'value'=>''],	'summary'=>$remote_summary],
		];
	}
}

// 获取 CDN 设置
function wpjam_cdn_get_setting($name){
	return WPJAM_CDN::get_instance()->get_setting($name);
}

//注册 CDN 服务
function wpjam_register_cdn($name, $args){
	WPJAM_CDN::register($name, $args);
}

function wpjam_unregister_cdn($name){
	WPJAM_CDN::unregister($name);
}

function wpjam_is_image($image_url){
	$ext_types	= wp_get_ext_types();
	$img_exts	= $ext_types['image'];

	$image_parts	= explode('?', $image_url);

	return preg_match('/\.('.implode('|', $img_exts).')$/i', $image_parts[0]);
}

function wpjam_is_remote_image($img_url){
	return WPJAM_CDN::is_remote_image($img_url);
}

wpjam_register_cdn('aliyun_oss',	['title'=>'阿里云OSS',	'file'=>WPJAM_BASIC_PLUGIN_DIR.'cdn/aliyun_oss.php']);
wpjam_register_cdn('qcloud_cos',	['title'=>'腾讯云COS',	'file'=>WPJAM_BASIC_PLUGIN_DIR.'cdn/qcloud_cos.php']);
wpjam_register_cdn('ucloud',		['title'=>'UCloud', 	'file'=>WPJAM_BASIC_PLUGIN_DIR.'cdn/ucloud.php']);
wpjam_register_cdn('qiniu',			['title'=>'七牛云存储',	'file'=>WPJAM_BASIC_PLUGIN_DIR.'cdn/qiniu.php']);

add_action('plugins_loaded', function(){
	$result	= WPJAM_CDN::load();

	if(!$result){
		return;
	}

	// 不用生成 -150x150.png 这类的图片
	add_filter('intermediate_image_sizes_advanced',	['WPJAM_CDN', 'filter_intermediate_image_sizes_advanced']);
	add_filter('image_size_names_choose',			['WPJAM_CDN', 'filter_image_size_names_choose']);

	add_filter('wpjam_thumbnail',	['WPJAM_CDN', 'host_replace'], 1);
	add_filter('upload_dir',		['WPJAM_CDN', 'filter_upload_dir']);
	add_filter('image_downsize',	['WPJAM_CDN', 'filter_image_downsize'], 10 ,3);
	add_filter('wp_resource_hints',	['WPJAM_CDN', 'filter_wp_resource_hints'], 10, 2);
	add_filter('the_content',		['WPJAM_CDN', 'content_images'], 5);

	if(!is_admin()){
		add_filter('wpjam_html',	['WPJAM_CDN', 'html_replace'], 9);
	}

	if(wpjam_cdn_get_setting('remote') == 'download'){
		if(is_admin()){
			add_filter('content_save_pre', ['WPJAM_CDN', 'fetch_remote_images']);
		}
	}elseif(wpjam_cdn_get_setting('remote') == 'rewrite'){
		if(!is_multisite()){
			include WPJAM_BASIC_PLUGIN_DIR.'cdn/remote.php';
		}
	}
}, 99);

if(is_admin()){
	wpjam_add_basic_sub_page('wpjam-cdn', [
		'menu_title'	=> 'CDN加速',
		'function'		=> 'option',
		'order'			=> 20,
		'summary'		=> 'CDN 加速让你使用云存储对博客的静态资源进行 CDN 加速，详细介绍请点击：<a href="https://blog.wpjam.com/m/wpjam-basic-cdn/" target="_blank">CDN 加速</a>。',
	]);

	add_action('wpjam_plugin_page_load', function($plugin_page){
		if($plugin_page == 'wpjam-cdn'){
			if(isset($_GET['reset'])){
				delete_option('wpjam-cdn');
			}

			wpjam_register_option('wpjam-cdn', ['WPJAM_CDN','get_option_setting']);

			add_filter('option_wpjam-cdn', ['WPJAM_CDN', 'filter_option_value']);
		}
	});
}