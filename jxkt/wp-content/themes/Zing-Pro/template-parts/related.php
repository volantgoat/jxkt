<div class="related">
	<h2><span>相关推荐</span></h2>
	<ul class="post-list">
	<?php
	$post_num = 6;
	$exclude_id = $post->ID;
	$posttags = get_the_tags(); $i = 0;
	if ( $posttags ) {
		$tags = '';
		foreach ( $posttags as $tag ) $tags .= $tag->term_id . ',';
		$args = array(
			'post_status' => 'publish',
			'tag__in' => explode(',', $tags),
			'post__not_in' => explode(',', $exclude_id),
			'ignore_sticky_posts' => 1,
			'orderby' => 'comment_date',
			'posts_per_page' => $post_num
		);
		query_posts($args);
		while( have_posts() ) { the_post(); ?>
		<li>
			<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
		</li>
		<?php
		$exclude_id .= ',' . $post->ID; $i ++;
		}
		wp_reset_query();
	}
	if ( $i < $post_num ) {
		$cats = ''; foreach ( get_the_category() as $cat ) $cats .= $cat->cat_ID . ',';
		$args = array(
			'category__in' => explode(',', $cats),
			'post__not_in' => explode(',', $exclude_id),
			'ignore_sticky_posts' => 1,
			'orderby' => 'comment_date',
			'posts_per_page' => $post_num - $i
		);
		query_posts($args);
		while( have_posts() ) {the_post(); ?>
		<li>
			<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
		</li>
		<?php
		$i++; }
		wp_reset_query();
	}
	if ( $i  == 0 )  echo '<li>暂无相关文章!</li>';
	?>
	</ul>
</div>
