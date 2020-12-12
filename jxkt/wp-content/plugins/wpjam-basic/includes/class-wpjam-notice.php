<?php
class WPJAM_Notice{
	public static function add($notice){
		return WPJAM_Admin_Notice::get_instance(get_current_blog_id())->add($notice);
	}

	public static function ajax_delete(){
		if($notice_key = wpjam_get_data_parameter('notice_key')){
			WPJAM_User_Notice::get_instance(get_current_user_id())->delete($notice_key);

			if(current_user_can('manage_options')){
				WPJAM_Admin_Notice::get_instance(get_current_blog_id())->delete($notice_key);
			}
		}

		wpjam_send_json();
	}

	public static function on_admin_notices(){
		if($errors = WPJAM_Admin_Error::get_errors()){
			foreach ($errors as $error){
				echo '<div class="notice notice-'.$error['type'].' is-dismissible"><p>'.$error['message'].'</p></div>';
			}
		}

		$admin_notice_obj	= WPJAM_Admin_Notice::get_instance(get_current_blog_id());
		$user_notice_obj	= WPJAM_User_Notice::get_instance(get_current_user_id());

		if($notice_key	= wpjam_get_parameter('notice_key')){
			$user_notice_obj->delete($notice_key);

			if(current_user_can('manage_options')){
				$admin_notice_obj->delete($notice_key);
			}
		}

		$notices	= $user_notice_obj->get_notices();

		if(current_user_can('manage_options')){
			$notices	= array_merge($notices, $admin_notice_obj->get_notices());
		}

		if(empty($notices)){
			return;
		}

		uasort($notices, function($n, $m){ return $m['time'] <=> $n['time']; });

		$modal_notice	= '';

		foreach ($notices as $notice_key => $notice){
			$notice = wp_parse_args($notice, [
				'type'		=> 'info',
				'class'		=> 'is-dismissible',
				'admin_url'	=> '',
				'notice'	=> '',
				'title'		=> '',
				'modal'		=> 0,
			]);

			$admin_notice	= $notice['notice'];

			if($notice['admin_url']){
				$admin_notice	.= $notice['modal'] ? "\n\n" : ' ';
				$admin_notice	.= '<a style="text-decoration:none;" href="'.add_query_arg(compact('notice_key'), home_url($notice['admin_url'])).'">点击查看<span class="dashicons dashicons-arrow-right-alt"></span></a>';
			}

			$admin_notice	= wpautop($admin_notice).wpjam_get_page_button('delete_notice', ['data'=>compact('notice_key')]);

			if($notice['modal']){
				if(empty($modal_notice)){	// 弹窗每次只显示一条
					$modal_notice	= wpjam_json_encode($admin_notice);
					$modal_title	= $notice['title'] ?: '消息';
				}
			}else{
				echo '<div class="notice notice-'.$notice['type'].' '.$notice['class'].'">'.$admin_notice.'</div>';
			}
		}

		if($modal_notice){ ?>

		<script type="text/javascript">
		jQuery(function($){
			$('#tb_modal').html('<?php echo $modal_notice; ?>');
			tb_show('<?php echo esc_js($modal_title); ?>', "#TB_inline?inlineId=tb_modal&height=200");
			tb_position();
		});
		</script>

		<?php }
	}
}

class WPJAM_Admin_Notice{
	private $blog_id	= 0;
	private $notices	= [];

	private static $instances	= [];

	public static function get_instance($blog_id=0){
		if(!isset(self::$instances[$blog_id])){
			self::$instances[$blog_id] = new self($blog_id);
		}

		return self::$instances[$blog_id];
	}

	private function __construct($blog_id=0){
		$this->blog_id	= $blog_id;

		$notices = is_multisite() ? get_blog_option($blog_id, 'wpjam_notices') : get_option('wpjam_notices');

		if($notices){
			$this->notices	= array_filter($notices, function($notice){ return $notice['time'] > time() - MONTH_IN_SECONDS * 3; });
		}
	}

	private function __clone(){}

	private function __wakeup(){}

	public function get_notices(){
		return $this->notices;
	}

	public function add($notice){
		$notice['time']	= $notice['time'] ?? time();
		$key			= $notice['key'] ?? md5(maybe_serialize($notice));

		$this->notices[$key]	= $notice;

		return $this->save();
	}

	public function delete($key){
		if(isset($this->notices[$key])){
			unset($this->notices[$key]);
			return $this->save();
		}

		return true;
	}

