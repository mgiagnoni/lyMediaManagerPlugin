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
  protected $parent = null;
  protected $old_path = null;

  public function create(lyMediaFolder $parent)
  {
    if(!$this->getName())
    {
      throw new lyMediaException('Can\'t create a folder without name');
    }
    $this->setParent($parent);
    $this->save();
  }
  public function move($new_parent)
  {
    $this->setParent($new_parent);
    $this->save();
  }
  public function getIndentName()
  {
    return str_repeat('-- ', $this->level) . $this->name;
  }

  public function getPath()
  {
    return $this->getNode()->getPath(DIRECTORY_SEPARATOR, true) . DIRECTORY_SEPARATOR;
  }
  public function postInsert($event)
  {
    if($this->getParent())
    {
      $this->getNode()->insertAsLastChildOf($this->getParent());
    }
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
  public function preInsert($event)
  {
    $this->updateRelativePath();
    lyMediaTools::createAssetFolder($this->getRelativePath());
  }
  public function preUpdate($event)
  {
    if($this->old_path)
    {
      //Moved
      lyMediaTools::moveAssetFolder($this->old_path, $this->getRelativePath());
      $this->old_path = null;
      $this->getNode()->moveAsLastChildOf($this->parent);
    }
  }
  public function retrieveAssets($params)
  {
    return $this->retrieveAssetsQuery($params)->execute();
  }
  public function retrieveAssetsQuery($params)
  {
    $by = $params['sort_field'] == 'date' ? 'created_at' : 'filename';
    $dir = $params['sort_dir'] == 'desc' ? ' desc' : '';

    return Doctrine_Query::create()
      ->from('lyMediaAsset a')
      ->where('a.folder_id = ?', $this->getId())
      ->orderBy($by . $dir);
  }
  public function setParent($folder)
  {
    if(!isset($this->parent) || $this->parent->getId() != $folder->getId())
    {
      $this->parent = $folder;
      $this->updateRelativePath();
    }
  }
  public function getParent()
  {
    if(isset($this->parent))
    {
      return $this->parent;
    }
    else
    {
      return $this->getNode()->getParent();
    }
  }
  
  protected function updateRelativePath()
  {
    $relative_path = ($this->getParent() ? $this->getParent()->getRelativePath() : '') . $this->getName() . '/';

    if($this->getRelativePath() != $relative_path)
    {
      $this->old_path = $this->getRelativePath();
      $this->setRelativePath($relative_path);
    }
  }
}