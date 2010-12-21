<?php
/*
 * This file is part of the lyMediaManagerPlugin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Generator helper class for the lyMediaManagerPlugin lyMediaAsset module.
 *
 * @package     lyMediaManagerPlugin
 * @subpackage  lyMediaAsset
 * @copyright   Copyright (C) 2010 Massimo Giagnoni.
 * @license     http://www.symfony-project.org/license MIT
 * @version     SVN: $Id$
 */
class lyMediaAssetGeneratorHelper extends BaseLyMediaAssetGeneratorHelper
{
  public function linkToList($params)
  {
    $user = sfContext::getInstance()->getUser();
    if($user->getAttribute('view') == 'icons')
    {
      return '<li class="sf_admin_action_list">'.link_to(__('Go back'), '@ly_media_asset_icons' . ($user->getAttribute('popup', 0) ? '?&popup=1' : '') ).'</li>';
    }
    elseif($user->getAttribute('view') == 'folder')
    {
      return '<li class="sf_admin_action_list">'.link_to(__('Go back'), '@ly_media_folder') .'</li>';
    }
    else
    {
      return parent::linkToList($params);
    }
  }
  public function sortIcon($sort_dir, $popup)
  {
    if($sort_dir != 'desc')
    {
      $sort_dir = 'asc';
    }
    return link_to(
      image_tag("/lyMediaManagerPlugin/images/sort-$sort_dir","alt=$sort_dir"),
      '@ly_media_asset_icons?&dir=' .($sort_dir == 'desc' ? 'asc' : 'desc') . ($popup ? '&popup=1' : ''),
      array('title' => 'Switch sort direction')
    );
  }
}