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
}