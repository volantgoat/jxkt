<?php
class WPJAM_Util{
	public static function parse_shortcode_attr($str,  $tagnames=null){
		$pattern = get_shortcode_regex([$tagnames]);

		if(preg_match("/$pattern/", $str, $m)){
			return shortcode_parse_atts( $m[3] );
		}else{
			return [];
		}
	}

	public static function human_time_diff($from,  $to=0) {
		$to		= ($to)?:time();
		$day	= date('Y-m-d',$from);
		$today	= date('Y-m-d');

		$secs	= $to - $from;	//距离的秒数
		$days	= $secs / DAY_IN_SECONDS;

		$from += get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ;

		if($secs > 0){
			if((date('Y')-date('Y',$from))>0 && $days>3){//跨年且超过3天
				return date('Y-m-d',$from);
			}else{

				if($days<1){//今天
					if($secs<60){
						return $secs.'秒前';
					}elseif($secs<3600){
						return floor($secs/60)."分钟前";
					}else {
						return floor($secs/3600)."小时前";
					}
				}else if($days<2){	//昨天
					$hour=date('g',$from);
					return "昨天".$hour.'点';
				}elseif($days<3){	//前天
					$hour=date('g',$from);
					return "前天".$hour.'点';
				}else{	//三天前
					return date('n月j号',$from);
				}
			}
		}else{
			if((date('Y')-date('Y',$from))<0 && $days<-3){//跨年且超过3天
				return date('Y-m-d',$from);
			}else{

				if($days>-1){//今天
					if($secs>-60){
						return absint($secs).'秒后';
					}elseif($secs>-3600){
						return floor(absint($secs)/60)."分钟前";
					}else {
						return floor(absint($secs)/3600)."小时前";
					}
				}else if($days>-2){	//昨天
					$hour=date('g',$from);
					return "明天".$hour.'点';
				}elseif($days>-3){	//前天
					$hour=date('g',$from);
					return "后天".$hour.'点';
				}else{	//三天前
					return date('n月j号',$from);
				}
			}
		}
	}

	public static function get_current_page_url(){
		return set_url_scheme('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
	}

	public static function unicode_decode($str){
		return preg_replace_callback('/\\\\u([0-9a-f]{4})/i', function($matches){
			return mb_convert_encoding(pack("H*", $matches[1]), 'UTF-8', 'UCS-2BE');
		}, $str);
	}

	public static function get_video_mp4($id_or_url){
		if(filter_var($id_or_url, FILTER_VALIDATE_URL)){ 
			if(preg_match('#http://www.miaopai.com/show/(.*?).htm#i',$id_or_url, $matches)){
				return 'http://gslb.miaopai.com/stream/'.esc_attr($matches[1]).'.mp4';
			}elseif(preg_match('#https://v.qq.com/x/page/(.*?).html#i',$id_or_url, $matches)){
				return self::get_qqv_mp4($matches[1]);
			}elseif(preg_match('#https://v.qq.com/x/cover/.*/(.*?).html#i',$id_or_url, $matches)){
				return self::get_qqv_mp4($matches[1]);
			}else{
				return str_replace(['%3A','%2F'], [':','/'], urlencode($id_or_url));
			}
		}else{
			return self::get_qqv_mp4($id_or_url);
		}
	}

	public static function get_qqv_mp4($vid){
		if(strlen($vid) > 20){
			return new WP_Error('invalid_qqv_vid', '非法的腾讯视频 ID');
		}

		$mp4 = wp_cache_get($vid, 'qqv_mp4');
		if($mp4 === false){
			$response	= wpjam_remote_request('http://vv.video.qq.com/getinfo?otype=json&platform=11001&vid='.$vid, ['timeout'=>4,	'need_json_decode'	=>false]);

			if(is_wp_error($response)){
				return $response;
			}

			$response	= trim(substr($response, strpos($response, '{')),';');
			$response	= wpjam_json_decode($response);

			if(is_wp_error($response)){
				return $response;
			}

			if(empty($response['vl'])){
				return new WP_Error('illegal_qqv', '该腾讯视频不存在或者为收费视频！');
			}

			$u		= $response['vl']['vi'][0];
			$p0		= $u['ul']['ui'][0]['url'];
			$p1		= $u['fn'];
			$p2		= $u['fvkey'];

			$mp4	= $p0.$p1.'?vkey='.$p2;

			wp_cache_set($vid, $mp4, 'qqv_mp4', HOUR_IN_SECONDS*6);
		}

		return $mp4;
	}

	public static function get_qqv_id($id_or_url){
		if(filter_var($id_or_url, FILTER_VALIDATE_URL)){ 
			if(preg_match('#https://v.qq.com/x/page/(.*?).html#i',$id_or_url, $matches)){
				return $matches[1];
			}elseif(preg_match('#https://v.qq.com/x/cover/.*/(.*?).html#i',$id_or_url, $matches)){
				return $matches[1];
			}else{
				return '';
			}
		}else{
			return $id_or_url;
		}
	}

	// 移除除了 line feeds 和 carriage returns 所有控制字符
	public static function strip_control_characters($text){
		return preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F]/u', '', $text);
		// return preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x80-\x9F]/u', '', $str);
	}

