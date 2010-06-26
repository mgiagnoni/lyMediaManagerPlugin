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

function thumbnail_image_tag($asset, $type ='small', $options = array())
{
  return image_tag(lyMediaTools::getThumbnailURI($asset, $type), $options);
}