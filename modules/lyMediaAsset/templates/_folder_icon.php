<div class="lymedia_folder">
  <div class="lymedia_folder_frame">
    <?php echo link_to(image_tag('/lyMediaManagerPlugin/images/folder', 'alt=folder title='.$folder->getName()), '@ly_media_asset_icons?folder_id=' . $folder->getId() . ($popup ? '&popup=1' : '')); ?>
  </div>
  <div class="lymedia_caption">
    <?php echo $folder->getName(); ?>
  </div>
  <div class="lymedia_iconbar">
    <ul class="lymedia_actions_icons">
      <li class="edit">
        <?php echo link_to(image_tag('/lyMediaManagerPlugin/images/edit', 'alt=edit'), 'ly_media_folder_edit', $folder, array('title' => 'edit folder'));?>
      </li>
      <li class="delete">
        <?php echo link_to(image_tag('/lyMediaManagerPlugin/images/delete', 'alt=delete'), 'ly_media_folder_delete', $folder, array('method' => 'delete', 'confirm' => 'Are you sure?', 'title' => 'delete folder')) ?>
      </li>
    </ul>
  </div>
</div>