	// 去掉非 utf8mb4 字符
	public static function strip_invalid_text($str, $charset='utf8mb4'){
		$regex	= '/
			(
				(?: [\x00-\x7F]                  # single-byte sequences   0xxxxxxx
				|   [\xC2-\xDF][\x80-\xBF]       # double-byte sequences   110xxxxx 10xxxxxx';

		if($charset === 'utf8mb3' || $charset === 'utf8mb4'){
			$regex	.= '
			|   \xE0[\xA0-\xBF][\x80-\xBF]   # triple-byte sequences   1110xxxx 10xxxxxx * 2
				|   [\xE1-\xEC][\x80-\xBF]{2}
				|   \xED[\x80-\x9F][\x80-\xBF]
				|   [\xEE-\xEF][\x80-\xBF]{2}';
		}

		if($charset === 'utf8mb4'){
			$regex	.= '
				|    \xF0[\x90-\xBF][\x80-\xBF]{2} # four-byte sequences   11110xxx 10xxxxxx * 3
				|    [\xF1-\xF3][\x80-\xBF]{3}
				|    \xF4[\x80-\x8F][\x80-\xBF]{2}';
		}

		$regex		.= '
			){1,40}                  # ...one or more times
			)
			| .                      # anything else
			/x';

		return preg_replace($regex, '$1', $str);
	}

	public static function strip_4_byte_chars($chars){
		return preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $chars);
		// return preg_replace('/[\x{10000}-\x{10FFFF}]/u', "\xEF\xBF\xBD", $chars);
	}

	//获取纯文本
	public static function get_plain_text($text){

		$text = wp_strip_all_tags($text);

		$text = str_replace('"', '', $text); 
		$text = str_replace('\'', '', $text);
		// replace newlines on mac / windows?
		$text = str_replace("\r\n", ' ', $text);
		// maybe linux uses this alone
		$text = str_replace("\n", ' ', $text);
		$text = str_replace("  ", ' ', $text);

		return trim($text);
	}

	// 获取第一段
	public static function get_first_p($text){
		if($text){
			$text = explode("\n", trim(strip_tags($text))); 
			$text = trim($text['0']); 
		}
		return $text;
	}

	public static function mb_strimwidth($text, $start=0, $length=40, $trimmarker='...', $encoding='utf-8'){
		return mb_strimwidth(self::get_plain_text($text), $start, $length, $trimmarker, $encoding);
	}

	public static function blacklist_check($text, $name='内容'){
		if(empty($text)){
			return false;
		}

		$pre	= apply_filters('wpjam_pre_blacklist_check', null, $text, $name);

		if(!is_null($pre)){
			return $pre;
		}

		$moderation_keys	= trim(get_option('moderation_keys'));
		$disallowed_keys	= trim(get_option('disallowed_keys'));

		$words = explode("\n", $moderation_keys ."\n".$disallowed_keys);

		foreach ((array)$words as $word){
			$word = trim($word);

			// Skip empty lines
			if ( empty($word) ) {
				continue;
			}

			// Do some escaping magic so that '#' chars in the
			// spam words don't break things:
			$word	= preg_quote($word, '#');
			if ( preg_match("#$word#i", $text) ) {
				return true;
			}
		}

		return false;
	}

	public static function download_image($image_url, $name=''){
		if(empty($name)){
			preg_match('/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $image_url, $matches);

			if(!$matches){
				return new WP_Error('image_sideload_failed', '无效的图片链接');
			}

			$name	= md5($image_url).'.'.$matches[1];
		}

		$file_array	= [
			'name'		=> $name,
			'tmp_name'	=> download_url($image_url)
		];

		if(is_wp_error($file_array['tmp_name'])){
			return	$file_array['tmp_name'];
		}

		$file	= wp_handle_sideload($file_array, ['test_form' => false]);

		if(isset($file['error'])){
			return new WP_Error('upload_error', $file['error']);
		}

		return $file;
	}

	public static function array_push(&$array, $data=null, $key=false){
		$data	= (array)$data;

		$offset	= $key === false ? false : array_search($key, array_keys($array));
		$offset	= $offset ? $offset : false;

		if($offset){
			$array = array_merge(
				array_slice($array, 0, $offset), 
				$data, 
				array_slice($array, $offset)
			);
		}else{	// 没指定 $key 或者找不到，就直接加到末尾
			$array = array_merge($array, $data);
		}
	}

	public static function items_sort($items, $order_key='order'){
		array_walk($items, function(&$item)use($order_key){
			static $index; 

			$index	= empty($index) ? 1 : ($index+1);
			$item	= wp_parse_args($item, [$order_key=>10, 'index'=>$index]);
		});

		return wp_list_sort($items, [$order_key=>'DESC', 'index'=>'ASC'], '', true);
	}

	public static function array_merge($arr1, $arr2){
		foreach($arr2 as $key => &$value){
			if(is_array($value) && isset($arr1[$key]) && is_array($arr1[$key])){
				$arr1[$key]	= self::array_merge($arr1[$key], $value);
			}else{
				$arr1[$key]	= $value;
			}
		}

		return $arr1;
	}

	public static function get_ip(){
		return $_SERVER['REMOTE_ADDR'] ??'';
	}

	public static function parse_ip($ip=''){
		$ip	= $ip ?: self::get_ip();

		if($ip == 'unknown'){
			return false;
		}

		$ipdata	= IP::find($ip);

		return [
			'ip'		=> $ip,
			'country'	=> $ipdata['0'] ?? '',
			'region'	=> $ipdata['1'] ?? '',
			'city'		=> $ipdata['2'] ?? '',
			'isp'		=> '',
		];
	}

	public static function parse_user_agent($user_agent='', $referer=''){
		$user_agent	= $user_agent ?: ($_SERVER['HTTP_USER_AGENT'] ?? '');
		$user_agent	= $user_agent.' ';	// 为了特殊情况好匹配
		$referer	= $referer ?: $_SERVER['HTTP_REFERER'] ?? '';

		$os = $device =  $app = $browser = '';
		$os_version = $browser_version = $app_version = 0;

		if(strpos($user_agent, 'iPhone') !== false){
			$device	= 'iPhone';
			$os 	= 'iOS';
		}elseif(strpos($user_agent, 'iPad') !== false){
			$device	= 'iPad';
			$os 	= 'iOS';
		}elseif(strpos($user_agent, 'iPod') !== false){
			$device	= 'iPod';
			$os 	= 'iOS';
		}elseif(strpos($user_agent, 'Android') !== false){
			$os		= 'Android';

			if(preg_match('/Android ([0-9\.]{1,}?); (.*?) Build\/(.*?)[\)\s;]{1}/i', $user_agent, $matches)){
				if(!empty($matches[1]) && !empty($matches[2])){
					$os_version	= trim($matches[1]);

					$device		= $matches[2];

					if(strpos($device,';')!==false){
						$device	= substr($device, strpos($device,';')+1, strlen($device)-strpos($device,';'));
					}

					$device		= trim($device);
					// $build	= trim($matches[3]);
				}
			}
		}elseif(stripos($user_agent, 'Windows NT')){
			$os		= 'Windows';
		}elseif(stripos($user_agent, 'Macintosh')){
			$os		= 'Macintosh';
		}elseif(stripos($user_agent, 'Windows Phone')){
			$os		= 'Windows Phone';
		}elseif(stripos($user_agent, 'BlackBerry') || stripos($user_agent, 'BB10')){
			$os		= 'BlackBerry';
		}elseif(stripos($user_agent, 'Symbian')){
			$os		= 'Symbian';
		}else{
			$os		= 'unknown';
		}

		if($os == 'iOS'){
			if(preg_match('/OS (.*?) like Mac OS X[\)]{1}/i', $user_agent, $matches)){
				$os_version	= floatval(trim(str_replace('_', '.', $matches[1])));
			}
		}

		if(strpos($user_agent, 'MicroMessenger') !== false){
			if(strpos($referer, 'https://servicewechat.com') !== false){
				$app	='weapp';
			}else{
				$app	= 'weixin';
			}

			if(preg_match('/MicroMessenger\/(.*?)\s/', $user_agent, $matches)){
				$app_version = $matches[1];
			}

			if(preg_match('/NetType\/(.*?)\s/', $user_agent, $matches)){
				$net_type = $matches[1];
			}
		}elseif(strpos($user_agent, 'ToutiaoMicroApp') !== false || strpos($referer, 'https://tmaservice.developer.toutiao.com') !== false){
			$app	= 'bytedance';
		}

		global $is_lynx, $is_gecko, $is_opera, $is_safari, $is_chrome, $is_IE, $is_edge;

		if($is_safari){
			$browser	= 'safrai';
			if(preg_match('/Version\/(.*?)\s/i', $user_agent, $matches)){
				$browser_version	= floatval(trim($matches[1]));
			}
		}elseif($is_chrome){
			$browser	= 'chrome';
			if(preg_match('/Chrome\/(.*?)\s/i', $user_agent, $matches)){
				$browser_version	= floatval(trim($matches[1]));
			}
		}elseif(stripos($user_agent, 'Firefox') !== false){
			$browser	= 'firefox';
			if(preg_match('/Firefox\/(.*?)\s/i', $user_agent, $matches)){
				$browser_version	= floatval(trim($matches[1]));
			}
		}elseif($is_edge){
			$browser	= 'edge';
			if(preg_match('/Edge\/(.*?)\s/i', $user_agent, $matches)){
				$browser_version	= floatval(trim($matches[1]));
			}
		}elseif($is_lynx){
			$browser	= 'lynx';
		}elseif($is_gecko){
			$browser	= 'gecko';
		}elseif($is_opera){
			$browser	= 'opera';
		}elseif($is_IE){
			$browser	= 'ie';
		}

		$data	= compact('os', 'device', 'app', 'browser', 'os_version', 'browser_version', 'app_version');

		return apply_filters('wpjam_determine_var', $data, $user_agent, $referer);
	}
}

