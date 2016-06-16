<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-user-group" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-user-group" class="form-horizontal">
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name; ?></label>
            <div class="col-sm-10">
              <input type="text" name="name" value="<?php echo $name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
              <?php if ($error_name) { ?>
              <div class="text-danger"><?php echo $error_name; ?></div>
              <?php  } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_access; ?></label>
            <div class="col-sm-10">
              <div class="well well-sm" style="height: 150px; overflow: auto;">
                <?php foreach ($permissions as $permission) { ?>
                <div class="checkbox">
                  <label>
                    <?php if (in_array($permission, $access)) { ?>
                    <input type="checkbox" name="permission[access][]" id="ace_<?php echo str_replace("/","_",$permission); ?>" value="<?php echo $permission; ?>" checked="checked" />
                    <?php echo $permission; ?>
                    <?php } else { ?>
                    <input type="checkbox" name="permission[access][]" id="ace_<?php echo str_replace("/","_",$permission); ?>" value="<?php echo $permission; ?>" />
                    <?php echo $permission; ?>
                    <?php } ?>
                  </label>
                </div>
                <?php } ?>
              </div>
              <a onclick="$(this).parent().find(':checkbox').prop('checked', true);"><?php echo $text_select_all; ?></a> / <a onclick="$(this).parent().find(':checkbox').prop('checked', false);"><?php echo $text_unselect_all; ?></a></div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_modify; ?></label>
            <div class="col-sm-10">
              <div class="well well-sm" style="height: 150px; overflow: auto;">
                <?php foreach ($permissions as $permission) { ?>
                <div class="checkbox">
                  <label>
                    <?php if (in_array($permission, $modify)) { ?>
                    <input type="checkbox" name="permission[modify][]" id="mod_<?php echo str_replace("/","_",$permission); ?>" value="<?php echo $permission; ?>" checked="checked" />
                    <?php echo $permission; ?>
                    <?php } else { ?>
                    <input type="checkbox" name="permission[modify][]" id="mod_<?php echo str_replace("/","_",$permission); ?>" value="<?php echo $permission; ?>" />
                    <?php echo $permission; ?>
                    <?php } ?>
                  </label>
                </div>
                <?php } ?>
              </div>
              <a onclick="$(this).parent().find(':checkbox').prop('checked', true);"><?php echo $text_select_all; ?></a> / <a onclick="$(this).parent().find(':checkbox').prop('checked', false);"><?php echo $text_unselect_all; ?></a></div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_access; ?></label>
            <div class="col-sm-10">
              <div class="well well-sm" style="height: 150px; overflow: auto;">
                <div class="checkbox">
                  <label>
                    <a href="javascript:ativar('basic');">Basico</a><BR>
                    <a href="javascript:ativar('portfolio');">Portfolio</a><BR>
                    <a href="javascript:ativar('blog');">Blog</a><BR>
                    <a href="javascript:ativar('ecommerce');">E-commerce</a><BR>
                    <a href="javascript:ativar('marketing');">Marketing</a><BR>
                    <a href="javascript:ativar('reports');">Relatorios</a><BR>
                    <a href="javascript:ativar('extensions');">Extensoes</a><BR>
                    <a href="javascript:ativar('desing');">Desing</a><BR>
                    <a href="javascript:ativar('users');">Usuários</a><BR>
                 </label>
                </div>
              </div>
             </div>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>

