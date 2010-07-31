<div class="lymedia_asset">
  <div class="lymedia_asset_frame">
    <?php if($popup): ?>
    <div class="lymedia_popup_info">
      <span>
        <?php echo image_path(lyMediaTools::getAssetURI($asset)); ?>
      </span>
      <?php foreach(lyMediaTools::getThumbnailSettings() as $type => $params): ?>
        <span class="<?php echo $type; ?>">
          <?php echo thumbnail_image_path($asset, $folder->getRelativePath(), $type); ?>
        </span>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <?php echo thumbnail_image_tag($asset, $folder->getRelativePath(), 'small', 'alt=asset title=' . $asset->getTitle()); ?>
  </div>
  <div class="lymedia_caption">
    <?php echo lyMediaTools::formatAssetCaption($asset); ?>
  </div>
  <div class="lymedia_iconbar">
    <ul class="lymedia_actions_icons">
      <li class="edit">
        <?php echo link_to(image_tag('/lyMediaManagerPlugin/images/edit', 'alt=edit'), 'ly_media_asset_edit', $asset, array('title' => 'edit asset'));?>
      </li>
      <li class="delete">
        <?php echo link_to(image_tag('/lyMediaManagerPlugin/images/delete', 'alt=delete'), 'ly_media_asset_delete', $asset, array('method' => 'delete', 'confirm' => 'Are you sure?', 'title' => 'delete asset')) ?>
      </li>
    </ul>
  </div>
</div>