class WPJAM_Var{
	public $data	= [];

	public static $instance	= null;

	private function __construct(){
		$this->data	= WPJAM_Util::parse_user_agent();
	}

	public static function get_instance(){
		if(is_null(self::$instance)){
			self::$instance	= new self();
		}

		return self::$instance;
	}

	public static function is_os($os){
		return self::get_os() == $os;
	}

	public static function is_app($app){
		return self::get_app() == $app;
	}

	public static function get_device(){
		return self::get_instance()->data['device'];
	}

	public static function get_os(){
		return self::get_instance()->data['os'];
	}

	public static function get_os_version(){
		return self::get_instance()->data['os_version'];
	}

	public static function get_browser(){
		return self::get_instance()->data['browser'];
	}

	public static function get_browser_version(){
		return self::get_instance()->data['browser_ver'];
	}

	public static function get_app(){
		return self::get_instance()->data['app'];
	}

	public static function get_app_version(){
		return self::get_instance()->data['app_version'];
	}
}

class WPJAM_Bit{
	protected $bit;

	public function __construct($bit){
		$this->set_bit($bit);
	}

	public function set_bit($bit){
		$this->bit	= $bit;
	}

	public function get_bit(){
		return $this->bit;
	}

	public function has($bit){
		return ($this->bit & $bit) == $bit;
	}

	public function add($bit){
		$this->bit = $this->bit | $bit;

		return $this->bit;
	}

	public function remove($bit){
		$this->bit = $this->bit & (~$bit);

		return $this->bit;
	}
}

class WPJAM_Platform{
	protected static $platforms	= [
		'weapp'		=> [
			'bit'		=> 1,
			'order'		=> 4,
			'title'		=> '小程序',
			'verify'	=> 'is_weapp'
		],
		'weixin'	=> [
			'bit'		=> 2,
			'order'		=> 4,
			'title'		=> '微信网页',
			'verify'	=> 'is_weixin'
		],
		'mobile'	=>[
			'bit'		=> 4,
			'order'		=> 8,
			'title'		=> '移动网页',
			'verify'	=> 'wp_is_mobile'
		],
		'web'			=>[
			'bit'		=> 8,
			'title'		=> '网页',
			'verify'	=> '__return_true'
		],
		'template'		=>[
			'bit'		=> 8,
			'title'		=> '网页',
			'verify'	=> '__return_true'
		]
	];

