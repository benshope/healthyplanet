<h3 class="prestige_comment_form_head"><?php _e('Leave a comment', 'prestige'); ?><a href="#cancel" id="cancel_comment" title="<?php _e('Cancel reply', 'prestige'); ?>"><?php _e('Cancel reply', 'prestige'); ?></a></h3>
<form class="prestige_comment_form" id='prestige_comment_form'>
	<div class="form-line block">
		<input type="text" name="comment-user-name" id="comment-user-name" value="<?php _e('Your Name', 'prestige'); ?>" onfocus="clearInput(this,'focus','<?php _e('Your Name', 'prestige'); ?>')" onblur="clearInput(this,'blur','<?php _e('Your Name', 'prestige'); ?>')"/>	
	</div>
	<div class="form-line block">
		<input type="text" name="comment-user-email" id="comment-user-email" value="<?php _e('Your Email Address', 'prestige'); ?>" onfocus="clearInput(this,'focus','<?php _e('Your Email Address', 'prestige'); ?>')" onblur="clearInput(this,'blur','<?php _e('Your Email Address', 'prestige'); ?>')"/>	
	</div>
	<div class="form-line block">
		<textarea rows="0" cols="0" name="comment-message" id="comment-message" onfocus="clearInput(this,'focus','<?php _e('Message', 'prestige'); ?>')" onblur="clearInput(this,'blur','<?php _e('Message', 'prestige'); ?>')"><?php _e('Message', 'prestige'); ?></textarea>	
	</div>
	<div class="form-line">
		<a href="javascript:submitCommentForm();" class="button block" id="comment-send"><?php _e('Post comment', 'prestige'); ?></a>
		<input type="hidden" name="post_id" value="<?php echo esc_attr(get_the_ID()); ?>" />
		<input type="hidden" name="parent_id" value="<?php echo $parent->ID; ?>" />
		<input type="hidden" name="action" value="prestige_comment_form" />
		<input type="hidden" name="comment_parent_id" value="0" />
		<input type="hidden" name="paged" value="1" />
	</div>
</form>