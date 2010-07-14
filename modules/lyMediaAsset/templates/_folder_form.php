<form action="<?php echo url_for('@ly_media_folder_collection?action=add')?>" method="post">
  <?php echo $form['name']->renderLabel(); ?>
  <?php echo image_tag('/lyMediaManagerPlugin/images/folder-new', 'alt=new folder'); ?>
  <?php echo $form['name']->render(); ?>
  <input type="submit" value="Create" />
  <?php echo $form->renderHiddenFields(); ?>
</form>
