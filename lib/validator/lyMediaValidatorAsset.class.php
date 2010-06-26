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
  }
  protected function doClean($values)
  {
    $folder = Doctrine::getTable('lyMediaFolder')
        ->find($values['folder_id']);

    if($folder === false)
    {
      throw new sfValidatorError($this, 'invalid');
    }

    if($values['filename'] instanceof sfvalidatedFile)
    {
      //Asset is being uploaded
      $fname = $values['filename']->getOriginalName();
    }
    else
    {
      //Asset is being edited
      $fname = $values['filename'];
    }

    $my_id = empty($values['id']) ? 0 : $values['id'];
    $assets = $folder->getAssets();
    
    foreach($assets as $a)
    {
      if($fname == $a->getFilename() && $a->getId() != $my_id)
      {
        //TODO: better error message needed
        throw new sfValidatorError($this, 'invalid');
      }
    }
    return $values;
  }
}