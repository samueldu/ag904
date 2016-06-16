<style>
.btn-newsletter {
	height: 46px;
}
.newsletter-msg {
	margin-top: 20px;
}
</style>

<script language="javascript">
jQuery(document).ready(function($) {
	$('#lt_newsletter_form').submit(function(){
		$.ajax({
			type: 'post',
			url: '<?php echo $action; ?>',
			data:$("#lt_newsletter_form").serialize(),
			dataType: 'json',			
			beforeSend: function() {
				$('.btn-newsletter').attr('disabled', true).button('loading');
			},	
			complete: function() {
				$('.btn-newsletter').attr('disabled', false).button('reset');
			},				
			success: function(json) {
				$('.alert, .text-danger').remove();
				$('.form-group').removeClass('has-error');

				if (json.error) {
					$('#lt_newsletter_module').after('<div class="alert alert-danger newsletter-msg">' + json.error + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				} else {
					$('#lt_newsletter_module').after('<div class="alert alert-success newsletter-msg">' + json.success + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
					$('#lt_newsletter_email').val('');
				}
			}

		});
		return false;
	});
});
</script>

<div class="row" id="newsletter<?php echo $module; ?>">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-heading"><h4><?php echo $heading_title; ?></h4></div>
			<div class="panel-body">
				<div class="col-sm-12"><?php echo $text_intro; ?></div>
				<div class="col-sm-12">
				<form id="lt_newsletter_form">
					<div class="form-group">
						<div id="lt_newsletter_module" class="input-group">
							<div class="input-group-addon"><i data-toggle="tooltip" data-placement="bottom" title="" class="fa fa-info-circle" data-original-title="<?php echo $text_description; ?>"></i></div>
							<input type="email" required name="lt_newsletter_email" id="lt_newsletter_email" class="form-control input-lg" placeholder="<?php echo $entry_email; ?>">
							<div class="input-group-btn">
								<button type="submit" class="btn btn-newsletter btn-lg"><?php echo $text_button; ?></button>
							</div>
						</div>
					</div>
				</form>
				</div>
			</div>
		</div>
	</div>
</div>