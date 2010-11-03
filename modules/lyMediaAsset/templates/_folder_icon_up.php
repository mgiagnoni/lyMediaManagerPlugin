<?php if($folder->getLevel() > 0): ?>
<div class="lymedia_up">
  <?php echo link_to(image_tag('/lyMediaManagerPlugin/images/go-up', 'alt=up title=up'), '@ly_media_asset_icons?folder_id=' . $folder->getNode()->getParent()->getId() . ($popup ? '&popup=1' : '')); ?>
</div>
<?php endif; ?>
