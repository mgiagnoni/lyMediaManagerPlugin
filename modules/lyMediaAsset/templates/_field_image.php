<?php
//Displays asset image in edit form
$ly_media_asset = $form->getObject();
?>
<div class="sf_admin_form_row">
  <div class="lymedia_image_field">
    <?php echo thumbnail_image_tag($ly_media_asset, null, 'medium', 'alt=' . $ly_media_asset->getTitle());?>
  </div>
  <div class="lymedia_image_info">
    <div class="info_field_label mime-type"><?php echo __('Mime type'); ?></div>
    <div class="info_field_text mime-type"><?php echo $ly_media_asset->getType(); ?></div>
    <div class="info_field_label date"><?php echo __('Date'); ?></div>
    <div class="info_field_text date"><?php echo format_date($ly_media_asset->getCreatedAt(), 'd MMMM, yyyy') ?></div>
  </div>
</div>