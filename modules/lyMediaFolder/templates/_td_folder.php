<?php
echo link_to(
  image_tag('/lyMediaManagerPlugin/images/folder_small.png', array('class' => 'level' . $ly_media_folder->getLevel())),
  'ly_media_asset_icons', array('folder_id' => $ly_media_folder->getId()), array('title' => __('browse "%folder%"', array('%folder%'=> $ly_media_folder->getRelativePath()))));