	public function save(){
		if(empty($this->notices)){
			return is_multisite() ? delete_blog_option($this->blog_id, 'wpjam_notices') : delete_option('wpjam_notices');
		}else{
			return is_multisite() ? update_blog_option($this->blog_id, 'wpjam_notices', $this->notices) : update_option('wpjam_notices', $this->notices);
		}
	}
}

class WPJAM_User_Notice{
	private $user_id	= 0;
	private $notices	= [];

	private static $instances	= [];

	public static function get_instance($user_id){
		if(!isset(self::$instances[$user_id])){
			self::$instances[$user_id] = new self($user_id);
		}

		return self::$instances[$user_id];
	}

	private function __construct($user_id){
		$this->user_id	= $user_id;

		if($user_id && ($notices = get_user_meta($user_id, 'wpjam_notices', true))){
			$this->notices	= array_filter($notices, function($notice){ return $notice['time'] > time() - MONTH_IN_SECONDS * 3; });
		}
	}

	private function __clone(){}

	private function __wakeup(){}

	public function get_notices(){
		return $this->notices;
	}

	public function add($notice){
		$notice['time']	= $notice['time'] ?? time();
		$key			= $notice['key'] ?? md5(maybe_serialize($notice));

		$this->notices[$key]	= $notice;

		return $this->save();
	}

	public function delete($key){
		if(isset($this->notices[$key])){
			unset($this->notices[$key]);
			return $this->save();
		}

		return true;
	}

	public function save(){
		if(empty($this->notices)){
			return delete_user_meta($this->user_id, 'wpjam_notices');
		}else{
			return update_user_meta($this->user_id, 'wpjam_notices', $this->notices);
		}
	}
}

Class WPJAM_Admin_Error{
	public static $errors = [];

	public static function add_error($message='', $type='success'){
		if($message){
			if(is_wp_error($message)){
				self::$errors[]	= ['message'=>$message->get_error_message(), 'type'=>'error'];
			}elseif($type){
				self::$errors[]	= compact('message','type');
			}
		}
	}

	public static function get_errors(){
		return self::$errors;
	}
}

class WPJAM_User_Message{
	private $user_id	= 0;
	private $messages	= [];

	private static $instances	= [];

	public static function get_instance($user_id){
		if(!isset(self::$instances[$user_id])){
			self::$instances[$user_id] = new self($user_id);
		}

		return self::$instances[$user_id];
	}

	public static function ajax_delete(){
		$message_id	= wpjam_get_data_parameter('message_id', ['santize_callback'=>'intval']);
		$user_id	= get_current_user_id();

		$message_obj	= self::get_instance($user_id);
		$messages		= $message_obj->get_messages();

		if($messages && isset($messages[$message_id])){
			$result	= $message_obj->delete($message_id);

			if(is_wp_error($result)){
				wpjam_send_json($result);
			}else{
				wpjam_send_json(['message_id'=>$message_id]);
			}
		}

		wpjam_send_json(['errcode'=>'invalid_message_id', '无效的消息ID']);
	}

	private function __clone(){}

	private function __wakeup(){}

	private function __construct($user_id){
		$this->user_id	= $user_id;

		if($user_id && ($messages = get_user_meta($user_id, 'wpjam_messages', true))){
			$this->messages	= array_filter($messages, function($message){ return $message['time'] > time() - MONTH_IN_SECONDS * 3; });
		}
	}

	public function get_messages(){
		return $this->messages;
	}

	public function get_unread_count(){
		$messages	= array_filter($this->messages, function($message){ return $message['status'] == 0; });

		return count($messages);
	}

	public function set_all_read(){
		array_walk($this->messages, function(&$message){ $message['status'] == 1; });

		return $this->save();
	}

	public function add($message){
		$message	= wp_parse_args($message, [
			'sender'	=> '',
			'receiver'	=> '',
			'type'		=> '',
			'content'	=> '',
			'status'	=> 0,
			'time'		=> time()
		]);

		$message['content'] = wp_strip_all_tags($message['content']);

		$this->messages[]	= $message;

		return $this->save();
	}

	public static function delete($i){
		if(isset($this->messages[$i])){
			unset($this->messages[$i]);
			return $this->save();
		}

		return true;
	}

	public function save(){
		if(empty($this->messages)){
			return delete_user_meta($this->user_id, 'wpjam_messages');
		}else{
			return update_user_meta($this->user_id, 'wpjam_messages', $this->messages);
		}
	}
}