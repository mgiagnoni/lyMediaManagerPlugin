<div id="lymedia_folder_path">
  <?php if(!$popup): ?>
    <span class="view">
      <?php if($sf_user->getAttribute('view') == 'icons'):?>
        <?php echo link_to(__('list'), '@ly_media_asset'); ?>
      <?php else: ?>
        <?php echo __('list'); ?>
      <?php endif; ?> |
      <?php if($sf_user->getAttribute('view') == 'list'):?>
        <?php echo link_to(__('icons'), '@ly_media_asset_icons'); ?>
      <?php else: ?>
        <?php echo __('icons'); ?>
      <?php endif; ?>
    </span>
  <?php endif; ?>
  <?php if(isset($folder)): ?> 
    <?php echo path_links($folder, $popup); ?>
  <?php endif; ?>
  &nbsp;
</div>
