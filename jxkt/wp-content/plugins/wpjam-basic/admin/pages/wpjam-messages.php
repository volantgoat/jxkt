<?php
wpjam_register_page_action('delete_message', [
	'button_text'	=> '删除',
	'class'			=> 'message-delete',
	'direct'		=> true,
	'confirm'		=> true,
	'callback'		=> ['WPJAM_User_Message', 'ajax_delete']
]);

function wpjam_messages_page(){
	$user_id		= get_current_user_id();
	$message_obj	= WPJAM_User_Message::get_instance($user_id);

	$messages		= $message_obj->get_messages();

	if(empty($messages)){ 
		echo '<p>暂无站内消息</p>';
		return;
	}

	if($message_obj->get_unread_count()){
		$message_obj->set_all_read();
	}

	$sender_ids			= [];
	$post_ids_list		= [];
	$comment_ids_list	= [];

	foreach($messages as $message) {
		$sender_ids[]	= $message['sender'];
		$blog_id		= $message['blog_id'];
		$post_id		= $message['post_id'];
		$comment_id		= $message['comment_id'];
		if($blog_id){
			if($post_id){
				$post_ids_list[$blog_id][]		= $post_id;
			}

			if($comment_id){
				$comment_ids_list[$blog_id][]	= $comment_id;
			}
		}
	}

	$senders	= get_users(['blog_id'=>0, 'include'=>$sender_ids]);

	foreach ($post_ids_list as $blog_id => $post_ids) {
		$switched	= is_multisite() ? switch_to_blog($blog_id) : false;

		wpjam_get_posts($post_ids);

		if($switched){
			restore_current_blog();
		}
	}

	foreach ($comment_ids_list as $blog_id => $comment_ids) {
		$switched	= is_multisite() ? switch_to_blog($blog_id) : false;

		get_comments(['include'=>$comment_ids]);

		if($switched){
			restore_current_blog();
		}
	}
	?>

	<ul class="messages">
		<?php foreach ($messages as $i => $message) { 
			$alternate	= empty($alternate)?'alternate':'';
			$sender		= get_userdata($message['sender']);

			$type		= $message['type'];
			$content	= $message['content'];
			$blog_id	= $message['blog_id'];
			$post_id	= $message['post_id'];
			$comment_id	= $message['comment_id'];
			

			if(empty($sender)){
				continue;
			}

			if($blog_id && $post_id){
				$switched	= is_multisite() ? switch_to_blog($blog_id) : false;

				$post		= get_post($post_id);

				if($post){
					$topic_title	= $post->post_title;
				}

				if($switched){
					restore_current_blog();
				}
			}else{
				$topic_title		= '';
			}

			
		?>
		<li id="message_<?php echo $i; ?>" class="<?php echo $alternate; echo empty($message['status'])?' unread':'' ?>">
			<div class="sender-avatar"><?php echo get_avatar($message['sender'], 60);?></div>
			<div class="message-time"><?php echo wpjam_human_time_diff($message['time']);?><p><?php echo wpjam_get_page_button('delete_message',['data'=>['message_id'=>$i]]);?></p></div>
			<div class="message-content">
			
			<?php 

			if($type == 'topic_comment'){
				$prompt	= '<span class="message-sender">'.$sender->display_name.'</span>在你的帖子「<a href="'.admin_url('admin.php?page=wpjam-topics&action=comment&id='.$post_id.'#comment_'.$comment_id).'">'.$topic_title.'</a>」给你留言了：'."\n\n";
			}elseif($type == 'comment_reply' || $type == 'topic_reply'){
				$prompt	= '<span class="message-sender">'.$sender->display_name.'</span>在帖子「<a href="'.admin_url('admin.php?page=wpjam-topics&action=comment&id='.$post_id.'#comment_'.$comment_id).'">'.$topic_title.'</a>」回复了你的留言：'."\n\n";
			}else{
				$prompt	= '<span class="message-sender">'.$sender->display_name.'：'."\n\n";
			}

			echo wpautop($prompt.$content);

			?>
			</div>
		</li>
		<?php } ?>
	</ul>
	
	<?php
}

add_action('admin_head', function(){
	?>
	<style type="text/css">
		ul.messages{ max-width:640px; }
		ul.messages li {margin: 10px 0; padding:10px; margin:10px 0; background: #fff; min-height: 60px;}
		ul.messages li.alternate{background: #f9f9f9;}
		ul.messages li.unread{font-weight: bold;}
		ul.messages li a {text-decoration:none;}
		ul.messages li div.sender-avatar {float:left; margin:0px 10px 0px 0;}
		ul.messages li div.message-time{float: right; width: 60px;}
		ul.messages li .message-delete{color: #a00;}
		ul.messages li div.message-content p {margin: 0 70px 10px 70px; }
	</style>
	<script type="text/javascript">
	jQuery(function($){
		$('body').on('page_action_success', function(e, response){
			var action		= response.page_action;
			var action_type	= response.page_action_type;

			if(action == 'delete_message'){
				var message_id	= response.message_id;
				$('#message_'+message_id).animate({opacity: 0.1}, 500, function(){ $(this).remove();});
			}
		});
	});

	</script>
	<?php
});