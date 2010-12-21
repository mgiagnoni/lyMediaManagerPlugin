<?php
/*
 * This file is part of the lyMediaManagerPlugin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * lyMediaFolder table class.
 *
 * @package     lyMediaManagerPlugin
 * @subpackage  model
 * @copyright   Copyright (C) 2010 Massimo Giagnoni.
 * @license     http://www.symfony-project.org/license MIT
 * @version     SVN: $Id$
 */
class PluginlyMediaFolderTable extends Doctrine_Table
{
  public function createRoot($root_name)
  {
    $folder = new lyMediaFolder();
    $folder->setName($root_name);
    $folder->save();
    $this->getTree()->createRoot($folder);
    return $folder;
  }
  public function getRoot()
  {
    return $this->getTree()->fetchRoot();
  }
  public function retrieveCurrent($folder_id)
  {
    if($folder_id)
    {
      $folder = $this->find($folder_id);
    }
    else
    {
      //Root
      $folder = $this->getRoot();
      if(false === $folder)
      {
        throw new sfException('You must create a root folder. Use the `php symfony media:create-root` command for that.');
      }
    }
    return $folder;
  }
  public function retrieveFolderList(Doctrine_Query $q)
  {
    $alias = $q->getRootAlias();
    $q->leftJoin($alias . '.Assets a');
    $q->orderBy($alias . '.lft');
    return $q;
  }
}