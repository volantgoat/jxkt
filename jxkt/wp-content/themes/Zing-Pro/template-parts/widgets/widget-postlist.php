<?php
//2和1文章插件
//widget xintheme_postlist

add_action('widgets_init', function(){register_widget('xintheme_postlist' );});
class xintheme_postlist extends WP_Widget {

	public function __construct() {
		$widget_ops = array( 'description' => '可以选择显示最新文章、随机文章。' );
		parent::__construct('xintheme_postlist', __('【XinTheme】小图文章展示'), $widget_ops);
	}

    function widget($args, $instance) {
        extract( $args );
		$limit = $instance['limit'];
		$title = apply_filters('widget_name', $instance['title']);
		$cat          = $instance['cat'];
		$orderby      = $instance['orderby'];
		echo $before_widget;
		echo $before_title.$title.$after_title; 
        echo xintheme_widget_postlist($orderby,$limit,$cat);
        echo $after_widget;	
    }

	function form($instance) {
		$instance['title'] = ! empty( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$instance['orderby'] = ! empty( $instance['orderby'] ) ? esc_attr( $instance['orderby'] ) : '';
		$instance['cat'] = ! empty( $instance['cat'] ) ? esc_attr( $instance['cat'] ) : '';
		$instance['limit']    = isset( $instance['limit'] ) ? absint( $instance['limit'] ) : 5;
?>
<p style="clear: both;padding-top: 5px;">
	<label>显示标题：（例如：最新文章、随机文章）
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" />
	</label>
</p>
<p>
	<label> 排序方式：
		<select style="width:100%;" id="<?php echo $this->get_field_id('orderby'); ?>" name="<?php echo $this->get_field_name('orderby'); ?>" style="width:100%;">
			<option value="date" <?php selected('date', $instance['orderby']); ?>>发布时间</option>
			<option value="rand" <?php selected('rand', $instance['orderby']); ?>>随机文章</option>
		</select>
	</label>
</p>
<p>
	<label>
		分类限制：
		<p>只显示指定分类，填写数字，用英文逗号隔开，例如：1,2 </p>
		<p>排除指定分类的文章，填写负数，用英文逗号隔开，例如：-1,-2。</p>
		<input style="width:100%;" id="<?php echo $this->get_field_id('cat'); ?>" name="<?php echo $this->get_field_name('cat'); ?>" type="text" value="<?php echo $instance['cat']; ?>" size="24" />
	</label>
</p>
<p>
	<label> 显示数目：
		<input class="widefat" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="number" value="<?php echo $instance['limit']; ?>" />
	</label>
</p>
<p><?php show_category();?><br/><br/></p>
<?php
	}
}

function xintheme_widget_postlist($orderby,$limit,$cat){
?>
	<ul class="widget_SpecialCatPosts">
		<?php
			$args = array(
				'post_status' => 'publish', // 只选公开的文章.
				'post__not_in' => array(get_the_ID()),//排除当前文章
				'ignore_sticky_posts' => 1, // 排除置頂文章.
				'orderby' =>  $orderby, // 排序方式.
				'cat'     => $cat,
				'order'   => 'DESC',
				'showposts' => $limit,
				'tax_query' => array( array( 
				'taxonomy' => 'post_format',
				'field' => 'slug',
				'terms' => array(
					//请根据需要保留要排除的文章形式
					'post-format-aside',
					
					),
				'operator' => 'NOT IN',
				) ),
			);
			//$query_posts = new WP_Query();
			$query_posts = dahuzi_query( $args );
			//$query_posts->query($args);
			while( $query_posts->have_posts() ) { $query_posts->the_post(); ?>
			<li>
				<a href="<?php the_permalink(); ?>">
					<img src="<?php echo post_thumbnail(250, 188); ?>" alt="<?php the_title(); ?>" title="<?php the_title(); ?>" class="thumb"></a>
				<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
			</li>
			<?php } wp_reset_query();?>
	</ul>
<?php
}
?>
