<li class="col-2-1 not-animated"<?php echo data_animate();?>>
	<div class="entry-item">
		<div class="time">
			<p class="time-day"><?php the_time('d') ?></p>
			<p class="time-date"><?php the_time('Y-m') ?></p>
		</div>
		<div class="entry-title">
			<h2><a href="<?php the_permalink(); ?>"><?php the_title_attribute(); ?></a></h2>
		</div>
		<div class="entry-summary">
			<div class="qhd-content">
				<p><?php echo mb_strimwidth(strip_tags($post->post_content),0,110,'...');?></p>
			</div>
		</div>
	</div>
</li>