	public static function register($key, $args=[]){
		self::$platforms[$key]	= $args;
	}

	public static function unregister($key){
		unset(self::$platforms[$key]);
	}

	public static function get_all($sort=true){
		$platforms	= self::$platforms;

		if($sort){
			uasort($platforms, function ($p1, $p2){
				$order1	= $p1['order'] ?? 10;
				$order2	= $p2['order'] ?? 10;
				return $order1 <=> $order2;
			});
		}

		return $platforms;
	}

	public static function get_options($type='bit'){
		$platforms	= self::get_all();

		if($type == 'key'){
			return wp_list_pluck($platforms, 'title');
		}elseif($type == 'bit'){
			$platforms	= array_filter($platforms, function($platform){
				return !empty($platform['bit']);
			});

			return wp_list_pluck($platforms, 'title', 'bit');
		}else{
			return wp_list_pluck($platforms, 'bit');
		}
	}

	public static function is_platform($platform){
		$platforms	= self::get_all();

		if(is_numeric($platform)){
			$options	= array_flip(wp_list_pluck($platforms, 'bit'));

			if(isset($options[$platform])){
				$platform	= $options[$platform];
			}
		}

		$platform	= $platforms[$platform] ?? false;

		if($platform){
			return call_user_func($platform['verify']);
		}else{
			return false;
		}
	}

	public static function get_current_platform($platforms=[], $type='bit'){
		$options	= self::get_options($type);

		foreach($options as $platform=>$title){
			if($platforms){
				if(in_array($platform, $platforms) && self::is_platform($platform)){
					return $platform;
				}
			}else{
				if(self::is_platform($platform)){
					return $platform;
				}
			}
		}

		return '';
	}
}

class WPJAM_Path{
	private $page_key;
	private $page_type	= '';
	private $post_type	= '';
	private $taxonomy	= '';
	private $fields		= [];
	private $tabbars	= [];
	private $title		= '';
	private $paths		= [];
	private $pages		= [];
	private $callbacks	= [];
	private static $path_objs	= [];

	public function __construct($page_key, $args=[]){
		$this->page_key		= $page_key;
		$this->page_type	= $args['page_type'] ?? '';
		$this->title		= $args['title'] ?? '';

		if($this->page_type == 'post_type'){
			$this->post_type	= $args['post_type'] ?? $this->page_key;
		}elseif($this->page_type == 'taxonomy'){
			$this->taxonomy		= $args['taxonomy'] ?? $this->page_key;
		}
	}

	public function get_title(){
		return $this->title;
	}

	public function get_page_type(){
		return $this->page_type;
	}

	public function get_post_type(){
		return $this->post_type;
	}

	public function get_taxonomy(){
		return $this->taxonomy;
	}

	public function get_taxonomy_key(){
		if($this->taxonomy == 'category'){
			return 'cat';
		}elseif($this->taxonomy == 'post_tag'){
			return 'tag_id';
		}elseif($this->taxonomy){
			return $this->taxonomy.'_id';
		}else{
			return '';
		}
	}

	public function get_fields(){
		if($this->fields){
			$fields	= $this->fields ?? '';

			if($fields && is_callable($fields)){
				$fields	= call_user_func($fields, $this->page_key);
			}

			return $fields;
		}else{
			$fields	= [];

			if($this->page_type == 'post_type'){
				if($post_type_obj = get_post_type_object($this->post_type)){
					$fields[$this->post_type.'_id']	= ['title'=>'',	'type'=>'text',	'class'=>'all-options',	'data_type'=>'post_type',	'post_type'=>$this->post_type, 'placeholder'=>'请输入'.$post_type_obj->label.'ID或者输入关键字筛选',	'required'];
				}
			}elseif($this->page_type == 'taxonomy'){
				if($taxonomy_obj = get_taxonomy($this->taxonomy)){
					$taxonomy_key	= $this->get_taxonomy_key();

					if($taxonomy_obj->hierarchical){
						$levels		= $taxonomy_obj->levels ?? 0;
						$terms		= wpjam_get_terms(['taxonomy'=>$this->taxonomy,	'hide_empty'=>0], $levels);
						$terms		= wpjam_flatten_terms($terms);
						$options	= $terms ? wp_list_pluck($terms, 'name', 'id') : [];

						$fields[$taxonomy_key]	= ['title'=>'',	'type'=>'select',	'options'=>$options];
					}else{
						$fields[$taxonomy_key]	= ['title'=>'',	'type'=>'text',		'data_type'=>'taxonomy',	'taxonomy'=>$this->taxonomy];
					}
				}
			}elseif($this->page_type == 'author'){
				$fields['author']	= ['title'=>'',	'type'=>'select',	'options'=>wp_list_pluck(get_users(['who'=>'authors']), 'display_name', 'ID')];
			}

			return $fields;
		}
	}

	public function get_tabbar($type){
		return $this->tabbars[$type] ?? false;
	}

	public function set_title($title){
		$this->title	= $title;
	}

	public function set_path($type, $path=''){
		$this->paths[$type]	= $path;

		if($path){
			if(strrpos($path, '?')){
				$path_parts	= explode('?', $path);
				$this->pages[$type]	= $path_parts[0];
			}else{
				$this->pages[$type]	= $path;
			}
		}
	}

	public function remove_path($type){
		unset($this->paths[$type]);
	}

	public function set_callback($type, $callback=''){
		$this->callbacks[$type]	= $callback;
	}

	public function set_fields($type, $fields=[]){
		$this->fields	= array_merge($this->fields, $fields);
	}

	public function set_tabbar($type, $tabbar=false){
		$this->tabbars[$type]	= $tabbar;
	}

	public function get_page($type){
		return $this->pages[$type] ?? '';
	}

