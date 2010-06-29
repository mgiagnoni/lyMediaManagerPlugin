<div id="lymedia_folder_menu">
  <div class="lymedia_folder_name">
    <?php echo image_tag('/lyMediaManagerPlugin/images/folder-open', 'alt=folder title=' . $folder->getName()); ?>
    <?php echo link_to($folder->getName(), 'ly_media_folder_edit', $folder); ?>
  </div>
  <div class="lymedia_folder_sort">
    <?php echo __('Sort by:'); ?>&nbsp;
    <?php if($sort_field == 'name'):?>
      <?php  echo $helper->sortIcon($sort_dir, $folder, $popup), __('Name'); ?>
    <?php else: ?>
    <?php echo link_to(__('Name'), '@ly_media_asset_icons?sort=name' . '&folder_id='.$folder->getId() . ($popup ? '&popup=1' : ''), array('title'=>'Sort by name')); ?>
    <?php endif; ?>&nbsp;
    <?php if($sort_field == 'date'):?>
      <?php echo $helper->sortIcon($sort_dir, $folder, $popup), __('Date'); ?>
    <?php else: ?>
    <?php echo link_to(__('Date'), '@ly_media_asset_icons?sort=date' . '&folder_id='.$folder->getId() . ($popup ? '&popup=1' : ''), array('title'=>'Sort by date')); ?>
    <?php endif; ?>
  </div>
  <div class="lymedia_folder_actions">
    <ul>
      <li><?php echo link_to(__('Upload file'), '@ly_media_asset_new'); ?></li>
      <li><?php echo link_to(__('Create subfolder'), '@ly_media_folder_new');?></li>
    </ul>
  </div>
</div>