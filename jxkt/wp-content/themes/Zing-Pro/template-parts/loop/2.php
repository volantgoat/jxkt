<li>
<div class="scrollable-item">
	<p class="scrollable-img">
		<a href="<?php the_permalink(); ?>"><img src="<?php echo post_thumbnail($post_img_width,$post_img_height); ?>" alt="<?php the_title(); ?>"/></a>
	</p>
	<h2><a href="<?php the_permalink(); ?>"><?php the_title_attribute(); ?></a></h2>
</div>
</li>