	private function get_post_path($args){
		$post_id	= isset($args[$this->post_type.'_id']) ? intval($args[$this->post_type.'_id']) : 0;

		if(empty($post_id)){
			$pt_object	= get_post_type_object($this->post_type);
			return new WP_Error('empty_'.$this->post_type.'_id', $pt_object->label.'ID不能为空并且必须为数字');
		}

		if($args['path_type'] == 'template'){
			return get_permalink($post_id);
		}else{
			if(strpos($args['path'], '%post_id%')){
				return str_replace('%post_id%', $post_id, $args['path']);
			}else{
				return $args['path'];
			}
		}
	}

	private function get_term_path($args){
		$tax_key	= $this->get_taxonomy_key();
		$term_id	= isset($args[$tax_key]) ? intval($args[$tax_key]) : 0;

		if(empty($term_id)){
			$tax_object	= get_taxonomy($this->taxonomy);
			return new WP_Error('empty_'.$tax_key, $tax_object->label.'ID不能为空并且必须为数字');
		}

		if($args['path_type'] == 'template'){
			return get_term_link($term_id, $this->taxonomy);
		}else{
			if(strpos($args['path'], '%term_id%')){
				return str_replace('%term_id%', $term_id, $args['path']);
			}else{
				return $args['path'];
			}
		}
	}

	private function get_author_path($args){

		$author	= isset($args['author']) ? intval($args['author']) : 0;

		if(empty($author)){
			return new WP_Error('empty_author', '作者ID不能为空并且必须为数字。');
		}

		if($args['path_type'] == 'template'){
			return get_author_posts_url($author);
		}else{
			if(strpos($args['path'], '%author%')){
				return str_replace('%author%', $author, $args['path']);
			}else{
				return $args['path'];
			}
		}
	}

	private function get_callback($type){
		if(!empty($this->callbacks[$type])){
			return $this->callbacks[$type];
		}elseif($this->page_type == 'post_type'){
			return [$this, 'get_post_path'];
		}elseif($this->page_type == 'taxonomy'){
			return [$this, 'get_term_path'];
		}elseif($this->page_type == 'author'){
			return [$this, 'get_author_path'];
		}else{
			return '';
		}
	}

	public function get_path($type, $args=[]){
		$path		= $this->paths[$type] ?? '';
		$callback	= $this->get_callback($type);

		if($callback && is_callable($callback)){
			$args['path_type']	= $type;
			$args['path']		= $path;

			return call_user_func($callback, $args);
		}else{
			if(isset($this->paths[$type])){
				return $path;
			}else{
				if(isset($args['backup'])){
					return new WP_Error('invalid_page_key_backup', '备用页面无效');
				}else{
					return new WP_Error('invalid_page_key', '页面无效');
				}
			}
		}
	}

	public function get_raw_path($type){
		return $this->paths[$type] ?? '';
	}

	public function has($types, $operator='AND', $strict=false){
		$types	= (array) $types;

		foreach ($types as $type){
			$has	= isset($this->paths[$type]) || isset($this->callbacks[$type]);

			if($strict && $has && isset($this->paths[$type]) && $this->paths[$type] === false){
				$has	= false;
			}

			if($operator == 'AND'){
				if(!$has){
					return false;
				}
			}elseif($operator == 'OR'){
				if($has){
					return true;
				}
			}
		}

		if($operator == 'AND'){
			return true;
		}elseif($operator == 'OR'){
			return false;
		}
	}

	public static function parse_item($item, $path_type, $backup=false){
		if($backup){
			$page_key	= $item['page_key_backup'] ?: 'none';
		}else{
			$page_key	= $item['page_key'] ?? '';
		}

		$parsed	= [];

		if($page_key == 'none'){
			if(!empty($item['video'])){
				$parsed['type']		= 'video';
				$parsed['video']	= $item['video'];
				$parsed['vid']		= wpjam_get_qqv_id($item['video']);
			}else{
				$parsed['type']		= 'none';
			}
		}elseif($page_key == 'external'){
			if($path_type == 'web'){
				$parsed['type']		= 'external';
				$parsed['url']		= $item['url'];
			}
		}elseif($page_key == 'web_view'){
			if($path_type == 'web'){
				$parsed['type']		= 'external';
				$parsed['url']		= $item['src'];
			}else{
				$parsed['type']		= 'web_view';
				$parsed['src']		= $item['src'];
			}
		}elseif($page_key){
			if($path_obj = self::get_instance($page_key)){
				if($backup){
					$backup_item	= ['backup'=>true];

					if($path_fields = $path_obj->get_fields()){
						foreach($path_fields as $field_key => $path_field){
							$backup_item[$field_key]	= $item[$field_key.'_backup'] ?? '';
						}
					}

					$path	= $path_obj->get_path($path_type, $backup_item);
				}else{
					$path	= $path_obj->get_path($path_type, $item);
				}

				if(!is_wp_error($path)){
					if(is_array($path)){
						$parsed	= $path;
					}else{
						$parsed['type']		= '';
						$parsed['page_key']	= $page_key;
						$parsed['path']		= $path;
					}
				}
			}
		}

		return $parsed;
	}

