<?php if(post_password_required()) return;?>
<div id="comments" class="comments-area">
	<?php comments_number('', '<h3 class="comments-title"><span>1 条评论</span></h3>', '<h3 class="comments-title"><span>% 条评论</span></h3>' );?>
	
	<?php if(have_comments()){ ?>

		<ol class="comment-list">
			<?php wp_list_comments('type=comment&callback=wpjam_theme_list_comments'); ?>
		</ol>

		<?php the_comments_pagination(['prev_text'=>'上一页', 'next_text'=>'下一页']); ?>

	<?php } ?>

	<?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) { ?>
		<p class="no-comments"><?php _e( 'Comments are closed.' ); ?></p>
	<?php } ?>

	<?php comment_form(); ?>
</div>