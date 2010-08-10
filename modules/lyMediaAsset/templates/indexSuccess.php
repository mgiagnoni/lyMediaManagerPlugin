<?php use_helper('I18N','Date') ?>

<?php include_partial('lyMediaAsset/assets') ?>

<div id="sf_admin_container">
  <h1><?php echo __($configuration->getListTitle(), array(), 'messages') ?></h1>

  <?php include_partial('lyMediaAsset/flashes') ?>

  <div id="sf_admin_header">
    <?php include_partial('lyMediaAsset/list_header', array('pager' => $pager)) ?>
  </div>
  <?php include_partial('lyMediaAsset/folder_path', array('folder' => null, 'popup' => 0)); ?>
  <?php if ($configuration->hasFilterForm()): ?>
    <div id="sf_admin_bar">
      <?php include_partial('lyMediaAsset/filters', array('form' => $filters, 'configuration' => $configuration)) ?>
    </div>
  <?php endif; ?>

  <div id="sf_admin_content">
    <form action="<?php echo url_for('ly_media_asset_collection', array('action' => 'batch')) ?>" method="post">
    <?php include_partial('lyMediaAsset/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper)) ?>
    <ul class="sf_admin_actions">
      <?php include_partial('lyMediaAsset/list_batch_actions', array('helper' => $helper)) ?>
      <?php include_partial('lyMediaAsset/list_actions', array('helper' => $helper)) ?>
    </ul>
    </form>
  </div>

  <div id="sf_admin_footer">
    <?php include_partial('lyMediaAsset/list_footer', array('pager' => $pager)) ?>
  </div>
</div>