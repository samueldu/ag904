<?php echo $header; ?>
<?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="button" data-toggle="tooltip" title="<?php echo $button_read; ?>" class="btn btn-info" onclick="$('#execute').val('markasread');$('#form').submit();">
					<i class="fa fa-check-square-o"></i>
				</button>
				
                <a href="<?php echo $csvfile; ?>" data-toggle="tooltip" title="<?php echo $button_csv; ?>" class="btn btn-primary">
					<i class="fa fa-download"></i>
				</a>
				
                <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="deleteContact()">
					<i class="fa fa-trash-o"></i>
				</button>
				
                <?php if(isset($found_user_view_all)) { ?>
  					<a href="<?php echo $view_all_ticket; ?>" class="btn btn-primary" data-toggle="tooltip" title="<?php echo $button_view_all; ?>"><i class="fa fa-eye"></i></a>
  				<?php } ?>
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
                <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $heading_title; ?></h3>
            </div>
			
			<div class="panel-body">
				<form action="<?php echo $execute; ?>" method="post" enctype="multipart/form-data" id="form">
					<div class="table-responsive">
						<table class="table table-bordered table-hover">
							<input type="hidden" name="execute" id="execute" />
							<thead>
								<tr>
									<td width="1" style="text-align: center;">
										<input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" />
									</td>
									<td class="text-left"><?php echo $column_name; ?></td>
									<td class="text-left"><?php echo $column_email; ?></td>
									<td class="text-left"><?php echo $column_ip; ?></td>
									<td class="text-right"><?php echo $column_description; ?></td>
                                    <td class="text-right"><?php echo $column_description; ?></td>
									<td class="text-right"><?php echo $column_action; ?></td>
								</tr>
							</thead>
							<tbody>
								<?php if ($contact_info) { ?>
									<?php foreach ($contact_info as $contact) { ?>
										<tr <?php if ($contact['is_read'] == 0) { echo 'class="active"'; } ?>>
											<td class="text-left">
												<input type="checkbox" name="selected[]" value="<?php echo $contact['contact_id']; ?>" />
											</td>
											<td class="text-left">
												<?php 
												if ($contact['is_read'] == 0) { 
													echo '<i class="fa fa-circle"></i>';
												} else {
													echo '<i class="fa fa-circle-thin"></i>';
												} 
												?>
												<?php echo $contact['firstname']; ?>
											</td>
											<td class="text-left"> 
												<?php echo $contact['email'] ?>
											</td>
											<td class="text-left"> 
												<?php echo $contact['ipaddress'] ?>
											</td>
                                            <td class="text-left">
                                                <?php echo $contact['data'] ?>
                                            </td>
											<td class="text-right">
												<?php $contact_message = $contact['enquiry']; $message = substr($contact_message, 0,'80');  echo $message; ?>...
											</td>
											<td class="text-right">  
												<a href="<?php echo $contact['view'] ?>" data-toggle="tooltip" title="<?php echo $button_view; ?>" class="btn btn-info"><i class="fa fa-eye"></i></a>
												<a href="<?php echo $contact['reply'] ?>" data-toggle="tooltip" title="<?php echo $button_reply; ?>" class="btn btn-primary"><i class="fa fa-reply-all"></i></a>
											</td> 
										</tr>
									<?php } ?>		
								<?php } else { ?>
									<tr>
										<td class="text-center" colspan="8"><?php echo $text_no_results; ?></td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					</form>
				</div>
            <div class="row">
                <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
                <div class="col-sm-6 text-right"><?php echo $results; ?></div>
            </div>
			</div>
		</div>
	</div>
	
	<script type="text/javascript">
	function deleteContact() {
		if (confirm("<?php echo $text_confirm; ?>")) {
			$('#execute').val('delete'); 
			$('#form').submit();
		} else {
			return false;
		}
	}
	</script>
<?php echo $footer; ?>