<?php use_helper('I18N', 'Date') ?>
<?php include_partial('lyMediaFolder/assets') ?>

<div id="sf_admin_container">
  <h1><?php echo __('Folders', array(), 'messages') ?></h1>

  <?php include_partial('lyMediaFolder/flashes') ?>

  <div id="sf_admin_header">
    <?php include_partial('lyMediaFolder/list_header', array('pager' => $pager)) ?>
  </div>
  <?php include_partial('lyMediaAsset/folder_path', array('folder' => null, 'popup' => 0)); ?>

  <div id="sf_admin_bar">
    <?php include_partial('lyMediaFolder/filters', array('form' => $filters, 'configuration' => $configuration)) ?>
  </div>

  <div id="sf_admin_content">
    <form action="<?php echo url_for('ly_media_folder_collection', array('action' => 'batch')) ?>" method="post">
    <?php include_partial('lyMediaFolder/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper)) ?>
    <ul class="sf_admin_actions">
      <?php include_partial('lyMediaFolder/list_batch_actions', array('helper' => $helper)) ?>
      <?php include_partial('lyMediaFolder/list_actions', array('helper' => $helper)) ?>
    </ul>
    </form>
  </div>

  <div id="sf_admin_footer">
    <?php include_partial('lyMediaFolder/list_footer', array('pager' => $pager)) ?>
  </div>
</div>
