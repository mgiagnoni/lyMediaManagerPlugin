<?php
/*
 * This file is part of the lyMediaManagerPlugin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Base actions for the lyMediaManagerPlugin lyMediaFolder module.
 *
 * @package     lyMediaManagerPlugin
 * @subpackage  lyMediaFolder
 * @copyright   Copyright (C) 2010 Massimo Giagnoni.
 * @license     http://www.symfony-project.org/license MIT
 * @version     SVN: $Id$
 */
abstract class BaselyMediaFolderActions extends autoLyMediaFolderActions
{
  /**
   * Deletes a folder.
   *
   * @param sfWebRequest $request
   */
  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $object = $this->getRoute()->getObject();

    $this->dispatcher->notify(new sfEvent($this, 'admin.delete_object', array('object' => $object)));

    $redir = '@ly_media_asset_icons?folder_id=' . $this->getUser()->getAttribute('folder_id', 0) . ($this->getUser()->getAttribute('popup', 0) ? '&popup=1' : '');

    if ($object->getNode()->isValidNode())
    {
      if($object->getNode()->getDescendants() !== false)
      {
        $this->getUser()->setFlash('error', 'Can\'t delete folder as it contains sub-folders.');
        $this->redirect($redir);
      }
      
      $object->getNode()->delete();
    } 
    else 
    {
      $object->delete();
    }

    $this->getUser()->setFlash('notice', 'Folder successfully deleted.');

    $this->redirect($redir);
  }
}