<script>
function ativar(item) {

  var checks = ['mod','ace'];

  if(item == "basic")
  {
    var inputs = [
      'catalog_information',
      'catalog_download',
      'common_column_left',
      'common_content',
      'common_filemanager',
      'common_menu',
      'common_profile',
      'common_stats',
      'module_contact',
            'setting_setting',
      'setting_store'
    ];

  }

  else if(item=='portfolio') {

    var inputs = [
      'portfolio_article',
      'portfolio_category',
      'portfolio_author',
      'portfolio_comment',
      'portfolio_install',
      'portfolio_report',
      'module_portfolio',
      'module_portfolio_category'];
  }
  else if (item == 'blog')
  {
    var inputs = [
    'module_simple_blog',
    'module_simple_blog_category',
    'simple_blog_article',
    'simple_blog_author',
    'simple_blog_category',
    'simple_blog_comment',
    'simple_blog_install',
    'simple_blog_report'];
  }
  else if( item == "ecommerce") {

    var inputs = [

    'catalog_attribute',
    'catalog_attribute_group',
    'catalog_category',
    'catalog_filter',
    'catalog_manufacturer',
    'catalog_option',
    'catalog_product',
    'catalog_recurring',
    'catalog_review',
    'extension_feed',
    'extension_fraud',
    'extension_installer',
    'extension_modification',
    'extension_module',
    'extension_news',
    'extension_openbay',
    'extension_payment',
    'extension_shipping',
    'extension_total',
    'feed_google_base',
    'feed_google_sitemap',
    'feed_openbaypro',
    'fraud_fraudlabspro',
    'fraud_maxmind',
    'localisation_country',
    'localisation_currency',
    'localisation_geo_zone',
    'localisation_language',
    'localisation_length_class',
    'localisation_location',
    'localisation_order_status',
    'localisation_return_action',
    'localisation_return_reason',
    'localisation_return_status',
    'localisation_stock_status',
    'localisation_tax_class',
    'localisation_tax_rate',
    'localisation_weight_class',
    'localisation_zone',
    'marketing_affiliate',
    'marketing_contact',
    'marketing_coupon',
    'marketing_marketing',
    'module_account',
    'module_affiliate',
    'module_amazon_button',
    'module_amazon_login',
    'module_amazon_pay',
    'module_banner',
    'module_bestseller',
    'module_carousel',
    'module_category',
    'module_contact',
    'module_ebay_listing',
    'module_ebaydisplay',
    'module_featured',
    'module_filter',
    'module_gallery',
    'module_google_hangouts',
    'module_html',
    'module_information',
    'module_latest',
    'module_news',
    'module_pp_button',
    'module_pp_login',
    'module_slideshow',
    'module_special',
    'module_store',
    'module_vqmod_manager',
    'openbay_amazon',
    'openbay_amazon_listing',
    'openbay_amazon_product',
    'openbay_amazonus',
    'openbay_amazonus_listing',
    'openbay_amazonus_product',
    'openbay_ebay',
    'openbay_ebay_profile',
    'openbay_ebay_template',
    'openbay_etsy',
    'openbay_etsy_product',
    'openbay_etsy_shipping',
    'openbay_etsy_shop',
    'payment_amazon_checkout',
    'payment_amazon_login_pay',
    'payment_authorizenet_aim',
    'payment_authorizenet_sim',
    'payment_bank_transfer',
    'payment_bluepay_hosted',
    'payment_bluepay_redirect',
    'payment_cheque',
    'payment_cod',
    'payment_firstdata',
    'payment_firstdata_remote',
    'payment_free_checkout',
    'payment_g2apay',
    'payment_globalpay',
    'payment_globalpay_remote',
    'payment_klarna_account',
    'payment_klarna_invoice',
    'payment_liqpay',
    'payment_nochex',
    'payment_paymate',
    'payment_paypoint',
    'payment_payza',
    'payment_perpetual_payments',
    'payment_pp_express',
    'payment_pp_payflow',
    'payment_pp_payflow_iframe',
    'payment_pp_pro',
    'payment_pp_pro_iframe',
    'payment_pp_standard',
    'payment_realex',
    'payment_realex_remote',
    'payment_sagepay_direct',
    'payment_sagepay_server',
    'payment_sagepay_us',
    'payment_securetrading_pp',
    'payment_securetrading_ws',
    'payment_skrill',
    'payment_twocheckout',
    'payment_web_payment_software',
    'payment_worldpay',
    'report_affiliate',
    'report_affiliate_activity',
    'report_affiliate_login',
    'report_customer_activity',
    'report_customer_credit',
    'report_customer_login',
    'report_customer_online',
    'report_customer_order',
    'report_customer_reward',
    'report_marketing',
    'report_product_purchased',
    'report_product_viewed',
    'report_sale_coupon',
    'report_sale_order',
    'report_sale_return',
    'report_sale_shipping',
    'report_sale_tax',
    'sale_custom_field',
    'sale_customer',
    'sale_customer_ban_ip',
    'sale_customer_group',
    'sale_order',
    'sale_recurring',
    'sale_return',
    'sale_voucher',
    'sale_voucher_theme',
    'setting_client',
    'setting_setting',
    'setting_store',
    'shipping_auspost',
    'shipping_citylink',
    'shipping_fedex',
    'shipping_flat',
    'shipping_free',
    'shipping_item',
    'shipping_parcelforce_48',
    'shipping_pickup',
    'shipping_royal_mail',
    'shipping_ups',
    'shipping_usps',
    'shipping_weight',
    'tool_backup',
    'tool_error_log',
    'tool_upload',
    'total_coupon',
    'total_credit',
    'total_handling',
    'total_klarna_fee',
    'total_low_order_fee',
    'total_reward',
    'total_shipping',
    'total_sub_total',
    'total_tax',
    'total_total',
    'total_voucher'];
  }
  else if(item == "extensions")
  {
    var inputs = [
      'extension_feed',
      'extension_fraud',
      'extension_installer',
      'extension_modification',
      'extension_module',
      'extension_news',
      'extension_openbay',
      'extension_payment',
      'extension_shipping',
      'extension_total'];

  }
  else if(item == "marketing")
  {
    var inputs = [
      'marketing_affiliate',
      'marketing_contact',
      'marketing_coupon',
      'marketing_marketing'];

  }
  else if(item == "reports")
  {
    var inputs = [
      'report_affiliate',
      'report_affiliate_activity',
      'report_affiliate_login',
      'report_customer_activity',
      'report_customer_credit',
      'report_customer_login',
      'report_customer_online',
      'report_customer_order',
      'report_customer_reward',
      'report_marketing',
      'report_product_purchased',
      'report_product_viewed',
      'report_sale_coupon',
      'report_sale_order',
      'report_sale_return',
      'report_sale_shipping',
      'report_sale_tax'];
  }
  else if(item == "desing")
  {
    var inputs = [
      'design_banner',
      'design_layout'];
  }
  else if(item == "users")
  {
    var inputs = [
      'user_api',
      'user_user',
      'user_user_permission'];
  }

  /*

   'common_column_left',
   'common_content',
   'common_filemanager',
   'common_menu',
   'common_profile',
   'common_stats',
   'catalog_information',
   'catalog_download',

  */

  for	(indexChecks = 0; indexChecks < checks.length; indexChecks++) {
    for (index = 0; index < inputs.length; index++) {
      if ($('#'+checks[indexChecks]+'_' + inputs[index]).is(':checked')) {
        $('#'+checks[indexChecks]+'_' + inputs[index]).prop('checked', false);
      } else {
        $('#'+checks[indexChecks]+'_' + inputs[index]).prop('checked', true);
      }
    }
  }
}
</script>

<?php echo $footer; ?> 