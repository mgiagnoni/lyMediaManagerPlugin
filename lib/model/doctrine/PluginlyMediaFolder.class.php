<?php
/*
 * This file is part of the lyMediaManagerPlugin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * lyMediaFolder record class.
 *
 * @package     lyMediaManagerPlugin
 * @subpackage  model
 * @copyright   Copyright (C) 2010 Massimo Giagnoni.
 * @license     http://www.symfony-project.org/license MIT
 * @version     SVN: $Id$
 */
abstract class PluginlyMediaFolder extends BaselyMediaFolder
{
  protected $parent_id = null;

  public function changedParent($new_parent_id)
  {
    $this->parent_id = $new_parent_id;
  }

  public function getIndentName()
  {
    return str_repeat('-- ', $this->level) . $this->name;
  }

  public function getPath()
  {
    return $this->getNode()->getPath(DIRECTORY_SEPARATOR, true) . DIRECTORY_SEPARATOR;
  }
  public function preDelete($event)
  {
    $record = $event->getInvoker();
    
    //Delete folder contents
    foreach($record->getAssets() as $a)
    {
      $a->delete();
    }
    lyMediaTools::deleteAssetFolder($record->getRelativePath());
  }
  public function preSave($event)
  {
    $record = $event->getInvoker();

    if(empty($this->parent_id))
    {
      $parent = $record->getNode()->getParent();
    }
    else
    {
      $parent = $this->getTable()->find($this->parent_id);
    }
    
    $relative_path = '';

    if($parent)
    {
      $relative_path = $parent->getNode()->getPath('/', true) . '/';
    }
    
    $relative_path .= $record->getName() . '/';
    
    if(!$record->exists())
    {
      lyMediaTools::createAssetFolder($relative_path);
    }
    else if($record->getRelativePath() != $relative_path)
    {
      lyMediaTools::moveAssetFolder($record->getRelativePath(), $relative_path);
    }

    $record->setRelativePath($relative_path);
  }
}