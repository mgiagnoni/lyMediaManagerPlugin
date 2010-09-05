<div id="lymedia_thumb_menu">
  <div class="title">
    <?php echo __('Choose size'); ?>
  </div>
  <span class="_original"><?php echo __('original'); ?></span>
  <?php foreach(lyMediaThumbnails::getThumbnailSettings() as $type => $params):?>
  <span class="<?php echo $type;?>"><?php echo __($type) . ' ('. $params['width'] . 'x' . $params['height'] . ')'; ?></span>
  <?php endforeach; ?>
  <span class="_cancel"><?php echo __('Cancel'); ?></span>
</div>
