<?php echo $header; ?>
<?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
                <button type="button" data-toggle="tooltip" title="<?php echo $button_send; ?>" class="btn btn-primary" onclick="$('#form').submit();">
					<i class="fa fa-envelope"></i>
				</button>
				<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default">
					<i class="fa fa-reply"></i>
				</a>
			</div>
			<h1><?php echo $heading_title; ?></h1>
			<ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
					<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
					<?php } ?>
				</ul>
			</div>
		</div>
		
	    <div class="container-fluid">
	        <?php if ($error) { ?>
	            <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error; ?>
	                <button type="button" class="close" data-dismiss="alert">&times;</button>
	            </div>
	        <?php } ?>
        
	        <?php if ($success) { ?>
	            <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
	                <button type="button" class="close" data-dismiss="alert">&times;</button>
	            </div>
	        <?php } ?>
			
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-envelope"></i> <?php echo $heading_title; ?></h3>
				</div>
			
	        	<div class="panel-body">
		        	<form action="<?php echo $send; ?>" method="post" enctype="multipart/form-data" id="form" class="form-horizontal">
						<?php if ($single_data) { ?>
							<?php foreach ($single_data as $contact) { ?>
						
								<div class="form-group required">
									<label class="col-sm-2 control-label" for="name"><?php echo $column_name; ?></label>
									<div class="col-sm-10">
										<input type="text" name="name" id="name" placeholder="<?php echo $column_name; ?>" value="<?php echo $contact['firstname']; ?>" class="form-control" />
									</div>
								</div>
						
								<div class="form-group required">
									<label class="col-sm-2 control-label" for="email"><?php echo $column_email; ?></label>
									<div class="col-sm-10">
										<input type="text" name="email" id="email" placeholder="<?php echo $column_email; ?>" value="<?php echo $contact['email'] ?>" class="form-control" />
									</div>
								</div>
						
								<div class="form-group required">
									<label class="col-sm-2 control-label" for="message"><?php echo $column_description; ?></label>
									<div class="col-sm-10">
										<textarea name="enquiry" id="message" placeholder="<?php echo $column_description; ?>" class="form-control"></textarea>
									</div>
								</div>
							<?php } ?>		
						<?php } else { ?>
							<div class="text-center">
								<?php echo $text_no_results; ?>
							</div>
						<?php } ?>
					</form>
	        	</div>
			</div>
		</div>
	</div>
<?php echo $footer; ?>
<script type="text/javascript">
$('#message').summernote({
	height: 300
});
</script> 