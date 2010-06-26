<div id="lymedia_folder_menu">
  <div class="lymedia_folder_name">
    <?php echo image_tag('/lyMediaManagerPlugin/images/folder-open', 'alt=folder title=' . $folder->getName()); ?>
    <?php echo link_to($folder->getName(), 'ly_media_folder_edit', $folder); ?>
  </div>
  <div class="lymedia_folder_actions">
    <ul>
      <li><?php echo link_to(__('Upload file'), '@ly_media_asset_new'); ?></li>
      <li><?php echo link_to(__('Create subfolder'), '@ly_media_folder_new');?></li>
    </ul>
  </div>
</div>