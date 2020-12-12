<div class="related">
	<h2><span>在线留言</span></h2>
	<div id="dahuzi_contact" class="contact-section section-padding"<?php echo data_animate();?>>
		<div class="page-width">
			<div class="contact-col-md-12">
				<div class="contact-form-area">
					<div class="contact-form-holder">
						<form method="POST" class="contact-validation-active" id="contact-form" novalidate="novalidate">
							<div class="contact-col-md-6">
								<input type="text" name="yourname" id="yourname" class="form-control" placeholder="姓名 *" aria-required="true">
							</div>
							<div class="contact-col-md-6">
								<input type="text" name="phone" id="phone" class="form-control" placeholder="联系电话 *" aria-required="true">
							</div>
							<div class="contact-col-md-6">
								<input type="email" name="mail" id="email" class="form-control" placeholder="联系邮箱">
							</div>
							<div>
								<textarea class="form-control" name="message" id="note" placeholder="请输入您的留言内容，收到留言后会尽快与您联系..." aria-required="true"></textarea>
								<input type="text" name="current_url" id="current_url" class="form-control" value="<?php echo home_url(add_query_arg(array()));?>" style="display:none">
							</div>
							<div class="submit-btn-wrapper">
								<input type="hidden" name="action" value="dahuzi_contact_ajax">
								<button id="submit_message" type="submit" class="theme-btn-s4">提交留言</button>
								<div id="form-messages"></div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>