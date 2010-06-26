<div class="sf_admin_form_row">
<?php
//Displays asset path in edit form
$ly_media_asset = $form->getObject();
?>
<div class="info_field_label"><?php echo __('Path'); ?></div>
<div class="info_field_text"><?php echo $ly_media_asset->getPath(); ?></div>
</div>
