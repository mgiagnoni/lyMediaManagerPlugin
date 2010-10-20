<?php use_helper('I18N') ?>
<?php include_partial('lyMediaAsset/assets') ?>
<div id="sf_admin_container">
  <?php include_partial('lyMediaAsset/flashes') ?>
  <?php include_partial('lyMediaAsset/folder_path', array('folder' => $folder, 'popup' => $popup)); ?>

  <div id="sf_admin_content">
    <div id="lymedia_icons">
      <?php include_partial('lyMediaAsset/folder_menu', array(
          'folder' => $folder,
          'sort_field' => $sort_field,
          'sort_dir' => $sort_dir,
          'popup' => $popup,
          'helper' => $helper,
          'folder_form' => $folder_form,
          'upload_form' => $upload_form,
          'nbfiles' => $pager->getnbResults(),
          'nbfolders' => $nbfolders,
          'total_size' => $total_size,
          'hide' => $hide
        )); ?>
      <div class="popup_help"><?php if($popup) { echo __('Click an image to select it.'); } ?></div>
      <?php include_partial('lyMediaAsset/folder_icon_up', array('folder' => $folder, 'popup' => $popup)); ?>
      <?php if($folders): ?>
        <?php foreach($folders as $f): ?>
          <?php include_partial('lyMediaAsset/folder_icon', array('folder' => $f, 'popup' => $popup)); ?>
        <?php endforeach; ?>
      <?php endif; ?>
      <?php foreach($pager->getResults() as $a): ?>
        <?php include_partial('lyMediaAsset/asset_icon', array('asset' => $a, 'folder' => $folder, 'popup' => $popup)); ?>
      <?php endforeach; ?>
      <?php if($pager->haveToPaginate()): ?>
        <?php include_partial('lyMediaAsset/pagination_icons', array('pager' => $pager, 'popup' => $popup)); ?>
      <?php endif; ?>
      <?php if($popup) { include_partial('lyMediaAsset/popup_menu'); } ?>
      <div class="clear"></div>
    </div>
  </div>
</div>