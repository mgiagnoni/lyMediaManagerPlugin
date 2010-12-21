<?php
/*
 * This file is part of the lyMediaManagerPlugin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Generator helper class for the lyMediaManagerPlugin lyMediaFolder module.
 *
 * @package     lyMediaManagerPlugin
 * @subpackage  lyMediaFolder
 * @copyright   Copyright (C) 2010 Massimo Giagnoni.
 * @license     http://www.symfony-project.org/license MIT
 * @version     SVN: $Id$
 */
class lyMediaFolderGeneratorHelper extends BaseLyMediaFolderGeneratorHelper
{
  public function linkToList($params)
  {
    $user = sfContext::getInstance()->getUser();
    if($user->getAttribute('view') == 'icons')
    {
      return '<li class="sf_admin_action_list">'.link_to(__('Go back'), '@ly_media_asset_icons?folder_id=' . $user->getAttribute('folder_id', 0) . ($user->getAttribute('popup', 0) ? '&popup=1' : '')) . '</li>';
    }
    elseif($user->getAttribute('view') == 'folder')
    {
      return '<li class="sf_admin_action_list">'.link_to(__('Go back'), '@ly_media_folder') .'</li>';
    }
    else
    {
      return '<li class="sf_admin_action_list">'.link_to(__('Go back'), '@ly_media_asset') . '</li>';
    }
  }
}
