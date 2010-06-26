<div class="sf_admin_form_row">
<?php
//Displays folder path in edit form
$ly_media_folder = $form->getObject();
?>
<div class="info_field_label"><?php echo __('Path'); ?></div>
<div class="info_field_text"><?php echo $ly_media_folder->getPath(); ?></div>
</div>
