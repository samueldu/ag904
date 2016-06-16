<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<button type="submit" form="form-item" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
				<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
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
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo "TNT"; ?></h3>
			</div>
			<div class="panel-body">
				<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-item" class="form-horizontal">

	    <table class="form">
			<tr>
			  <h3><span class="required">*</span> <?php echo $text_aviso; ?></h3>
			  <hr/>	        
	      </tr>
	      
	      <tr>
	        <td><span class="required">*</span> <?php echo $entry_user_login; ?><br />
	          </td>
	        <td><input name="tnt_user_login" type="text" id="tnt_user_login" value="<?php echo $tnt_user_login; ?>" />
	         <?php if ($error_user_login) { ?>
	         <span class="error"><?php echo $error_user_login; ?></span>
	         <?php  } ?>
	        </td>
	      </tr>

	      <tr>
	        <td><span class="required">*</span> <?php echo $entry_ie; ?><br />
	          </td>
	        <td><input name="tnt_ie" type="text" id="tnt_ie" value="<?php echo $tnt_ie; ?>" />
	         <?php if ($error_ie) { ?>
	         <span class="error"><?php echo $error_ie; ?></span>
	         <?php  } ?>
	        </td>
	      </tr>

	       <tr>
	        <td><span class="required">*</span> <?php echo $entry_cnpj; ?><br />
	          </td>
	        <td><input name="tnt_cnpj" type="text" id="tnt_cnpj" value="<?php echo $tnt_cnpj; ?>" />
	         <?php if ($error_cnpj) { ?>
	         <span class="error"><?php echo $error_cnpj; ?></span>
	         <?php  } ?>
	        </td>
	      </tr>

	      <tr>
	        <td><span class="required">*</span> <?php echo $entry_situacao_trib; ?><br />
	          </td>
	        <td><input name="tnt_situacao_trib" type="text" id="tnt_situacao_trib" value="<?php echo $tnt_situacao_trib; ?>" />
	         <?php if ($error_situacao_trib) { ?>
	         <span class="error"><?php echo $error_situacao_trib; ?></span>
	         <?php  } ?>
	        </td>
	      </tr>

	      <tr>
	        <td><span class="required">*</span> <?php echo $entry_tipo_pessoa; ?><br />
	          </td>
	        <td><input name="tnt_tipo_pessoa" type="text" id="tnt_tipo_pessoa" value="<?php echo $tnt_tipo_pessoa; ?>" />
	         <?php if ($error_tipo_pessoa) { ?>
	         <span class="error"><?php echo $error_tipo_pessoa; ?></span>
	         <?php  } ?>
	        </td>
	      </tr>

	      <tr>
	        <td><span class="required">*</span> <?php echo $entry_postcode; ?><br />
	          </td>
	        <td><input name="tnt_postcode" type="text" id="tnt_postcode" value="<?php echo $tnt_postcode; ?>" />
	         <?php if ($error_postcode) { ?>
	         <span class="error"><?php echo $error_postcode; ?></span>
	         <?php  } ?>
	        </td>
	      </tr>

	      <tr>
	        <td><span class="required">*</span> <?php echo $entry_tipo_servico; ?><br />
	          </td>
	        <td><input name="tnt_tipo_servico" type="text" id="tnt_tipo_servico" value="<?php echo $tnt_tipo_servico; ?>" />
	         <?php if ($error_tipo_servico) { ?>
	         <span class="error"><?php echo $error_tipo_servico; ?></span>
	         <?php  } ?>
	        </td>
	      </tr>

	      <tr>
	        <td><span class="required">*</span> <?php echo $entry_tipo_frete; ?><br />
	          </td>
	        <td><input name="tnt_tipo_frete" type="text" id="tnt_tipo_frete" value="<?php echo $tnt_tipo_frete; ?>" />
	         <?php if ($error_tipo_frete) { ?>
	         <span class="error"><?php echo $error_tipo_frete; ?></span>
	         <?php  } ?>
	        </td>
	      </tr>
	      
	      <tr>
	        <td><span class="required">*</span> <?php echo $entry_divisao_cliente; ?><br />
	          </td>
	        <td><input name="tnt_divisao_cliente" type="text" id="tnt_divisao_cliente" value="<?php echo $tnt_divisao_cliente; ?>" />
	         <?php if ($error_divisao_cliente) { ?>
	         <span class="error"><?php echo $error_divisao_cliente; ?></span>
	         <?php  } ?>
	        </td>
	      </tr>

          <tr>
            <td><?php echo $entry_servicos; ?></td>
            <td>
	        <?php if ($error_servico) { ?>
	        <span class="error"><?php echo $error_servico; ?></span>
	        <?php  } ?>              
            <div class="scrollbox">
                <?php $class = 'odd'; ?>
                <div class="even">
                  <?php if ($tnt_padrao) { ?>
                  <input type="checkbox" name="tnt_padrao" value="true" checked="checked" />
                  <?php echo $text_tnt_padrao; ?>
                  <?php } else { ?>
                  <input type="checkbox" name="tnt_padrao" value="true" />
                  <?php echo $text_tnt_padrao; ?>
                  <?php } ?>
                </div>
                
          	</div>
         	</td>
          </tr>	      
	      
	     
	      <tr>
	        <td><?php echo $entry_adicional; ?><br />
	          </td>
	        <td><input name="tnt_adicional" type="text" id="tnt_adicional" value="<?php echo $tnt_adicional; ?>" />
	        </td>
	      </tr> 
	      
	      <tr>
	        <td><?php echo $entry_prazo_adicional; ?><br />
	          </td>
	        <td><input name="tnt_prazo_adicional" type="text" id="tnt_prazo_adicional" value="<?php echo $tnt_prazo_adicional; ?>" />
	        </td>
	      </tr> 	                            
	      
	      
	      <tr>
	        <td><?php echo $entry_geo_zone; ?></td>
	        <td><select name="tnt_geo_zone_id">
	            <option value="0"><?php echo $text_all_zones; ?></option>
	            <?php foreach ($geo_zones as $geo_zone) { ?>
	            <?php if ($geo_zone['geo_zone_id'] == $tnt_geo_zone_id) { ?>
	            <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
	            <?php } else { ?>
	            <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
	            <?php } ?>
	            <?php } ?>
	          </select></td>
	      </tr>
	      <tr>
	        <td width="25%"><?php echo $entry_status; ?></td>
	        <td><select name="tnt_status">
	            <?php if ($tnt_status) { ?>
	            <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
	            <option value="0"><?php echo $text_disabled; ?></option>
	            <?php } else { ?>
	            <option value="1"><?php echo $text_enabled; ?></option>
	            <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
	            <?php } ?>
	          </select></td>
	      </tr>
	      <tr>
	        <td><?php echo $entry_sort_order; ?></td>
	        <td><input type="text" name="tnt_sort_order" value="<?php echo $tnt_sort_order; ?>" size="1" /></td>
	      </tr>
	    </table>
	  </form>
    </div>
  </div>
</div>
</div>
<?php echo $footer; ?>
