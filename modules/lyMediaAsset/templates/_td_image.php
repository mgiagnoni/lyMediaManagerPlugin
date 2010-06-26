<?php
echo link_to(
  thumbnail_image_tag($ly_media_asset, 'small', 'alt=' . $ly_media_asset->getFilename() . ' title=' . $ly_media_asset->getFilename()),
  'ly_media_asset_edit', $ly_media_asset);
?>
