<?php
/*
 * This file is part of the lyMediaManagerPlugin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * lyMediaHelper class.
 *
 * @package     lyMediaManagerPlugin
 * @subpackage  helper
 * @copyright   Copyright (C) 2010 Massimo Giagnoni.
 * @license     http://www.symfony-project.org/license MIT
 * @version     SVN: $Id$
 */
function path_links($folder, $popup = false, $separator = ' / ')
{
  $path = array();
  
  $elements = $folder->getNode()->getAncestors();
  if($elements)
  {
    foreach($elements as $e)
    {
      $path[] = link_to($e->getName(), '@ly_media_asset_icons?folder_id=' . $e->getId() . ($popup ? '&popup=1' : ''));
    }
  }
  $path[] = $folder->getName();

  return implode($separator, $path);
}

function thumbnail_image_tag($asset, $folder_path = null, $type ='small', $options = array())
{
  return image_tag(thumbnail_uri($asset, $folder_path, $type), $options);
}
function thumbnail_image_path($asset, $folder_path = null, $type ='small')
{
  return image_path(thumbnail_uri($asset, $folder_path, $type));
}

function thumbnail_uri($asset, $folder_path = null, $type ='small')
{
  if($asset->supportsThumbnails())
  {
    $uri = '/' . (isset($folder_path) ? $folder_path : $asset->getFolderPath()) . lyMediaThumbnails::getThumbnailFolder() . '/' . $asset->getThumbnailFile($type);
  }
  else
  {
    $uri = '/lyMediaManagerPlugin/images/' . $asset->getThumbnailFile($type);
  }

   return $uri;
}
function format_asset_caption($asset)
{
  return(nl2br(wordwrap($asset->getFilename(),sfConfig::get('app_lyMediaManager_caption_row_max_chars',20), "\n", true)));
}