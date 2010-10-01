<?php
/*
 * This file is part of the lyMediaManagerPlugin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * lyMediaManagerPlugin asset validator.
 *
 * @package     lyMediaManagerPlugin
 * @subpackage  validator
 * @copyright   Copyright (C) 2010 Massimo Giagnoni.
 * @license     http://www.symfony-project.org/license MIT
 * @version     SVN: $Id$
 */
class lyMediaValidatorAsset extends sfValidatorBase
{
  public function configure($options = array(), $messages = array())
  {
    $this->setMessage('invalid', 'Can\'t save file.');
    $this->addMessage('folder_unwritable', 'File system error: "%folder%" folder is not writable or doesn\'t exist.');
    $this->addMessage('file_exists', 'Can\'t save "%name%". A file with the same name exists in "%folder%".');
  }
  protected function doClean($values)
  {
    $folder = lyMediaFolderTable::getInstance()
        ->find($values['folder_id']);

    if($folder === false)
    {
      throw new sfValidatorError($this, 'invalid');
    }

    $fs = new lyMediaFileSystem();
    if(!$fs->is_dir($folder->getRelativePath()) || !$fs->is_writable($folder->getRelativePath()))
    {
      throw new sfValidatorError($this, 'folder_unwritable', array('folder' => $folder->getRelativePath()));
    }
    
    $my_id = empty($values['id']) ? 0 : $values['id'];
    $assets = $folder->getAssets();
    
    foreach($assets as $a)
    {
      if($values['filename'] == $a->getFilename() && $a->getId() != $my_id)
      {
        throw new sfValidatorError($this, 'file_exists', array('name' => $values['filename'], 'folder' => $folder->getName()));
      }
    }
    return $values;
  }
}