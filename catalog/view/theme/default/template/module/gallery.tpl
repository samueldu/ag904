<div id="gallery<?php echo $module; ?>" class="cookit-gallery">
  <?php foreach ($banners as $banner) { ?>
  <a href="<?php echo $banner['image']; ?>" style="background-image:url('<?php echo $banner['thumb']; ?>')" title="Tauranga Bridge"></a>
  <?php } ?>
</div>

<script type="text/javascript"><!--
$(function(){
    $('#gallery<?php echo $module; ?> a').touchTouch();
});
--></script>
<style>
#gallery<?php echo $module; ?> a{
  background-size: 90% !important;
  background-position: center;
  background-repeat: no-repeat;
  border: 1px solid #00AEEF;


  display: block;
  float: left;
  margin: 2px 4px;
  width: <?php echo $setting['thumb_width']; ?>px;
  height: <?php echo $setting['thumb_height']; ?>px;
  background-size:contain;
}
</style>