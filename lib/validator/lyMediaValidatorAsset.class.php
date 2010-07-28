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
    $this->addMessage('file_exists', 'Can\'t save "%name%". A file with the same name exists in "%folder%"');
  }
  protected function doClean($values)
  {
    $folder = Doctrine::getTable('lyMediaFolder')
        ->find($values['folder_id']);

    if($folder === false)
    {
      throw new sfValidatorError($this, 'invalid');
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