	public static function validate_item($item, $path_types){
		$page_key	= $item['page_key'];

		if($page_key == 'none'){
			return true;
		}elseif($page_key == 'web_view'){
			$path_types	= array_diff($path_types, ['web']);
		}

		if($path_obj = self::get_instance($page_key)){
			$backup_check	= false;

			foreach ($path_types as $path_type) {
				$path	= $path_obj->get_path($path_type, $item);

				if(is_wp_error($path)){
					if(count($path_types) <= 1 || $path->get_error_code() != 'invalid_page_key'){
						return $path;
					}else{
						$backup_check	= true;
						break;
					}
				}
			}
		}else{
			if(count($path_types) <= 1){
				return new WP_Error('invalid_page_key', '页面无效');
			}

			$backup_check	= true;
		}

		if($backup_check){
			$page_key	= $item['page_key_backup'] ?: 'none';

			if($page_key == 'none'){
				return true;
			}

			if($path_obj = self::get_instance($page_key)){
				$backup		= ['backup'=>true];

				if($path_obj && ($path_fields = $path_obj->get_fields())){
					foreach($path_fields as $field_key => $path_field){
						$backup[$field_key]	= $item[$field_key.'_backup'] ?? '';
					}
				}

				foreach ($path_types as $path_type) {
					$path	= $path_obj->get_path($path_type, $backup);

					if(is_wp_error($path)){
						return $path;
					}
				}
			}else{
				return new WP_Error('invalid_page_key_backup', '备用页面无效');
			}
		}

		return true;
	}

	public static function get_item_link_tag($parsed, $text){
		if($parsed['type'] == 'none'){
			return $text;
		}elseif($parsed['type'] == 'external'){
			return '<a href_type="web_view" href="'.$parsed['url'].'">'.$text.'</a>';
		}elseif($parsed['type'] == 'web_view'){
			return '<a href_type="web_view" href="'.$parsed['src'].'">'.$text.'</a>';
		}elseif($parsed['type'] == 'mini_program'){
			return '<a href_type="mini_program" href="'.$parsed['path'].'" appid="'.$parsed['appid'].'">'.$text.'</a>';
		}elseif($parsed['type'] == 'contact'){
			return '<a href_type="contact" href="" tips="'.$parsed['tips'].'">'.$text.'</a>';
		}elseif($parsed['type'] == ''){
			return '<a href_type="path" page_key="'.$parsed['page_key'].'" href="'.$parsed['path'].'">'.$text.'</a>';
		}
	}

	public static function get_tabbar_options($path_type){
		$options	= [];

		if($path_objs	= self::$path_objs){
			foreach ($path_objs as $page_key => $path_obj){
				if($tabbar	= $path_obj->get_tabbar($path_type)){
					if(is_array($tabbar)){
						$text	= $tabbar['text'];
					}else{
						$text	= $path_obj->get_title();
					}

					$options[$page_key]	= $text;
				}
			}
		}

		return $options;
	}

	public static function get_path_fields($path_types, $for=''){
		if(empty($path_types)){
			return [];
		}

		$path_types	= (array) $path_types;

		$backup_fields_required	= count($path_types) > 1 && $for != 'qrcode';

		if($backup_fields_required){
			$backup_fields	= ['page_key_backup'=>['title'=>'',	'type'=>'select',	'options'=>['none'=>'只展示不跳转'],	'description'=>'&emsp;跳转页面不生效时将启用备用页面']];
			$backup_show_if_keys	= [];
		}

		$page_key_fields	= ['page_key'	=> ['title'=>'',	'type'=>'select',	'options'=>[]]];

		if($path_objs = self::$path_objs){
			$strict	= boolval($for == 'qrcode');

			foreach ($path_objs as $page_key => $path_obj){
				if(!$path_obj->has($path_types, 'OR', $strict)){
					continue;
				}

				$page_key_fields['page_key']['options'][$page_key]	= $path_obj->get_title();

				if($path_fields = $path_obj->get_fields()){
					foreach($path_fields as $field_key => $path_field){
						if(isset($page_key_fields[$field_key])){
							$page_key_fields[$field_key]['show_if']['value'][]	= $page_key;
						}else{
							$path_field['title']	= '';
							$path_field['show_if']	= ['key'=>'page_key','compare'=>'IN','value'=>[$page_key]];

							$page_key_fields[$field_key]	= $path_field;
						}
					}
				}

				if($backup_fields_required){
					if($path_obj->has($path_types, 'AND')){
						if($page_key == 'module_page' && $path_fields){
							$backup_fields['page_key_backup']['options'][$page_key]	= $path_obj->get_title();

							foreach($path_fields as $field_key => $path_field){
								$path_field['show_if']	= ['key'=>'page_key_backup','value'=>$page_key];
								$backup_fields[$field_key.'_backup']	= $path_field;
							}
						}elseif(empty($path_fields)){
							$backup_fields['page_key_backup']['options'][$page_key]	= $path_obj->get_title();
						}
					}else{
						if($page_key == 'web_view'){
							if(!$path_obj->has(array_diff($path_types, ['web']), 'AND')){
								$backup_show_if_keys[]	= $page_key;
							}
						}else{
							$backup_show_if_keys[]	= $page_key;
						}
					}
				}
			}
		}

		if($for == 'qrcode'){
			return ['page_key_set'	=> ['title'=>'页面',	'type'=>'fieldset',	'fields'=>$page_key_fields]];
		}else{
			$page_key_fields['page_key']['options']['none']	= '只展示不跳转';

			$fields	= ['page_key_set'	=> ['title'=>'页面',	'type'=>'fieldset',	'fields'=>$page_key_fields]];

			if($backup_fields_required){
				$show_if	= ['key'=>'page_key','compare'=>'IN','value'=>$backup_show_if_keys];

				$fields['page_key_backup_set']	= ['title'=>'备用',	'type'=>'fieldset',	'fields'=>$backup_fields, 'show_if'=>$show_if];
			}

			return $fields;
		}
	}

	public static function get_page_keys($path_type){
		$pages	= [];

		if($path_objs = self::$path_objs){
			foreach ($path_objs as $page_key => $path_obj){
				if($page = $path_obj->get_page($path_type)){
					$pages[]	= compact('page_key', 'page');
				}
			}
		}

		return $pages;
	}

