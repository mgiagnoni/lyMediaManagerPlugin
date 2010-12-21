<?php $ct = count($ly_media_folder->getAssets());?>
<?php echo $ct ?>&nbsp;
(<?php echo link_to(__('show'), '@ly_media_asset?folder_id=' . $ly_media_folder->getId()); ?>)
