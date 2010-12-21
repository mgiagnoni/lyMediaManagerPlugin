<div id="lymedia_folder_path">
  <?php if(!$popup): ?>
    <span class="view">
      <?php if($sf_user->getAttribute('view') == 'list'):?>
        <?php echo __('media'); ?>
      <?php else: ?>
        <?php echo link_to(__('media'), '@ly_media_asset'); ?>
      <?php endif; ?> |
      <?php if($sf_user->getAttribute('view') == 'folder'):?>
        <?php echo __('folders'); ?>
      <?php else: ?>
        <?php echo link_to(__('folders'), '@ly_media_folder'); ?>
      <?php endif; ?> |
      <?php if($sf_user->getAttribute('view') == 'icons'):?>
        <?php echo __('browse'); ?>
      <?php else: ?>
        <?php echo link_to(__('browse'), '@ly_media_asset_icons'); ?>
      <?php endif; ?>
    </span>
  <?php endif; ?>
  <?php if(isset($folder)): ?> 
    <?php echo path_links($folder, $popup); ?>
  <?php endif; ?>
  &nbsp;
</div>