	public static function create($page_key, $args=[]){
		$path_obj	= self::get_instance($page_key);

		if(is_null($path_obj)){
			$path_obj	= new WPJAM_Path($page_key, $args);

			self::$path_objs[$page_key]	= $path_obj;
		}

		if(!empty($args['path_type'])){
			$path_type	= $args['path_type'];

			if(isset($args['path'])){
				$path_obj->set_path($path_type, $args['path']);
			}

			if(!empty($args['callback'])){
				$path_obj->set_callback($path_type, $args['callback']);
			}

			if(!empty($args['fields'])){
				$path_obj->set_fields($path_type, $args['fields']);
			}

			$tabbar	= $args['tabbar'] ?? false;
			$path_obj->set_tabbar($path_type, $tabbar);
		}

		return $path_obj;
	}

	public static function unregister($page_key, $path_type=''){
		if($path_type){
			if($path_obj = self::get_instance($page_key)){
				$path_obj->remove_path($path_type);
			}
		}else{
			unset(self::$path_objs[$page_key]);
		}
	}

	public static function get_instance($page_key){
		return self::$path_objs[$page_key] ?? null;
	}

	public static function get_by($args=[]){
		$path_objs	= [];

		if(self::$path_objs && $args){
			$path_type	= $args['path_type'] ?? '';
			$page_type	= $args['page_type'] ?? '';
			$post_type	= $args['post_type'] ?? '';
			$taxonomy	= $args['taxonomy'] ?? '';

			foreach (self::$path_objs as $page_key => $path_obj) {
				if($path_type && !$path_obj->has($path_type)){
					continue;
				}

				if($page_type && $path_obj->get_page_type() != $page_type){
					continue;
				}

				if($post_type && $path_obj->get_post_type() != $post_type){
					continue;
				}

				if($taxonomy && $path_obj->get_taxonomy() != $taxonomy){
					continue;
				}

				$path_objs[$page_key]	= $path_obj;
			}
		}

		return $path_objs;
	}

	public static function get_all(){
		return self::$path_objs;
	}
}

wp_cache_add_global_groups(['wpjam_list_cache']);

class WPJAM_ListCache{
	private $key;

	public function __construct($key){
		$this->key	= $key;
	}

	private function get_items(&$cas_token){
		$items	= wp_cache_get_with_cas($this->key, 'wpjam_list_cache', $cas_token);

		if($items === false){
			$items	= [];
			wp_cache_add($this->key, [], 'wpjam_list_cache', DAY_IN_SECONDS);
			$items	= wp_cache_get_with_cas($this->key, 'wpjam_list_cache', $cas_token);
		}

		return $items;
	}

	private function set_items($cas_token, $items){
		return wp_cache_cas($cas_token, $this->key, $items, 'wpjam_list_cache', DAY_IN_SECONDS);
	}

	public function get_all(){
		$items	= wp_cache_get($this->key, 'wpjam_list_cache');
		return $items ?: [];
	}

	public function get($k){
		$items = $this->get_all();
		return $items[$k]??false;  
	}

	public function add($item, $k=null){
		$cas_token	= '';
		$retry		= 10;

		do{
			$items	= $this->get_items($cas_token);

			if($k!==null){
				if(isset($items[$k])){
					return false;
				}

				$items[$k]	= $item;
			}else{
				$items[]	= $item;
			}

			$result	= $this->set_items($cas_token, $items);

			$retry	 -= 1;
		}while (!$result && $retry > 0);

		return $result;
	}

	public function increment($k, $offset=1){
		$cas_token	= '';
		$retry		= 10;

		do{
			$items		= $this->get_items($cas_token);
			$items[$k]	= $items[$k]??0; 
			$items[$k]	= $items[$k]+$offset;

			$result	= $this->set_items($cas_token, $items);

			$retry	 -= 1;
		}while (!$result && $retry > 0);

		return $result;
	}

	public function decrement($k, $offset=1){
		return $this->increment($k, 0-$offset);
	}

	public function set($item, $k){
		$cas_token	= '';
		$retry		= 10;

		do{
			$items		= $this->get_items($cas_token);
			$items[$k]	= $item;
			$result		= $this->set_items($cas_token, $items);
			$retry 		-= 1;
		}while(!$result && $retry > 0);

		return $result;
	}

	public function remove($k){
		$cas_token	= '';
		$retry		= 10;

		do{
			$items	= $this->get_items($cas_token);
			if(!isset($items[$k])){
				return false;
			}
			unset($items[$k]);
			$result	= $this->set_items($cas_token, $items);
			$retry 	-= 1;
		}while(!$result && $retry > 0);

		return $result;
	}

	public function empty(){
		$cas_token		= '';
		$retry	= 10;

		do{
			$items	= $this->get_items($cas_token);
			if($items == []){
				return [];
			}
			$result	= $this->set_items($cas_token, []);
			$retry 	-= 1;
		}while(!$result && $retry > 0);

		if($result){
			return $items;
		}

		return $result;
	}
}

class WPJAM_Cache{
	/* HTML 片段缓存
	Usage:

	if (!WPJAM_Cache::output('unique-key')) {
		functions_that_do_stuff_live();
		these_should_echo();
		WPJAM_Cache::store(3600);
	}
	*/
	public static function output($key) {
		$output	= get_transient($key);
		if(!empty($output)) {
			echo $output;
			return true;
		} else {
			ob_start();
			return false;
		}
	}

	public static function store($key, $cache_time='600') {
		$output = ob_get_flush();
		set_transient($key, $output, $cache_time);
		echo $output;
	}
}

class WPJAM_Crypt{
	private $method		= 'aes-256-cbc';
	private $key 		= '';
	private $iv			= '';
	private $options	= OPENSSL_ZERO_PADDING;
	private $block_size	= 32;	// 注意 PHP 默认 aes cbc 算法的 block size 都是 16 位

	public function __construct($args=[]){
		foreach ($args as $key => $value) {
			if(in_array($key, ['key', 'method', 'options', 'iv', 'block_size'])){
				$this->$key	= $value;
			}
		}
	}

