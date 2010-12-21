<?php
echo link_to(
  thumbnail_image_tag($ly_media_asset, null, 'mini', 'alt=' . $ly_media_asset->getPath() . ' title=' . $ly_media_asset->getPath()),
  'ly_media_asset_edit', $ly_media_asset);
?>
