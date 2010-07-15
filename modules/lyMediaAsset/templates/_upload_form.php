<form action="<?php echo url_for('@ly_media_asset_collection?action=upload')?>" method="post" enctype="multipart/form-data">
  <?php echo $form['filename']->renderLabel(); ?>
  <?php echo image_tag('/lyMediaManagerPlugin/images/upload', 'alt=upload'); ?>
  <?php echo $form['filename']->render(); ?>
  <input class="upload" type="submit" value="Upload" />
  <?php echo $form->renderHiddenFields(); ?>
</form>
