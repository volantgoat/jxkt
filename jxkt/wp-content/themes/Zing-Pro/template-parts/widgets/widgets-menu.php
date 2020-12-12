<?php

add_action('widgets_init', function(){register_widget('xintheme_menu' );});
class xintheme_menu extends WP_Widget {
	function __construct() {
		$widget_ops = array( 'classname' => 'dahuzi-menu', 'description' => '自动获取并显示当前页面根目录下的二级菜单和二级页面，没有二级分类或页面，则不显示' );
		parent::__construct('xintheme_menu', '「XinTheme」导航菜单 ', $widget_ops);
	}

	function widget( $args, $instance ) {
		extract( $args );
		echo $before_widget;
		$title = apply_filters('widget_name', $instance['title']);
		//echo $before_title.$title.$after_title; 
		echo xintheme_widgets_menu($title);
		echo $after_widget;
	}

	function form($instance) {?>
			<p>自动获取并显示当前页面根目录下的二级菜单和二级页面，当前页面没有二级分类或页面，则不显示</p>
		<?php }
	}
	function xintheme_widgets_menu($title){ ?>


	<div class="widget widget_nav_menu">
		<?php if ( is_page() ) {
		global $wpdb,$post;
		$parent_page = $post->ID;
		while($parent_page) {
			$page_query = $wpdb->get_row("SELECT ID, post_title, post_status, post_parent FROM $wpdb->posts WHERE ID = '$parent_page'");
			$parent_page = $page_query->post_parent;
		}
		$parent_id = $page_query->ID;
		$parent_title = $page_query->post_title;
		if ($wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_parent = '$parent_id' AND post_status != 'attachment'")) {

	    	$subpage = wp_list_pages('depth=1&echo=0&child_of='.$parent_id);
	    	if($subpage) { ?>

			<div class="widget__title block-heading block-heading--line">
				<h3 class="widget__title-text"><?php echo $parent_title; ?></h3>
			</div>
			<div class="menu-container">
				<ul class="menu">
					<?php wp_list_pages('depth=1&sort_column=menu_order&title_li=&child_of='. $parent_id); ?>
				</ul>
			</div>
			<?php } else { ?>
				<style>.widget.dahuzi-menu{display:none}</style>
			<?php } ?>
		<?php } ?>

		<?php }else{

		$this_category = get_the_category();
		$category_id = $this_category[0]->cat_ID;
		$parent_id = get_category_root_id( $category_id );
		$category_link = get_category_link( $parent_id );
        $childcat = get_categories('child_of='.$parent_id);
		if( $childcat && $parent_id ){?>

		<div class="widget__title block-heading block-heading--line">
			<h3 class="widget__title-text"><?php echo get_cat_name( $parent_id ); ?></h3>
		</div>
		<div class="menu-container">
			<ul class="menu">
				<?php wp_list_cats("orderby=id&child_of=" . $parent_id . "&depth=2&hide_empty=0"); ?>
			</ul>
		</div>
		<?php } else { ?>
			<style>.widget.dahuzi-menu{display:none}</style>
		<?php }?>

		<?php } wp_reset_query(); ?>
	</div>

	<?php }