	public function encrypt($text){
		if($this->options == OPENSSL_ZERO_PADDING && $this->block_size){
			$text	= $this->pkcs7_pad($text, $this->block_size);	//使用自定义的填充方式对明文进行补位填充
		}

		return openssl_encrypt($text, $this->method, $this->key, $this->options, $this->iv);
	}

	public function decrypt($encrypted_text){
		try{
			$text	= openssl_decrypt($encrypted_text, $this->method, $this->key, $this->options, $this->iv);
		}catch(Exception $e){
			return new WP_Error('decrypt_aes_failed', 'aes 解密失败');
		}

		if($this->options == OPENSSL_ZERO_PADDING && $this->block_size){
			$text	= $this->pkcs7_unpad($text, $this->block_size);	//去除补位字符
		}

		return $text;
	}

	public static function pkcs7_pad($text, $block_size=32){	//对需要加密的明文进行填充 pkcs#7 补位
		//计算需要填充的位数
		$amount_to_pad	= $block_size - (strlen($text) % $block_size);
		$amount_to_pad	= $amount_to_pad ?: $block_size;

		//获得补位所用的字符
		return $text . str_repeat(chr($amount_to_pad), $amount_to_pad);
	}

	public static function pkcs7_unpad($text, $block_size){	//对解密后的明文进行补位删除
		$pad	= ord(substr($text, -1));

		if($pad < 1 || $pad > $block_size){
			$pad	= 0;
		}

		return substr($text, 0, (strlen($text) - $pad));
	}

	public static function weixin_pad($text, $appid){
		$random = self::generate_random_string(16);		//获得16位随机字符串，填充到明文之前
		return $random.pack("N", strlen($text)).$text.$appid;
	}

	public static function weixin_unpad($text, &$appid){	//去除16位随机字符串,网络字节序和AppId
		$text		= substr($text, 16, strlen($text));
		$len_list	= unpack("N", substr($text, 0, 4));
		$text_len	= $len_list[1];
		$appid		= substr($text, $text_len + 4);
		return substr($text, 4, $text_len);
	}

	public static function sha1(...$args){
		sort($args, SORT_STRING);

		return sha1(implode($args));
	}

	public static function generate_weixin_signature($token, &$timestamp='', &$nonce='', $encrypt_msg=''){
		$timestamp	= $timestamp ?: time();
		$nonce		= $nonce ?: self::generate_random_string(8);
		return self::sha1($encrypt_msg, $token, $timestamp, $nonce);
	}

	public static function generate_random_string($length){
		$alphabet	= "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
		$max		= strlen($alphabet);

		$token		= '';
		for ($i = 0; $i < $length; $i++) {
			$token .= $alphabet[self::crypto_rand_secure(0, $max - 1)];
		}

		return $token;
	}

	private static function crypto_rand_secure($min, $max){
		$range	= $max - $min;

		if($range < 1){
			return $min;
		}

		$log	= ceil(log($range, 2));
		$bytes	= (int)($log / 8) + 1;		// length in bytes
		$bits	= (int)$log + 1;			// length in bits
		$filter	= (int)(1 << $bits) - 1;	// set all lower bits to 1

		do {
			$rnd	= hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
			$rnd	= $rnd & $filter;	// discard irrelevant bits
		}while($rnd > $range);

		return $min + $rnd;
	}
}

class IP{
	private static $ip = null;
	private static $fp = null;
	private static $offset = null;
	private static $index = null;
	private static $cached = [];

	public static function find($ip){
		if (empty( $ip ) === true) {
			return 'N/A';
		}

		$nip	= gethostbyname($ip);
		$ipdot	= explode('.', $nip);

		if ($ipdot[0] < 0 || $ipdot[0] > 255 || count($ipdot) !== 4) {
			return 'N/A';
		}

		if (isset( self::$cached[$nip] ) === true) {
			return self::$cached[$nip];
		}

		if (self::$fp === null) {
			self::init();
		}

		$nip2 = pack('N', ip2long($nip));

		$tmp_offset	= (int) $ipdot[0] * 4;
		$start		= unpack('Vlen',
			self::$index[$tmp_offset].self::$index[$tmp_offset + 1].self::$index[$tmp_offset + 2].self::$index[$tmp_offset + 3]);

		$index_offset = $index_length = null;
		$max_comp_len = self::$offset['len'] - 1024 - 4;
		for ($start = $start['len'] * 8 + 1024; $start < $max_comp_len; $start += 8) {
			if (self::$index[$start].self::$index[$start+1].self::$index[$start+2].self::$index[$start+3] >= $nip2) {
				$index_offset = unpack('Vlen',
					self::$index[$start+4].self::$index[$start+5].self::$index[$start+6]."\x0");
				$index_length = unpack('Clen', self::$index[$start+7]);

				break;
			}
		}

		if ($index_offset === null) {
			return 'N/A';
		}

		fseek(self::$fp, self::$offset['len'] + $index_offset['len'] - 1024);

		self::$cached[$nip] = explode("\t", fread(self::$fp, $index_length['len']));

		return self::$cached[$nip];
	}

	private static function init(){
		if (self::$fp === null) {
			self::$ip = new self();

			self::$fp = fopen(WP_CONTENT_DIR.'/uploads/17monipdb.dat', 'rb');
			if (self::$fp === false) {
				throw new Exception('Invalid 17monipdb.dat file!');
			}

			self::$offset = unpack('Nlen', fread(self::$fp, 4));
			if (self::$offset['len'] < 4) {
				throw new Exception('Invalid 17monipdb.dat file!');
			}

			self::$index = fread(self::$fp, self::$offset['len'] - 4);
		}
	}

	public function __destruct(){
		if (self::$fp !== null) {
			fclose(self::$fp);
		}
	}
}