<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
        <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-store').submit() : false;"><i class="fa fa-trash-o"></i></button>
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
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
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
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-store">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-left"></td>
                  <td class="text-left"><?php echo $column_name; ?></td>
                    <td class="text-left"><?php echo $column_install; ?></td>
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($stores) { ?>
                <?php foreach ($stores as $store) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($store['client_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $store['client_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $store['client_id']; ?>" />
                    <?php } ?></td>
                    <Td><img src="<?php echo $store['logo']; ?>"></Td>
                  <td class="text-left"><?php echo $store['name']; ?></td>
                    <td class="text-left">
                        <?php
                        /*
                        if($store['install']==0)
                        {
                        ?>
                        <a href="javascript:void(0);" onclick="javascript:install('<?php echo $store['client_id']; ?>','0');"><?php echo $column_not_installed; ?></a>
                        <?php
                        }
                        else
                        {
                        ?>
                        <?php echo $column_installed; ?>
                        <?php
                            }
                            */

                        ?>
                      <?php echo $column_installed; ?>
                    </td>
                    <td class="text-right"><a href="<?php echo $store['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="4"><?php echo $text_no_results; ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>

<Script>

function install(client,store)
$.ajax({
url: 'index.php?route=setting/client/install&token=<?php echo $_GET['token']; ?>',
type: 'post',
data: 'client=' + client+'&store='+store+'&token=<?php echo $_GET['token']; ?>',
dataType: 'json',
beforeSend: function() {

},
complete: function() {

},
success: function(json) {
    alert(json['total']);
setTimeout(function () {
$('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] + '</span>');
}, 100);
}
});

    </script>