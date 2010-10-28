<div id="lymedia_folder_menu<?php if($hide) {echo '_hidden';} ?>">
  <span class="lymedia_folder_hide"><?php echo link_to($hide == 1 ? __('Show') : __('Hide'), '@ly_media_asset_icons?hide=' . ($hide == 1 ? 0:1) . ($popup ? '&popup=1' : ''), array('title' => $hide == 1 ? __('Show folder menu') : __('Hide folder menu'))) ?></span>
  <div class="lymedia_folder_name">
    <?php echo image_tag('/lyMediaManagerPlugin/images/folder-open', 'alt=folder title=' . $folder->getName()); ?>
    <?php echo link_to_if($folder->level > 0, $folder->getName(), 'ly_media_folder_edit', $folder); ?>
    <div class="lymedia_folder_stats">
      <?php if($nbfiles > 0): ?>
        <span class="files">
          <?php echo format_number_choice('[1]1 file|(1,+Inf]%1% files', array('%1%' => $nbfiles), $nbfiles) ?>
        </span>
        <span class="size">
          <?php echo ' ('. $total_size . ' kB) '; ?>
        </span>
      <?php endif; ?>
      <?php if($nbfolders > 0): ?>
        <span class="folders">
          <?php echo format_number_choice('[1]1 folder|(1,+Inf]%1% folders', array('%1%' => $nbfolders), $nbfolders) ?>
        </span>
      <?php endif; ?>
    </div>
  </div>
  <div class="lymedia_folder_sort">
    <?php echo __('Sort by:'); ?>&nbsp;
    <?php if($sort_field == 'name'):?>
      <?php  echo $helper->sortIcon($sort_dir, $popup), __('Name'); ?>
    <?php else: ?>
    <?php echo link_to(__('Name'), '@ly_media_asset_icons?sort=name' . ($popup ? '&popup=1' : ''), array('title'=>'Sort by name')); ?>
    <?php endif; ?>&nbsp;
    <?php if($sort_field == 'date'):?>
      <?php echo $helper->sortIcon($sort_dir, $popup), __('Date'); ?>
    <?php else: ?>
    <?php echo link_to(__('Date'), '@ly_media_asset_icons?sort=date' . ($popup ? '&popup=1' : ''), array('title'=>'Sort by date')); ?>
    <?php endif; ?>
  </div>
  <div class="lymedia_folder_actions">
    <?php include_partial('lyMediaAsset/folder_form', array('form' => $folder_form));?>
    <?php include_partial('lyMediaAsset/upload_form', array('form' => $upload_form));?>
  </div>
</div>